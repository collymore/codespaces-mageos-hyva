<?php

declare(strict_types=1);

namespace Develodesign\Punchout\Model\Cookie2\Framework\Session;

use Develodesign\Punchout\Helper\PunchoutSession;
use Magento\Framework\Session\Generic;

class GenericPlugin {

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
     * @param Generic $subject
     * @param $result Generic
     * @return $result Generic
     */
    public function afterStart(Generic $subject, $result)
    { 
        $this->updateCookieParams();
        return $result;
    }

    /**
     * @param Generic $subject
     * @param $result Generic
     * @return $result Generic
     */
    public function afterRegenerateId(Generic $subject, $result)
    {
        $this->updateCookieParams();
        return $result;
    }

    /**
     * @return $this
     */
    protected function updateCookieParams()
    {
        if (version_compare(PHP_VERSION, "7.3.0", ">=")) {
            return $this->updateCookieParamsWithOptions();
        } else {
            return $this->updateCookieParamsWithoutOptions();
        }
    }

    /**
     * Handle PHP versions above and equal PHP 7.3
     *
     * @return $this
     */
    protected function updateCookieParamsWithOptions()
    {
        $params = session_get_cookie_params();
        if (!empty($params['secure']) && !empty($params['samesite']) && (strtolower($params['samesite']) === 'none')) {
            return $this;
        }

        $params['secure'] = true;
        $params['samesite'] = 'None';

        session_set_cookie_params($params);
        return $this;
    }

    /**
     * Handle PHP versions below PHP 7.3
     *
     * @return $this
     */
    protected function updateCookieParamsWithoutOptions()
    {
        $params = session_get_cookie_params();

        if (!empty($params['secure']) && !empty($params['path'])
            && (strpos($params['path'], 'SameSite') !== false)
        ) {
            return $this;
        }

        $params['secure'] = true;
        $params['path'] = empty($params['path']) ? '/' : $params['path'];
        if (strpos($params['path'], 'SameSite') === false) {
            $params['path'] .= '; SameSite=None';
        }

        session_set_cookie_params(
            $params['lifetime'],
            $params['path'],
            $params['domain'],
            !empty($params['secure']),
            !empty($params['httponly'])
        );

        return $this;
    }
}
