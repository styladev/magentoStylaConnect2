<?php
namespace Styla\Connect2\Model;

use Magento\Framework\Model\AbstractModel;
use Styla\Connect2\Helper\Config as ConfigHelper;

class Page
    extends \Magento\Framework\DataObject
{
    /**
     * Magazine page cache tag
     */
    const CACHE_TAG = 'magazine_page';

    /**
     * @var string
     */
    protected $_cacheTag = 'magazine_page';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magazine_page';

    protected $tags;
    protected $baseTags;
    protected $_username;
    protected $_apiVersion;

    protected $stylaApi;
    protected $configHelper;

    public function __construct(
        \Styla\Connect2\Model\Styla\Api $stylaApi,
        ConfigHelper $configHelper,
        array $data = []
    )
    {
        $this->stylaApi     = $stylaApi;
        $this->configHelper = $configHelper;

        return parent::__construct($data);
    }

    public function save()
    {
        throw new \Exception('save is not supported!');
    }

    public function load($modelId, $field = null)
    {
        throw new \Exception('load not supported. Use loadByPath(), instead.');
    }

    /**
     * Load the page from styla, using it's path
     *
     * @param string $path
     * @return \Styla\Connect2\Model\Page
     */
    public function loadByPath($path)
    {
        $data = $this->_getApi()
            ->requestPageData($path);

        if ($data) {
            $this->setData($data);
            $this->setData('exist', true);

            if ($data['status']) {
                $this->setData('statusCode', $data['status']);
            } else {
                $this->setData('statusCode', "200");
            }
        } else {
            $this->setData('exist', false);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSeoStatusCode()
    {
        return $this->getData('statusCode');
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return $this->getData('exist') ? true : false;
    }

    /**
     * @return array
     */
    public function getBaseMetaData()
    {
        if (!$this->baseTags) {
            $tags = [
                'title'       => $this->getTitle(),
                'description' => $this->getMetaDescription(),
                'keywords'    => $this->getMetaKeywords(),
                'robots'      => $this->getMetaRobots(),
            ];

            $this->baseTags = array_filter($tags);
        }

        return $this->baseTags;
    }

    /**
     * @return array
     */
    public function getAdditionalMetaTags()
    {
        return array_diff_key(
            $this->getTags(),
            $this->getBaseMetaData()
        );
    }

    /**
     * @return array
     */
    public function getTags()
    {
        if (!$this->tags) {
            $this->tags = [];
            $tags       = $this->getData('tags');

            if (!$tags) {
                $tags = [];
            }

            foreach ($tags as $data) {
                $tagName = $data['tag'];

                $added = false;
                foreach (['name', 'property'] as $key) {
                    if (isset($data['attributes'][$key])) {
                        $added = true;
                        $this->addTag($tagName . '-' . $data['attributes'][$key], $data);
                    }
                }

                if (!$added) {
                    $this->tags[$tagName][] = $data;
                }

            }
        }

        return $this->tags;
    }

    /**
     *
     * @param string $name
     * @param array $data
     * @return \Styla\Connect2\Model\Page
     */
    public function addTag($name, $data)
    {
        if (!isset($this->tags[$name])) {
            $this->tags[$name] = [];
        }
        $this->tags[$name][] = $data;

        return $this;
    }

    /**
     * @param $type
     * @return array
     */
    public function getTag($type)
    {
        $tags = $this->getTags();
        if (isset($tags[$type])) {
            return $tags[$type];
        }

        return [];
    }

    /**
     * @param            $type
     * @param mixed      $default
     * @return mixed
     */
    public function getSingleContentTag($type, $default = false)
    {
        $tag = $this->getTag($type);

        if ($tag) {
            $tag = reset($tag);

            if (isset($tag['content']) && $tag['content']) {
                return $tag['content'];
            }

            if (isset($tag['attributes'], $tag['attributes']['content'])
                && $tag['attributes']['content']
            ) {
                return $tag['attributes']['content'];
            }
        }

        return $default;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getSingleContentTag('title', '');
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->getSingleContentTag('meta-description', '');
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->getSingleContentTag('meta-keywords', '');
    }

    /**
     * @return string
     */
    public function getMetaRobots()
    {
        return $this->getSingleContentTag('meta-robots', '');
    }

    public function getNoScript()
    {
        $html = $this->getData('html');

        return isset($html['body']) ? $html['body'] : '';
    }

    /**
     * no multi-language support yet so just take the config value
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->getConfigHelper()->getLanguageCode();
    }

    /**
     *
     * @return string
     */
    public function getCssUrl()
    {
        $cssUrl = $this->getConfigHelper()->getAssetsUrl(ConfigHelper::ASSET_TYPE_CSS);
        return $cssUrl;
    }

    /**
     * Get Styla client name
     *
     * @return string
     */
    public function getUsername()
    {
        if (null === $this->_username) {
            $this->_username = $this->getConfigHelper()->getUsername();
        }

        return $this->_username;
    }

    /**
     * Get the current url for Styla's JS script, used for loading the magazine page
     *
     * @return string
     */
    public function getScriptUrl()
    {
        $scriptUrl = $this->getConfigHelper()->getAssetsUrl(ConfigHelper::ASSET_TYPE_JS);
        return $scriptUrl;
    }

    /**
     *
     * @return \Styla\Connect2\Helper\Config
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * @return \Styla\Connect2\Model\Styla\Api
     */
    protected function _getApi()
    {
        return $this->stylaApi;
    }
}
