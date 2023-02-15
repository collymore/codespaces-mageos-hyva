<?php

namespace Develodesign\Punchout\Model\File;

use Develodesign\Punchout\Model\PunchoutGroupFactory;
use Develodesign\Punchout\Model\PunchoutGroupRepository;
use Develodesign\Punchout\Model\ResourceModel\PunchoutGroup\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;

class ImportFileHandler
{
    /**
     * CSV Processor
     *
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var array
     */
    protected $validCSVMimeType = [
        'text/plain', 'text/csv',
        'application/csv','text/comma-separated-values',
        'application/excel','application/vnd.msexcel',
        'text/x-csv', 'application/vnd.ms-excel',
        'text/anytext', 'application/octet-stream',
        'application/txt','application/vnd.oasis.opendocument.text',
        'application/vnd.google-apps.spreadsheet',
        'application/vnd.google-apps.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    /**
     * @var CollectionFactory
     */
    protected $punchoutGroupCollectionFactory;

    /**
     * @var PunchoutGroupRepository
     */
    protected $punchoutGroupRepository;

    /**
     * @var PunchoutGroupFactory
     */
    protected $punchoutGroupFactory;

    public function __construct(
        Csv $csvProcessor,
        CollectionFactory $punchoutGroupCollectionFactory,
        PunchoutGroupRepository $punchoutGroupRepository,
        PunchoutGroupFactory $punchoutGroupFactory
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->punchoutGroupFactory = $punchoutGroupFactory;
        $this->punchoutGroupRepository = $punchoutGroupRepository;
        $this->punchoutGroupCollectionFactory = $punchoutGroupCollectionFactory;
    }

    /**
     * Retrieve a list of fields required for CSV file (order is important!)
     *
     * @return array
     */
    private function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('Group Name'),
            1 => __('Status'),
            2 => __('Email Address'),
            3 => __('Is Parent'),
            4 => __('Shared Secret'),
            5 => __('DUNS Identity'),
            6 => __('OCI Username'),
            7 => __('OCI Password'),
            8 => __('Street'),
            9 => __('City'),
            10 => __('Country ID'),
            11 => __('Postcode'),
            12 => __('Telephone')
        ];
    }

    private function isValidFileType($fileType): bool
    {
        if (!in_array($fileType, $this->validCSVMimeType, true)) {
            throw new \RuntimeException(sprintf('CSVMimeType: Invalid file MIME type provided: %s ', $fileType));
        }
        return true;
    }

    /**
     * Filters file content
     *
     * @param array $csvRawData
     * @return array
     */
    private function filterFileData(array $csvRawData): array
    {
        $rawDataRows = [];

        foreach ($csvRawData as $rowIndex => $dataRow) {

            // skip headers
            if ($rowIndex === 0) {
                continue;
            }

            $processedCol = [];
            foreach ($dataRow as $colKey => $colValue) {
                // Heads row
                $processedCol[trim($csvRawData[0][$colKey])] = $colValue;
            }

            $rawDataRows[] = $processedCol;
        }
        return $rawDataRows;
    }

    /**
     * Validates csv file headers using  the required fields
     * @param array $headerFields
     * @return array|null
     */
    private function isValidHeaderFields(array $headerFields): ?array
    {
        $requiredFields = $this->getRequiredCsvFields();
        if ($result = array_intersect($headerFields, $requiredFields)) {
            return $result;
        }
        return null;
    }

    /**
     * Import single account manager file
     *
     * @param array $file
     *
     * @throws LocalizedException*@throws \Exception
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new LocalizedException(__('Invalid file upload attempt.'));
        }

        if ($this->isValidFileType($file['type']) === true) {
            $csvRawData = $this->csvProcessor->getData($file['tmp_name']);

            // first row of file represents headers
            $headers = $this->isValidHeaderFields($csvRawData[0]);
            if (null !== $headers) {
                $processedRawDataRows = $this->filterFileData($csvRawData);
                foreach ($processedRawDataRows as $processedRawData) {
                    $modelData = $this->punchoutGroupCollectionFactory->create()->addFieldToFilter('group_email', $processedRawData['Email Address'])->getData();
                    try {
                        if ($modelData) {
                            $id = (int)$modelData[0]['punchoutgroup_id'];
                            $matchingModel =$this->punchoutGroupRepository->get($id);
                            $matchingModel->setGroupName($processedRawData['Group Name']);
                            $matchingModel->setStatus($processedRawData['Status']);
                            $matchingModel->setGroupEmail($processedRawData['Email Address']);
                            $matchingModel->setIsParent($processedRawData['Is Parent']);
                            $matchingModel->setSharedSecret($processedRawData['Shared Secret']);
                            $matchingModel->setDunsIdentity($processedRawData['DUNS Identity']);
                            $matchingModel->setOciUsername($processedRawData['OCI Username']);
                            $matchingModel->setOciPassword($processedRawData['OCI Password']);
                            $matchingModel->setStreet($processedRawData['Street']);
                            $matchingModel->setCity($processedRawData['City']);
                            $matchingModel->setCountryId($processedRawData['Country ID']);
                            $matchingModel->setPostcode($processedRawData['Postcode']);
                            $matchingModel->setTelephone($processedRawData['Telephone']);
                            $this->punchoutGroupRepository->save($matchingModel);
                        } else {
                            /** @var \Develodesign\Punchout\Model\PunchoutGroup $punchoutGroupModel */
                            $punchoutGroupModel = $this->punchoutGroupFactory->create();
                            $newData = [
                                'group_name' => $processedRawData['Group Name'],
                                'status'  => $processedRawData['Status'],
                                'group_email' => $processedRawData['Email Address'],
                                'is_parent'  => $processedRawData['Is Parent'],
                                'shared_secret'  => $processedRawData['Shared Secret'],
                                'duns_identity'  => $processedRawData['DUNS Identity'],
                                'oci_username'  => $processedRawData['OCI Username'],
                                'oci_password'  => $processedRawData['OCI Password'],
                                'street'  => $processedRawData['Street'],
                                'city'  => $processedRawData['City'],
                                'country_id'  => $processedRawData['Country ID'],
                                'postcode'  => $processedRawData['Postcode'],
                                'telephone' => $processedRawData['Telephone']
                            ];
                            $punchoutGroupModel->setData($newData);
                            $this->punchoutGroupRepository->save($punchoutGroupModel);
                        }
                    } catch (\Exception $e) {
                        $php_errormsg = sprintf(
                            'Failed to sabe new punchout group record'
                        );
                        throw new CouldNotSaveException(__('Error:' . $php_errormsg));
                    }
                }
            }
        }
    }
}
