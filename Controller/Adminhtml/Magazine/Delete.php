<?php
/**
 * @package Styla/Connect2
 * @author Oskar Wolanin <owolanin@divante.co>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Styla\Connect2\Controller\Adminhtml\Magazine;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Styla\Connect2\Model\MagazineFactory;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Registry;
use \Magento\Framework\App\Cache\Manager;

class Delete extends Action
{
    /**
     * @var bool|PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var MagazineFactory
     */
    protected $magazineFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Manager
     */
    protected $cacheManager;

    /**
     * Add constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param MagazineFactory $magazineFactory
     * @param Registry $coreRegistry
     * @param Manager $cacheManager
     *
     * @return void
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        MagazineFactory $magazineFactory,
        Registry $coreRegistry,
        Manager $cacheManager
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->magazineFactory = $magazineFactory;
        $this->messageManager = $context->getMessageManager();
        $this->resultFactory = $context->getResultFactory();
        $this->coreRegistry = $coreRegistry;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $magazine = $this->magazineFactory->create();
            $magazine->load($id);
            $magazine->delete();
            $resultRedirect = $this->resultRedirectFactory->create();

            $this->cacheManager->clean(['layout', 'block_html', 'full_page']);

            return $resultRedirect->setPath('*/*/index');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }

    /**
     * @param $magazineData
     *
     * @return array
     */
    private function filterSaveData(&$magazineData)
    {
        if (isset($magazineData['form_key'])) {
            unset($magazineData['form_key']);
        }

        if (isset($magazineData['id_field_name'])) {
            unset($magazineData['id_field_name']);
        }

        $magazineData['front_name'] = preg_replace('!\s+!', ' ', $magazineData['front_name']);
        $magazineData['front_name'] = str_replace(' ', '-', $magazineData['front_name']);

        return $magazineData;
    }
}