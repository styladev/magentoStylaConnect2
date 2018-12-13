<?php
namespace Styla\Connect2\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;

class Navigation implements ObserverInterface
{
    protected $stylaHelper;
    protected $request;

    public function __construct(
        \Styla\Connect2\Helper\Data $stylaHelper,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->stylaHelper = $stylaHelper;
        $this->request      = $request;
    }

    /**
     * This will add a navigation link for the styla magazine in the main navigation tree.
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->stylaHelper->isNavigationLinkEnabled()) {
            return $this;
        }

        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();

        $tree        = $menu->getTree();
        $magazineUrl = $this->stylaHelper->getAbsoluteMagazineUrl($this->stylaHelper->getCurrentMagazine());
        $data        = [
            'name'      => $this->stylaHelper->getCurrentMagazine()->getNavigationLabel(),
            'id'        => 'styla-magazine',
            'url'       => $magazineUrl,
            'is_active' => $this->request->getControllerModule() == "Styla_Connect2"
        ];

        $node = new Node($data, 'id', $tree, $menu);
        $menu->addChild($node);

        return $this;
    }
}