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
	 * @var string
	 */
	protected $_optionsDivId;

	/**
	 * initialize options block
	 * 
	 * (non-PHPdoc)
	 * @see Mage_Core_Block_Abstract::_beforeToHtml()
	 */
	protected function _beforeToHtml()
	{
		parent::_construct();

		$this->_item = $this->getParentBlock()->getItem();
		$this->_optionsDivId = 'option_edit_' . $this->_item->getId();

		/* @var $optionsBlock SSE_EditCustomOptions_Block_Options */
		$optionsBlock = $this->getLayout()->createBlock(SSE_EditCustomOptions_Block_Options::ALIAS, 'additional.product.info.customoptions.options');
		$optionsBlock->init($this->_item);
		$this->append($optionsBlock, 'options');
	}
	/**
	 * Returns unique ID for the options popup div element
	 * 
	 * @return string
	 */
	public function getOptionsDivId()
	{
		return $this->_optionsDivId;
	}
}