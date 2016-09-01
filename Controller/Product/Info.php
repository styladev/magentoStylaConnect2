<?php
namespace Styla\Connect2\Controller\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Info extends \Magento\Framework\App\Action\Action
{
    protected $productRepository;
    protected $productInfo;
    protected $resultJsonFactory;
    
    public function __construct(\Magento\Framework\App\Action\Context $context,
        ProductRepositoryInterface $productRepository,
        \Styla\Connect2\Model\Product\Info $productInfo,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productInfo = $productInfo;
        $this->resultJsonFactory = $resultJsonFactory;
        
        parent::__construct($context);
    }
    
    public function execute()
    {
        $errors = [];
        $result = null;
        
        try {
            $product = $this->_initProduct();
            
            $this->productInfo->setProduct($product);
            
            $result = $this->productInfo->getInfo();
        } catch(\Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        //return the json...
        $jsonResult = $this->resultJsonFactory->create();
        if(!empty($errors)) {
            $jsonResult->setData(['errors' => $errors]);
            $jsonResult->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_NOT_FOUND);
        } else {
            $jsonResult->setData($result);
        }
        
        return $jsonResult;
    }
    
    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
        try {
            $product = false;
            
            if($sku = $this->getRequest()->getParam('sku')) {
                $product = $this->productRepository->getBySku($sku, false, $storeId);
            } elseif($id = (int)$this->getRequest()->getParam('product', $this->getRequest()->getParam('id', false))) {
                $product = $this->productRepository->getById($id, false, $storeId);
            }
            
            if(!$product->getId()) {
                throw new \Exception('Invalid product.');
            }
            
            if(!$product->getIsSalable()) {
                throw new \Exception('Product is unavailable.');
            }
            
            return $product;
        } catch (NoSuchEntityException $e) {
            throw new \Exception('Invalid product.');
        }
        
        throw new \Exception('Invalid product.');
    }
}
