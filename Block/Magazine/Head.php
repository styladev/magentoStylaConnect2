<?php
namespace Styla\Connect2\Block\Magazine;

use Styla\Connect2\Block\Magazine;

class Head extends Magazine
{
    /**
     *
     * @return array
     */
    public function getMetaTags()
    {
        return $this->getPage()
            ->getAdditionalMetaTags();
    }
}