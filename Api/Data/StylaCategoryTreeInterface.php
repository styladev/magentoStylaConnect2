<?php
namespace Styla\Connect2\Api\Data;

/**
 * Styla Category interface.
 * @api
 */
interface StylaCategoryTreeInterface
{
    /**
     * @return string
     */
    public function getId();
    
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return string
     */
    public function getImage();
    
    /**
     * @return \Styla\Connect2\Api\Data\StylaCategoryTreeInterface[]
     */
    public function getChildren();
}

