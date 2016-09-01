<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

use Styla\Connect2\Model\Api\Converter\Type as ConverterType;

class Url extends ConverterType\AbstractType
{
    protected function _convertItem($item) {
        //get the public product url
        $this->_convertedValue = $item->getProductUrl();
    }
}

