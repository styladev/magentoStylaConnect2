<?php

namespace Styla\Connect2\Block\Magazine;

use Styla\Connect2\Block\Magazine;
use Styla\Connect2\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Styla\Connect2\Helper\Config;

class Content extends Magazine
{
    protected $helper;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        Config $configHelper,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry ,$configHelper, $data);
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
        return $this->helper->getCurrentMagazine();
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