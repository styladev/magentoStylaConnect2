<?php
namespace Styla\Connect2\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STYLA_API_CONNECTOR_URL_PRODUCTION = 'http://live.styla.com/api/magento';
    
    const XML_USERNAME = 'styla_connect2/general/username';
    const XML_ENABLED = 'styla_connect2/general/enable';
    const XML_FRONTEND_NAME = 'styla_connect2/general/frontend_name';
    
    //these are the configuration fields which may be returned by the connector
    protected $_apiConfigurationFields = array(
        'client' => self::XML_USERNAME,
        'rootpath' => self::XML_FRONTEND_NAME,
    );
    
    protected $resourceConfig;
    
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        $this->resourceConfig = $resourceConfig;
        
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
    
    public function isEnabled()
    {
        return $this->getScopeConfig()->getValue(self::XML_ENABLED);
    }
    
    public function getFrontendName()
    {
        return $this->getScopeConfig()->getValue(self::XML_FRONTEND_NAME);
    }
    
    public function getConnectorApiUrl()
    {
        //TODO: fix this to be configurable
        return self::STYLA_API_CONNECTOR_URL_PRODUCTION;
    }
    
    public function parseScope($scope = null)
    {
        $scope = 'default';
        $scopeId = 0;
        
        return is_array($scope) ? $scope : ['scope' => $scope, 'scope_id' => $scopeId];
    }
    
    public function updateConnectionConfiguration(array $connectionData, $scope = null)
    {
        $scope = $this->parseScope($scope);
        
        foreach($this->_apiConfigurationFields as $fieldName => $configurationPath) {
            if (!isset($connectionData[$fieldName])) {
                continue; //not all data needs to be returned. we save whatever we can
            }
            
            //save the configuration
            $this->resourceConfig->saveConfig($configurationPath, $connectionData[$fieldName], $scope['scope'], $scope['scope_id']);
        }
    }
}