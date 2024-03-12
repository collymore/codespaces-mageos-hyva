<?php

namespace Punchout\Cookie2\Stdlib\Cookie;

use Magento\Framework\Stdlib\Cookie\CookieMetadata as BaseCookieMetadata;
use Punchout\Cookie2\Framework\Utils;

class CookieMetadata extends BaseCookieMetadata
{
    /**
     * CE/EE 2.4.3+
     *
     * Setter for Cookie SameSite attribute
     *
     * @param  string $sameSite
     * @return $this
     */
    public function setSameSite(string $sameSite): BaseCookieMetadata
    {
        if (!$this->getSecure()) {
            return $this->set(self::KEY_SECURE, true);
        }
        return parent::setSameSite('None');
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return Utils::wrapCookieMetadata(parent::__toArray());
    }
}
