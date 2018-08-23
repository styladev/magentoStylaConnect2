<?php
namespace Styla\Connect2\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Styla\Connect2\Model\PageFactory as StylaPageFactory;
use Styla\Connect2\Model\Page as StylaPage;
use Styla\Connect2\Helper\Config as Config;
use Magento\Framework\View\Result\Page as ResultPage;

class Page extends AbstractHelper
{
    /**
     *
     * @var StylaPageFactory
     */
    protected $_pageFactory;

    /**
     *
     * @var Config
     */
    protected $_configHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StylaPageFactory $pageFactory,
        Config $configHelper
    )
    {
        $this->_pageFactory  = $pageFactory;
        $this->_configHelper = $configHelper;

        return parent::__construct($context);
    }

    /**
     * @param ResultPage $pageResult
     * @param bool                                $path
     * @return bool|Page
     */
    public function getPage(ResultPage $pageResult, $path = false)
    {
        /** @var string $currentPath */
        $currentPath = $this->getPath($path);

        /** @var StylaPage $page */
        //load from styla
        $page = $this->_pageFactory->create()->loadByPath($currentPath);

        if (!$page->exist()) {
            return false;
        }

        //set the proper page layout
        $this->setPageLayout($page, $pageResult);

        return $page;
    }

    /**
     * @param bool                                $path
     * @return string
     */
    public function getStatusCode($path = false)
    {
        /** @var string $currentPath */
        $currentPath = $this->getPath($path);

        /** @var StylaPage $page */
        //load from styla
        $page = $this->_pageFactory->create()->loadByPath($currentPath);

        $statusCode = $page->getSeoStatusCode();

        if (!$statusCode){
            $statusCode = "200";
        }

        return $statusCode;
    }

    /**
     *
     * @param StylaPage $page
     * @param ResultPage $pageResult
     */
    public function setPageLayout(StylaPage $page, ResultPage $pageResult)
    {
        if (!$this->_configHelper->isUsingMagentoLayout()) {
            $pageResult->getConfig()->setPageLayout('empty');
        }

        //fill the head metadata with our page's meta
        $pageMeta   = $page->getBaseMetaData();
        $pageConfig = $pageResult->getConfig();

        //info: as adding a meta title by default also triggers a "page.main.title" block
        //to display it, we're removing that block in the stylaconnect2page_page_view.xml layout file
        if(isset($pageMeta['title'])) {
            $pageConfig->getTitle()->set($pageMeta['title']);
        }

        if (isset($pageMeta['keywords'])) {
            $pageConfig->setKeywords($pageMeta['keywords']);
        }

        if (isset($pageMeta['description'])) {
            $pageConfig->setDescription($pageMeta['description']);
        }
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public function getPath($path = false)
    {
        return $path !== false ? $path : '/';
    }
}
