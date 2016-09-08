<?php
namespace Styla\Connect2\Model\Product\Info;

use Styla\Connect2\Model\Product\Info\Renderer\DefaultRenderer;
use Styla\Connect2\Model\Product\Info\Renderer as InfoRenderer;

class RendererFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $scopeConfig;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $typeClass
     * @param array        $arguments
     * @return Event
     */
    protected function _create($typeClass, $arguments = [])
    {
        return $this->_objectManager->create($typeClass, $arguments);
    }

    /**
     * Create the right renderer, based on the product type id
     *
     * @param string $productType
     * @return DefaultRenderer
     */
    public function createRenderer($productType = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    {
        switch ($productType) {
            case 'configurable':
                $rendererClass = InfoRenderer\Configurable::class;
                break;

            //simple or any other type:
            case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE:
            default:
                $rendererClass = InfoRenderer\DefaultRenderer::class;
                break;
        }

        return $this->_create($rendererClass);
    }
}
