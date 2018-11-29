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
    /**
     * @var string
     */
    const CACHE_TAG = 'styla_connect2_magazine';

    /**
     * @var string
     */
    protected $_cacheTag = 'styla_connect2_magazine';

    /**
     * @var string
     */
    protected $_eventPrefix = 'styla_magazine';

    /**
     * @var string
     */
    protected $_eventObject = 'magazine';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Styla\Connect2\Model\ResourceModel\Magazine');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDefault()
    {
        $this->_getResource()->load(1, 'is_default');

        return $this;
    }

    /**
     * @param string $frontName
     *
     * @return $this
     */
    public function loadByFrontName($frontName)
    {
        $this->load($frontName, 'front_name');

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (int)$this->getData('is_active') === 1;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return (int)$this->getData('is_default') === 1;
    }

    /**
     * @return bool
     */
    public function useMagentoLayout()
    {
        return (int)$this->getData('use_magento_layout') === 1;
    }

    /**
     * @return bool
     */
    public function includeInNavigation()
    {
        return (int)$this->getData('include_in_navigation') === 1;
    }
}
