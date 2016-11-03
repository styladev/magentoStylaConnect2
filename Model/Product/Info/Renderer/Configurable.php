<?php
namespace Styla\Connect2\Model\Product\Info\Renderer;

use Styla\Connect2\Model\Product\Configurable\ConfigurableAttributeData;
use Magento\ConfigurableProduct\Helper\Data as ConfigurableHelper;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\DataObjectFactory;

class Configurable
    extends \Styla\Connect2\Model\Product\Info\Renderer\DefaultRenderer
{
    protected $_product;
    protected $_allowProducts;

    /**
     *
     * @var ConfigurableAttributeData
     */
    protected $configurableAttributeData;
    
    /**
     *
     * @var ConfigurableHelper
     */
    protected $helper;
    
    /**
     *
     * @var ProductHelper
     */
    protected $catalogProduct;

    public function __construct(
        ConfigurableAttributeData $configurableData,
        ConfigurableHelper $helper,
        ProductHelper $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        EventManager $eventManager,
        DataObjectFactory $dataObjectFactory
    )
    {
        $this->configurableAttributeData = $configurableData;
        $this->helper                    = $helper;
        $this->catalogProduct            = $catalogProduct;

        return parent::__construct($storeManager, $stockRegistry, $taxCalculation, $taxHelper, $priceHelper, $eventManager, $dataObjectFactory);
    }

    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        if (null === $this->_allowProducts) {
            $products          = [];
            $allProducts       = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
            foreach($allProducts as $product) {
                $products[$product->getId()] = $product; //makes it simpler to reference, later
            }
            
            $this->_allowProducts = $products;
        }
        return $this->_allowProducts;
    }

    /**
     * Add configurable product's options data to the product info array.
     * This method is basically the same logic that's used for generating the options selects on the product view page.
     * For reference, see ->getJsonConfig() method of the product view block.
     *
     * @param Product $product
     * @param array                          $productInfo
     * @return array
     */
    protected function _collectAdditionalProductInfo(Product $product, $productInfo)
    {
        parent::_collectAdditionalProductInfo($product, $productInfo);

        /**
         * the following configurable-data collecting code is a simplified version
         * of the logic used to gather product options on a catalog/product/view details page.
         * Copied here to keep these two separate
         */
        $this->_product = $product;

        $store = $this->getCurrentStore();

        $regularPrice = $this->_product->getPriceInfo()->getPrice('regular_price');
        $finalPrice   = $this->_product->getPriceInfo()->getPrice('final_price');

        $options        = $this->helper->getOptions($this->_product, $this->getAllowProducts());
        $attributesData = $this->configurableAttributeData->getAttributesDataExtended($this->_product, $options, $this->getAllowProducts());
        
        $configurableInfo = [
            'attributes'   => $attributesData['attributes'],
            'template'     => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices'       => [
                'oldPrice'   => [
                    'amount' => $this->_registerJsPrice($regularPrice->getAmount()->getValue()),
                ],
                'basePrice'  => [
                    'amount' => $this->_registerJsPrice(
                        $finalPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->_registerJsPrice($finalPrice->getAmount()->getValue()),
                ],
            ],
            'productId'    => $this->_product->getId(),

            //these things come in the original configurable product view block, they can be returned if needed, too:
            //'chooseText' => __('Choose an Option...'),
            //'images' => isset($options['images']) ? $options['images'] : [],
            //'index' => isset($options['index']) ? $options['index'] : [],
        ];

        if ($this->_product->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $configurableInfo['defaultValues'] = $attributesData['defaultValues'];
        }

        $productInfo = array_merge($productInfo, $configurableInfo);
        return $productInfo;
    }
    
    /**
     * @return array
     */
    protected function getOptionPrices()
    {
        $prices = [];
        foreach ($this->getAllowProducts() as $product) {
            $priceInfo = $product->getPriceInfo();

            $prices[$product->getId()] =
                [
                    'oldPrice'   => [
                        'amount' => $this->_registerJsPrice(
                            $priceInfo->getPrice('regular_price')->getAmount()->getValue()
                        ),
                    ],
                    'basePrice'  => [
                        'amount' => $this->_registerJsPrice(
                            $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount()
                        ),
                    ],
                    'finalPrice' => [
                        'amount' => $this->_registerJsPrice(
                            $priceInfo->getPrice('final_price')->getAmount()->getValue()
                        ),
                    ]
                ];
        }
        return $prices;
    }
}