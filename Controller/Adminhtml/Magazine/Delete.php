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
use \Styla\Connect2\Model\MagazineFactory;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Cache\Manager;

class Delete extends Action
{
    /**
     * @var MagazineFactory
     */
    protected $magazineFactory;

    /**
     * @var Manager
     */
    protected $cacheManager;

    /**
     * Add constructor.
     *
     * @param Context $context
     * @param MagazineFactory $magazineFactory
     * @param Manager $cacheManager
     *
     * @return void
     */
    public function __construct(
        Context $context,
        MagazineFactory $magazineFactory,
        Manager $cacheManager
    )
    {
        parent::__construct($context);
        $this->magazineFactory = $magazineFactory;
        $this->resultFactory = $context->getResultFactory();
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
}