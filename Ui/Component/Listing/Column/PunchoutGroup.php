<?php

namespace Develodesign\Punchout\Ui\Component\Listing\Column;

    use Develodesign\Punchout\Service\PunchoutGroupService;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\View\Element\UiComponent\ContextInterface;
    use Magento\Framework\View\Element\UiComponentFactory;
    use Magento\Ui\Component\Listing\Columns\Column;

class PunchoutGroup extends Column
{
    /**
     * @var PunchoutGroupService
     */
    protected $punchoutGroupService;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PunchoutGroupService $punchoutGroupService,
        array $components = [],
        array $data = []
    ) {
        $this->punchoutGroupService = $punchoutGroupService;
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
                        $item['punchoutgroup_id'] = $punchoutGroup->getGroupName() . ' <' . $punchoutGroup->getGroupEmail() . '>';
                    } else {
                        $item['punchoutgroup_id'] = $item['group_name'] . ' <' . $item['group_email'] . '>';
                    }
                } catch (NoSuchEntityException $e) {
                }
            }
        }

        return $dataSource;
    }
}
