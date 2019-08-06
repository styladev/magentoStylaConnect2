<?php

namespace Styla\Connect2\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\Registry;
use Magento\Framework\App\Action\Redirect;

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
     * @var \Styla\Connect2\Model\MagazineFactory
     */
    protected $magazineFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\ActionFactory       $actionFactory
     * @param \Magento\Framework\App\ResponseInterface   $response
     * @param \Styla\Connect2\Model\MagazineFactory      $magazineFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry                $registry
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Styla\Connect2\Model\MagazineFactory $magazineFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Registry $registry
    ) {
        $this->registry        = $registry;
        $this->magazineFactory = $magazineFactory;
        $this->actionFactory   = $actionFactory;
        $this->_response       = $response;
        $this->storeManager    = $storeManager;
    }

    /**
     * {@inheritDoc}
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        // this route is running before the standard MVC router, so forwarding must be omitted second time
        if ($request->getModuleName() === 'stylaconnect2page') {
            return false;
        }

        $path      = $this->_getRequestPath($request);
        $frontName = $this->_getFrontName($path) === '' ? null : $this->_getFrontName($path);

        if ($path === false) {
            return false;
        }

        $magazineModel = $this->magazineFactory->create();
        $magazine      = $magazineModel->loadByFrontName($frontName, $this->storeManager->getStore()->getId());

        if (!$magazine || !$magazine->isActive()) {
            return false;
        }

        $this->registry->register('current_magazine', $magazine);

        $routeSettings = $this->_getRouteSettings($magazine, $path, $request);
        //setModule name is the front name
        $request
            ->setModuleName('stylaconnect2page')
            ->setControllerName('page')
            ->setActionName('view')
            ->setParam('path', $routeSettings);

        return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
    }

    /**
     * Get only the last part of the route, leading up to a specific page
     *
     * @param string $path
     *
     * @return string
     */
    protected function _getRouteSettings($magazine, $path, \Magento\Framework\App\RequestInterface $request)
    {
        //the path should not contain the trailing slash, the styla api is not expecting it
        $path = rtrim(str_replace($magazine->getFrontName(), '', $path), '/');

        //all the get params should be retained
        $requestParameters = $this->_getRequestParamsString($request);

        $route = $path.($requestParameters ? '?'.$requestParameters : '');

        return $route;
    }

    protected function _getRequestPath(\Magento\Framework\App\RequestInterface $request)
    {
        return trim($request->getRequestString(), '/');
    }

    /**
     * Can this request's path be processed by this router?
     *
     * @param string $path
     *
     * @return string|boolean
     */
    protected function _getFrontName($path)
    {
        //we expect the magazine's frontend name to be the first element in the path_info
        $path         = trim($path, '/').'/';
        $elements     = explode('/', $path, 2);
        $frontendName = reset($elements);

        return trim($frontendName, '/');
    }

    /**
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return string
     */
    protected function _getRequestParamsString(\Magento\Framework\App\RequestInterface $request)
    {
        $allRequestParameters = $request->getQuery();

        return count($allRequestParameters) ? http_build_query($allRequestParameters) : '';
    }
}
