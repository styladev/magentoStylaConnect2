<?php
namespace Styla\Connect2\Block\Magazine;

class Content extends \Styla\Connect2\Block\Magazine
{
    /**
     * 
     * @return string
     */
    public function getNoscript()
    {
        return $this->getPage()
            ->getNoScript();
    }

    /**
     * 
     * @return string
     */
    public function getRootPath()
    {
        return $this->getConfigHelper()->getRouteName();
    }

    /**
     * 
     * @return string
     */
    public function getPluginVersion()
    {
        return $this->getConfigHelper()->getPluginVersion();
    }
}