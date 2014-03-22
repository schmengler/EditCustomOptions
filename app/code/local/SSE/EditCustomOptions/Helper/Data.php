<?php
/**
 * Default helper
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isOrderEditable(Mage_Sales_Model_Order $order)
	{
		$allowedStates = explode(',', Mage::getStoreConfig('sales/editcustomoptions/allowed_order_state'));
		return in_array($order->getState(), $allowedStates);
	}
}
