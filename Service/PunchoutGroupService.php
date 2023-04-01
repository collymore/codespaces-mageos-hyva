<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory;
    use Magento\Framework\DataObject;
    use Magento\Framework\Exception\NoSuchEntityException;

    class PunchoutGroupService
    {
        /**
         * @var CollectionFactory
         */
        private $punchoutGroupCollection;
        public function __construct(
            CollectionFactory $punchoutGroupCollection
        ) {
            $this->punchoutGroupCollection = $punchoutGroupCollection;
        }
    
        public function loadPunchOutGroupBySecretDuns($sharedSecret,$dunsIdentity): DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('shared_secret', ['eq' => $sharedSecret])
                ->addFieldToFilter('duns_identity', ['eq' => $dunsIdentity])
                ->getFirstItem();
        }
    
        public function loadPunchOutGroupByDunsIdentity($dunsIdentity): DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('duns_identity', ['eq' => $dunsIdentity])
                ->getFirstItem();
        }

        public function loadPunchOutGroupByAribaNetworkId($aribaNetworkId): DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('ariba_network_id', ['eq' => $aribaNetworkId])
                ->getFirstItem();
        }
    
        public function loadPunchOutGroupByAribaNetworkSecret($sharedSecret,$aribaNetworkId): DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('ariba_network_id', ['eq' => $aribaNetworkId])
                ->addFieldToFilter('shared_secret', ['eq' => $sharedSecret])
                ->getFirstItem();
        }

        public function loadPunchOutGroupByCredentials($sharedSecret, $dunsIdentity, $aribaNetworkId): DataObject|array
        {
            $punchoutGroup = $this->loadPunchOutGroupBySecretDuns($sharedSecret,$dunsIdentity);
            if((!(int)$punchoutGroup->getPunchoutgroupId()) > 0) {
                $punchoutGroup = $this->loadPunchOutGroupByDunsIdentity($dunsIdentity);
                if(((!(int)$punchoutGroup->getPunchoutgroupId()) > 0) && !empty(trim($aribaNetworkId))) {
                    $punchoutGroup = $this->loadPunchOutGroupByAribaNetworkId($aribaNetworkId);
                }
            }
            if ($punchoutGroup === null || $punchoutGroup->getPunchoutgroupId() === null) {
                throw new NoSuchEntityException(
                    __(
                        'No such PunchoutGroup entity with %fieldName = %fieldValue, %field2Name = %field2Value, %field3Name = %field3Value',
                        [
                            'fieldName'   => 'sharedSecret',
                            'fieldValue'  => $sharedSecret,
                            'field2Name'  => 'dunsIdentity',
                            'field2Value' => $dunsIdentity,
                            'field3Name'  => 'aribaNetworkId',
                            'field3Value' => $aribaNetworkId,
                        ]
                    )
                );
            }
            return $punchoutGroup;
    
        }
    
        public function loadPunchOutGroupByOciCredentials(string $username, string $password): ?DataObject
        {
            $punchoutGroup = $this->punchoutGroupCollection->create()
                ->addFieldToFilter('oci_username', ['eq' => $username])
                ->addFieldToFilter('oci_password', ['eq' => $password]);
            if (!($punchoutGroup->count() > 0)) {
                return null;
            }
            return $punchoutGroup->getFirstItem();
        }
        
        public function loadPunchOutGroupById(int $punchoutGroupId)
        {
            $punchoutGroup = $this->punchoutGroupCollection->create()
                ->addFieldToFilter('punchoutgroup_id', ['eq' => $punchoutGroupId]);
            if($punchoutGroup->count() > 0){
                return $punchoutGroup->getFirstItem();
            }
            return null;
            
        }
    }
