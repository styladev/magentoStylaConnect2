<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

use Styla\Connect2\Model\Api\Converter\Type as ConverterType;

class Entity extends ConverterType\AbstractType
{
    protected function _convertItem($item)
    {
        //get the entity ID in an edition safe manner (row_id|entity_id)
        $this->_convertedValue = $item->getId();
    }
}
