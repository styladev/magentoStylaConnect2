<?php
namespace Styla\Connect2\Api;

/**
 * Category interface.
 * @api
 */
interface CategoryManagementInterface
{
    /**
     * Retrieve list of categories
     *
     * @param int $rootCategoryId
     * @param int $depth
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Styla\Connect2\Api\Data\StylaCategoryTreeInterface containing Tree objects
     */
    public function getTree($rootCategoryId = null, $depth = null);
}
