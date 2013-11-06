<?php
/**
 * If this extension is added to Product_Controller, child products will
 * redirect to the parent page using a 301 redirect. This allows the
 * child products to be searchable etc.
 *
 * This is OPTIONAL, depending on how you want your site to function.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 11.05.2013
 * @package shop_groupedproducts
 */
class RedirectChildProductsExtension extends Extension
{
	public function onAfterInit() {
		$product = $this->owner->data();
		$parent = $product->Parent();
		if ($parent && $parent->exists() && $parent instanceof GroupedProduct) {
			$this->owner->redirect( $parent->Link() );
		}
	}
}