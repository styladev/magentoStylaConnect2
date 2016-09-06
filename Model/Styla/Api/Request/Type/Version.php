<?php
namespace Styla\Connect2\Model\Styla\Api\Request\Type;


class Version extends AbstractType
{
    protected $_requestType = \Styla\Connect2\Model\Styla\Api::REQUEST_TYPE_VERSION;

    /**
     * Get the versioning api url, according to the current store and mode of operation
     *
     * @return string
     */
    public function getApiUrl()
    {
        $config = $this->getConfigHelper();

        $versionUrl = $config->getApiVersionUrl();
        return $versionUrl;
    }

    /**
     *
     * @return string
     */
    public function getResponseType()
    {
        return \Styla\Connect2\Model\Styla\Api\Response\Type\Version::class;
    }
}