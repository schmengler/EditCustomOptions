<?php
/**
 * Default helper
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Returns true if order may be edited by currently logged in customer
	 * 
	 * @param Mage_Sales_Model_Order $order
	 * @return boolean
	 */
	public function isOrderEditable(Mage_Sales_Model_Order $order)
	{
		if (!$order->getCustomerId()) {
			return false;
		}
		if (Mage::getSingleton('customer/session')->getCustomerId() != $order->getCustomerId()) {
			return false;
		}
		$allowedStates = explode(',', Mage::getStoreConfig('sales/editcustomoptions/allowed_order_state'));
		return in_array($order->getState(), $allowedStates);
	}
}
