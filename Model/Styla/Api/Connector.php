<?php
namespace Styla\Connect2\Model\Styla\Api;

use Magento\Integration\Model\Integration;

use Magento\Integration\Model\Oauth\Consumer;
use Magento\Integration\Model\Oauth\ConsumerFactory;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\TokenFactory;

class Connector
{
    const STYLA_API_CONNECTOR_URL_PRODUCTION = 'http://live.styla.com/api/magento';

    const ADMIN_USERNAME      = 'StylaConnect2AdminUser';
    const ADMIN_EMAIL_PREPEND = 'styla_connect2_';
    const CONSUMER_NAME       = 'Styla Api Consumer';
    const INTEGRATION_NAME    = 'Styla_Connect_Integration';

    protected $userFactory;
    protected $connectionData;
    protected $messageManager;
    protected $oauthService;
    protected $consumerFactory;
    protected $tokenFactory;
    protected $stylaApi;
    protected $configHelper;
    protected $integrationService;
    protected $request;

    //these are the resources that our Styla Integration will be able to use.
    //currently, we only need the products and categories
    protected $integrationResources = [
        'Magento_Catalog::products', 'Magento_Catalog::categories'
    ];

    public function __construct(
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Integration\Api\OauthServiceInterface $oauthService,
        ConsumerFactory $consumerFactory,
        TokenFactory $tokenFactory,
        \Styla\Connect2\Model\Styla\Api $stylaApi,
        \Styla\Connect2\Helper\Config $configHelper,
        \Magento\Integration\Api\IntegrationServiceInterface $integrationService,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->messageManager     = $messageManager;
        $this->userFactory        = $userFactory;
        $this->oauthService       = $oauthService;
        $this->consumerFactory    = $consumerFactory;
        $this->tokenFactory       = $tokenFactory;
        $this->stylaApi           = $stylaApi;
        $this->configHelper       = $configHelper;
        $this->integrationService = $integrationService;
        $this->request            = $request;
    }


    public function getConnectorApiUrl()
    {
        $connectionUrl = self::STYLA_API_CONNECTOR_URL_PRODUCTION;

        if ($this->configHelper->isDeveloperMode() && $forcedUrl = $this->request->getParam('connection_url')) {
            //do some basic validation on the url given by the admin
            if (filter_var($forcedUrl, FILTER_VALIDATE_URL) === false) {
                throw new \Exception('The Connection URL you provided is invalid.');
            }

            $connectionUrl = $forcedUrl;
        }

        return $connectionUrl;
    }

    /**
     * Translate the data returned from the store switcher to a config-saveable scope ids
     *
     * @param array $formData
     * @return array
     */
    protected function _getConnectionScope($formData)
    {
        $defaultScope = ['scope' => 'default', 'scope_id' => 0];

        if (!isset($formData['store_switcher'])) {
            return $defaultScope;
        }

        if ($formData['store_switcher']) {
            return ['scope' => 'stores', 'scope_id' => $formData['store_switcher']];
        }

        if ($formData['store_group_switcher']) {
            return ['scope' => 'groups', 'scope_id' => $formData['store_group_switcher']];
        }

        if ($formData['website_switcher']) {
            return ['scope' => 'websites', 'scope_id' => $formData['website_switcher']];
        }

        return $defaultScope;
    }

    /**
     * Connect to Styla. This is the main method.
     *
     * @param array $postData
     */
    public function connect(array $postData)
    {
        $this->connectionData = $postData;

        //we need an integration for styla
        $integration = $this->getIntegration();

        $connectionScope = $this->_getConnectionScope($postData);

        //i need an oauth consumer
        $consumer = $this->getConsumer($integration);

        //i need to get or create and authorize a token for this consumer
        $token = $this->getToken($consumer);

        //at this point, i should have all the stuff i need for making my connection
        //i'll now be sending this all to styla:
        $connectionData = $this->sendRegistrationRequest($this->getStylaLoginData(), $consumer, $token);

        //save the connection data i got from styla:
        $this->configHelper->updateConnectionConfiguration($connectionData, $connectionScope);
    }

