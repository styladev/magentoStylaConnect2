<?php
namespace Styla\Connect2\Block\Magazine;

use Styla\Connect2\Block\Magazine;

class Content extends Magazine
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
        return $this->getConfigHelper()->getRootPath();
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