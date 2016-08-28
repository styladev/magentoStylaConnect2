<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Styla\Connect2\Api\Data;

/**
 * Customer interface.
 * @api
 */
interface ProductInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();
}
