<?php

namespace Develodesign\Punchout\Ui\Component\Listing\Column;

    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\View\Element\UiComponent\ContextInterface;
    use Magento\Framework\View\Element\UiComponentFactory;
    use Magento\Ui\Component\Listing\Columns\Column;
    use Psr\Log\LoggerInterface;

class PunchoutGroup extends Column
{
    /**
     * @var PunchoutGroupService
     */
    protected $punchoutGroupService;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PunchoutGroupService $punchoutGroupService,
        LoggerInterface $logger,
        array $components = [],
        array $data = []
    ) {
        $this->punchoutGroupService = $punchoutGroupService;
        $this->logger = $logger;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $punchoutGroupId = $item['punchoutgroup_id'];
                try {
                    $punchoutGroup = $this->punchoutGroupService->loadPunchOutGroupById($punchoutGroupId);
                    if ($punchoutGroup) {
                        $groupName = $punchoutGroup->getGroupName();
                        $groupEmail = $punchoutGroup->getGroupEmail();
                        $item['punchoutgroup_id'] = "{$groupName} <{$groupEmail}>";
                    } else {
                        $item['punchoutgroup_id'] = "";
                    }
                   
                } catch (NoSuchEntityException $e) {
                    $this->logger->warning("Non existant Punchout group attempted to be loaded : {$punchoutGroupId}");
                }
            }
        }
        return $dataSource;
    }
}
