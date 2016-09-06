<?php
namespace Styla\Connect2\Model\Product\Info;

use Styla\Connect2\Model\Api\Converter\Type\AbstractType;
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
     * @param AbstractType $type
     * @param array        $arguments
     * @return Event
     */
    protected function _create($type, $arguments = [])
    {
        return $this->_objectManager->create($type, $arguments);
    }

    /**
     * Create the right renderer, based on the product type id
     *
     * @param string $productType
     * @return \Styla\Connect2\Model\Product\Info\Renderer\DefaultRenderer
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
