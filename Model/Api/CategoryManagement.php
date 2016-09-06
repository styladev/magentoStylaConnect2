<?php
namespace Styla\Connect2\Model\Api;

class CategoryManagement extends \Magento\Catalog\Model\CategoryManagement
{
    /**
     *
     * @var \Styla\Connect2\Model\Api\Category\Tree
     */
    protected $stylaCategoryTree;

    /**
     *
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     *
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface                $categoryRepository
     * @param \Magento\Catalog\Model\Category\Tree                            $categoryTree
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesFactory
     * @param \Styla\Connect2\Model\Api\Category\Tree                         $stylaCategoryTree
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Category\Tree $categoryTree,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesFactory,
        \Styla\Connect2\Model\Api\Category\Tree $stylaCategoryTree
    )
    {
        //we need our own, extended tree structure, because the default magento one doesn't support any additional
        //attributes (and we need images, for example)
        $this->stylaCategoryTree = $stylaCategoryTree;

        return parent::__construct($categoryRepository, $categoryTree, $categoriesFactory);
    }

    /**
     *
     * @param int $rootCategoryId
     * @param int $depth
     * @return \Styla\Connect2\Model\Api\Category\Tree
     */
    public function getTree($rootCategoryId = null, $depth = null)
    {
        $category = null;
        if ($rootCategoryId !== null) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryRepository->get($rootCategoryId);
        } elseif ($this->isAdminStore()) {
            $category = $this->getTopLevelCategory();
        }
        $result = $this->stylaCategoryTree->getTree($this->stylaCategoryTree->getRootNode($category), $depth);

        return $result;
    }

    /**
     * Get top level hidden root category
     *
     * @return \Magento\Catalog\Model\Category
     */
    private function getTopLevelCategory()
    {
        $categoriesCollection = $this->categoriesFactory->create();
        return $categoriesCollection->addFilter('level', ['eq' => 0])->getFirstItem();
    }

    /**
     * Check is request use default scope
     *
     * @return bool
     */
    private function isAdminStore()
    {
        return $this->getScopeResolver()->getScope()->getCode() == \Magento\Store\Model\Store::ADMIN_CODE;
    }

    /**
     * Get store manager for operations with admin code
     *
     * @return \Magento\Framework\App\ScopeResolverInterface
     */
    private function getScopeResolver()
    {
        if ($this->scopeResolver == null) {
            $this->scopeResolver = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\App\ScopeResolverInterface::class);
        }

        return $this->scopeResolver;
    }
}
