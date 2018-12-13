<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\ResourceModel\Db\Context;
use \Magento\Store\Model\StoreManagerInterface;
use Styla\Connect2\Model\Collection;
use \Styla\Connect2\Model\ResourceModel\Magazine\CollectionFactory;

class Magazine extends AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
protected $collectionFactory;
    /**
     * Magazine constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     *
     * @return void
     */
    public function __construct(Context $context, StoreManagerInterface $storeManager, CollectionFactory $collectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('styla_magazine', 'id');
    }

    /**
     * @param string $field
     * @param int|string $value
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    /*protected function _getLoadSelect($field, $value, $object)
    {
        $fieldName = $field;

        /*$filter = $this->objectManager->create('Magento\Framework\Api\Filter');
        $filter->setData('field',$field);
        $filter->setData('value',$value);
        $filter->setData('condition_type','eq');

        //add our filter(s) to a group
        $filter_group = $this->objectManager->create('Magento\Framework\Api\Search\FilterGroup');
        $filter_group->setData('filter', $filter);

        //add the group(s) to the search criteria object
        $search_criteria = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaInterface');
        $search_criteria->setFilterGroups([$filter_group]);

        //query the repository for the object(s)
        $repo = $this->objectManager->get('Styla\Connect2\Model\Api\ProductRepository');
        $result = $repo->getList($search_criteria);
        $products = $result->getItems();




        $field     = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($field . '=?', $value)
            ->limit(1);

        if ($fieldName === 'front_name') {
            //select the magazine with the right front name and store
            $select
                ->where('store_id = ? OR is_default = 1', $this->storeManager->getStore()->getId())
                ->order('is_default ASC');
        }

        return $select;
    }*/
}