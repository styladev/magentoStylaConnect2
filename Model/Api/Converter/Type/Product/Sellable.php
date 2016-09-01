<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

use Styla\Connect2\Model\Api\Converter\Type as ConverterType;

class Sellable extends ConverterType\AbstractType
{
    protected function _convertItem($item) {
        //can the product be sold?
        $this->_convertedValue = $item->getIsSalable();
    }
}

