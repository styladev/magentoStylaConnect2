<?php
namespace Styla\Connect2\Model\Api;

class StylaProduct extends \Magento\Catalog\Model\Product implements \Styla\Connect2\Api\Data\StylaProductInterface
{
    public function getSku()
    {
        return "Sku";
    }
    
    public function getId()
    {
        return time();
    }
    
    public function getName()
    {
        return time();
    }
}
