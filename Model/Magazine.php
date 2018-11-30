<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Model;

use \Magento\Framework\Exception\LocalizedException;
use \Styla\Connect2\Model\ResourceModel\Magazine as MagazineResourceModel;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;

class Magazine extends AbstractModel implements MagazineInterface
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
     * @var
     */
    protected $magazineResourceModel;

    /**
     * Magazine constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param MagazineResourceModel $magazine
     *
     * @return void
     */
    public function __construct(
        Context $context,
        Registry $registry,
        MagazineResourceModel $magazine
    ) {
        parent::__construct($context, $registry);
        $this->magazineResourceModel = $magazine;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(MagazineResourceModel::class);
    }

    /**
     * @return MagazineResourceModel
     */
    public function loadDefault()
    {
        $this->magazineResourceModel->load($this, MagazineInterface::ACTIVE, MagazineInterface::IS_DEFAULT);

        return $this->magazineResourceModel;
    }

    /**
     * @param string $frontName
     *
     * @return MagazineResourceModel
     */
    public function loadByFrontName($frontName)
    {
        $this->magazineResourceModel->load($this, $frontName, MagazineInterface::FRONT_NAME);

        return $this->magazineResourceModel;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getData('is_active');
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->getData('is_default');
    }

    /**
     * @return bool
     */
    public function useMagentoLayout()
    {
        return (bool) $this->getData('use_magento_layout');
    }

    /**
     * @return bool
     */
    public function includeInNavigation()
    {
        return (bool) $this->getData('include_in_navigation');
    }
}
