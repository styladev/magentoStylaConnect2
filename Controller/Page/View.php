<?php
namespace Styla\Connect2\Controller\Page;

use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    protected $registry;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        
        parent::__construct($context);
    }
    
    public function execute()
    {
        $path = $this->getRequest()->getParam('path', false);
        
        $page = $this->resultPageFactory->create();
        $pageData = $this->_objectManager->get('Styla\Connect2\Helper\Page')->getPage($page, $path);
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
