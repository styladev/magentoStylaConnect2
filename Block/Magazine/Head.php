<?php
namespace Styla\Connect2\Block\Magazine;

class Head
    extends \Styla\Connect2\Block\Magazine
{
    /**
     * 
     * @return []
     */
    public function getMetaTags()
    {
        return $this->getPage()
            ->getAdditionalMetaTags();
    }
}