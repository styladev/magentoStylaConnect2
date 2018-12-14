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
use \Magento\Framework\Message\ManagerInterface;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Registry;

class Edit extends Action
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
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Add constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param MagazineFactory $magazineFactory
     *
     * @return void
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        MagazineFactory $magazineFactory,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Registry $coreRegistry
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->magazineFactory = $magazineFactory;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $magazineData = $this->getRequest()->getPostValue();
        if (!empty($magazineData)) {
            $magazineData = $this->filterSaveData($magazineData);
            $magazine = $this->magazineFactory->create();
            $magazine->setData($magazineData)->save();
            $resultRedirect = $this->resultRedirectFactory->create();

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

        return $magazineData;
    }
}