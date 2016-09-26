<?php
namespace Styla\Connect2\Controller\Adminhtml\Connector;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory as PageFactory;
use Magento\Framework\App\Action\Context;

class Connect extends Action
{
    /**
     *
     * @var PageFactory 
     */
    protected $pageFactory;
    
    /**
     * 
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(Context $context, PageFactory $pageFactory) {
        $this->pageFactory = $pageFactory;
        
        return parent::__construct($context);
    }
    
    public function execute() {
        /** @var ResultPage $page */
        $page = $this->pageFactory->create();
        
        return $page;
    }
}
