<?php

namespace Styla\Connect2\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;

class Navigation implements ObserverInterface
{
    /**
     * @var \Styla\Connect2\Helper\Data
     */
    protected $stylaHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    protected $magazineFactory;

    public function __construct(
        \Styla\Connect2\Helper\Data $stylaHelper,
        \Magento\Framework\App\Request\Http $request,
        \Styla\Connect2\Model\ResourceModel\Magazine\CollectionFactory $magazineFactory
    ) {
        $this->magazineFactory = $magazineFactory;
        $this->stylaHelper = $stylaHelper;
        $this->request = $request;
    }

    /**
     * This will add a navigation link for the styla magazine in the main navigation tree.
     *
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $magazines = $this->magazineFactory->create();

        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();

        $tree = $menu->getTree();

        foreach ($magazines as $magazine) {
            if (false === (bool) $this->stylaHelper->isMagazineIncludedInNavigation($magazine)) {
                continue;
            }
            $data = [
                'name' => $magazine->getNavigationLabel(),
                'id' => 'styla-magazine-' . $magazine->getId(),
                'url' => $this->stylaHelper->getAbsoluteMagazineUrl($magazine),
                'is_active' => (string) $this->request->getControllerModule() === 'stylaconnect2page',
            ];

            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
        }

        return $this;
    }
}