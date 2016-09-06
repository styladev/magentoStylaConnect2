<?php
namespace Styla\Connect2\Model\Styla\Api;

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
     * @param       $type
     * @param array $arguments
     * @return \Styla\Connect2\Model\Styla\Api\Request\Type\AbstractType
     */
    public function create($type, $arguments = [])
    {
        return $this->_objectManager->create($type, $arguments);
    }
}
