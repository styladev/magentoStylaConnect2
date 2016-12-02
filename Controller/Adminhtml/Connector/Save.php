<?php
namespace Styla\Connect2\Controller\Adminhtml\Connector;

use Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use Styla\Connect2\Model\Styla\Api\Connector;

class Save extends Action
{
    /**
     *
     * @var Connector
     */
    protected $connector;

    /**
     * @param Action\Context|Context $context
     * @param Connector              $connector
     */
    public function __construct(Context $context, Connector $connector)
    {
        $this->connector = $connector;

        return parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                $this->connector->connect($data);

                $this->messageManager->addSuccessMessage('Successfully connected to Styla. You can now use your magazine.');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        //redirect back to the styla settings page
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('admin/system_config/edit', ['section' => 'styla_connect2']);

        return $resultRedirect;
    }
}
