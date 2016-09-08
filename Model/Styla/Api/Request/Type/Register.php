<?php
namespace Styla\Connect2\Model\Styla\Api\Request\Type;

use Styla\Connect2\Model\Styla\Api as StylaApi;
use Styla\Connect2\Model\Styla\Api\Connector;

class Register extends AbstractType
{
    protected $_requestType = StylaApi::REQUEST_TYPE_REGISTER_MAGENTO_API;

    /**
     *
     * @var Connector
     */
    protected $connector;

    public function __construct(
        StylaApi $stylaApi,
        \Styla\Connect2\Helper\Config $configHelper,
        Connector $connector
    )
    {
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
    public function getResponseType()
    {
        return \Styla\Connect2\Model\Styla\Api\Response\Type\Register::class;
    }
}