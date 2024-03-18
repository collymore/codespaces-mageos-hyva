<?php

declare(strict_types=1);

namespace Develodesign\Punchout\Model\Cookie2\Framework\Session;

use Develodesign\Punchout\Helper\PunchoutSession;
use Magento\Framework\Session\Config;
use Develodesign\Punchout\Model\Cookie2\Utils;

class ConfigPlugin {

    /**
    * @var PunchoutSession
    */
    protected $helperPunchoutSession;

    /**
    * constructor
    * @param PunchoutSession $helperPunchoutSession
    */
    public function __construct(
        PunchoutSession $helperPunchoutSession
    ) {
        $this->helperPunchoutSession = $helperPunchoutSession;
    }

    /**
     * Always set cookie is secure
     *
     * @param Config $subject
     * @return boolean
     */
    public function beforeSetCookieSecure(Config $subject, $cookieSecure)
    {
        return true;
    }

    /**
     * Always set cookie is secure
     *
     * @param Config $subject
     * @param string $cookieSameSite
     * @return string
     */
    public function beforeSetCookieSameSite(Config $subject, string $cookieSameSite = 'Lax')
    {
        return 'None';
    }

    /**
     * Always return true
     *
     * @param Config $subject
     * @param boolean $result
     * @return boolean
    */
    public function afterGetCookieSecure(Config $subject, $result)
    {
        return true;
    }

    /**
     * @param Config $subject
     * @param boolean $result
     * @return string
     */
    public function afterGetCookieSameSite(Config $subject, $result): string
    {
        return 'None';
    }

    /**
     * @param Config $subject
     * @param boolean $result
     * @return array
     */
    public function afterGetOptions(Config $subject, $result)
    {
        return Utils::wrapSessionOptions($result);
    }
}
