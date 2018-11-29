<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Model\ResourceModel;


class Magazine extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('styla_magazine', 'id');
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $fieldName = $field;
        $field     = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($field . '=?', $value)
            ->limit(1);

        if ($fieldName === 'front_name') {
            //select the magazine with the right front name and store
            $select
                ->where('store_id = ? OR is_default = 1', Mage::app()->getStore()->getId())
                ->order('is_default ASC');
        }

        return $select;
    }
}