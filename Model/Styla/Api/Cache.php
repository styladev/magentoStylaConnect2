<?php
namespace Styla\Connect2\Model\Styla\Api;

/**
 * TODO: this isn't wotking, it's still a mage1 class!
 */

/**
 * Class Styla_Connect_Model_Styla_Api_Cache
 *
 */
class Cache
{
    const CACHE_TAG   = 'STYLA_CONNECT';
    const CACHE_GROUP = 'styla_connect';

    protected $_cache;
    protected $_api;


    /**
     * Is this cache type enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        $useCache = Mage::app()->useCache(self::CACHE_GROUP);

        return $useCache;
    }

    /**
     * @param            $data
     * @param null       $id
     * @param array      $tags
     * @param bool|false $specificLifetime
     * @param int        $priority
     */
    public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        $tags[] = self::CACHE_TAG;
        $tags   = array_unique($tags);

        if ($specificLifetime === false) {
            $specificLifetime = $this->getCacheLifetime();
        }

        $this->getCache()->save($data, $id, $tags, $specificLifetime, $priority);
    }

    /**
     * @param            $id
     * @param bool|false $doNotTestCacheValidity
     * @param bool|false $doNotUnserialize
     * @return false|mixed
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        return $this->getCache()->load($id, $doNotTestCacheValidity, $doNotUnserialize);
    }

    /**
     *
     * @return Styla_Connect_Model_Styla_Api
     */
    public function getApi()
    {
        if (!$this->_api) {
            $this->_api = Mage::getSingleton('styla_connect/styla_api');
        }

        return $this->_api;
    }

    /**
     * Store the api response in cache, if possible
     *
     * @param Styla_Connect_Model_Styla_Api_Request_Type_Abstract  $request
     * @param Styla_Connect_Model_Styla_Api_Response_Type_Abstract $response
     */
    public function storeApiResponse($request, $response)
    {
        if (!$this->isEnabled() || $response->getHttpStatus() !== 200) {
            return;
        }

        $cachedData = serialize($response->getRawResult());
        $cacheKey   = $this->getCacheKey($request);

        $this->save($cachedData, $cacheKey);
    }

    public function getCacheLifetime()
    {
        return Mage::helper('styla_connect/config')->getCacheLifetime();
    }

    public function getApiVersion()
    {
        return $this->getApi()->getCurrentApiVersion();
    }

    /**
     *
     * @param Styla_Connect_Model_Styla_Api_Request_Type_Abstract $request
     * @return string
     */
    public function getCacheKey($request)
    {
        $key = $request->getRequestType() . $request->getRequestPath() . "_" . $this->getApiVersion();

        return $key;
    }

    /**
     * If possible, load a cached response
     *
     * @param Styla_Connect_Model_Styla_Api_Request_Type_Abstract $request
     * @return boolean|Styla_Connect_Model_Styla_Api_Response_Type_Abstract
     */
    public function getCachedApiResponse($request)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $key    = $this->getCacheKey($request);
        $cached = $this->load($key);
        if (!$cached) {
            return false;
        }

        //rebuild the response object
        $response = $this->getApi()->getResponse($request);
        $response->setHttpStatus(200);
        $response->setRawResult(unserialize($cached));

        return $response;
    }

    /**
     *
     * @return Zend_Cache_Core
     */
    protected function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Mage::app()->getCache();
        }

        return $this->_cache;
    }

}