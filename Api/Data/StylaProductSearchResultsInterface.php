<?php
namespace Styla\Connect2\Api\Data;

/**
 * Styla Product Search Results interface.
 * @api
 */
interface StylaProductSearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Styla\Connect2\Api\Data\StylaProductInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Styla\Connect2\Api\Data\StylaProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
