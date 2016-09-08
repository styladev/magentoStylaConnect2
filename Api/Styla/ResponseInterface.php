<?php
namespace Styla\Connect2\Api\Styla;

interface ResponseInterface
{
    /**
     * Set the headers from this response, already parsed to an array
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setResponseHeaders(array $headers);
    
    /**
     * Get a response header by name
     *
     * @param string $headerName
     * @return bool|string
     */
    public function getResponseHeader($headerName);
    
    /**
     * Get the final result of an Api call. If the api response is in json, it wll be processed, first.
     *
     * @return string
     * @throws \Exception
     */
    public function getResult();
    
    /**
     * Did this request return a normal, valid response?
     *
     * @return boolean
     */
    public function isOk();
    
    /**
     * Get the API response data as-is, without any processing
     *
     * @return mixed
     */
    public function getRawResult();
    
    /**
     * 
     * @param mixed $result
     */
    public function setRawResult($result);
    
    /**
     *
     * @param mixed                                $apiCallResult
     * @param \Magento\Framework\HTTP\Adapter\Curl $apiService
     */
    public function initialize($apiCallResult, \Magento\Framework\HTTP\Adapter\Curl $apiService);
    
    /**
     * @return mixed
     * @throws \Exception
     */
    public function getProcessedResult();
    
    /**
     * @return mixed
     * @throws \Exception
     */
    public function getJsonResult();
}
