<?php
/**
 * Defines a product that can contain other products.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 11.05.2013
 * @package shop_groupedproducts
 */
class GroupedProduct extends Product
{
	private static $allowed_children = array('Product');
	private static $default_child = 'Product';

	/** @var bool - Can you purchase the group as a distinct product. Different from canPurchase. */
	private static $can_purchase_group = false;


	/**
	 * I'm not using the Children method from Hierarchy for two reasons.
	 * First, that method checks ShowInMenus and I want that to be able
	 * to be turned off for child products.
	 * Second, if we ever add many-to-many support via an extension or
	 * child class this method can be easily overridden, in which case
	 * templates and other classes don't need to know or care or change.
	 *
	 * @return DataList
	 */
	public function ChildProducts() {
		return Product::get()->filter('ParentID', $this->ID)->sort('Sort');
	}
}

class GroupedProduct_Controller extends Product_Controller
{

}