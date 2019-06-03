<?php
namespace Styla\Connect2\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Styla\Connect2\Model\Styla\Api as StylaApi;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Store\Model\StoreManagerInterface;

class Config extends AbstractHelper
{
    const DEFAULT_ROUTE_NAME = 'magazin';

    const URL_ASSETS_PROD = '//client-scripts.styla.com/';
    const URL_PART_JS     = 'scripts/clients/%s.js?v=%s';
    const URL_PART_CSS    = 'styles/clients/%s.css?v=%s';

    const ASSET_TYPE_CSS = 'css';
    const ASSET_TYPE_JS  = 'js';

    const URL_VERSION_PROD = 'http://live.styla.com/';
    const URL_PART_VERSION = 'api/version/%s';

    const URL_SEO_PROD = 'http://seo.styla.com/';

    const XML_USERNAME           = 'styla_connect2/general/username';
    const XML_ENABLED            = 'styla_connect2/general/enable';
    const XML_FRONTEND_NAME      = 'styla_connect2/general/frontend_name';
    const XML_LANGUAGE_CODE      = 'general/locale/code';
    const XML_NAVIGATION_ENABLED = 'styla_connect2/general/menu_link_enabled';
    const XML_NAVIGATION_LABEL   = 'styla_connect2/general/menu_link_label';
    const XML_USING_LAYOUT       = 'styla_connect2/general/is_using_magento_layout';
    const XML_IS_DEVELOPER       = 'styla_connect2/developer/is_developer_mode';
    const XML_OVERRIDE_URL       = 'styla_connect2/developer/override_%s_url';
    const XML_CACHE_LIFETIME     = 'styla_connect2/general/cache_lifetime';

    //these are the configuration fields which may be returned by the connector
    protected $_apiConfigurationFields = [
        'client'   => self::XML_USERNAME,
        'rootpath' => self::XML_FRONTEND_NAME,
    ];

    /**
     *
     * @var ResourceConfig
     */
    protected $resourceConfig;

    /**
     *
     * @var StylaApi
     */
    protected $stylaApi;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $_apiVersion;
    protected $_configuredRouteName;
    protected $_isDeveloperMode;
    protected $_rootPath;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ResourceConfig $resourceConfig,
        StylaApi $stylaApi,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->stylaApi       = $stylaApi;
        $this->resourceConfig = $resourceConfig;
        $this->storeManager   = $storeManager;
        $this->request        = $request;

