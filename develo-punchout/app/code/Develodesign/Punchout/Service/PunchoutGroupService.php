<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory;

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
    
        private function loadPunchOutGroupBySecret($sharedSecret): \Magento\Framework\DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('shared_secret', ['eq' => $sharedSecret])
                ->getFirstItem();
        }
    
        private function loadPunchOutGroupByDunsIdentity($dunsIdentity): \Magento\Framework\DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('duns_identity', ['eq' => $dunsIdentity])
                ->getFirstItem();
        }

        private function loadPunchOutGroupByAribaNetworkId($aribaNetworkId): \Magento\Framework\DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('ariba_network_id', ['eq' => $aribaNetworkId])
                ->getFirstItem();
        }

        public function loadPunchOutGroupByCredentials($sharedSecret, $dunsIdentity, $aribaNetworkId): \Magento\Framework\DataObject|array
        {
            $loadTypes = ['secret', 'identity', 'ariba_network'];
            $matchingPunchoutGroup = [];
          
            // Loop through each load type.
            foreach ($loadTypes as $loadType) {
                switch ($loadType) {
                    case 'secret':
                        $matchingPunchoutGroup = $this->loadPunchOutGroupBySecret($sharedSecret);
                        break;
                    case 'identity':
                        $matchingPunchoutGroup = $this->loadPunchOutGroupByDunsIdentity($dunsIdentity);
                        break;
                    case 'ariba_network':
                        if (!empty(trim($aribaNetworkId))) {
                            $matchingPunchoutGroup = $this->loadPunchOutGroupByAribaNetworkId($aribaNetworkId);
                        }
                        break;
                    default:
                        // Throw an exception if an invalid load type is provided.
                        throw new \InvalidArgumentException(sprintf('Invalid load type: %s', $loadType));
                }
               // toDo: rethink this
                /*if (is_array($matchingPunchoutGroup) && empty($matchingPunchoutGroup)) {
                    switch ($loadType) {
                        case 'secret':
                            throw new \RuntimeException(sprintf(
                                'No matching PunchOut Group found for shared secret: %s',
                                $sharedSecret
                            ));
                        case 'identity':
                            throw new \RuntimeException(sprintf(
                                'No matching PunchOut Group found for DUNS identity: %s',
                                $dunsIdentity
                            ));
                        case 'ariba_network':
                            throw new \RuntimeException(sprintf(
                                'No matching PunchOut Group found for Ariba Network ID: %s',
                                $aribaNetworkId
                            ));
                    }
                }*/
            }
            
            return $matchingPunchoutGroup;
        }
    }
