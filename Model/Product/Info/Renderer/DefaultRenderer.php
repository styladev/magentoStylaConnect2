<?php
namespace Styla\Connect2\Model\Product\Info\Renderer;

class DefaultRenderer
{
    //const EVENT_COLLECT_ADDITIONAL_INFO = 'styla_connect_product_info_renderer_collect_additional';

    protected $_store;
    protected $manufacturerAttribute;
    protected $_storeManager;
    protected $stockRegistry;
    protected $taxCalculation;
    protected $taxHelper;
    protected $priceHelper;
    
    public function __construct(
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
            \Magento\Tax\Model\Calculation $taxCalculation,
            \Magento\Tax\Helper\Data $taxHelper,
            \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->stockRegistry = $stockRegistry;
        $this->taxCalculation = $taxCalculation;
        $this->taxHelper = $taxHelper;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Collect the data and return it as array, ready to be turned into json
     *
     * @return array
     */
    final public function render($product)
    {
        $productInfo = $this->_collectProductInfo($product);

        return $productInfo;
    }
    
    /**
     * Retrieve current store
     *
     * @return \Magento\Store\Model\Store
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Collect the basic information about the product and return it as an array.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _collectProductInfo($product)
    {
        //basic product info, same for every possible product type
        $productInfo = array(
            'id'            => $product->getId(),
            'type'          => $product->getTypeId(),
            'name'          => $product->getName(),
            'saleable'      => $product->getIsSalable(),
            'price'         => $this->priceHelper->currency($product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(), true, false),
            'priceTemplate' => $this->getPriceTemplate(),
        );

        //if product has active special price
        if ($oldPrice = $this->getOldPrice($product)) {
            $productInfo['oldPrice'] = $oldPrice;
        }

        //allowed sale quantities
        if ($qtyLimits = $this->getProductQtyLimits($product)) {
            list($minQty, $maxQty) = $qtyLimits;

            if ($minQty !== null) {
                $productInfo['minqty'] = $minQty;
            }

            if ($maxQty !== null) {
                $productInfo['maxqty'] = $maxQty;
            }
        }
        
        //add product tax info
        if ($taxInfo = $this->getProductTax($product)) {
            $productInfo['tax'] = $taxInfo;
        }

        //get additional info, if possible
        //this may be different for various product types
        $productInfo = $this->_collectAdditionalProductInfo($product, $productInfo);

        return $productInfo;
    }

    /**
     * Get the tax info, if this product has a tax class
     * This will load a default tax rate (default address, default customer type)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean|array
     */
    public function getProductTax($product)
    {
        $taxId = $product->getTaxClassId();
        if (null === $taxId) {
            return false;
        }

        $store = $this->_getStore();
        $taxRequest     = $this->taxCalculation->getRateRequest(
            null,
            null,
            null,
            $store
        ); //for default address and customer class
        
        $taxRate = $this->taxCalculation->getRate($taxRequest->setProductClassId($taxId)); //get calculated default rate
        
        //get detailed tax info
        $taxRateInfo = $this->taxCalculation->getResource()->getRateInfo($taxRequest);
        $taxLabel    = isset($taxRateInfo['process'][0]['id']) ? $taxRateInfo['process'][0]['id'] : '';

        $isTaxIncluded = $this->taxHelper->priceIncludesTax($store);

        $taxInfo = array(
            'rate'        => $taxRate,
            'label'       => $taxLabel,
            'taxIncluded' => $isTaxIncluded,
            'showLabel'   => true, //TODO: this should have a system config?
        );

        return $taxInfo;
    }

    /**
     * If there are limits for qty in cart for this product, return them
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean|array
     */
    public function getProductQtyLimits($product)
    {
        $minQty = null;
        $maxQty = null;
        
        $stockItem = $this->stockRegistry->getStockItem($product->getId());

        if ($stockItem) {
            $minQty = ($stockItem->getUseConfigMinSaleQty()
            && $stockItem->getMinSaleQty() > 0 ? $stockItem->getMinSaleQty() * 1 : null);
            $maxQty = ($stockItem->getUseConfigMaxSaleQty()
            && $stockItem->getMaxSaleQty() > 0 ? $stockItem->getMaxSaleQty() * 1 : null);
        } else {
            return false;
        }

        return array($minQty, $maxQty);
    }

    /**
     * Return the default store
     *
     * @return \Magento\Store\Model\Store
     */
    protected function _getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Get the price temaplate for the current store
     *
     * @return string
     */
    public function getPriceTemplate()
    {
        $currencyFormat = $this->_getStore()->getCurrentCurrency()->getOutputFormat();
        
        //convert to a format acceptable for Styla
        //normally is contains %s for inserting the price value
        return str_replace('%s', '#{price}', $currencyFormat);
    }

    /**
     * Return the "normal price" of the product, if it has a special price and this special price is active
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getOldPrice($product)
    {
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        $finalPrice   = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

        if ($regularPrice != $finalPrice) {
            return $this->_registerJsPrice($regularPrice);
        } else {
            return false;
        }
    }

    /**
     * Load and collect any other product info that we may need
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array                      $productInfo
     * @return array
     */
    protected function _collectAdditionalProductInfo($product, $productInfo)
    {
        return $productInfo; //todo: fix this wih a magento2 event
        
        //can be overridden and used in productType-specific classes to get more detailed attributes

        //allow for collecting additional data outside of the renderer
        $transportObject = new Varien_Object();
        $transportObject->setProductInfo($productInfo);
        $transportObject->setProduct($product);
        Mage::dispatchEvent(self::EVENT_COLLECT_ADDITIONAL_INFO, array('transport_object' => $transportObject));

        $productInfo = $transportObject->getProductInfo();

        return $productInfo;
    }
    
    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }
}