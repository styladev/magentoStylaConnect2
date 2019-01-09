<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

use Styla\Connect2\Model\Api\Converter\Type as ConverterType;
use Styla\Connect2\Helper\Data as StylaHelper;
use Magento\Store\Model\StoreManagerInterface;

class Url extends ConverterType\AbstractType
{
    protected $stylaHelper;

    protected $storeManager;

    public function __construct(
        StylaHelper $helper,
        StoreManagerInterface $storeManager)
    {
        $this->stylaHelper = $helper;
        $this->storeManager = $storeManager;
    }

    protected function _convertItem($item)
    {
        //get the public product url
        $this->_convertedValue = $item->getProductUrl();
        if ($this->useRelativeUrls()) {
            $this->_convertedValue = str_replace(
                $this->storeManager->getStore()->getBaseUrl(),
                '/',
                $item->getProductUrl()
            );
        }
    }

    /**
     * Should only return the relative part of the urls
     *
     * @return bool
     */
    protected function useRelativeUrls()
    {
        return $this->stylaHelper->isUsingRelativeProductUrls();
    }
}

