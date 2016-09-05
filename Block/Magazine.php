<?php
namespace Styla\Connect2\Block;

use Magento\Framework\View\Element\Template;

class Magazine extends Template
{
    /**
     *
     * @var \Styla\Connect2\Model\Page
     */
    protected $_page;
    
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
    
    /**
     *
     * @var \Styla\Connect2\Helper\Config
     */
    protected $_configHelper;
    
    public function __construct(Template\Context $context, 
            \Magento\Framework\Registry $registry,
            \Styla\Connect2\Helper\Config $configHelper,
            array $data = array()
    ) {
        $this->_configHelper = $configHelper;
        $this->_registry = $registry;
        
        return parent::__construct($context, $data);
    }
    
    /**
     * 
     * @return \Styla\Connect2\Model\Page
     */
    public function getPage()
    {
        if(null === $this->_page) {
            $this->_page = $this->_registry->registry('styla_page');
        }
        
        return $this->_page;
    }

    /**
     *
     * @return \Styla\Connect2\Helper\Config
     */
    public function getConfigHelper()
    {
        return $this->_configHelper;
    }
}