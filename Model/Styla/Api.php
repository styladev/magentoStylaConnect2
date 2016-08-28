<?php
namespace Styla\Connect2\Model\Styla;

use Styla\Connect2\Model\Styla\Api\Request as StylaRequest;
use Styla\Connect2\Model\Styla\Api\Response as StylaResponse;
use Magento\Framework\HTTP\Adapter\Curl as Curl;

class Api
{
    const REQUEST_TYPE_SEO                  = 'seo';
    const REQUEST_TYPE_VERSION              = 'version';
    const REQUEST_TYPE_REGISTER_MAGENTO_API = 'register';
    
    protected $requestFactory;
    protected $responseFactory;
    protected $curl;
    
    /**
     * these options are used for initializing the connector to api service
     */
    protected $_apiConnectionOptions = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HTTPHEADER,
        array(
            'Accept: application/json',
        ),
    );
    
    public function __construct(
        \Styla\Connect2\Model\Styla\Api\RequestFactory $requestFactory,
        \Styla\Connect2\Model\Styla\Api\ResponseFactory $responseFactory,
        Curl $curl
    ) {
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->curl = $curl;
    }
    
    /**
     * Get the api service connector
     *
     * @return Varien_Http_Adapter_Curl
     */
    public function getService($addResultHeaders = false)
    {
        $this->curl->setOptions($this->_apiConnectionOptions);

        //this will tell curl to omit headers in result, if false
        $this->curl->setConfig(array('header' => $addResultHeaders));

        return $this->curl;
    }
    
    public function callService(
        StylaRequest\Type\AbstractType $request,
        $canUseCache = true,
        $useResultHeadersInResponse = false
    )
    {
//        $cache = $this->getCache();
//        if ($canUseCache && $cachedResponse = $cache->getCachedApiResponse($request)) {
//            return $cachedResponse;
//        }
        
        $requestApiUrl = $request->getApiUrl();
        /** @var Varien_Http_Adapter_Curl $service */
        $service       = $this->getService($useResultHeadersInResponse);
        
        //include the request timeout, if set
        $requestTimeoutOptions = $request->getConnectionTimeoutOptions();
        if($requestTimeoutOptions) {
            $service->setOptions($requestTimeoutOptions);
        }
        
        //fill in the post params, if this is a POST request
        $requestMethod = $request->getConnectionType();
        $requestBody   = '';
        
        if ($requestMethod == \Zend\Http\Request::METHOD_POST) {
            $requestBody = $request->getParams();
        }

        $service->write(
            $request->getConnectionType(),
            $requestApiUrl,
            '1.1',
            array('Accept: application/json'),
            $requestBody
        );
        
        $result = $service->read();
        if (!$result) {
            throw new \Exception("Couldn't get a result from the API.");
        }
        
        /**
         * the result can contain both the body and http headers, if the $addResultHeaders var is active.
         * we'll need to parse this info, before giving it to the response object
         */
        $resultBody    = $result;
        $resultHeaders = false;
        if ($useResultHeadersInResponse) {
            $result        = $this->parseHttpResponse($result);
            $resultBody    = $result['body'];
            $resultHeaders = $result['headers'];
        }

        $response = $this->getResponse($request);
        $response->initialize($resultBody, $service);

        if ($resultHeaders) {
            $response->setResponseHeaders($resultHeaders);
        }

//        if ($canUseCache && $response->getHttpStatus() === 200) {
//            $cache->storeApiResponse($request, $response);
//        }

        return $response;
    }
    
    /**
     * Parse a http response, containing both the headers and content and return it as array
     *
     * @param string $response
     * @return array
     */
    public function parseHttpResponse($response)
    {
        $headers = array();
        if (false === strpos($response, "\r\n\r\n")) {
            return array('headers' => array(), 'body' => $response);
        }

        list($headerContent, $bodyContent) = explode("\r\n\r\n", $response, 2);

        foreach (explode("\r\n", $headerContent) as $i => $header) {
            if ($i === 0) {
                $headers['http_code'] = $header;
            } else {
                list($headerName, $value) = explode(': ', $header);
                $headers[$headerName] = $value;
            }
        }

        return array(
            'headers' => $headers,
            'body'    => $bodyContent,
        );
    }
    
    public function getRequest($type)
    {
        $request = $this->requestFactory->create($type);
        
        return $request;
    }
    
    public function getResponse($request)
    {
        $request = $this->responseFactory->create($request->getResponseType());
        
        return $request;
    }
}
