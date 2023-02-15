<?php

namespace Develodesign\Punchout\Block\Adminhtml\PunchoutGroup\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ImportButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Import File'),
            'class' => 'save primary',
            'on_click' => sprintf("location.href = '%s';", $this->getImportUrl()),
            'sort_order' => 110,
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getImportUrl(): string
    {
        return $this->getUrl('*/*/import');
    }
}
