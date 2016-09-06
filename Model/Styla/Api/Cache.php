<?php
namespace Styla\Connect2\Model\Styla\Api;

use Styla\Connect2\Model\Styla\Api\Request\Type\AbstractType as StylaRequest;

class Cache
{
    const CACHE_TAG   = 'STYLA_CONNECT2';
    const CACHE_GROUP = 'styla_connect2';

    /**
     * We're using the builtin Magento2 WebApi (Web Services) cache
     *
     * @var \Magento\Webapi\Model\Cache\Type\Webapi
     */
    protected $_cache;

    protected $_configHelper;
    protected $_api;
    protected $_cacheLifetime;

    public function __construct(
        \Magento\Webapi\Model\Cache\Type\Webapi $cache,
        \Styla\Connect2\Model\Styla\Api $api,
        \Styla\Connect2\Helper\Config $configHelper
    )
    {
        $this->_cache        = $cache;
        $this->_api          = $api;
        $this->_configHelper = $configHelper;
    }

    /**
     * @param            $data
     * @param null       $id
     * @param bool|false $specificLifetime
     *
     * @return bool
     */
    public function save($data, $id, $specificLifetime = null)
    {
        if ($specificLifetime === null) {
            $specificLifetime = $this->getCacheLifetime();
        }

        if (is_array($data)) {
            $data = serialize($data);
        }

        return $this->_cache->save($data, $id, [self::CACHE_TAG], $specificLifetime);
    }

    /**
     * @param            $id
     * @param bool|false $doNotTestCacheValidity
     * @return false|mixed
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        if (false === $doNotTestCacheValidity) {
            if (false === $this->_cache->test($id)) {
                return false;
            }
        }

        $data = $this->_cache->load($id);
        return $data ? $data : false;
    }

    /**
     *
     * @return \Styla\Connect2\Model\Styla\Api
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * Store the api response in cache, if possible
     *
     * @param Request\Type\AbstractType  $request
     * @param Response\Type\AbstractType $response
     */
    public function storeApiResponse(Request\Type\AbstractType $request, Response\Type\AbstractType $response)
    {
        if ($response->getHttpStatus() !== 200) {
            return;
        }

        $cachedData = serialize($response->getRawResult());
        $cacheKey   = $this->getCacheKey($request);

        $this->save($cachedData, $cacheKey);
    }

    /**
     *
     * @return int|null
     */
    public function getCacheLifetime()
    {
        if (null === $this->_cacheLifetime) {
            $this->_cacheLifetime = $this->_configHelper->getCacheLifetime();
        }
        return $this->_cacheLifetime;
    }

    /**
     *
     * @return string|bool
     */
    public function getApiVersion()
    {
        return $this->getApi()->getCurrentApiVersion();
    }

    /**
     *
     * @param  $request
     * @return string
     */
    public function getCacheKey(StylaRequest $request)
    {
        $key = $request->getRequestType() . $request->getRequestPath() . "_" . $this->getApiVersion();

        return $key;
    }

    /**
     * If possible, load a cached response
     *
     * @param StylaRequest $request
     * @return boolean|\Styla\Connect2\Model\Styla\Api\Response\Type\AbstractType
     */
    public function getCachedApiResponse(StylaRequest $request)
    {
        $key    = $this->getCacheKey($request);
        $cached = $this->load($key, true);
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
     * @return \Magento\Webapi\Model\Cache\Type\Webapi
     */
    protected function getCache()
    {
        return $this->_cache;
    }

}