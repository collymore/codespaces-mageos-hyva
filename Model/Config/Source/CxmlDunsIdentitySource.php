<?php

namespace Develodesign\Punchout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CxmlDunsIdentitySource implements OptionSourceInterface
{
    
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'sender', 'label' => __('Sender -/cXML/Header/Sender/Credential/Identity')],
            ['value' => 'from', 'label' => __('From - /cXML/Header/From/Credential/Identity')],
            ['value' => 'both', 'label' => __('Both')],
        ];
    }
}