    public function getIntegration()
    {
        //do we have an integration already?
        $existingIntegration = $this->integrationService->findByName(self::INTEGRATION_NAME);
        if ($existingIntegration && $existingIntegration->getId()) {
            $this->_registerIntegration($existingIntegration); //make sure the integration is actually activated

            return $existingIntegration;
        }

        //new integration
        $integrationData = [
            'name'          => self::INTEGRATION_NAME,
            'all_resources' => 0,
            'resource'      => $this->integrationResources
        ];

        $integration = $this->integrationService->create($integrationData);
        $this->_registerIntegration($integration); //activate the integration

        return $integration;
    }

    /**
     * Register the integration as activated, so it's usable.
     *
     * @param $integration
     * @throws \Exception
     */
    protected function _registerIntegration(Integration $integration)
    {
        if ($integration->getStatus() == Integration::STATUS_ACTIVE) {
            return; //already done
        }

        //create the permanent token
        if ($this->oauthService->createAccessToken($integration->getConsumerId(), 1)) {
            $integration->setStatus(Integration::STATUS_ACTIVE)->save();
        } else {
            throw new \Exception('Failed authorizing the Styla integration.');
        }
    }

    /**
     * Send the registration data to Styla
     *
     * @param array $loginData
     * @param       $consumer
     * @param       $token
     * @return array
     * @throws \Exception
     */
    public function sendRegistrationRequest($loginData, Consumer $consumer, Token $token)
    {
        /** @var Request\Type\Register $apiRequest */
        $apiRequest = $this->stylaApi->getRequest(Request\Type\Register::class);
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

    /**
     *
     * @return array|bool
     */
    public function getStylaLoginData()
    {
        return isset($this->connectionData['styla']) ? $this->connectionData['styla'] : false;
    }

    /**
     *
     * @deprecated the integration doesn't need an admin user
     */
    public function getAdminUser()
    {
        $user           = $this->userFactory->create();
        $userCollection = $user->getCollection()
            ->addFieldToFilter('username', self::ADMIN_USERNAME);

        $existingUser = $userCollection->getFirstItem();
        if ($existingUser->getId()) {
            return $existingUser;
        }

        //no user to give, so we'll create a new one
        $stylaLoginData = $this->getStylaLoginData();
        if ($stylaLoginData === false) {
            throw new \Exception('Wrong data. Please go through the connection form again.');
        }

        $adminEmail = self::ADMIN_EMAIL_PREPEND . $stylaLoginData['email'];

        //create the new user here
        try {
            $user->setData([
                'username'  => self::ADMIN_USERNAME,
                'firstname' => 'Styla',
                'lastname'  => 'Connect2',
                'email'     => $adminEmail,
                'password'  => $this->_generateAdminPassword(),
            ])->save();
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
        return substr(str_shuffle(strtolower(sha1(rand() . time() . "styla_connect2"))), 0, 9);
    }

    /**
     * @param $integration
     * @return Consumer
     * @throws \Exception
     */
    public function getConsumer(Integration $integration)
    {
        //the integration must already have a consumer, so we're just getting it here
        $consumer = $this->consumerFactory->create()->load($integration->getConsumerId());
        if (!$consumer->getId()) {
            throw new \Exception('Invalid Styla Integration.');
        }

        return $consumer;
    }

    protected function getToken(Consumer $consumer)
    {
        if (!$consumer->getId()) {
            throw new \Exception("Invalid consumer provided.");
        }

        $token = $this->tokenFactory->create();

        $tokenCollection = $token->getCollection()
            ->addFieldToFilter('consumer_id', $consumer->getId());

        $existingToken = $tokenCollection->getFirstItem();
        if ($existingToken && $existingToken->getId()) {
            return $existingToken;
        }

        throw new \Exception('Missing token for the Styla Integration.');
    }
}
