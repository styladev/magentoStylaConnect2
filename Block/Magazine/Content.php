<?php
namespace Styla\Connect2\Block\Magazine;

/**
 * Class Styla_Connect_Block_Magazine_Content
 */
class Content extends \Styla\Connect2\Block\Magazine
{

    public function getNoscript()
    {
        return $this->getPage()
            ->getNoScript();
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