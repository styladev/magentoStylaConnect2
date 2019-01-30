<?php
namespace Styla\Connect2\Controller\Page;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Registry;

class View extends Action
{
    /**
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @var Registry
     */
    protected $registry;

    /**
     * View constructor.
     * @param \Magento\Framework\App\Action\Context               $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Magento\Framework\Registry                         $registry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Registry $registry,
        \Magento\Framework\App\Cache\TypeListInterface $cache
    )
    {
        $cache->cleanType('layout');
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry             = $registry;

        parent::__construct($context);
    }

    public function execute()
    {
        /** @var string|bool $path */
        $path = $this->getRequest()->getParam('path', false);

        /** @var ResultPage $page */
        $page = $this->resultPageFactory->create();

        $pageData = $this->_objectManager->get('Styla\Connect2\Helper\Page')->getPage($page, $path);
        $pageStatusCode = $this->_objectManager->get('Styla\Connect2\Helper\Page')->getStatusCode();

        $page->setHttpResponseCode($pageStatusCode);

        if ($pageData === false) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        //save the styla page model for later reference
        //this is how i pass the object for rendering
        $this->registry->register('styla_page', $pageData);

        return $page;
    }
}