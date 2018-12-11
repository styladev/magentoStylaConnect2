<?php
namespace Styla\Connect2\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Styla\Connect2\Helper\Config;

class Magazine extends Template
{
    /**
     *
     * @var \Styla\Connect2\Model\Page
     */
    protected $_page;

    /**
     *
     * @var Registry
     */
    protected $_registry;

    /**
     *
     * @var Config
     */
    protected $_configHelper;

    public function __construct(Template\Context $context, Registry $registry, Config $configHelper, array $data = [])
    {
        $this->_configHelper = $configHelper;
        $this->_registry     = $registry;

        parent::__construct($context, $data);
    }

    /**
     *
     * @return \Styla\Connect2\Model\Page
     */
    public function getPage()
    {
        if (null === $this->_page) {
            $this->_page = $this->_registry->registry('styla_page');
        }

        return $this->_page;
    }

    /**
     * @deprecated
     * @return Config
     */
    public function getConfigHelper()
    {
        return $this->_configHelper;
    }
}