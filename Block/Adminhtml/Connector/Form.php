<?php
namespace Styla\Connect2\Block\Adminhtml\Connector;
use Magento\Framework\View\Element\Template;

class Form extends Template
{
    protected function _prepareLayout() {
        return parent::_prepareLayout();
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
