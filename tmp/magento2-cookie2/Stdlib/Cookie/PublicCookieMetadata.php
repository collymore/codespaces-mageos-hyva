<?php

namespace Punchout\Cookie2\Stdlib\Cookie;

use Magento\Framework\Stdlib\Cookie\CookieMetadata as BaseCookieMetadata;
use Punchout\Cookie2\Framework\Utils;

class PublicCookieMetadata extends \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
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
            $this->setSecure(true);
        }
        return parent::setSameSite('None');
    }

    /**
     * Set whether the cookie is only available under HTTPS
     *
     * @param bool $secure
     * @return $this
     */
    public function setSecure($secure)
    {
        return parent::setSecure(true);
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return Utils::wrapCookieMetadata(parent::__toArray());
    }
}
