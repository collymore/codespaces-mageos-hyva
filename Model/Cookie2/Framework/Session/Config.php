<?php

declare(strict_types=1);

namespace Develodesign\Punchout\Model\Cookie2\Framework\Session;

use Magento\Framework\Session\Config\ConfigInterface;
use Develodesign\Punchout\Model\Cookie2\Utils;

use Develodesign\Punchout\Helper\PunchoutSession;
use Magento\Framework\App\ObjectManager;

/**
 * Magento session configuration
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Config extends \Magento\Framework\Session\Config
{

    /**
     * Set session.cookie_secure
     *
     * @param bool $cookieSecure
     * @return \Punchout\Cookie2\Framework\Session\Config
     */
    public function setCookieSecure($cookieSecure)
    {
        return parent::setCookieSecure(true);
    }

    /**
     * Set session.cookie_samesite
     *
     * @param string $cookieSameSite
     * @return \Punchout\Cookie2\Framework\Session\Config
     */
    public function setCookieSameSite(string $cookieSameSite = 'Lax'): ConfigInterface
    {
        return parent::setCookieSameSite('None');
    }

    /**
     * @return bool
     */
    public function getCookieSecure()
    {
        return true;
    }

    /**
     * Get session.cookie_samesite
     *
     * @return string
     */
    public function getCookieSameSite(): string
    {
        return 'None';
    }

    /**
     * Get all options set
     *
     * @return array
     */
    public function getOptions()
    {
        return Utils::wrapSessionOptions(parent::getOptions());
    }

    /**
     * Get is customer punchout session
     *
     * @return boolean
     */

    protected function isPucnhoutSession()
    {
        $objectManager = ObjectManager::getInstance();
        return $objectManager->get('Develodesign\Punchout\Helper\PunchoutSession')->getIsPunchotSession();
    }
}
