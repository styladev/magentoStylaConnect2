<?php

namespace Styla\Connect2\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     *
     * @var \Styla\Connect2\Helper\Config
     */
    protected $_configHelper;

    /**
     * @param \Magento\Framework\App\ActionFactory     $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Styla\Connect2\Helper\Config $configHelper
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_response     = $response;
        $this->_configHelper = $configHelper;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if(!$this->_configHelper->isConfiguredForThisStore()) {
            return false; //module entirely disabled or magazine username not set.
        }
        
        $identifier = trim($request->getPathInfo(), '/');

        $stylaFrontendName = $this->_getFrontendName();
        if (strpos($identifier, $stylaFrontendName) !== false) {

            $request->setModuleName('stylaconnect2page')->setControllerName('page')->setActionName('view');
        } else {
            //There is no match
            return false;
        }

        //we want the part after the initial magazine uri, as it may point us to the user's intention
        $route = $this->_getRouteSettings($identifier, $request);
        $request->setParam('path', $route);

        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward', ['request' => $request]
        );
    }

    /**
     * Get only the last part of the route, leading up to a specific page
     *
     * @param string $path
     * 
     * @return string
     */
    protected function _getRouteSettings($path, \Magento\Framework\App\RequestInterface $request)
    {
        //the path should not contain the trailing slash, the styla api is not expecting it
        $path = rtrim(str_replace($this->_getFrontendName(), '', $path), '/');
        
        //all the get params should be retained
        $requestParameters = $this->_getRequestParamsString($request);

        $route = $path . ($requestParameters ? '?' . $requestParameters : '');
        return $route;
    }
    
    /**
     * 
     * @param \Magento\Framework\App\RequestInterface $request
     * @return string
     */
    protected function _getRequestParamsString(\Magento\Framework\App\RequestInterface $request)
    {
        $allRequestParameters = $request->getQuery();
        
        return count($allRequestParameters) ? http_build_query($allRequestParameters) : '';
    }

    /**
     *
     * @return string
     */
    protected function _getFrontendName()
    {
        return $this->_configHelper->getFrontendName();
    }
}
