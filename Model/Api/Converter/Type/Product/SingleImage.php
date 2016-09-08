<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

use Styla\Connect2\Model\Api\Converter\Type as ConverterType;
use Styla\Connect2\Helper\Converter as ConverterHelper;

class SingleImage extends ConverterType\AbstractType
{
    /*
     * @var ConverterHelper
     */
    protected $converterHelper;

    public function __construct(ConverterHelper $converterHelper)
    {
        $this->converterHelper = $converterHelper;
    }

    /**
     *
     * @param mixed $item
     */
    protected function _convertItem($item)
    {
        $value = $item->getData($this->getMagentoField());
        if (!$value) {
            return;
        }

        $this->_convertedValue = $this->converterHelper->getUrlForMedia($value);
    }
}
