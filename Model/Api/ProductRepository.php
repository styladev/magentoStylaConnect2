<?php
namespace Styla\Connect2\Model\Api;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository
{
    protected $stylaSearchResultsFactory;

    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper, \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository, \Magento\Catalog\Model\ResourceModel\Product $resourceModel, \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer, \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Api\FilterBuilder $filterBuilder, \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface, \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter, \Magento\Catalog\Model\Product\Option\Converter $optionConverter, \Magento\Framework\Filesystem $fileSystem, \Magento\Framework\Api\ImageContentValidatorInterface $contentValidator, \Magento\Framework\Api\Data\ImageContentInterfaceFactory $contentFactory, \Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap $mimeTypeExtensionMap, \Magento\Framework\Api\ImageProcessorInterface $imageProcessor, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
            \Styla\Connect2\Api\Data\StylaSearchResultsInterfaceFactory $stylaSearchResultsFactory
    ) {
        $this->stylaSearchResultsFactory = $stylaSearchResultsFactory;
        
        return parent::__construct($productFactory, $initializationHelper, $searchResultsFactory, $collectionFactory, $searchCriteriaBuilder, $attributeRepository, $resourceModel, $linkInitializer, $linkTypeProvider, $storeManager, $filterBuilder, $metadataServiceInterface, $extensibleDataObjectConverter, $optionConverter, $fileSystem, $contentValidator, $contentFactory, $mimeTypeExtensionMap, $imageProcessor, $extensionAttributesJoinProcessor);
    }
    
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        if($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create()
                    ->setPageSize(4)
                    ->setCurrentPage(0);
        }
        
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);

        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        
        //let's do some good old joins, to get all the data i need in this one
        $select = $collection->getSelect();
        $select->joinLeft(['galval' => 'catalog_product_entity_media_gallery_value'], 'galval.entity_id = e.entity_id', [
            //new \Zend_Db_Expr('GROUP_CONCAT(galval.record_id SEPARATOR "|") as galvalue'),
            'galval.label as caption'
        ]);
        
        $select->joinLeft(['gal' => 'catalog_product_entity_media_gallery'], 'galval.value_id = gal.value_id', [
            new \Zend_Db_Expr('GROUP_CONCAT(gal.value SEPARATOR "|") as galvalue')
        ]);
        
        $select->group('e.entity_id');

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
            
        $collection->load();
        
        //tetest
        foreach($collection as $item) {
            $this->_doConvert($item);
        }
        
        $searchResult = $this->stylaSearchResultsFactory->create();
        
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
    
    protected function _doConvert($item)
    {
        $item->setData('shop_id', $item->getId());
        $item->setData('image_small', $item->getSmallImage());
        $item->setData('page_url', $item->getProductUrl());
        $item->setData('images', explode("|", $item->getGalvalue()));
        $item->setData('shop', (bool)$item->getIsSalable());
    }
    
    public function test()
    {
        return new \Styla\Connect2\Model\Api\StylaProduct();
    }
}
