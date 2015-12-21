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
    public function __construct($controller, $name, GroupedProduct $product)
    {
        $fields = new FieldList();

        // add quantity etc fields
        // TODO: These should probably be cleaned up and put in composite fields or something
        foreach ($product->ChildProducts() as $child) {
            if (!$child->hasExtension('GroupedCartFormChildHooks')) {
                user_error('Child Products must have GroupedCartFormChildHooks applied.');
            }
            $vars = $child->hasMethod('Variations') ? $child->Variations() : null;
            if ($vars && $vars->count() > 0) {
                foreach ($child->getVariationFields() as $f) {
                    $fields->push($f);
                }
            }

            $fields->push($child->getQuantityField());
        }

        $actions = new FieldList(array(
            FormAction::create('addtocart', _t('GroupedCartForm.ADD', 'Add to Cart'))
        ));

        // integrate with wishlist module
        if (class_exists('WishList')) {
            $actions->unshift(FormAction::create('addtowishlist', _t('GroupedCartForm.ADDTOWISHLIST', 'Add to Wish List')));
        }

        parent::__construct($controller, $name, $fields, $actions);
    }


    /**
     * Handles form submission
     * @param array $data
     * @return bool|\SS_HTTPResponse
     */
    public function addtocart(array $data)
    {
        $groupedProduct = $this->getController()->data();

        if (empty($data) || empty($data['Product']) || !is_array($data['Product'])) {
            $this->sessionMessage(_t('GroupedCartForm.EMPTY', 'Please select at least one product.'), 'bad');
            $this->extend('updateErrorResponse', $this->request, $response, $groupedProduct, $data, $this);
            return $response ? $response : $this->controller->redirectBack();
        }

        $cart = ShoppingCart::singleton();

        foreach ($data['Product'] as $id => $prodReq) {
            if (!empty($prodReq['Quantity']) && $prodReq['Quantity'] > 0) {
                $prod = Product::get()->byID($id);
                if ($prod && $prod->exists()) {
                    $saveabledata = (!empty($this->saveablefields)) ? Convert::raw2sql(array_intersect_key($data, array_combine($this->saveablefields, $this->saveablefields))) : $prodReq;
                    $buyable = $prod;

                    if (isset($prodReq['Attributes'])) {
                        $buyable = $prod->getVariationByAttributes($prodReq['Attributes']);
                        if (!$buyable || !$buyable->exists()) {
                            $this->sessionMessage("{$prod->InternalItemID} is not available with the selected options.", "bad");
                            $this->extend('updateErrorResponse', $this->request, $response, $groupedProduct, $data, $this);
                            return $response ? $response : $this->controller->redirectBack();
                        }
                    }

                    if (!$cart->add($buyable, (int)$prodReq['Quantity'], $saveabledata)) {
                        $this->sessionMessage($cart->getMessage(), $cart->getMessageType());
                        $this->extend('updateErrorResponse', $this->request, $response, $groupedProduct, $data, $this);
                        return $response ? $response : $this->controller->redirectBack();
                    }
                }
            }
        }

        $this->extend('updateGroupCartResponse', $this->request, $response, $groupedProduct, $data, $this);
        return $response ? $response : ShoppingCart_Controller::direct($cart->getMessageType());
    }


    /**
     * @param array $data
     */
    public function addtowishlist(array $data)
    {
        if (!class_exists('WishList')) {
            user_error('Wish List module not installed.');
        }
        $groupedProduct = $this->getController()->data();

        if (empty($data) || empty($data['Product']) || !is_array($data['Product'])) {
            $this->sessionMessage(_t('GroupedCartForm.EMPTY', 'Please select at least one product.'), 'bad');
            $this->extend('updateErrorResponse', $this->request, $response, $groupedProduct, $data, $this);
            return $response ? $response : $this->controller->redirectBack();
        }

        $list = WishList::current();

        foreach ($data['Product'] as $id => $prodReq) {
            if (!empty($prodReq['Quantity']) && $prodReq['Quantity'] > 0) {
                $prod = Product::get()->byID($id);
                if ($prod && $prod->exists()) {
                    $buyable = $prod;

                    if (isset($prodReq['Attributes'])) {
                        $buyable = $prod->getVariationByAttributes($prodReq['Attributes']);
                        if (!$buyable || !$buyable->exists()) {
                            $this->sessionMessage("{$prod->InternalItemID} is not available with the selected options.", "bad");
                            $this->extend('updateErrorResponse', $this->request, $response, $groupedProduct, $data, $this);
                            return $response ? $response : $this->controller->redirectBack();
                        }
                    }

                    $list->addBuyable($buyable);
                }
            }
        }

        $this->extend('updateGroupWishListResponse', $this->request, $response, $groupedProduct, $data, $this);
        return $response ? $response : $this->controller->redirect(WishListPage::inst()->Link());
    }
}
