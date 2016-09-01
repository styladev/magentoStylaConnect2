<?php
namespace Styla\Connect2\Block\Magazine;

/**
 * Class Styla_Connect_Block_Magazine_Head
 */
class Head
    extends \Styla\Connect2\Block\Magazine
{
    public function getMetaTags()
    {
        return $this->getPage()
            ->getAdditionalMetaTags();
    }
}