<?php
namespace Styla\Connect2\Model\Styla\Api;

use Magento\Integration\Model\Oauth\ConsumerFactory;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Authorization\Model\UserContextInterface as UserContextInterface;

class Connector
{
    const ADMIN_USERNAME = 'StylaConnect2AdminUser';
    const ADMIN_EMAIL_PREPEND = "styla_connect2_";
    const CONSUMER_NAME = "Styla Api Consumer";
    
    protected $userFactory;
    protected $connectionData;
    protected $messageManager;
    protected $oauthService;
    protected $consumerFactory;
    protected $tokenFactory;
    protected $stylaApi;
    protected $configHelper;
    
    public function __construct(
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Integration\Model\OauthService $oauthService,
        ConsumerFactory $consumerFactory,
        TokenFactory $tokenFactory,
        \Styla\Connect2\Model\Styla\Api $stylaApi,
        \Styla\Connect2\Helper\Config $configHelper
    ) {
        $this->messageManager = $messageManager;
        $this->userFactory = $userFactory;
        $this->oauthService = $oauthService;
        $this->consumerFactory = $consumerFactory;
        $this->tokenFactory = $tokenFactory;
        $this->stylaApi = $stylaApi;
        $this->configHelper = $configHelper;
    }
    
    public function connect(array $postData)
    {
        $this->connectionData = $postData;
        
        //TODO:
        $connectionScope = null;
        //$connectionScope = $this->_getConnectionScope($connectionFormData, $scopeData);
        
        //i need an admin user
        $user = $this->getAdminUser();
        if($user === false) {
            throw new \Exception("Can't create the admin user.");
        }
        
        //i need an oauth consumer
        $consumer = $this->getConsumer();
        
        //i need to get or create and authorize a token for this consumer
        $token = $this->getToken($consumer, $user);
        
        //at this point, i should have all the stuff i need for making my connection
        $connectionData = $this->sendRegistrationRequest($this->getStylaLoginData(), $consumer, $token);
        
        $this->configHelper->updateConnectionConfiguration($connectionData, $connectionScope);
    }
    
    public function sendRegistrationRequest($loginData, $consumer, $token)
    {
        $apiRequest = $this->stylaApi->getRequest(\Styla\Connect2\Model\Styla\Api\Request\Type\Register::class);
        $apiRequest->setConnectionType(\Zend\Http\Request::METHOD_POST);
        $apiRequest->setParams([
            'styla_email'     => $loginData['email'],
            'styla_password'  => $loginData['password'],
            'consumer_key'    => $consumer->getKey(),
            'consumer_secret' => $consumer->getSecret(),
            'token_key'       => $token->getToken(),
            'token_secret'    => $token->getSecret(),
        ]);
        
        $apiResponse = $this->stylaApi->callService($apiRequest, false, true); //no cache, but use the http headers in results
        if (!$apiResponse->isOk()) {
            throw new \Exception(
                "Couldn't connect to Styla API. Error result: " . $apiResponse->getHttpStatus()
                . ($apiResponse->getError() ? ' - ' . $apiResponse->getError() : '')
            );
        }

        //setup the api urls for this client
        /** @var array $connectionData */
        $connectionData = $apiResponse->getResult();
        
        return $connectionData;
    }
    
    public function getStylaLoginData()
    {
        return isset($this->connectionData['styla']) ? $this->connectionData['styla'] : false;
    }
    
    public function getAdminUser()
    {
        $user = $this->userFactory->create();
        $userCollection = $user->getCollection()
                ->addFieldToFilter('username', self::ADMIN_USERNAME);
        
        $existingUser = $userCollection->getFirstItem();
        if($existingUser->getId()) {
            return $existingUser;
        }
        
        //no user to give, so we'll create a new one
        $stylaLoginData = $this->getStylaLoginData();
        if($stylaLoginData === false) {
            throw new Exception('Wrong data. Please go through the connection form again.');
        }
        
        $adminEmail = self::ADMIN_EMAIL_PREPEND . $stylaLoginData['email'];
        
        //create the new user here
        try {
            $user->setData(array(
                'username' => self::ADMIN_USERNAME,
                'firstname' => 'Styla',
                'lastname'  => 'Connect2',
                'email' => $adminEmail,
                'password'  => $this->_generateAdminPassword(),
            ))->save();
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            
            return false;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            
            return false;
        }
        
        return $user->getId() ? $user : false;
    }
    
    /**
     * Returns an alphanumeric string 9-char long
     * 
     * @return string
     */
    protected function _generateAdminPassword()
    {
        return substr(str_shuffle(strtolower(sha1(rand() . time() . "styla_connect2"))),0, 9);
    }
    
    public function getConsumer()
    {
        //try to find an already existing consumer
        $consumerCollection = $this->consumerFactory->create()->getCollection()
                ->addFieldToFilter('name', self::CONSUMER_NAME);
        
        $existingConsumer = $consumerCollection->getFirstItem();
        if($existingConsumer->getId()) {
            return $existingConsumer;
        }
        
        //we need to create a new consumer
        try {
            $consumer = $this->oauthService->createConsumer(['name' => self::CONSUMER_NAME]);
        } catch(\Magento\Framework\Oauth\Exception $e) {
            throw \Exception($e->getMessage());
        }
        
        return $consumer;
    }
    
    public function getToken(\Magento\Integration\Model\Oauth\Consumer $consumer, $adminUser)
    {
        //do we have a token already?
        $ourToken = $this->_getExistingToken($consumer);
        
        //if no token, i gotta create one
        //this will get me the token i need:
        if(!$ourToken && !$this->oauthService->createAccessToken($consumer->getId(), true)) {
            throw new \Exception('Failed creating an access token for this consumer.');
        }
        
        //the token needs to be set to our admin user, and validated now
        $ourToken = $this->_getExistingToken($consumer);
        if(!$ourToken) {
            throw new \Exception('Failed loading an access token.');
        }
        
        if(!$ourToken->getAuthorized()) {
            $ourToken->setUserType(UserContextInterface::USER_TYPE_ADMIN);
            $ourToken->setAdminId($adminUser->getId());
            $ourToken->setAuthorized(1);
            
            $ourToken->save();
        }
        
        return $ourToken;
    }
    
    protected function _getExistingToken($consumer)
    {
        if(!$consumer->getId()) {
            throw new \Exception("Invalid consumer provided.");
        }
        
        $token = $this->tokenFactory->create();
        
        $tokenCollection = $token->getCollection()
                ->addFieldToFilter('consumer_id', $consumer->getId());
        $existingToken = $tokenCollection->getFirstItem();
        if($existingToken && $existingToken->getId()) {
            return $existingToken;
        }
        
        return false;
    }
}
