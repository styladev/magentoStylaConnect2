<?php
namespace Styla\Connect2\Model\Styla\Api\Response\Type;

abstract class AbstractType
{
    const CONTENT_TYPE_PLAIN = 'plain';
    const CONTENT_TYPE_JSON  = 'json';

    const HEADER_CACHE_CONTROL        = 'Cache-Control';
    const HEADER_CACHE_CONTROL_MAXAGE = 'max-age';

    protected $_httpStatus;
    protected $_error;
    protected $_result;
    protected $_responseHeaders;

    protected $_contentType = self::CONTENT_TYPE_PLAIN;

    /**
     * Set the headers from this response, already parsed to an array
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setResponseHeaders(array $headers)
    {
        $this->_responseHeaders = $headers;

        return $this;
    }

    /**
     * Get a response header by name
     *
     * @param string $headerName
     * @return bool|string
     */
    public function getResponseHeader($headerName)
    {
        return is_array(
            $this->_responseHeaders
        ) && isset($this->_responseHeaders[$headerName]) ? $this->_responseHeaders[$headerName] : false;
    }

    /**
     * Get the value of the cache control header for this response
     *
     * @param string $key
     * @return boolean|string
     */
    public function getCacheControlValue($key = self::HEADER_CACHE_CONTROL_MAXAGE)
    {
        $header = $this->getResponseHeader(self::HEADER_CACHE_CONTROL);
        if (!$header) {
            return false;
        }

        $values = explode(' ', $header);
        if (empty($values)) {
            return false;
        }

        $cacheControl = [];
        foreach ($values as $line) {
            list($name, $value) = explode('=', $line);
            $cacheControl[$name] = $value;
        }

        return isset($cacheControl[$key]) ? $cacheControl[$key] : false;
    }

    /**
     * Get the final result of an Api call. If the api response is in json, it wll be processed, first.
     *
     * @return string
     * @throws \Exception
     */
    public function getResult()
    {
        if ($this->getHttpStatus() != 200) {
            throw new \Exception(
                "The Styla Api request didn't return results: " . $this->getHttpStatus() . ' - ' . $this->getError()
            );
        }

        $result = $this->getProcessedResult();

        return $result;
    }

    /**
     * Did this request return a normal, valid response?
     *
     * @return boolean
     */
    public function isOk()
    {
        if ($this->getHttpStatus() == 200) {
            return true;
        }

        return false;
    }

    /**
     * Get the API response data as-is, without any processing
     *
     * @return mixed
     */
    public function getRawResult()
    {
        return $this->_result;
    }

    public function setRawResult($result)
    {
        $this->_result = $result;
    }

    public function setHttpStatus($status)
    {
        $this->_httpStatus = $status;
    }

    /**
     *
     * @param mixed                                $apiCallResult
     * @param \Magento\Framework\HTTP\Adapter\Curl $apiService
     */
    public function initialize($apiCallResult, $apiService)
    {
        $this->_result     = $apiCallResult;
        $this->_error      = $apiService->getError();
        $this->_httpStatus = $apiService->getInfo(CURLINFO_HTTP_CODE);
    }

    public function getHttpStatus()
    {
        return $this->_httpStatus;
    }

    public function getError()
    {
        return $this->_error;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getProcessedResult()
    {
        switch ($this->_contentType) {
            case self::CONTENT_TYPE_JSON:
                return $this->getJsonResult();
            case self::CONTENT_TYPE_PLAIN:
                return $this->_result;
            default:
                throw new \Exception(sprintf('unknown content type %s', $this->_contentType));
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getJsonResult()
    {
        $jsonResult = json_decode($this->_result, true);
        if ($jsonResult === null) {
            throw new \Exception('Error parsing a JSON Api result.');
        }

        return $jsonResult;
    }
}