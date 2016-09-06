<?php
namespace Styla\Connect2\Model\Api\Converter\Type;

class Simple extends AbstractType
{
    /**
     *
     * @param mixed $item
     */
    protected function _convertItem($item)
    {
        $this->_convertedValue = $item->getData($this->getMagentoField());
    }
}
