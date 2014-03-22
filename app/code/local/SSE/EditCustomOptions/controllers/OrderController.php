<?php
class SSE_EditCustomOptions_OrderController extends Mage_Core_Controller_Front_Action
{
	protected $_quote;
	protected $_order;
	protected $_item;

	/**
	 * similar to Mage_Checkout_CartController::updateItemOptionsAction(), but saves the quote
	 * directy without using the cart and then copies the updated options to the order
	 */
	public function updateItemOptionsAction()
	{
		$options = $this->_getOrderItem()->getProductOptions();
		// + operator instead of array_merge to handle duplicate numeric keys
		$options['info_buyRequest']['options'] = $this->getRequest()->getParam('options', array()) + $options['info_buyRequest']['options'];

		$quoteItem = $this->_getQuote()->getItemById($this->_getOrderItem()->getQuoteItemId());
		$quoteItem = $this->_getQuote()->updateItem(
				$this->_getOrderItem()->getQuoteItemId(),
				$options['info_buyRequest']);

		$this->_getQuote()->save();
		$this->_getOrderItem()->setQuoteItemId($quoteItem->getId())
			->save();

		// reload quote item with options (only provided through collection)
		//TODO option collection with addItemFilter might work too
		$quoteItem = $quoteItem->getCollection()->setQuote($this->_getQuote())->addFieldToFilter('item_id', $quoteItem->getId())->getFirstItem();

		$tmpOrderItem = Mage::getModel('sales/convert_quote')->itemToOrderItem($quoteItem);
		$this->_getOrderItem()->setProductOptions($tmpOrderItem->getProductOptions())->save();

		Mage::getSingleton('core/session')->addSuccess('Updated options');
		$this->_redirect('sales/order/view', array('order_id' => $this->_getOrder()->getId()));
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
		if ($this->_item === null) {
			$itemId = (int) $this->getRequest()->getParam('item_id');
			if ($itemId === 0) {
				Mage::throwException('No order item ID specified');
			}
			$item = Mage::getModel('sales/order_item')->load($itemId);
			if ($item->getId() == $itemId) {
				$this->_item = $item;
			} else {
				Mage::throwException("Order item ID {$itemId} not found");
			}
		}
		return $this->_item;
	}
}