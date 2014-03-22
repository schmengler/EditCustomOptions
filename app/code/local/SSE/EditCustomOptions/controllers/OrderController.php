<?php
/**
 * Controller for custom option edit form action
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_OrderController extends Mage_Core_Controller_Front_Action
{
	protected $_quote;
	protected $_quoteItem;
	protected $_order;
	protected $_orderItem;

	/**
	 * similar to Mage_Checkout_CartController::updateItemOptionsAction(), but saves the quote
	 * directy without using the cart and then copies the updated options to the order
	 */
	public function updateItemOptionsAction()
	{
		$this->_updateQuoteItem();
		$this->_updateOrderItem();

		Mage::getSingleton('core/session')->addSuccess('Updated options');
		$this->_redirect('sales/order/view', array('order_id' => $this->_getOrder()->getId()));
	}
	/**
	 * Updates custom options in quote item based on request
	 * 
	 * @return SSE_EditCustomOptions_OrderController
	 */
	protected function _updateQuoteItem()
	{
		$this->_quoteItem = $this->_getQuote()->getItemById($this->_getOrderItem()->getQuoteItemId());
		$this->_quoteItem = $this->_getQuote()->updateItem(
				$this->_getOrderItem()->getQuoteItemId(),
				$this->_getUpdatedBuyRequest());
		
		$this->_getQuote()->save();

		return $this;
	}
	/**
	 * Updates custom options in order item based on updated quote item
	 * 
	 * @return SSE_EditCustomOptions_OrderController
	 */
	protected function _updateOrderItem()
	{
		$tmpOrderItem = Mage::getModel('sales/convert_quote')->itemToOrderItem($this->_quoteItem);
		$this->_getOrderItem()
			->setProductOptions($tmpOrderItem->getProductOptions())
			->setQuoteItemId($this->_quoteItem->getId())
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
	 * Returns quote for the order
	 * 
	 * @return Mage_Sales_Model_Quote
	 */
	protected function _getQuote()
	{
		if ($this->_quote === null) {
			$this->_quote = Mage::getModel('sales/quote')->load($this->_getOrder()->getQuoteId());
		}
		return $this->_quote;
	}
	/**
	 * Returns the order
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder()
	{
		if ($this->_order === null) {
			$this->_order = $this->_getOrderItem()->getOrder();
		}
		return $this->_order;
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