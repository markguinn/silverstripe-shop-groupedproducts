<?php
/**
 * Extension for child products if using GroupCartForm.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 04.25.2014
 * @package shop_groupedproducts
 */
class GroupedCartFormChildHooks extends DataExtension
{
	private static $quantity_field_type = 'number';

	/**
	 * @param String $label
	 * @return NumericField
	 */
	public function getQuantityField($label = '') {
		$f = new NumericField("Product[{$this->owner->ID}][Quantity]", $label);
		$f->setAttribute('type', Config::inst()->get('GroupedCartFormChildHooks', 'quantity_field_type'));
		$f->addExtraClass('grouped-quantity');
		return $f;
	}


	/**
	 * @return ArrayList
	 */
	public function getVariationFields() {
		$out = array();
		$attributes = $this->owner->VariationAttributeTypes()->sort('Label');

		foreach ($attributes as $attribute) {
			$field = $attribute->getDropDownField(null, $this->owner->possibleValuesForAttributeType($attribute));
			if ($field) {
				$field->setName("Product[{$this->owner->ID}][Attributes][$attribute->ID]");
				$out[] = $field;
			} else {
				$out[] = new LiteralField('empty'.$attribute->ID, '');
			}
		}

//		if(self::$include_json){ //TODO: this should be included as js validation instead
//			$vararray = array();
//			if($vars = $product->Variations()){
//				foreach($vars as $var){
//					$vararray[$var->ID] = $var->AttributeValues()->map('ID','ID');
//				}
//			}
//			$fields->push(new HiddenField('VariationOptions','VariationOptions',json_encode($vararray)));
//		}

		return new ArrayList($out);
	}
}