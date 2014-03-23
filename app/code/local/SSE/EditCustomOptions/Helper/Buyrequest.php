<?php
/**
 * Helper that deals with Magentos $buyRequest object
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_Helper_Buyrequest
{
	const ALIAS = 'editcustomoptions/buyrequest';

	/**
	 * Return buyRequest object (with custom options) for order item
	 * 
	 * @param Mage_Sales_Model_Order_Item $item
	 * @return Varien_Object
	 */
	public function getBuyRequestFromOrderItem(Mage_Sales_Model_Order_Item $item)
	{
		$buyRequest = new Varien_Object();
		$_options = array();
		foreach ($this->_getItemOptions($item) as $_option) {
			if ($_option['option_type'] === 'file') {
				$_option['option_value'] = unserialize($_option['option_value']);
			}
			$_options[$_option['option_id']] = $_option['option_value'];
		}
		$buyRequest->setOptions($_options);
		return $buyRequest;
	}
	/**
	 * Return custom options from item
	 * 
	 * @see Mage_Sales_Block_Order_Item_Renderer_Default::getItemOptions()
	 * @return array
	 */
	protected function _getItemOptions(Mage_Sales_Model_Order_Item $item)
	{
		$result = array();
		if ($options = $item->getProductOptions()) {
			if (isset($options['options'])) {
				$result = array_merge($result, $options['options']);
			}
			if (isset($options['additional_options'])) {
				$result = array_merge($result, $options['additional_options']);
			}
			if (isset($options['attributes_info'])) {
				$result = array_merge($result, $options['attributes_info']);
			}
		}
		return $result;
	}
	/**
	 * Merges custom options from buyRequest into existing buyRequest
	 * 
	 * @param array $originalRequest original buyRequest in array form
	 * @param array $updateRequest   buyRequest with updated custom options in array form
	 * @return Varien_Object
	 */
	public function mergeBuyRequest(array $originalRequest, array $updateRequest)
	{
		// merge custom options
		// + operator instead of array_merge to handle duplicate numeric keys
		$originalRequest['options'] = $updateRequest['options'] + $originalRequest['options'];
		
		// merge additional request parameters (necessary for changed files)
		unset($updateRequest['options']);
		unset($updateRequest['qty']);
		return new Varien_Object($updateRequest + $originalRequest);
	}
}