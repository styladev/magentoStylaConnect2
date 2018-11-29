<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Model;

class Magazine extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'styla_connect2_magazine';

    protected $_cacheTag = 'styla_connect2_magazine';

    protected $_eventPrefix = 'styla_magazine';

    protected $_eventObject = 'magazine';

    protected function _construct()
    {
        $this->_init('Styla\Connect2\Model\ResourceModel\Magazine');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function loadDefault()
    {
        $this->_getResource()->load(1, 'is_default');

        return $this;
    }

    public function loadByFrontName($frontName)
    {
        $this->load($frontName, 'front_name');

        return $this;
    }

    public function isActive()
    {
        return (int)$this->getData('is_active') === 1;
    }

    public function isDefault()
    {
        return (int)$this->getData('is_default') === 1;
    }

    public function useMagentoLayout()
    {
        return (int)$this->getData('use_magento_layout') === 1;
    }

    public function includeInNavigation()
    {
        return (int)$this->getData('include_in_navigation') === 1;
    }
}
