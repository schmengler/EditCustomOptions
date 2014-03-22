<?php
/**
 * Popup template
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_Block_Popup extends Mage_Core_Block_Template
{
	const ALIAS = 'editcustomoptions/popup';

	/**
	 * Order item
	 *
	 * @var Mage_Sales_Model_Order_Item
	 */
	protected $_item;

	/**
	 * initialize options block
	 * 
	 * (non-PHPdoc)
	 * @see Mage_Core_Block_Abstract::_beforeToHtml()
	 */
	protected function _beforeToHtml()
	{
		parent::_construct();

		$wrapperBlock = $this->getLayout()->createBlock('catalog/product_view', 'additional.product.info.customoptions.wrapper',
				array('template' => 'catalog/product/view/options/wrapper.phtml', 'product_id' => $this->getItem()->getProductId()));
		$this->append($wrapperBlock, 'wrapper');

		/* @var $optionsBlock SSE_EditCustomOptions_Block_Options */
		$optionsBlock = $this->getLayout()->createBlock(SSE_EditCustomOptions_Block_Options::ALIAS, 'additional.product.info.customoptions.options');
		$optionsBlock->init($this->getItem());
		$wrapperBlock->append($optionsBlock, 'options');
	}
	/**
	 * Set order item
	 * 
	 * @param Mage_Sales_Model_Order_Item $item
	 * @return SSE_EditCustomOptions_Block_Popup
	 */
	public function setItem(Mage_Sales_Model_Order_Item $item)
	{
		$this->_item = $item;
		return $this;
	}
	/**
	 * Returns order item
	 * 
	 * @return Mage_Sales_Model_Order_Item
	 */
	public function getItem()
	{
		return $this->_item;
	}
	public function getQty()
	{
		return $this->getItem()->getQtyOrdered() * 1;
	}
	/**
	 * Returns URL for form action
	 * 
	 * @return string
	 */
	public function getFormAction()
	{
		return Mage::getUrl('editcustomoptions/order/updateItemOptions', array('item_id' => $this->getItem()->getId()));
	}
}