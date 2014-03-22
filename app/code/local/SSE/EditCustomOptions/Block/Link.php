<?php
/**
 * Template for the edit link in order item
 * 
 * @author Fabian Schmengler
 *
 */
class SSE_EditCustomOptions_Block_Link extends Mage_Core_Block_Template
{
	const ALIAS = 'editcustomoptions/link';

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
		$this->_optionsDivId = 'option_edit_' . $this->getItem()->getId();
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
	/**
	 * Returns unique ID for the options popup div element
	 * 
	 * @return string
	 */
	public function getOptionsDivId()
	{
		return $this->_optionsDivId;
	}
	/**
	 * Returns URL for popup
	 * 
	 * @return string
	 */
	public function getPopupUrl()
	{
		return Mage::getUrl('editcustomoptions/order/edit', array('item_id' => $this->getItem()->getId()));
	}
}