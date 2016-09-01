<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Styla\Connect2\Api\Data;

/**
 * Customer interface.
 * @api
 */
interface StylaCategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Styla\Connect2\Api\Data\StylaCategoryInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Styla\Connect2\Api\Data\StylaCategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
