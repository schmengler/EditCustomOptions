<?php
/**
 * Controller for custom option edit form action
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_OrderController extends Mage_Core_Controller_Front_Action
{
	protected $_orderItem;

	/**
	 * Return module helper
	 * 
	 * @return SSE_EditCustomOptions_Helper_Data
	 */
	protected function _helper()
	{
		return Mage::helper('editcustomoptions');
	}
	/**
	 * Return session instance
	 * 
	 * @return Mage_Core_Model_Session
	 */
	protected function _session()
	{
		return Mage::getSingleton('core/session');
	}
	public function editAction()
	{
		if ($this->_helper()->isOrderEditable($this->_getOrderItem()->getOrder())) {
			Mage::register('current_item', $this->_getOrderItem());
			Mage::register('product', $this->_getOrderItem()->getProduct());
			$this->loadLayout();
		} else {
			$this->_session()->addError($this->__('Order cannot be changed anymore'));
			$this->loadLayout(array('default', 'error_noform'));
		}
		$this->renderLayout();
	}
	/**
	 * similar to Mage_Checkout_CartController::updateItemOptionsAction(), but saves the quote
	 * directy without using the cart and then copies the updated options to the order
	 */
	public function updateItemOptionsAction()
	{
		if (Mage::helper('editcustomoptions/data')->isOrderEditable($this->_getOrderItem()->getOrder())) {
			$this->_updateOrderItem($this->_updateQuoteItem())->_addStatusHistoryComment();
			Mage::getSingleton('core/session')->addSuccess($this->__('Updated Custom Options'));
		} else {
			Mage::getSingleton('core/session')->addError($this->__('Order cannot be changed anymore'));
		}

		$this->_redirect('sales/order/view', array('order_id' => $this->_getOrderItem()->getOrder()->getId()));
	}
	/**
	 * Updates custom options in quote item based on request
	 * 
	 * @return Mage_Sales_Model_Quote_Item
	 */
	protected function _updateQuoteItem()
	{
		/* @var $quote Mage_Sales_Model_Quote */
		$quote = Mage::getModel('sales/quote')->load($this->_getOrderItem()->getOrder()->getQuoteId());
		$quoteItem = $quote->updateItem(
				$this->_getOrderItem()->getQuoteItemId(),
				$this->_getUpdatedBuyRequest());
		$quoteItem->save();

		return $quoteItem;
	}
	/**
	 * Updates custom options in order item based on updated quote item
	 * 
	 * @return SSE_EditCustomOptions_OrderController
	 */
	protected function _updateOrderItem(Mage_Sales_Model_Quote_Item $quoteItem)
	{
		$tmpOrderItem = Mage::getModel('sales/convert_quote')->itemToOrderItem($quoteItem);
		$this->_getOrderItem()
			->setProductOptions($tmpOrderItem->getProductOptions())
			->setSku($tmpOrderItem->getSku())
			->setQuoteItemId($quoteItem->getId())
			->save();

		return $this;
	}
	/**
	 * Add comment in order history
	 * 
	 * @return SSE_EditCustomOptions_OrderController
	 */
	protected function _addStatusHistoryComment()
	{
		$this->_getOrderItem()->getOrder()
			->addStatusHistoryComment($this->__('Updated Custom Options'))
			->save();

		return $this;
	}
	/**
	 * Returns the buyRequest object with updated custom options.
	 * 
	 * @return Varien_Object
	 */
	protected function _getUpdatedBuyRequest()
	{
		$options = $this->_getOrderItem()->getProductOptions();
		$originalRequest = $options['info_buyRequest'];
		$updateRequest = $this->getRequest()->getParams();

		foreach ($options['options'] as $_option) {
			// unset options that affect price in request
			if ($this->_helper()->isOptionAffectingPrice($this->_getOrderItem()->getProduct()->getOptionById($_option['option_id']))) {
				unset($updateRequest['options'][$_option['option_id']]);
			}
			// unset generated file data in original buyRequest
			if ($_option['option_type'] === 'file') {
				unset($originalRequest['options'][$_option['option_id']]);
			}
		}

		return Mage::helper(SSE_EditCustomOptions_Helper_Buyrequest::ALIAS)
			->mergeBuyRequest($originalRequest, $updateRequest);
	}
	/**
	 * Returns the order item
	 * 
	 * @return Mage_Sales_Model_Order_Item
	 */
	protected function _getOrderItem()
	{
		if ($this->_orderItem === null) {
			$itemId = (int) $this->getRequest()->getParam('item_id');
			if ($itemId === 0) {
				Mage::throwException('No order item ID specified');
			}
			$item = Mage::getModel('sales/order_item')->load($itemId);
			if ($item->getId() == $itemId) {
				$this->_orderItem = $item;
			} else {
				Mage::throwException("Order item ID {$itemId} not found");
			}
		}
		return $this->_orderItem;
	}
}