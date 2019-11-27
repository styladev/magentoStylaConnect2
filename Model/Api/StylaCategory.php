<?php
namespace Styla\Connect2\Model\Api;

use Magento\Catalog\Model\Category;
use Styla\Connect2\Api\Data\StylaCategoryTreeInterface;

class StylaCategory extends Category implements StylaCategoryTreeInterface
{
    public function getChildren($recursive = false, $isActive = true, $sortByPosition = false)
    {
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

    public function getId()
    {
        // TODO: Implement getId() method.
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

}
