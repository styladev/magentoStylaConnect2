<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Styla\Connect2\Block\Adminhtml\Connector\Config;

use \Magento\Framework\Data\Form\Element\AbstractElement as AbstractElement;

/**
 * Renderer for PayPal banner in System Configuration
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Hint extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'Styla_Connect2::connector/hint.phtml';

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['help_link'])) {
            $this->setHelpLink($elementOriginalData['help_link']);
        }
        return $this->toHtml();
    }
}

