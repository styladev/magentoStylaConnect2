<?php

namespace Styla\Connect2\Block\Magazine;

use Styla\Connect2\Block\Magazine;
use Styla\Connect2\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Styla\Connect2\Helper\Config;
use Magento\Store\Model\StoreManagerInterface;

class Content extends Magazine
{
    protected $helper;

    protected $rootPath;

    protected $storeManager;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        Config $configHelper,
        Data $helper,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        parent::__construct($context, $registry, $helper, $data);
    }

    /**
     *
     * @return string
     */
    public function getNoscript()
    {
        return $this->getPage()
            ->getNoScript();
    }

    /**
     *
     * @return string
     */
    public function getRootPath()
    {
        if (null === $this->rootPath) {
            $routeName = trim($this->helper->getCurrentMagazine()->getFrontName(), '/') . '/';

            $url = parse_url(
                str_replace(
                    '/index.php/',
                    '/',
                    $this->storeManager->getStore()->getUrl(
                        $routeName,
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_LINK]
                    )
                )
            );
            $this->rootPath = isset($url['path']) ? $url['path'] : '';
        }

        return $this->rootPath;
    }

    /**
     *
     * @return string
     */
    public function getPluginVersion()
    {
        return $this->helper->getPluginVersion();
    }
}