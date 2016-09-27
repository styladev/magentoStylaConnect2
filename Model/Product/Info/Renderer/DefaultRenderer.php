<?php
namespace Styla\Connect2\Model\Product\Info\Renderer;

use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Tax\Model\Calculation as TaxCalculation;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\DataObjectFactory;

class DefaultRenderer
{
    const EVENT_COLLECT_ADDITIONAL_INFO = 'styla_product_info_renderer_collect_additional';

    protected $_store;
    protected $manufacturerAttribute;
    
    /**
     *
     * @var StoreManagerInterface 
     */
    protected $_storeManager;
    
    /**
     *
     * @var EventManager
     */
    protected $eventManager;
    
    /**
     *
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    
    /**
     *
     * @var StockRegistryInterface 
     */
    protected $stockRegistry;
    
    /**
     *
     * @var TaxCalculation
     */
    protected $taxCalculation;
    
    /**
     *
     * @var TaxHelper
     */
    protected $taxHelper;
    
    /**
     *
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * 
     * @param StoreManagerInterface $storeManager
     * @param StockRegistryInterface $stockRegistry
     * @param TaxCalculation $taxCalculation
     * @param TaxHelper $taxHelper
     * @param PriceHelper $priceHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StockRegistryInterface $stockRegistry,
        TaxCalculation $taxCalculation,
        TaxHelper $taxHelper,
        PriceHelper $priceHelper,
        EventManager $eventManager,
        DataObjectFactory $dataObjectFactory
    )
    {
        $this->_storeManager  = $storeManager;
        $this->stockRegistry  = $stockRegistry;
        $this->taxCalculation = $taxCalculation;
        $this->taxHelper      = $taxHelper;
        $this->priceHelper    = $priceHelper;
        $this->eventManager   = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Collect the data and return it as array, ready to be turned into json
     *
     * @var Product $product
     * @return array
     */
    public function render(Product $product)
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
     * @param Product $product
     * @return array
     */
    protected function _collectProductInfo(Product $product)
    {
        //basic product info, same for every possible product type
        $productInfo = [
            'id'            => $product->getId(),
            'type'          => $product->getTypeId(),
            'name'          => $product->getName(),
            'saleable'      => $product->getIsSalable(),
            'price'         => $this->priceHelper->currency($product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(), true, false),
            'priceTemplate' => $this->getPriceTemplate(),
        ];

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
     * @param  $product
     * @return boolean|array
     */
    public function getProductTax(Product $product)
    {
        $taxId = $product->getTaxClassId();
        if (null === $taxId) {
            return false;
        }

        $store      = $this->_getStore();
        $taxRequest = $this->taxCalculation->getRateRequest(
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

        $taxInfo = [
            'rate'        => $taxRate,
            'label'       => $taxLabel,
            'taxIncluded' => $isTaxIncluded,
            'showLabel'   => true, //TODO: this should have a system config?
        ];

        return $taxInfo;
    }

    /**
     * If there are limits for qty in cart for this product, return them
     *
     * @param Product $product
     * @return boolean|array
     */
    public function getProductQtyLimits(Product $product)
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

        return [$minQty, $maxQty];
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
     * @param Product $product
     * @return mixed
     */
    public function getOldPrice(Product $product)
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
     * @param Product $product
     * @param array                      $productInfo
     * @return array
     */
    protected function _collectAdditionalProductInfo(Product $product, $productInfo)
    {
        //allow for collecting additional data outside of the renderer
        $transportObject = $this->dataObjectFactory->create();
        $transportObject->setProductInfo($productInfo);
        $transportObject->setProduct($product);
        
        $this->eventManager->dispatch(self::EVENT_COLLECT_ADDITIONAL_INFO, ['transport_object' => $transportObject]);

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