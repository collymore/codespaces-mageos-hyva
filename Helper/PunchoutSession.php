<?php

namespace Develodesign\Punchout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\StorageInterface;

/* this is not used but chould be developed in future for disble cookies only for punchout session */

class PunchoutSession extends AbstractHelper
{
    /** @var StorageInterface */
    protected $storage;

    public function __construct(
        Context $context,
        StorageInterface $storage
    ) {
        $this->storage = $storage;
        parent::__construct($context);
    }

    public function getIsPunchotSession()
    {
        // If a customer has any of these assigned to the session and they are logged in, they are a punchout customer.
        // :-( all empty values
        if ($this->storage->getData('customer_id')
            && (
                !empty($this->storage->getData('payload_id')) // cxml
                || !empty($this->storage->getData('sender_identity')) // cxml
                || !empty($this->storage->getData('hook_url')) // oci
                || !empty($this->storage->getData('caller')) // oci
            )
        ) {
            return true;
        }

        return false;
    }
}
