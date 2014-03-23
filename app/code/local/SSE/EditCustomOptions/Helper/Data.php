<?php
/**
 * Default helper
 * 
 * @author Fabian Schmengler <fabian@schmengler-se.de>
 * @category SSE
 * @package SSE_EditCustomOptions
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
    /**
     * Returns true if final price of product depends on given option
     * 
     * @param Mage_Catalog_Model_Product_Option $option
     * @return boolean
     */
    public function isOptionAffectingPrice(Mage_Catalog_Model_Product_Option $option)
    {
        foreach ($option->getValuesCollection() as $value) {
            /* @var $value Mage_Catalog_Model_Product_Option_Value */
            if ($value->getPrice() * 1 > 0) {
                return true;
            }
        }
        return false;
    }
}
