<?php
namespace Styla\Connect2\Api;

interface ConverterInterface
{
    /**
     * 
     * @param \Magento\Catalog\Model\AbstractModel $item
     */
    public function convertItem($item);
    
    /**
     * 
     * @param array $arguments
     */
    public function setArguments(array $arguments);
    
    /**
     * 
     * @return string
     */
    public function getIdentifier();
    
    /**
     * 
     * @return array
     */
    public function getArguments();
    
    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function getArgument($name);
    
    /**
     * 
     * @return mixed
     */
    public function getConvertedValue();
}