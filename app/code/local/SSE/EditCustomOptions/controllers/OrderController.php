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
	 * similar to Mage_Checkout_CartController::updateItemOptionsAction(), but saves the quote
	 * directy without using the cart and then copies the updated options to the order
	 */
	public function updateItemOptionsAction()
	{
		$this->_updateOrderItem($this->_updateQuoteItem())->_addStatusHistoryComment();

		Mage::getSingleton('core/session')->addSuccess($this->__('Updated Custom Options'));
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
	 * Returns the buyRequest array with updated custom options
	 * 
	 * @return array
	 */
	protected function _getUpdatedBuyRequest()
	{
		$options = $this->_getOrderItem()->getProductOptions();
		// + operator instead of array_merge to handle duplicate numeric keys
		$options['info_buyRequest']['options'] = $this->getRequest()->getParam('options', array()) + $options['info_buyRequest']['options'];
		return $options['info_buyRequest'];
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