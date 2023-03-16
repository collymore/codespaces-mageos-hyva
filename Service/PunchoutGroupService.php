<?php

namespace Develodesign\Punchout\Service;

    use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory;
    use Magento\Framework\DataObject;

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
    
        private function loadPunchOutGroupBySecretDuns($sharedSecret,$dunsIdentity): \Magento\Framework\DataObject
        {
            return $this->punchoutGroupCollection->create()
                ->addFieldToFilter('shared_secret', ['eq' => $sharedSecret])
                ->addFieldToFilter('duns_identity', ['eq' => $dunsIdentity])
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
            //$result = new DataObject(['error' => false,'matching_punchout_group' => null,'message' => null]);
            $matchingPunchoutGroup = [];
          
            // Loop through each load type.
            foreach ($loadTypes as $loadType) {
                switch ($loadType) {
                    case 'secret':
                        //$result->setMatchingPunchoutGroup($this->loadPunchOutGroupBySecretDuns($sharedSecret,$dunsIdentity));
                        $matchingPunchoutGroup = $this->loadPunchOutGroupBySecretDuns($sharedSecret,$dunsIdentity);
                        break;
                    case 'identity':
                        //$result->setMatchingPunchoutGroup($this->loadPunchOutGroupByDunsIdentity($dunsIdentity));
                        $matchingPunchoutGroup = $this->loadPunchOutGroupByDunsIdentity($dunsIdentity);
                        break;
                    case 'ariba_network':
                        if (!empty(trim($aribaNetworkId))) {
                            //$result->setMatchingPunchoutGroup($this->loadPunchOutGroupByAribaNetworkId($dunsIdentity));
                            $matchingPunchoutGroup = $this->loadPunchOutGroupByAribaNetworkId($aribaNetworkId);
                        }
                        break;
                    default:
                        // Throw an exception if an invalid load type is provided.
                        throw new \InvalidArgumentException(sprintf('Invalid load type: %s', $loadType));
                }
               // toDo: rethink this
                /*if (null === $result->getMatchingPunchoutGroup()) {
                    switch ($loadType) {
                        case 'secret':
                            $result->setError(true);
                            $result->setMessage(sprintf(
                                'No matching PunchOut Group found for shared secret %s and duns identity: %s',
                                $sharedSecret,$dunsIdentity
                            ));
                            throw new \RuntimeException(sprintf(
                                'No matching PunchOut Group found for shared secret %s and duns identity: %s',
                                $sharedSecret,$dunsIdentity
                            ));
                        case 'identity':
                            $result->setError(true);
                            $result->setMessage(sprintf(
                                'No matching PunchOut Group found for DUNS identity: %s',
                                $dunsIdentity
                            ));
                            throw new \RuntimeException(sprintf(
                                'No matching PunchOut Group found for DUNS identity: %s',
                                $dunsIdentity
                            ));
                        case 'ariba_network':
                            $result->setError(true);
                            $result->setMessage(sprintf(
                                'No matching PunchOut Group found for Ariba Network ID: %s',
                                $aribaNetworkId
                            ));
                            throw new \RuntimeException(sprintf(
                                'No matching PunchOut Group found for Ariba Network ID: %s',
                                $aribaNetworkId
                            ));
                    }
                }*/
            }
            //return $result;
            return $matchingPunchoutGroup;
        }
    }
