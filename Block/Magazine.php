<?php
namespace Styla\Connect2\Block;
use Magento\Framework\View\Element\Template;

/**
 * Class Styla_Connect_Block_Magazine
 */
class Magazine extends Template
{
    protected $_page;
    protected $_registry;
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
    
    public function getPage()
    {
        if(null === $this->_page) {
            $this->_page = $this->_registry->registry('styla_page');
        }
        
        return $this->_page;
    }

    /**
     *
     * @return Styla_Connect_Helper_Config
     */
    public function getConfigHelper()
    {
        return $this->_configHelper;
    }
}