<?php
namespace Styla\Connect2\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_ENABLED = 'styla_connect2/general/enable';
    const XML_FRONTEND_NAME = 'styla_connect2/general/frontend_name';
    
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
}