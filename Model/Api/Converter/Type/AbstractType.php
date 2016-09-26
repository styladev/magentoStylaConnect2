<?php
namespace Styla\Connect2\Model\Api\Converter\Type;

use Styla\Connect2\Api\ConverterInterface as ConverterInterface;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection as Collection;
use Magento\Store\Api\Data\StoreInterface as Store;

abstract class AbstractType
    implements ConverterInterface
{
    const ARGUMENT_STYLA_FIELD   = "styla_field";
    const ARGUMENT_MAGENTO_FIELD = "magento_field";
    const ARGUMENT_IDENTIFIER    = "identifier";

    /**
     *
     * @var array
     */
    protected $_arguments;

    /**
     * Holds the final, converted value
     *
     * @var mixed
     */
    protected $_convertedValue;

    /**
     *
     * @param Collection $collection
     * @param Store                             $store
     */
    final public function addCollectionRequirements(Collection $collection, Store $store = null)
    {
        $this->_addCollectionRequirements($collection, $store);
    }

    /**
     * 
     * @param Collection $collection
     * @param Store $store
     */
    protected function _addCollectionRequirements(Collection $collection, Store $store = null)
    {
        //overwrite this in child converters, if you need to do something with the collection
    }

    /**
     * Do the data conversion here
     *
     * @var mixed $item
     */
    abstract protected function _convertItem($item);

    /**
     *
     * @return mixed
     */
    public function getConvertedValue()
    {
        return $this->_convertedValue;
    }

    /**
     *
     * @param mixed $item
     */
    final public function convertItem($item)
    {
        $this->_convertItem($item);

        //store the converted value in the field which styla expects to see
        $item->setData($this->getStylaField(), $this->_convertedValue);
    }

    /**
     *
     * @param array $arguments
     * @return \Styla\Connect2\Model\Api\Converter\Type\AbstractType
     */
    public function setArguments(array $arguments)
    {
        $this->_arguments = $arguments;

        return $this;
    }

    /**
     * Each converter may have an "identifier" that will be used to just add it's
     * collection requirements once
     *
     * @return string
     */
    public function getIdentifier()
    {
        $configuredIdentifier = $this->getArgument(self::ARGUMENT_IDENTIFIER);

        return $configuredIdentifier !== null ? $configuredIdentifier : self::class;
    }

    /**
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->_arguments;
    }

    /**
     *
     * @param string $name
     * @return mixed
     */
    public function getArgument($name)
    {
        return isset($this->_arguments[$name]) ? $this->_arguments[$name] : null;
    }

    /**
     *
     * @return string
     */
    public function getStylaField()
    {
        return $this->getArgument(self::ARGUMENT_STYLA_FIELD);
    }

    /**
     *
     * @return string
     */
    public function getMagentoField()
    {
        return $this->getArgument(self::ARGUMENT_MAGENTO_FIELD);
    }
}