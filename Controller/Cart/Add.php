<?php
namespace Styla\Connect2\Controller\Cart;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    protected $resultJsonFactory;
    
    protected $cartHelper;
    
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Checkout\Model\Cart $cart, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cartHelper = $cartHelper;
        
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $productRepository);
    }
    
    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
//        if (!$this->_formKeyValidator->validate($this->getRequest())) {
//            return $this->resultRedirectFactory->create()->setPath('*/*/');
//        }
        
//        $this->_renderHtml();die();

        $params = $this->getRequest()->getParams();
        
        //try loading the blocks
//        $page = $this->resultPageFactory->create();
//        $layout = $page->getLayout();
//        $block = $layout->getBlock('stylaconnect2.cart_content');
//        
//        var_dump($layout->renderElement('stylaconnect2.cart_content'));
        
        $success = false;
        $errors = [];
        
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                throw new \Exception('Invalid product.');
            }

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->cart->getQuote()->getHasError()) {
                $success = true;
            }
            
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $errors = array_unique(explode("\n", $e->getMessage()));

        } catch (\Exception $e) {
            $errors[] = __('We can\'t add this item to your shopping cart right now.');
        }
        
        $jsonResult = $this->resultJsonFactory->create();
        if($success) {
            
            $jsonResult->setData([
                'success' => 1,
                'html' => $this->_renderHtml()
            ]);
        } else {
            //nothing to render (error result), so we'll just return a json error response
            $jsonResult->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_NOT_FOUND);
            $jsonResult->setData($errors);
        }
        
        return $jsonResult;
    }
    
    protected function _renderHtml()
    {
        $page = $this->resultPageFactory->create();
        
        $layout = $page->getLayout();
        
        //render the contents of our car_content container
        return $layout->renderElement('stylaconnect2.cart_content');
    }
}
