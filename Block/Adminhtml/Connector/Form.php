<?php
namespace Styla\Connect2\Block\Adminhtml\Connector;

use Magento\Framework\View\Element\Template;
use Styla\Connect2\Helper\Config;

class Form extends Template
{
    /**
     *
     * @var Config
     */
    protected $configHelper;

    public function __construct(
        Config $configHelper,
        Template\Context $context, array $data = [])
    {
        $this->configHelper = $configHelper;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function isDeveloperMode()
    {
        return $this->configHelper->isDeveloperMode();
    }

    /**
     *
     * @return string
     */
    public function getConnectUrl()
    {
        return $this->getUrl('styla_connect2/connector/save');
    }
}
