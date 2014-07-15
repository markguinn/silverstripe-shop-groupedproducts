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
			$response->addRenderContext('PRODUCT', $groupedProduct);
			$response->addRenderContext('FORM', $form);
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
			$response->addRenderContext('PRODUCT', $groupedProduct);
			$response->addRenderContext('FORM', $form);
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
}