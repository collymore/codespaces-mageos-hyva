<?php
    
namespace Develodesign\Punchout\Observer\Cxml;

use Develodesign\Punchout\Api\ActivityEventLogRepositoryInterface;
use Develodesign\Punchout\Model\ActivityEventLogFactory;
use Develodesign\Punchout\Observer\BasePunchoutRequestEvent;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class CxmlSetupRequestEvent extends BasePunchoutRequestEvent implements ObserverInterface
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

    /**
     * @throws \Exception
     */
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
