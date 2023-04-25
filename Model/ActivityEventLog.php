<?php
    
    namespace Develodesign\Punchout\Model;

    use Develodesign\Punchout\Api\Data\ActivityEventLogInterface;
    use Develodesign\Punchout\Api\Data\ActivityEventLogInterfaceFactory;
    use Magento\Framework\Api\DataObjectHelper;
    
class ActivityEventLog extends \Magento\Framework\Model\AbstractModel
{
    protected $dataObjectHelper;
    
    protected $activityeventlogDataFactory;
    
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'develodesign_logactivity';
    
    
    /**
     * @param \Magento\Framework\Model\Context                                          $context
     * @param \Magento\Framework\Registry                                               $registry
     * @param \Develodesign\Punchout\Api\Data\ActivityEventLogInterfaceFactory       $activityeventlogDataFactory
     * @param DataObjectHelper                                                          $dataObjectHelper
     * @param \Develodesign\Punchout\Model\ResourceModel\ActivityEventLog            $resource
     * @param \Develodesign\Punchout\Model\ResourceModel\ActivityEventLog\Collection $resourceCollection
     * @param array                                                                     $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ActivityEventLogInterfaceFactory $activityeventlogDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Develodesign\Punchout\Model\ResourceModel\ActivityEventLog $resource,
        \Develodesign\Punchout\Model\ResourceModel\ActivityEventLog\Collection $resourceCollection,
        array $data = []
    ) {
        $this->activityeventlogDataFactory = $activityeventlogDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
     * Retrieve activityeventlog model with activityeventlog data
     * @return \Develodesign\Punchout\Api\Data\ActivityEventLogInterface
     */
    public function getDataModel()
    {
        $activityeventlogData = $this->getData();
        
        $activityeventlogDataObject = $this->activityeventlogDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $activityeventlogDataObject,
            $activityeventlogData,
            \Develodesign\Punchout\Api\Data\ActivityEventLogInterface::class
        );
        
        return $activityeventlogDataObject;
    }
}
