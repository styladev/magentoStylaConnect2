<?php
namespace Styla\Connect2\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Styla\Connect2\Api\Data\StylaProductSearchResultsInterface;

/**
 * Styla Product Api interface.
 * @api
 */
interface ProductRepositoryInterface
{
    /**
     * Get one product by it's id
     *
     * @param int $productId
     * @return StylaProductSearchResultsInterface
     */
    public function getOne($productId);
    
    /**
     * Get product list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return StylaProductSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);
}