        return parent::__construct($context);
    }

    /**
     * @return ScopeConfigInterface
     */
    private function getScopeConfig()
    {
        if (null === $this->scopeConfig) {
            $this->scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        }

        return $this->scopeConfig;
    }

    /**
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getScopeConfig()->getValue(self::XML_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is the module already configured (ready to work) in the current store
     *
     * @return bool
     */
    public function isConfiguredForThisStore()
    {
        return $this->getUsername() && $this->isEnabled();
    }

    public function getFrontendName()
    {
        return $this->getScopeConfig()->getValue(self::XML_FRONTEND_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
     *
     * @return StylaApi
     */
    protected function _getApi()
    {
        return $this->stylaApi;
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

        $clientName = $this->getUsername();
        $versionUrl = sprintf($url . self::URL_PART_VERSION, $clientName);

        return $versionUrl;
    }

    /**
     * Get the Assets Url (script,css)
     *
     * @param string $type
     * @return string
     */
    public function getAssetsUrl($type)
    {
        //is the url overriden in developer mode of the styla module?
        if ($overrideUrl = $this->getDeveloperModeUrl('cdn')) {
            $url = $overrideUrl;
        } else {
            $url = self::URL_ASSETS_PROD;
        }
        $clientName = $this->getUsername();
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
     * Get the overridden url, if the module is in developer mode.
     * Returns FALSE if the url is not overridden, or the developer mode is disabled.
     *
     * @param string $url
     * @return boolean|string
     */
    public function getDeveloperModeUrl($url)
    {
        if (!$this->isDeveloperMode()) {
            return false;
        }

        $path = sprintf(self::XML_OVERRIDE_URL, $url);
        $url  = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($url) {
            $url = rtrim($url, "/") . "/";
        }

        return $url;
    }

    /**
     * Is the module in developer mode?
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        if (null === $this->_isDeveloperMode) {
            $this->_isDeveloperMode = (bool)$this->scopeConfig->getValue(self::XML_IS_DEVELOPER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        return $this->_isDeveloperMode;
    }

    /**
     * Get the route name for the router.
     * Always appends a / character at the end.
     *
     * @return string
     */
    public function getRouteName()
    {
        $routeName = $this->getConfiguredRouteName();

        return trim($routeName, '/') . '/';
    }

    /**
     * Get the RootPath of the request.
     * eg. /magazine/story/one
     *
     * @return string
     */
    public function getRootPath()
    {
        if(null === $this->_rootPath) {
            $url = parse_url(str_replace('/index.php/', '/', $this->storeManager->getStore()->getUrl($this->getRouteName(), ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_LINK])));
            $this->_rootPath = isset($url['path']) ? $url['path'] : '';
        }

        return $this->_rootPath;
    }

    /**
     * Get the route to the magazine, as configured by the user.
     * Returns the default value, if no configuration is found.
     *
     * @return string
     */
    public function getConfiguredRouteName()
    {
        if (null === $this->_configuredRouteName) {
            $configuredRouteName        = $this->getFrontendName();
            $this->_configuredRouteName = $configuredRouteName ? $configuredRouteName : self::DEFAULT_ROUTE_NAME;
        }
        return $this->_configuredRouteName;
    }

    /**
     * Get the username, valid for retrieving data from Styla
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getScopeConfig()->getValue(self::XML_USERNAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is the frontend navigation menu button enabled?
     *
     * @return bool
     */
    public function isNavigationLinkEnabled()
    {
        if(!$this->isConfiguredForThisStore()) {
            return false;
        }

        return (bool)$this->getScopeConfig()->getValue(self::XML_NAVIGATION_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @return string
     */
    public function getNavigationLinkLabel()
    {
        return $this->getScopeConfig()->getValue(self::XML_NAVIGATION_LABEL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @return null|int
     */
    public function getCacheLifetime()
    {
        $cacheLifetime = $this->getScopeConfig()->getValue(self::XML_CACHE_LIFETIME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $cacheLifetime ? (int)$cacheLifetime : null;
    }

    /**
     *
     * @return bool
     */
    public function isUsingMagentoLayout()
    {
        return (bool)$this->getScopeConfig()->getValue(self::XML_USING_LAYOUT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the full public url of the styla magazine
     *
     * @return string
     */
    public function getFullMagazineUrl()
    {
        $url = $this->storeManager->getStore()->getBaseUrl() . $this->getConfiguredRouteName();

        return $url;
    }

    /**
     * Get the content language code
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->getScopeConfig()->getValue(self::XML_LANGUAGE_CODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getPluginVersion()
    {
        return "2.0.11";
    }

    /**
     *
     * @param mixed $scope
     * @return array
     */
    public function parseScope($scope = null)
    {
        $scopeName = 'default';
        $scopeId   = 0;

        return is_array($scope) ? $scope : ['scope' => $scopeName, 'scope_id' => $scopeId];
    }

    /**
     *
     * @param array $connectionData
     * @param mixed $scope
     */
    public function updateConnectionConfiguration(array $connectionData, $scope = null)
    {
        $scope = $this->parseScope($scope);

        foreach ($this->_apiConfigurationFields as $fieldName => $configurationPath) {
            if (!isset($connectionData[$fieldName])) {
                continue; //not all data needs to be returned. we save whatever we can
            }

            //save the configuration
            $this->resourceConfig->saveConfig($configurationPath, $connectionData[$fieldName], $scope['scope'], $scope['scope_id']);
        }
    }
}
