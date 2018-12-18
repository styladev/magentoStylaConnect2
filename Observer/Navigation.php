<?php

namespace Styla\Connect2\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
use Styla\Connect2\Model\ResourceModel\Magazine\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Styla\Connect2\Helper\Data;

class Navigation implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $stylaHelper;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var CollectionFactory
     */
    protected $magazineFactory;

    public function __construct(
        Data $stylaHelper,
        Http $request,
        CollectionFactory $magazineFactory
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

        /** @var Node $menu */
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