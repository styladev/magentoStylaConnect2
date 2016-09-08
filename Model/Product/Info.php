<?php
namespace Styla\Connect2\Model\Product;

use Magento\Catalog\Model\Product;

class Info
{
    const EVENT_GET_RENDERER = 'styla_connect_get_product_info_renderer';

    protected $_product;

    /**
     *
     * @var \Styla\Connect2\Model\Product\Info\RendererFactory
     */
    protected $rendererFactory;

    public function __construct(\Styla\Connect2\Model\Product\Info\RendererFactory $rendererFactory)
    {
        $this->rendererFactory = $rendererFactory;
    }

    /**
     * Get the product details as array
     *
     * @return array
     */
    public function getInfo()
    {
        return $this->_getProductInfoRenderer()->render($this->getProduct());
    }

    /**
     *
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->_product = $product;
    }

    /**
     *
     * @return Product
     * @throws \Exception
     */
    public function getProduct()
    {
        if (!$this->_product) {
            throw new \Exception('No product was set.');
        }

        return $this->_product;
    }

    /**
     *
     * @return \Styla\Connect2\Model\Product\Info\Renderer\DefaultRenderer
     */
    protected function _getProductInfoRenderer()
    {
        $productType = $this->getProduct()->getTypeId();
        $renderer    = $this->rendererFactory->createRenderer($productType);

        return $renderer;
    }
}