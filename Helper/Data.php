<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Styla\Connect2\Model\Styla\Api;
use Styla\Connect2\Model\Magazine;
use Styla\Connect2\Model\Page as StylaPage;

class Data extends AbstractHelper
{
    /** @var Magazine */
    protected $_currentMagazine;

    /** @var StylaPage */
    protected $_currentPage;

    /**
     * @var array
     */
    protected $_rootPaths = [];

    /**
     * @var bool
     */
    protected $_isDeveloperMode;

    /**
     * @var string
     */
    protected $_apiVersion;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Api
     */
    protected $stylaApi;

    const MODULE_NAME = 'Styla_Connect2';

    const IS_DEVELOPER_MODE_PATH = 'styla_connect2/developer/is_developer_mode';

    const URL_SEO_PROD = 'http://seo.styla.com/';

    const URL_ASSETS_PROD = '//client-scripts.styla.com/';

    const URL_PART_JS = 'scripts/clients/%s.js?v=%s';

    const URL_PART_CSS = 'styles/clients/%s.css?v=%s';

    const URL_VERSION_PROD = 'http://live.styla.com/';

    const URL_PART_VERSION = 'api/version/%s';

    const ASSET_TYPE_JS = 'js';

    const ASSET_TYPE_CSS = 'css';

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ModuleListInterface $moduleList
     * @param StoreManagerInterface $storeManager
     * @param Api $stylaApi
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ModuleListInterface $moduleList,
        StoreManagerInterface $storeManager,
        Api $stylaApi
    ) {
        $this->stylaApi = $stylaApi;
        $this->storeManager = $storeManager;
        $this->moduleList = $moduleList;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * @return StylaPage|null
     */
    public function getCurrentPage()
    {
        if (!$this->_currentPage) {
            $this->_currentPage = $this->registry->registry('styla_page');
        }

        return $this->_currentPage;
    }

    /**
     * @return Magazine|null
     */
    public function getCurrentMagazine()
    {
        if (!$this->_currentMagazine) {
            $this->_currentMagazine = $this->registry->registry('current_magazine');
        }

        return $this->_currentMagazine;
    }

    /**
     * @return string
     */
    public function getPluginVersion()
    {
        return $this->moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }

    /**
     * Is the module in developer mode?
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        if (null === $this->_isDeveloperMode) {
            $this->_isDeveloperMode = $this->scopeConfig->isSetFlag(self::IS_DEVELOPER_MODE_PATH);
        }

        return $this->_isDeveloperMode;
    }

    /**
     * Get the overridden url, if the module is in developer mode.
     * Returns FALSE if the url is not overridden, or the developer mode is disabled.
     *
     * @param string $url
     *
     * @return boolean|string
     */
    public function getDeveloperModeUrl($url)
    {
        if (!$this->isDeveloperMode()) {
            return false;
        }
        $path = sprintf('styla_connect2/developer/override_%s_url', $url);
        $url = $this->scopeConfig->getValue($path);
        if ($url) {
            $url = rtrim($url, '/') . '/';
        }

        return $url;
    }

    /**
     * @param Magazine $magazine
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAbsoluteMagazineUrl($magazine)
    {
        if (null === $magazine) {
            return '';
        }

        return $this->storeManager->getStore()->getBaseUrl() . $magazine->getFrontName();
    }

    /**
     * @param Magazine $magazine
     *
     * @return array
     */
    public function getMagazineRootPath($magazine)
    {
        if (!isset($this->_rootPaths[$magazine->getId()])) {
            $frontName = $magazine->getFrontName();
            //get the url to the magazine page, strip index.php from it. this gives me the root path for a magazine
            $url = parse_url(str_replace('/index.php/', '/', Mage::getUrl($frontName)));
            $this->_rootPaths[$magazine->getId()] = isset($url['path']) ? $url['path'] : '';
        }

        return $this->_rootPaths[$magazine->getId()];
    }

    /**
     * Get the SEO Api Url
     *
     * @return string
     */
    public function getApiSeoUrl()
    {
        if ($overrideUrl = $this->getDeveloperModeUrl('seo')) {
            $url = $overrideUrl;
        } else {
            $url = self::URL_SEO_PROD;
        }

        return $url;
    }

    /**
     * Get the Assets Url (script,css)
     *
     * @param string $type
     *
     * @return string
     */
    public function getAssetsUrl($type)
    {
        //is the url overridden in developer mode of the styla module?
        if ($overrideUrl = $this->getDeveloperModeUrl('cdn')) {
            $url = $overrideUrl;
        } else {
            $url = self::URL_ASSETS_PROD;
        }
        $clientName = $this->getClientName();
        $apiVersion = $this->getCurrentApiVersion();
        $assetsUrl = false;
        switch ($type) {
            case self::ASSET_TYPE_JS:
                $assetsUrl = $url . sprintf(self::URL_PART_JS, $clientName, $apiVersion);
                break;
            case self::ASSET_TYPE_CSS:
                $assetsUrl = $url . sprintf(self::URL_PART_CSS, $clientName, $apiVersion);
                break;
        }

        return $assetsUrl;
    }

    /**
     * Get the Content Version Number API Url
     *
     * @return string
     */
    public function getApiVersionUrl()
    {
        if ($overrideUrl = $this->getDeveloperModeUrl('api')) {
            $url = $overrideUrl;
        } else {
            $url = self::URL_VERSION_PROD;
        }
        $clientName = $this->getClientName();
        $versionUrl = sprintf($url . self::URL_PART_VERSION, $clientName);

        return $versionUrl;
    }

    /**
     * @return bool|string
     */
    public function getClientName()
    {
        if (null !== $this->getCurrentMagazine()) {
            return $this
                ->getCurrentMagazine()
                ->getClientName();
        }

        return false;
    }

    /**
     * Get the current version number of the content (script, css)
     *
     * @return string
     */
    public function getCurrentApiVersion()
    {
        if (null === $this->_apiVersion) {
            $this->_apiVersion = $this->_getApi()->getCurrentApiVersion();
        }

        return $this->_apiVersion;
    }

    /**
     * Get the content language code
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->scopeConfig->getValue('general/locale/code');
    }

    /**
     * @return string
     */
    public function getCacheLifetime()
    {
        return $this->scopeConfig->getValue('styla_connect2/general/cache_lifetime');
    }

    /**
     * @return bool
     */
    public function isUsingRelativeProductUrls()
    {
        return $this->scopeConfig->isSetFlag('styla_connect2/general/use_relative_product_url');
    }

    /**
     * @return Api
     */
    protected function _getApi()
    {
        return $this->stylaApi;
    }

    /**
     * @param Magazine $magazine
     *
     * @return bool
     */
    public function isMagazineIncludedInNavigation($magazine)
    {
        return (false !== (bool) $magazine->getIncludeInNavigation() &&
            (bool) $magazine->getIsActive() &&
            null !== $magazine->getNavigationLabel() &&
            (int) $this->storeManager->getStore()->getId() === (int) $magazine->getStoreId()
        );
    }
}
