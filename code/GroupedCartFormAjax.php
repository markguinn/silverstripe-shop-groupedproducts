<?php
/**
 * Ajax responses for the grouped cart form
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 07.15.2014
 * @package shop_groupedproducts
 */
class GroupedCartFormAjax extends Extension
{
	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHTTPResponse $response
	 * @param GroupedProduct $groupedProduct
	 * @param array $data
	 * @param GroupedCartForm $form [optional]
	 */
	public function updateGroupCartResponse(&$request, &$response, $groupedProduct, $data, $form=null) {
		if ($request->isAjax() && $this->owner->getController()->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getController()->getAjaxResponse();
			$this->setupRenderContexts($response, $groupedProduct, $form);

			// Because ShoppingCart::current() calculates the order once and
			// then remembers the total, and that was called BEFORE the product
			// was added, we need to recalculate again here. Under non-ajax
			// requests the redirect eliminates the need for this but under
			// ajax the total lags behind the subtotal without this.
			$order = ShoppingCart::curr();
			$order->calculate();

			$response->pushRegion('SideCart', $this->owner->getController());
			$response->triggerEvent('cartadd');
			$response->triggerEvent('cartchange', array('action' => 'add'));
		}
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHTTPResponse $response
	 * @param GroupedProduct $groupedProduct
	 * @param array $data
	 * @param GroupedCartForm $form [optional]
	 */
	public function updateGroupWishListResponse(&$request, &$response, $groupedProduct, $data, $form=null) {
		if ($request->isAjax() && $this->owner->getController()->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getController()->getAjaxResponse();
			$this->setupRenderContexts($response, $groupedProduct, $form);
			$response->triggerEvent('wishlistadd');
			$response->triggerEvent('wishlistchange', array('action' => 'add'));

			$n = 0;
			foreach ($data['Product'] as $info) {
				if ($info['Quantity'] > 0) $n++;
			}

			$s = $n == 1 ? '' : 's';
			$response->triggerEvent('statusmessage', "$n item{$s} added to " . WishList::current()->getTitle());
		}
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHTTPResponse $response
	 * @param GroupedProduct $groupedProduct
	 * @param array $data
	 * @param GroupedCartForm $form [optional]
	 */
	public function updateErrorResponse(&$request, &$response, $groupedProduct, $data, $form) {
		if ($request->isAjax() && $this->owner->getController()->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getController()->getAjaxResponse();

			$response->triggerEvent('statusmessage', array(
				'content'   => $form->Message(),
				'type'      => $form->MessageType(),
			));

			$form->clearMessage();
		}
	}


	/**
	 * Adds some standard render contexts for pulled regions.
	 *
	 * @param AjaxHTTPResponse $response
	 * @param GroupedProduct $groupedProduct
	 * @param Form $form
	 */
	protected function setupRenderContexts(AjaxHTTPResponse $response, $groupedProduct, $form) {
		if ($this->owner->getController()->hasMethod('Cart')) {
			$cart = $this->owner->getController()->Cart();
			if ($cart instanceof ViewableData) {
				$response->addRenderContext('CART', $this->owner->getController()->Cart());
			}
		}

		$response->addRenderContext('PRODUCT', $groupedProduct);
		$response->addRenderContext('FORM', $form);
	}
}
