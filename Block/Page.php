<?php
namespace Styla\Connect2\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Page extends Template
{
    protected $_page;
    protected $_registry;

    public function __construct(Template\Context $context, Registry $registry, array $data = [])
    {
        $this->_registry = $registry;

        return parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getPage()
    {
        if (null === $this->_page) {
            $this->_page = $this->_registry->registry('styla_page');
        }

        return $this->_page;
    }

    public function getRootPath()
    {
        return $this->getConfigHelper()->getRouteName();
    }

    public function getPluginVersion()
    {
        return $this->getConfigHelper()->getPluginVersion();
    }
}

