<?php
namespace Styla\Connect2\Api\Styla;

interface RequestInterface
{
    /**
     *
     * @return string
     */
    public function getApiUrl();
    
    /**
     * Initialize this request with data to pass on to the api service
     *
     * @param string $requestPath
     * @param mixed  $params
     *
     * @return $this
     */
    public function initialize($requestPath, $params = null);
    
    /**
     * Get the curl connection type - GET/POST
     *
     * @return string
     */
    public function getConnectionType();
    
    /**
     * Set the curl connection type - GET/POST
     *
     * @param  $type
     */
    public function setConnectionType($type);
    
    /**
     * Get the request path of this request
     *
     * @param bool $urlEncoded
     * @return string $requestPath
     */
    public function getRequestPath($urlEncoded = false);
    
    /**
     * Get the type name of this request
     *
     * @return string
     */
    public function getRequestType();
    
    /**
     * Get the class type name for the response type for this request
     *
     * @return string
     */
    public function getResponseType();
    
    /**
     * Get request params (used for POST-type requests)
     *
     * @param array $params
     */
    public function setParams(array $params);
    
    /**
     *
     * @return array
     */
    public function getParams();
}
