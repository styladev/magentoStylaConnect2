<?php
namespace Styla\Connect2\Helper;

use Magento\Framework\App\Action\Action;

class Page extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_pageFactory;
    protected $_configHelper;
    
    public function __construct(\Magento\Framework\App\Helper\Context $context,
            \Styla\Connect2\Model\PageFactory $pageFactory,
            \Styla\Connect2\Helper\Config $configHelper
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_configHelper = $configHelper;
        
        return parent::__construct($context);
    }
    
    /**
     * 
     * @param Action $action
     * @param type $path
     * @return \Magento\Framework\View\Result\Page|bool
     */
    public function getPage(\Magento\Framework\View\Result\Page $pageResult, $path = false)
    {
        $currentPath = $this->getPath($path);
        
        //load from styla
        $page = $this->_pageFactory->create()->loadByPath($currentPath);
        if(!$page->exist()) {
            return false;
        }
        
        //set the proper page layout
        $this->setPageLayout($page, $pageResult);
        
        return $page;
    }
    
    public function setPageLayout($page, $pageResult)
    {
        if(!$this->_configHelper->isUsingMagentoLayout()) {
            $pageResult->getConfig()->setPageLayout('empty');
        }
        
        //fill the head metadata with our page's meta
        $pageMeta = $page->getBaseMetaData();
        $pageConfig = $pageResult->getConfig();
        
        //this is commented-out, as it would display a nasty header above the catalog:
        //if(isset($pageMeta['title'])) {
        //    $pageConfig->getTitle()->set($pageMeta['title']);
        //}
        
        if(isset($pageMeta['keywords'])) {
            $pageConfig->setKeywords($pageMeta['keywords']);
        }
        
        if(isset($pageMeta['description'])) {
            $pageConfig->setKeywords($pageMeta['description']);
        }
    }
    
    public function getPath($path = false)
    {
        return $path !== false ? $path : '/';
    }
}
