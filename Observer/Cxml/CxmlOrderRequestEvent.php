<?php
    
namespace Develodesign\Punchout\Observer\Cxml;

use Develodesign\Punchout\Observer\BasePunchoutRequestEvent;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Develodesign\Punchout\Model\ActivityEventLogFactory;
use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class CxmlOrderRequestEvent extends BasePunchoutRequestEvent implements ObserverInterface
{
    /**
     * @var RemoteAddress
     */
    protected $remote;

    public function __construct(
        ActivityEventLogFactory $activityEventLogFactory,
        RemoteAddress $remote
    ) {
        $this->activityEventLogFactory = $activityEventLogFactory;
        $this->remote = $remote;
    }

    public function execute(Observer $observer)
    {
        $activityEvent = $this->activityEventLogFactory->create();
        return $activityEvent->setData([
            'event_type'       => $observer->getEvent()->getEventType(),
            'action'           => $observer->getEvent()->getAction(),
            'user_id'          => $observer->getEvent()->getUserId(),
            'punchoutgroup_id' => $observer->getEvent()->getPunchoutgroupId(),
            'info'             => $observer->getEvent()->getInfo(),
            'ip'          => $this->remote->getRemoteAddress()
        ])->save();
    }
}
