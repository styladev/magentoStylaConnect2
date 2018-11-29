<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Model\ResourceModel\Magazine;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'styla_magazine_collection';
    protected $_eventObject = 'magazine_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Styla\Connect2\Model\Magazine', 'Styla\Connect2\Model\ResourceModel\Magazine');
    }

    public function joinStoreCode()
    {
        $this->getSelect()->joinLeft(
            array('s' => $this->getTable('core/store')),
            'main_table.store_id = s.store_id',
            array('store_code' => 's.code')
        );
    }

    public function addTopNavigationFilter($storeId = null)
    {
        $this->getSelect()
            ->where('main_table.store_id = ? OR is_default = 1', $storeId)
            ->where('include_in_navigation = 1')
            ->where('is_active = 1');

        return $this;
    }

}