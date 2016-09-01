<?php
namespace Styla\Connect2\Model\Api;

class StylaCategory extends \Magento\Catalog\Model\Category implements \Styla\Connect2\Api\Data\StylaCategoryTreeInterface
{
    public function getChildren() {
        /**
         * TODO: todo and a warning: this method is needed by the styla api to be used for getting the children recursively,
         * but in this case i'm actually hiding a parent method, that normally does something completely else.
         * Can we change the name for this field in styla api, so it doesn't have to do that?
         * 
         */
        
        return $this->getChildrenData();
    }
    
    /**
     * 
     * @return string|null
     */
    public function getImage()
    {
        return $this->getData('image');
    }
}