<?php
namespace Styla\Connect2\Model\Styla\Api;

use Styla\Connect2\Api\Styla\RequestInterface;

class RequestFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return RequestInterface
     */
    public function create($type, $arguments = [])
    {
        return $this->_objectManager->create($type, $arguments);
    }
}
