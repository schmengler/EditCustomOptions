<?php
/**
 * Observer
 * 
 * @author Fabian Schmengler
 */
class SSE_EditCustomOptions_Model_Observer
{
	const POPUP_ALIAS_IN_LAYOUT = 'custom_options_edit_link';
	/**
	 * Adds popup block with custom option form to be used by item renderer in order overview
	 * 
	 * @param Varien_Event_Observer $observer
	 * @see event core_block_abstract_to_html_before
	 */
	public function addPopupBlockToLayout(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();
		if ($this->_shouldAddPopup($block)) {
			/* @var $container Mage_Core_Block_Text_List */
			$container = $block->getLayout()->getBlock('additional.product.info');
			$popup = $block->getLayout()->createBlock(SSE_EditCustomOptions_Block_Link::ALIAS, 'additional.product.info.customoptions', array('template' => 'sse/editcustomoptions/link.phtml'));
			$container->append($popup, self::POPUP_ALIAS_IN_LAYOUT);
		}
	}
	/**
	 * Remove popup block after rendering, so that following renderers can use their own instance
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function removePopupBlockFromLayout(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();
		if ($this->_shouldAddPopup($block)) {
			/* @var $container Mage_Core_Block_Text_List */
			$container = $block->getLayout()->getBlock('additional.product.info');
			$container->unsetChild(self::POPUP_ALIAS_IN_LAYOUT);
		}
	}
	protected function _shouldAddPopup(Mage_Core_Block_Abstract $block)
	{
		return $block instanceof Mage_Sales_Block_Order_Item_Renderer_Default
			&& Mage::helper('editcustomoptions/data')->isOrderEditable($block->getOrder())
			&& $block->getItem()->getProduct()->getHasOptions();
	}
}