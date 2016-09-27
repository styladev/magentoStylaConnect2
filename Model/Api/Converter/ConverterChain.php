<?php
namespace Styla\Connect2\Model\Api\Converter;

use Styla\Connect2\Api\ConverterInterface as ConverterInterface;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection as Collection;
use Magento\Store\Api\Data\StoreInterface as Store;
use Magento\Framework\Event\ManagerInterface as EventManager;

class ConverterChain
{
    const EVENT_CONVERT = 'styla_data_convert';
    
    /**
     *
     * @var array
     */
    protected $_converters;
    
    /**
     *
     * @var EventManager
     */
    protected $eventManager;
    
    public function __construct(EventManager $eventManager) {
        $this->eventManager = $eventManager;
    }

    /**
     *
     * @param ConverterInterface $converter
     */
    public function addConverter(ConverterInterface $converter)
    {
        $this->_converters[] = $converter;
    }

    /**
     *
     * @return array
     */
    public function getConverters()
    {
        return $this->_converters;
    }

    /**
     *
     * @param Collection $collection
     * @param Store $store
     */
    public function addCollectionRequirements(Collection $collection, Store $store = null)
    {
        $requirementsIdentifiers = [];

        //merge reqs from all converters, add them to the collection
        foreach ($this->getConverters() as $converter) {
            $identifier = $converter->getIdentifier();
            if (in_array($identifier, $requirementsIdentifiers)) {
                continue; //one type of converter only adds it's requirements once
            }

            $converter->addCollectionRequirements($collection);
            $requirementsIdentifiers[] = $identifier;
        }
    }

    /**
     *
     * @param Collection $collection
     */
    public function doConversion($collection)
    {
        $this->eventManager->dispatch(self::EVENT_CONVERT . '_before', [
            'collection' => $collection,
            'converter_chain' => $this 
        ]);
        
        foreach ($collection as $item) {
            foreach ($this->getConverters() as $converter) {
                $converter->convertItem($item);
            }
        }
        
        $this->eventManager->dispatch(self::EVENT_CONVERT . '_after', [
            'collection' => $collection,
            'converter_chain' => $this 
        ]);
    }
}
