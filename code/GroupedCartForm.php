<?php
/**
 * Combined AddProductForm and VariationForm for multiple products.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 04.25.2014
 * @package shop_groupedproducts
 */
class GroupedCartForm extends Form
{
	/**
	 * @param Controller     $controller
	 * @param String         $name
	 * @param GroupedProduct $product
	 */
	public function __construct($controller, $name, GroupedProduct $product) {
		$fields = new FieldList();

		// add quantity etc fields
		// TODO: These should probably be cleaned up and put in composite fields or something
		foreach ($product->ChildProducts() as $child) {
			if (!$child->hasExtension('GroupedCartFormChildHooks')) user_error('Child Products must have GroupedCartFormChildHooks applied.');
			$vars = $child->hasMethod('Variations') ? $child->Variations() : null;
			if ($vars && $vars->count() > 0) {
				foreach ($child->getVariationFields() as $f) {
					$fields->push($f);
				}
			}

			$fields->push( $child->getQuantityField() );
		}

		$actions = new FieldList(array(
			FormAction::create('addtocart', _t('GroupedCartForm.ADD', 'Add'))
		));

		parent::__construct($controller, $name, $fields, $actions);
	}


	/**
	 * Handles form submission
	 * @param array $data
	 */
	public function addtocart(array $data) {
		if (empty($data) || empty($data['Product']) || !is_array($data['Product'])) {
			$this->sessionMessage(_t('GroupedCartForm.EMPTY', 'Please select at least one product.'), 'bad');
			$this->controller->redirectBack();
			return;
		}

		$cart = ShoppingCart::singleton();

		foreach ($data['Product'] as $id => $prodReq) {
			if (!empty($prodReq['Quantity']) && $prodReq['Quantity'] > 0) {
				$prod = Product::get()->byID($id);
				if ($prod && $prod->exists()) {
					$saveabledata = (!empty($this->saveablefields)) ? Convert::raw2sql(array_intersect_key($data,array_combine($this->saveablefields,$this->saveablefields))) : $prodReq;
					$buyable = $prod;

					if (isset($prodReq['Attributes'])) {
						$buyable = $prod->getVariationByAttributes($prodReq['Attributes']);
						if (!$buyable || !$buyable->exists()) {
							$this->sessionMessage("{$prod->InternalItemID} is not available with the selected options.", "bad");
							return;
						}
					}

					if (!$cart->add($buyable, (int)$prodReq['Quantity'], $saveabledata)) {
						$this->sessionMessage($cart->getMessage(),$cart->getMessageType());
						$this->controller->redirectBack();
						return;
					}
				}
			}
		}

		ShoppingCart_Controller::direct($cart->getMessageType());
	}
}