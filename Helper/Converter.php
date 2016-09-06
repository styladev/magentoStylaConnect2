<?php
namespace Styla\Connect2\Helper;

class Converter extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected $_store;
    protected $_mediaUrl;
    protected $_catalogProductMediaConfig;

    /**
     *
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
    )
    {
        $this->_storeManager              = $storeManager;
        $this->_catalogProductMediaConfig = $catalogProductMediaConfig;
    }

    /**
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if (null === $this->_store) {
            $this->_store = $this->_storeManager->getStore();
        }

        return $this->_store;
    }


    /**
     * Get the full public url for the media file
     *
     * @param string $mediaPath
     * @param string $entityType
     * @return string
     */
    public function getUrlForMedia($mediaPath, $entityType = 'product')
    {
        if (null === $this->_mediaUrl) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );

            $this->_mediaUrl = $url;
        }

        $mediaPartPath = false;
        switch ($entityType) {
            case 'product':
                $mediaPartPath = $this->_catalogProductMediaConfig->getBaseMediaPath();
                break;
            case 'category':
                $mediaPartPath = 'catalog/category';
                break;
        }

        $fullUrl = $this->_mediaUrl . $mediaPartPath . $mediaPath;
        return $fullUrl;
    }
}
