<?php

/**
 * Class Styla_Connect_Model_Styla_Api_Request_Type_Version
 */
class Styla_Connect_Model_Styla_Api_Request_Type_Version extends Styla_Connect_Model_Styla_Api_Request_Type_Abstract
{
    /** @deprecated URL_API_VERSION there's now a separate url for the live and stage mode of operation, taken from the configuration helper */
    const URL_API_VERSION = 'http://live.styla.com/api/version/%s';
    protected $_requestType = Styla_Connect_Model_Styla_Api::REQUEST_TYPE_VERSION;

    /**
     * Get the versioning api url, according to the current store and mode of operation
     *
     * @return string
     */
    public function getApiUrl()
    {
        $config = $this->getConfigHelper();
        
        $versionUrl = $config->getApiVersionUrl();
        return $versionUrl;
    }
}