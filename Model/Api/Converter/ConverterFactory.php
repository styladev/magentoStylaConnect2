<?php
namespace Styla\Connect2\Model\Api\Converter;

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Styla\Connect2\Api\ConverterInterface as ConverterInterface;
use Styla\Connect2\Model\Api\Converter\ConverterChain;
use Magento\Framework\Event\ManagerInterface as EventManager;

class ConverterFactory
{
    const EVENT_GET_CONVERTERS = 'styla_get_data_converters';
    
    const TYPE_PRODUCT  = 'product';
    const TYPE_CATEGORY = 'category'; //currently none implemented

    const XML_CONVERTER_CONFIG = 'styla_connect2/rest_data/%s';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     *
     * @var EventManager
     */
    protected $eventManager;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var \Styla\Connect2\Model\Api\Converter\ConverterChainFactory
     */
    protected $converterChainFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Styla\Connect2\Model\Api\Converter\ConverterChainFactory $converterChainFactory,
        EventManager $eventManager
    )
    {
        $this->converterChainFactory = $converterChainFactory;
        $this->_objectManager        = $objectManager;
        $this->eventManager = $eventManager;
    }

    /**
     * @return ScopeConfigInterface
     */
    private function getScopeConfig()
    {
        if (null === $this->scopeConfig) {
            $this->scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        }

        return $this->scopeConfig;
    }

    /**
     * @param string $type
     * @param array                                                    $arguments
     * @return Event
     */
    protected function _create($type, $arguments = [])
    {
        return $this->_objectManager->create($type, $arguments);
    }

    /**
     * Create the converter chain, based on the current configuration
     *
     * @param string $type
     * @return ConverterChain
     */
    public function createConverterChain($type = self::TYPE_PRODUCT)
    {
        $converters = $this->_getConverterConfiguration($type);

        $converterChain = $this->converterChainFactory->create();

        foreach ($converters as $converterConfig) {
            /** @var ConverterInterface $converter */
            $converter = $this->_create($converterConfig['class']);
            $converter->setArguments($converterConfig['arguments']);

            $converterChain->addConverter($converter);
        }
        
        $this->eventManager->dispatch(self::EVENT_GET_CONVERTERS, ['converter_chain' => $converterChain]);

        return $converterChain;
    }

    /**
     * Load the converter configuration from the module's config.xml
     *
     * @param string $type
     * @return array
     */
    protected function _getConverterConfiguration($type)
    {
        $configurationPath = sprintf(self::XML_CONVERTER_CONFIG, $type);
        $converters        = $this->getScopeConfig()->getValue($configurationPath);

        return $converters;
    }
}