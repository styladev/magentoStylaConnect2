<?php
namespace Styla\Connect2\Model\Styla\Api;

class ResponseFactory
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
     * @param Styla\Connect2\Model\Styla\Api\Response\Type\AbstractType $type
     * @param array $arguments
     * @return Event
     */
    public function create($type, $arguments = [])
    {
        return $this->_objectManager->create($type, $arguments);
    }
}
