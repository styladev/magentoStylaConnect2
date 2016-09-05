<?php
namespace Styla\Connect2\Controller\Adminhtml\Connector;
use Magento\Framework\App\Action\Context;

class Save extends \Magento\Backend\App\Action
{
    protected $connector;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Context $context,
        \Styla\Connect2\Model\Styla\Api\Connector $connector
    ) {
        $this->connector = $connector;
        
        return parent::__construct($context);
    }
    
    public function execute() {
        $data = $this->getRequest()->getPostValue();
        
        if ($data) {
            try {
                $this->connector->connect($data);
                
                $this->messageManager->addSuccess('Successfully connected to Styla. You can now use your magazine.');
            } catch(\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        
        //redirect back to the connect page
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('styla_connect2/connector/connect');
        
        return $resultRedirect;
    }
}
