<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Styla\Connect2\Api;

/**
 * Customer CRUD interface.
 * @api
 */
interface ProductRepositoryInterface
{
    /**
     * Get customer by customer ID.
     *
     * @param int $productId
     * @return \Styla\Connect2\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If product with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($productId);
    
    /**
     * Get product list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Styla\Connect2\Api\Data\StylaSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);
    
    /**
     * test
     * 
     * @return \Styla\Connect2\Api\Data\StylaProductInterface
     */
    public function test();
}
