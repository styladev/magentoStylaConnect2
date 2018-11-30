<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Model;

use \Magento\Framework\Exception\LocalizedException;

interface MagazineInterface
{
    /**
     * @var int
     */
    public const ACTIVE = 1;

    /**
     * @var string
     */
    public const IS_DEFAULT = 'is_default';

    /**
     * @var string
     */
    public const FRONT_NAME = 'front_name';

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function loadDefault();

    /**
     * @param string $frontName
     *
     * @return $this
     */
    public function loadByFrontName($frontName);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return bool
     */
    public function isDefault();

    /**
     * @return bool
     */
    public function useMagentoLayout();

    /**
     * @return bool
     */
    public function includeInNavigation();

}