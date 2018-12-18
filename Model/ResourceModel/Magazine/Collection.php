<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Model\ResourceModel\Magazine;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Styla\Connect2\Model\Magazine as MagazineModel;
use Styla\Connect2\Model\ResourceModel\Magazine as MagazineResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'styla_magazine_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'magazine_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(MagazineModel::class, MagazineResourceModel::class);
    }

    /**
     * todo: do skasowania?
     * @return void
     */
    public function joinStoreCode()
    {
        $this->getSelect()->joinLeft(
            ['s' => $this->getTable('core/store')],
            'main_table.store_id = s.store_id',
            ['store_code' => 's.code']
        );
    }

    /**
     * todo: do skasowania?
     * @param int $storeId
     *
     * @return $this
     */
    public function addTopNavigationFilter($storeId = null)
    {
        $this->getSelect()
            ->where('main_table.store_id = ? OR is_default = 1', $storeId)
            ->where('include_in_navigation = 1')
            ->where('is_active = 1');

        return $this;
    }
}