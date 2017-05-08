<?php
namespace Styla\Connect2\Model\Api;

use Magento\Framework\Api\SortOrder;
use Styla\Connect2\Model\Api\Converter as DataConverter;
use Magento\Framework\Api\SearchCriteriaInterface as SearchCriteria;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Api\Search\SearchCriteriaFactory as FullTextSearchCriteriaFactory;
use Magento\Framework\Api\Search\SearchInterface as FullTextSearchApi;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Webapi\Rest\Response as RestResponse;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository
    implements \Styla\Connect2\Api\ProductRepositoryInterface
{
    const SEARCH_FILTER_QUERY = 'query';
    
    const DEFAULT_PAGE_SIZE = 46; //if no limit provided, this will be used
    
    const EVENT_GET_PRODUCTS = 'styla_get_product_collection';
    
    /**
     *
     * @var RestResponse
     */
    protected $response;

    /**
     *
     * @var \Styla\Connect2\Api\Data\StylaProductSearchResultsInterfaceFactory
     */
    protected $stylaSearchResultsFactory;

    /**
     *
     * @var \Styla\Connect2\Model\Api\Converter\ConverterFactory
     */
    protected $converterFactory;

    /**
     *
     * @var \Styla\Connect2\Model\Api\Converter\ConverterChain
     */
    protected $converters; //data converter chain

    /**
     *
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    
    /** 
     * 
     * @var FullTextSearchCriteriaFactory
     */
    protected $fullTextSearchCriteriaFactory;
    
    /** 
     * 
     * @var FullTextSearchApi
     */
    protected $fullTextSearchApi;
    
    /**
     *
     * @var Magento\Search\Model\QueryFactory
     */
    protected $queryFactory;
    
    /**
     *  @var Request
     */
    protected $request;
    
    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;
    
    /**
     *
     * @var EventManager
     */
    protected $eventManager;
    
    /**
     *
     * @var \Styla\Connect2\Helper\Converter
     */
    protected $converterHelper;

    /**
     *
     * @param \Magento\Catalog\Model\ProductFactory                               $productFactory
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper
     * @param \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory      $searchResultsFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory      $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                        $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface            $attributeRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product                        $resourceModel
     * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks   $linkInitializer
     * @param \Magento\Catalog\Model\Product\LinkTypeProvider                     $linkTypeProvider
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager
     * @param \Magento\Framework\Api\FilterBuilder                                $filterBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface            $metadataServiceInterface
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter                $extensibleDataObjectConverter
     * @param \Magento\Catalog\Model\Product\Option\Converter                     $optionConverter
     * @param \Magento\Framework\Filesystem                                       $fileSystem
     * @param \Magento\Framework\Api\ImageContentValidatorInterface               $contentValidator
     * @param \Magento\Framework\Api\Data\ImageContentInterfaceFactory            $contentFactory
     * @param \Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap         $mimeTypeExtensionMap
     * @param \Magento\Framework\Api\ImageProcessorInterface                      $imageProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface    $extensionAttributesJoinProcessor
     * @param \Styla\Connect2\Api\Data\StylaProductSearchResultsInterfaceFactory  $stylaSearchResultsFactory
     * @param \Styla\Connect2\Model\Api\Converter\ConverterFactory                $converterFactory
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder                    $filterGroupBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Catalog\Model\Product\Option\Converter $optionConverter,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Api\ImageContentValidatorInterface $contentValidator,
        \Magento\Framework\Api\Data\ImageContentInterfaceFactory $contentFactory,
        \Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap $mimeTypeExtensionMap,
        \Magento\Framework\Api\ImageProcessorInterface $imageProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Styla\Connect2\Api\Data\StylaProductSearchResultsInterfaceFactory $stylaSearchResultsFactory,
        \Styla\Connect2\Model\Api\Converter\ConverterFactory $converterFactory,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        FullTextSearchCriteriaFactory $searchCriteriaFactory,
        FullTextSearchApi $search,
        \Magento\Search\Model\QueryFactory $queryFactory,
        Request $request,
        SortOrderBuilder $sortOrderBuilder,
        EventManager $eventManager,
        RestResponse $response,
        \Styla\Connect2\Helper\Converter $converterHelper
    )
    {
        $this->stylaSearchResultsFactory       = $stylaSearchResultsFactory;
        $this->converterFactory                = $converterFactory;
        $this->filterGroupBuilder              = $filterGroupBuilder;
        $this->categoryFactory                 = $categoryFactory;
        $this->fullTextSearchApi               = $search;
        $this->fullTextSearchCriteriaFactory   = $searchCriteriaFactory;
        $this->queryFactory                    = $queryFactory;
        $this->request                         = $request;
        $this->response                        = $response;
        $this->sortOrderBuilder                = $sortOrderBuilder;
        $this->eventManager                    = $eventManager;
        $this->converterHelper                 = $converterHelper;

        return parent::__construct($productFactory, $initializationHelper, $searchResultsFactory, $collectionFactory, $searchCriteriaBuilder, $attributeRepository, $resourceModel, $linkInitializer, $linkTypeProvider, $storeManager, $filterBuilder, $metadataServiceInterface, $extensibleDataObjectConverter, $optionConverter, $fileSystem, $contentValidator, $contentFactory, $mimeTypeExtensionMap, $imageProcessor, $extensionAttributesJoinProcessor);
    }

    /**
     * If no search criteria is provided in the request, use this as default
     *
     * @return SearchCriteria
     */
    protected function _getDefaultSearchCriteria()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create()
            ->setPageSize(self::DEFAULT_PAGE_SIZE)
            ->setCurrentPage(0);
        
        $this->_setDefaultSortOrder($searchCriteria);
        return $searchCriteria;
    }
    
    /**
     * If no other sort order is already applied on the criteria,
     * add the default one (entity_id incremental)
     * 
     * @param SearchCriteria $searchCriteria
     */
    protected function _setDefaultSortOrder(SearchCriteria $searchCriteria)
    {
        //if there's no other paging defined in the criteria, we'll apply the default page size
        if(null === $searchCriteria->getPageSize()) {
            $searchCriteria->setPageSize(self::DEFAULT_PAGE_SIZE);
        }
        
        if($searchCriteria->getSortOrders()) {
            return;
        }
        
        $entityIdField = $this->converterHelper->getProductEntityIdField();
        
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder->create();
        $sortOrder->setField($entityIdField)->setDirection('asc');
        $searchCriteria->setSortOrders([$sortOrder]);
    }
    
    /**
     * Perform full text search and find IDs of matching products.
     *
     * @return int[]
     */
    protected function _searchProductsFullText()
    {
        //the query factory will get the term from the "q" url param
        $query = $this->queryFactory->get();
        $term = $query->getQueryText();
        
        $fulltextSearchCriteria = $this->fullTextSearchCriteriaFactory->create();
        $fulltextSearchCriteria->setRequestName('quick_search_container');
        
        $filter = $this->filterBuilder->setField('search_term')->setValue($term)->setConditionType('like')->create();
        $filterGroup = $this->filterGroupBuilder->addFilter($filter)->create();
        $fulltextSearchCriteria->setFilterGroups([$filterGroup]);
        
        //this returns all matching ids, in the score descending order. this result can't be paged.
        $searchResults = $this->fullTextSearchApi->search($fulltextSearchCriteria);
        $productIds = [];
        foreach ($searchResults->getItems() as $searchDocument) {
            $productIds[] = $searchDocument->getId();
        }
        
        return $productIds;
    }
    
    /**
     * Search for the fulltext search term, change the filter into a product_ids list
     * 
     * @param \Magento\Framework\Api\Filter $filter
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _applyFulltextSearch(\Magento\Framework\Api\Filter $filter)
    {
        $queryValue = $filter->getValue();
        if(!$queryValue) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }
        
        //a little hack. since magento will expect the query to be a get param of the request, we'll copy it there
        $this->request->setParam('q', $queryValue);
        
        $productIds = $this->_searchProductsFullText();
        if(!$productIds) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }
        
        //we no longer need our original query filter, we'll now turn it into the actual found product_ids filter, instead
        $entityIdField = $this->converterHelper->getProductEntityIdField();
        $filter->setConditionType('in')->setField($entityIdField)->setValue($productIds);
    }
    
    /**
     * The user may be searching for a specific text search term, as one of his searchCriterias, as in:
     * V1/styla_product?searchCriteria[filter_groups][0][filters][0][field]=query&searchCriteria[filter_groups][0][filters][0][value]=THESEARCHTERM
     * 
     * We will process this here, and turn this request into a list of product_ids matching his search term.
     * 
     * @param SearchCriteria $searchCriteria
     * @return SearchCriteria
     */
    protected function _processTextSearchCriteria(SearchCriteria $searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == self::SEARCH_FILTER_QUERY) {
                    $this->_applyFulltextSearch($filter);
                }
            }
        }
        
        return $searchCriteria;
    }

    /**
     *
     * @param int $productId
     * @return \Styla\Connect2\Api\Data\StylaProductSearchResultsInterface
     */
    public function getOne($productId)
    {
        //we'll be loading the normal collection, but with a singled-out entity_id of the product
        //as i need to run the same data converters on the result, as i would have on the product list
        $entityIdField = $this->converterHelper->getProductEntityIdField();
        $idFilter = $this->filterBuilder->setField($entityIdField)
            ->setValue($productId)
            ->setConditionType('eq')
            ->create();

        $filterGroup = $this->filterGroupBuilder->addFilter($idFilter)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder->create()
            ->setFilterGroups([$filterGroup]);

        $searchResult = $this->getList($searchCriteria);
        return $searchResult;
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return SearchCriteria
     */
    protected function _processSearchCriteria($searchCriteria = null)
    {
        //if no search criteria provided, apply default
        if ($searchCriteria === null) {
            $searchCriteria = $this->_getDefaultSearchCriteria();
            return $searchCriteria;
        }
        
        //if there's no other sort order applied, use the default one
        $this->_setDefaultSortOrder($searchCriteria);

        //there's one specific thing to do for category id search criteria.
        //that is, if a product is assigned to any category, it should also show up
        //when we search in any of this category's parent.
        foreach ($searchCriteria->getFilterGroups() as $group) {
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == 'category_id') {
                    $this->_addAllRelatedCategoriesCriteria($filter);
                }
            }
        }

        return $searchCriteria;
    }

    /**
     * When we search for a specific category by it's id, we should also add all this category's
     * children to the filter (the way magento anchor categories are loaded)
     *
     * @param \Magento\Framework\Api\Filter $filter
     */
    protected function _addAllRelatedCategoriesCriteria($filter)
    {
        //i'm only applying this if we're searching for one specific category, as in 'category_id EQ SOME_ID'
        $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
        if ($conditionType != 'eq') {
            return;
        }

        $categoryId = $filter->getValue();
        $category   = $this->categoryFactory->create()->load($categoryId);
        if (!$category->getId()) {
            return;
        }

        $allRelatedCategories              = [];
        $allRelatedCategories[$categoryId] = $categoryId;

        //we unfortunately also want all the children of this category, so more loading
        $childrenIds          = $category->getChildren($category, true);
        $allRelatedCategories = array_merge($allRelatedCategories, explode(',', $childrenIds));

        //now instead of one category, we get all it's children as well
        $filter->setValue($allRelatedCategories);
        $filter->setConditionType('in');
    }

    /**
     *
     * @param SearchCriteria $searchCriteria
     * @return \Styla\Connect2\Api\Data\StylaProductSearchResultsInterface
     */
    public function getList(SearchCriteria $searchCriteria = null)
    {
        //apply the default search criteria if none is defined, pre-process the filter values if needed
        $searchCriteria = $this->_processSearchCriteria($searchCriteria);
        
        //there may be a search query as one of the criterias. if it's there, apply the text search, first
        $searchCriteria = $this->_processTextSearchCriteria($searchCriteria);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);

        //load all the product attributes
        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        $entityIdField = $this->converterHelper->getProductEntityIdField();
        $collection->joinAttribute('status', 'catalog_product/status', $entityIdField, null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', $entityIdField, null, 'inner');

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
        
        //we should add a store filter to all the requests:
        $store = $this->storeManager->getStore();
        $collection->setStore($store);
        $collection->setVisibility(array( //visibility must be set before the store filter. otherwise it won't be processed at all
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
        ));
        $collection->addStoreFilter($store);
        
        //as our next step (loading and joinin additional data) will mess up magento's collection count,
        //for easiness of implementation i'll be checking the page size and totals now:
        $this->_setPagingHeaders($collection);

        //the data required by styla is different than what our collection returns,
        //so we run "converters" on the result. the converters may need additional joins on the collection, to work
        $store = $this->storeManager->getStore();
        $this->_addConverterRequirementsToCollection($collection, $store);

        $collection->load();

        //convert the data to what we need for styla
        $this->_doConvert($collection);

        $searchResult = $this->stylaSearchResultsFactory->create();

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        
        //dispatch the event
        $this->eventManager->dispatch(self::EVENT_GET_PRODUCTS, ['collection' => $collection, 'searchCriteria' => $searchCriteria]);
        
        if(!count($searchResult->getItems())) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }

        return $searchResult;
    }
    
    /**
     * Set the paging response headers
     * 
     * @param ProductCollection $collection
     */
    protected function _setPagingHeaders(ProductCollection $collection)
    {
        $totalCount = $collection->getSize();
        $pageSize = $collection->getPageSize();
        $currentPage = $collection->getCurPage();
        
        $totalPageCount = $pageSize ? ceil($totalCount / $pageSize) : 1;
        
        if ($currentPage === null) {
            $currentPage = 1;
        }
        
        //add the calculated totals to the final rest response
        $this->response->setHeader('X-Total-Count', $totalCount);
        $this->response->setHeader('X-Total-Pages', $totalPageCount);
        $this->response->setHeader('X-Current-Page', $currentPage);
    }

    /**
     * Add styla data converters requirements to the product collection.
     *
     * @param ProductCollection $collection
     */
    protected function _addConverterRequirementsToCollection(ProductCollection $collection, $store)
    {
        $converterChain = $this->getConverters();

        $converterChain->addCollectionRequirements($collection, $store);
    }

    /**
     * Get all the styla data converters defined for this store
     *
     * @return \Styla\Connect2\Model\Api\Converter\ConverterChain
     */
    public function getConverters()
    {
        if (null === $this->converters) {
            $this->converters = $this->converterFactory->createConverterChain(DataConverter\ConverterFactory::TYPE_PRODUCT);
        }

        return $this->converters;
    }

    /**
     * Do the data conversion to a format accepted by styla
     *
     * @param ProductCollection $collection
     */
    protected function _doConvert(ProductCollection $collection)
    {
        $this->getConverters()->doConversion($collection);
    }
}
