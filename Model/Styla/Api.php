<?php
namespace Styla\Connect2\Model\Styla;

use Styla\Connect2\Model\Styla\Api\Request as StylaRequest;
use Styla\Connect2\Model\Styla\Api\Response as StylaResponse;
use Styla\Connect2\Api\Styla\RequestInterface;
use Styla\Connect2\Api\Styla\ResponseInterface;
use Magento\Framework\HTTP\Adapter\Curl as Curl;
use Styla\Connect2\Model\Styla\Api\Cache as StylaCache;

class Api
{
    const REQUEST_TYPE_SEO                  = 'seo';
    const REQUEST_TYPE_VERSION              = 'version';
    const REQUEST_TYPE_REGISTER_MAGENTO_API = 'register';

    /**
     *
     * @var \Styla\Connect2\Model\Styla\Api\RequestFactory
     */
    protected $requestFactory;

    /**
     *
     * @var \Styla\Connect2\Model\Styla\Api\ResponseFactory
     */
    protected $responseFactory;

    /**
     *
     * @var \Magento\Framework\HTTP\Adapter\Curl
     */
    protected $curl;

    /**
     *
     * @var StylaCache
     */
    protected $cache;
    protected $cacheFactory;


    protected $_currentApiVersion;

    /**
     * these options are used for initializing the connector to api service
     */
    protected $_apiConnectionOptions = [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HTTPHEADER,
        [
            'Accept: application/json',
        ],
    ];

    public function __construct(
        \Styla\Connect2\Model\Styla\Api\RequestFactory $requestFactory,
        \Styla\Connect2\Model\Styla\Api\ResponseFactory $responseFactory,
        \Styla\Connect2\Model\Styla\Api\CacheFactory $cacheFactory,
        Curl $curl
    )
    {
        $this->requestFactory  = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->curl            = $curl;
        $this->cacheFactory    = $cacheFactory;
    }

    /**
     *
     * @return StylaCache
     */
    public function getCache()
    {
        if (null === $this->cache) {
            $this->cache = $this->cacheFactory->create();
        }

        return $this->cache;
    }

    /**
     *
     * @param string $requestPath
     * @return boolean|array
     */
    public function requestPageData($requestPath = '/')
    {
        if (!$requestPath) {
            $requestPath = '/';
        }

        try {
            $data = $this->getPageSeoData($requestPath);
            if (isset($data['status']) && $data['status'] !== 200) {
                return false;
            }
            unset($data['code'], $data['status']);

            return $data;
        } catch (\Exception  $e) {
            //todo: log magento exception

            return false;
        }
    }

    /**
     * 
     * @param string $requestPath
     * @return mixed
     */
    public function getPageSeoData($requestPath)
    {
        //check if a no-response status was cached
        $cache = $this->getCache();
        if ($cache->load('styla_seo_unreachable')) {
            return [];
        }

        $seoRequest = $this->getRequest(StylaRequest\Type\Seo::class)
            ->initialize($requestPath);

        try {
            $response = $this->callService($seoRequest, true, true);
        } catch (\Exception $e) {
            //in case of the SEO request, we don't mind if the connection was failed. we'll just save this failed status for 5 minutes
            //and not return anything.
            $cache->save("1", 'styla_seo_unreachable', 5 * 60); //save for 5 minutes

            return [];
        }

        return $response->getResult();
    }

    /**
     * Get the current cache version number from the Styla api
     *
     * @return string
     */
    public function getCurrentApiVersion()
    {
        if (!$this->_currentApiVersion) {
            $cache      = $this->getCache();
            $apiVersion = $cache->load('stylaapiversion');

            if (!$apiVersion) {
                /** @var RequestInterface $request */
                $request = $this->getRequest(StylaRequest\Type\Version::class);

                try {
                    /** @var ResponseInterface $response */
                    $response   = $this->callService($request, false, true);
                    $apiVersion = $response->getResult();

                    //if returned by the response, use the cache-control set lifetime
                    $cacheTime = $response->getCacheControlValue();

                    if (false === $cacheTime) {
                        $cacheTime = "3600";
                    }

                    //cache for $cacheTime seconds
                    $cache->save(
                        $apiVersion,
                        'stylaapiversion',
                        $cacheTime
                    );
                } catch (\Exception $e) {
                    //this request might possibly fail, for example when wrong url is set in developer mode

                    $apiVersion = 1;
                }
            }
            $this->_currentApiVersion = $apiVersion;
        }

        return $this->_currentApiVersion;
    }

    /**
     * Get the api service connector
     *
     * @param bool $addResultHeaders
     * @return Curl
     */
    public function getService($addResultHeaders = false)
    {
        $this->curl->setOptions($this->_apiConnectionOptions);

        //this will tell curl to omit headers in result, if false
        $this->curl->setConfig(
                [
                    'header' => $addResultHeaders, 
                    'timeout' => 5, //as some requests (seo) can take a bit longer to complete
                ]
            );

        return $this->curl;
    }

    /**
     *
     * @param RequestInterface               $request
     * @param bool                           $canUseCache Can return cached result?
     * @param bool                           $useResultHeadersInResponse Should the response also include the parsed headers?
     * @return mixed
     * @throws \Exception
     */
    public function callService(
        RequestInterface $request,
        $canUseCache = true,
        $useResultHeadersInResponse = false
    )
    {
        $cache = $this->getCache();
        if ($canUseCache && $cachedResponse = $cache->getCachedApiResponse($request)) {
            return $cachedResponse;
        }

        $requestApiUrl = $request->getApiUrl();
        $service       = $this->getService($useResultHeadersInResponse);

        //include the request timeout, if set
        $requestTimeoutOptions = $request->getConnectionTimeoutOptions();
        if ($requestTimeoutOptions) {
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
            ['Accept: application/json'],
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

        if ($canUseCache && $response->getHttpStatus() === 200) {
            $cache->storeApiResponse($request, $response);
        }

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
        $headers = [];
        if (false === strpos($response, "\r\n\r\n")) {
            return ['headers' => [], 'body' => $response];
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

        return [
            'headers' => $headers,
            'body'    => $bodyContent,
        ];
    }

    /**
     * @param string $type
     * @return RequestInterface
     */
    public function getRequest($type)
    {
        /** @var RequestInterface $request */
        $request = $this->requestFactory->create($type);

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function getResponse(RequestInterface $request)
    {
        $response = $this->responseFactory->create($request->getResponseType());

        return $response;
    }
}
