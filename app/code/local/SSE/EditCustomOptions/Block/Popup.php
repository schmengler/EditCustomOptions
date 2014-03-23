<?php
/**
 * Main popup block
 * 
 * @author Fabian Schmengler <fabian@schmengler-se.de>
 * @category SSE
 * @package SSE_EditCustomOptions
 *
 */
class SSE_EditCustomOptions_Block_Popup extends Mage_Catalog_Block_Product_View
{
    const ALIAS = 'editcustomoptions/popup';

    /**
     * Order item
     *
     * @var Mage_Sales_Model_Order_Item
     */
    protected $_item;

    /**
     * Do not create other product related blocks
     * 
     * (non-PHPdoc)
     * @see Mage_Catalog_Block_Product_View::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        return $this;
    }
    /**
     * initialize options block
     * 
     * (non-PHPdoc)
     * @see Mage_Core_Block_Abstract::_beforeToHtml()
     */
    protected function _beforeToHtml()
    {
        if (!$this->getItem()) {
            return parent::_beforeToHtml();
        }

        $wrapperBlock = $this->getLayout()->createBlock('catalog/product_view', 'additional.product.info.customoptions.wrapper',
                array('template' => 'catalog/product/view/options/wrapper.phtml', 'product_id' => $this->getItem()->getProductId()));
        $this->append($wrapperBlock, 'wrapper');

        /* @var $optionsBlock SSE_EditCustomOptions_Block_Options */
        $optionsBlock = $this->getLayout()->createBlock(SSE_EditCustomOptions_Block_Options::ALIAS, 'additional.product.info.customoptions.options');
        $optionsBlock->init($this->getItem());
        $wrapperBlock->append($optionsBlock, 'options');

        return parent::_beforeToHtml();
    }
    /**
     * Returns order item
     * 
     * @return Mage_Sales_Model_Order_Item
     */
    public function getItem()
    {
        if ($this->_item === null) {
            $this->_item = Mage::registry('current_item');
        }
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