<?php
namespace Styla\Connect2\Model\Styla\Api\Request\Type;


class Register extends \Styla\Connect2\Model\Styla\Api\Request\Type\AbstractType
{
    protected $_requestType = \Styla\Connect2\Model\Styla\Api::REQUEST_TYPE_REGISTER_MAGENTO_API;

    public function getApiUrl()
    {
        return $this->getConfigHelper()->getConnectorApiUrl();
    }
    
    public function getResponseType() {
        return \Styla\Connect2\Model\Styla\Api\Response\Type\Register::class;
    }
}