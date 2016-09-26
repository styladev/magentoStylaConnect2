<?php
namespace Styla\Connect2\Api;

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
     * @return \Styla\Connect2\Api\Data\StylaProductSearchResultsInterface
     */
    public function getOne($productId);
    
    /**
     * Get product list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Styla\Connect2\Api\Data\StylaProductSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);
    
    /**
     * Search products by fulltext terms
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Styla\Connect2\Api\Data\StylaProductSearchResultsInterface
     */
    public function search(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);
}
