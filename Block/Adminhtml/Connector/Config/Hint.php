<?php
namespace Styla\Connect2\Block\Adminhtml\Connector\Config;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement as AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class Hint
 */
class Hint extends Template implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'Styla_Connect2::connector/hint.phtml';

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
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

