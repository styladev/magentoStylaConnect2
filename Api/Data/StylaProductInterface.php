<?php
namespace Styla\Connect2\Api\Data;

/**
 * Styla Product interface.
 * @api
 */
interface StylaProductInterface
{
    /**
     * @return string
     */
    public function getShopId();
    
    /**
     * @return string
     */
    public function getImage();
    
    /**
     * @return string
     */
    public function getCaption();
    
    /**
     * @return string
     */
    public function getImageSmall();
    
    /**
     * @return string[]
     */
    public function getImages();
    
    /**
     * @return string
     */
    public function getPageUrl();
    
    /**
     * Is saleable?
     * 
     * @return boolean
     */
    public function getShop();
}

