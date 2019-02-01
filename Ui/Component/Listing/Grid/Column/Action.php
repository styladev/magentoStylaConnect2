<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Action extends Column
{
    /** @var string */
    const ROW_EDIT_URL = 'styla_connect2/magazine/edit';

    /** @var string */
    const ROW_DELETE_URL = 'styla_connect2/magazine/delete';

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $_editUrl;

    /**
     * @var string
     */
    private $deleteUrl;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $editUrl
     * @param string             $deleteUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::ROW_EDIT_URL,
        $deleteUrl = self::ROW_DELETE_URL
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_editUrl = $editUrl;
        $this->deleteUrl = $deleteUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_editUrl,
                            ['id' => $item['id']]
                        ),
                        'label' => __('Edit'),
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->deleteUrl,
                            ['id' => $item['id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Confirm deletion'),
                            'message' => __('Are you sure you want to remove this entry? '.
                                'Once you do this, the Styla content will no longer be visible. '.
                                'This action cannot be reverted. '.
                                'You can simply deactivate the entry without removing it.'
                                )
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}