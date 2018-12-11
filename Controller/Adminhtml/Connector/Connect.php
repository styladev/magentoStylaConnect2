<?php
namespace Styla\Connect2\Controller\Adminhtml\Connector;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory as PageFactory;
use Magento\Backend\App\Action\Context;

class Connect extends Action
{
    /**
     *
     * @var PageFactory 
     */
    protected $pageFactory;

    protected $test;
    /**
     * 
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(Context $context, PageFactory $pageFactory,
        \Styla\Connect2\Model\MagazineFactory $a,
        \Styla\Connect2\Model\ResourceModel\Magazine $b) {
        $this->pageFactory = $pageFactory;
        $this->test = $b;
        return parent::__construct($context);
    }

    public function execute() {
        /** @var ResultPage $page */
        $page = $this->pageFactory->create();

        /*$this->test->load( \Styla\Connect2\Model\Magazine::class, 1, 'store_id');
        $test = $this->test->create();
        $collection = $test->load(1, 'is_default');
        foreach($collection as $item){
            echo "<pre>";
            print_r($item->getData());
            echo "</pre>";
        }*/


        return $page;
    }
}
