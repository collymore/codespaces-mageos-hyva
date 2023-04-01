<?php
    
    namespace Develodesign\Punchout\Observer;
    
    use Develodesign\Punchout\Model\ActivityEventLogFactory;

    abstract class BasePunchoutRequestEvent
    {
        /**
         * @var ActivityEventLogFactory
         */
        protected $activityEventLogFactory;
    
        public function __construct(
            ActivityEventLogFactory $activityEventLogFactory
        )
        {
            $this->activityEventLogFactory = $activityEventLogFactory;
        }
    }
