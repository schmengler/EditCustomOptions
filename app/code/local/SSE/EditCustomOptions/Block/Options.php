<?php
/**
 * Renders the custom options popup in order overview
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_Block_Options extends Mage_Catalog_Block_Product_View_Options
{
	const ALIAS = 'editcustomoptions/options';

	/**
	 * Order item
	 * 
	 * @var Mage_Sales_Model_Order_Item
	 */
	protected $_item;

	/**
	 * Initialize block with order item
	 * 
	 * @param Mage_Sales_Model_Order_Item $item
	 * @return SSE_EditCustomOptions_Block_Options
	 */
	public function init(Mage_Sales_Model_Order_Item $item)
	{
		$this->_item = $item;

		$_product = $this->_item->getProduct();
		$_buyRequest = new Varien_Object();
		$_options = array();
		foreach ($this->_getItemOptions() as $_option) {
			$_options[$_option['option_id']] = $_option['option_value'];
		}
		$_buyRequest->setOptions($_options);
		Mage::helper('catalog/product')->prepareProductOptions($_product, $_buyRequest);
		$this->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
	         ->addOptionRenderer('file', 'catalog/product_view_options_type_file', 'sse/editcustomoptions/catalog/product/view/options/type/file.phtml')
	         ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
	         ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml')
	         ->setTemplate('catalog/product/view/options.phtml')
	         ->setProduct($_product);

		return $this;
	}
	/**
	 * Return custom options from item
	 * 
	 * @see Mage_Sales_Block_Order_Item_Renderer_Default::getItemOptions()
	 * @return array
	 */
	protected function _getItemOptions()
	{
		$result = array();
		if ($options = $this->_item->getProductOptions()) {
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
}