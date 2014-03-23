<?php
/**
 * Renders custom options form elements in popup
 * 
 * @author Fabian Schmengler <fabian@schmengler-se.de>
 * @category SSE
 * @package SSE_EditCustomOptions
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
        Mage::helper('catalog/product')->prepareProductOptions($_product, $this->_getBuyRequest());
        $this->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
             ->addOptionRenderer('file', 'catalog/product_view_options_type_file', 'catalog/product/view/options/type/file.phtml')
             ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
             ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml')
             ->setTemplate('catalog/product/view/options.phtml')
             ->setProduct($_product);

        return $this;
    }
    /**
     * Returns buyRequest object for custom options of order item
     * 
     * @return Varien_Object
     */
    protected function _getBuyRequest()
    {
        return Mage::helper(SSE_EditCustomOptions_Helper_Buyrequest::ALIAS)->getBuyRequestFromOrderItem($this->_item);
    }
    /**
     * Unset all options with prices
     * 
     * (non-PHPdoc)
     * @see Mage_Catalog_Block_Product_View_Options::getOptions()
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        foreach ($options as $key => $option) {
            /* @var $option Mage_Catalog_Model_Product_Option */
            if (Mage::helper('editcustomoptions')->isOptionAffectingPrice($option)) {
                unset($options[$key]);
            }
        }
        return $options;
    }
}