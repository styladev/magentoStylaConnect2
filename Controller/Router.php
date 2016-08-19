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
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
    \Magento\Framework\App\ActionFactory $actionFactory, \Magento\Framework\App\ResponseInterface $response,
     \Styla\Connect2\Helper\Config $configHelper
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
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
        $identifier = trim($request->getPathInfo(), '/');
        
        $stylaFrontendName = $this->_getFrontendName();
        if (strpos($identifier, $stylaFrontendName) !== false) {
            
            $request->setModuleName('stylaconnect2page')->setControllerName('page')->setActionName('view');
        } else {
            //There is no match
            return;
        }

        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward', ['request' => $request]
        );
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
