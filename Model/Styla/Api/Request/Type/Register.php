<?php
namespace Styla\Connect2\Model\Styla\Api\Request\Type;


class Register extends \Styla\Connect2\Model\Styla\Api\Request\Type\AbstractType
{
    protected $_requestType = \Styla\Connect2\Model\Styla\Api::REQUEST_TYPE_REGISTER_MAGENTO_API;

    protected $connector;
    
    public function __construct(\Styla\Connect2\Model\Styla\Api $stylaApi, \Styla\Connect2\Helper\Config $configHelper,
        \Styla\Connect2\Model\Styla\Api\Connector $connector
    ) {
        $this->connector = $connector;
        
        parent::__construct($stylaApi, $configHelper);
    }
    
    /**
     * 
     * @return string
     */
    public function getApiUrl()
    {
        $url = $this->connector->getConnectorApiUrl();
        return $url;
    }
    
    /**
     * 
     * @return string
     */
    public function getResponseType() {
        return \Styla\Connect2\Model\Styla\Api\Response\Type\Register::class;
    }
}