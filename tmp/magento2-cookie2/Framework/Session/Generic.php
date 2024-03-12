<?php

namespace Punchout\Cookie2\Framework\Session;

/**
 * Magento session configuration
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Generic extends \Magento\Framework\Session\Generic
{
    /**
     * Configure session handler and start session
     *
     * @throws \Magento\Framework\Exception\SessionException
     * @return $this
     */
    public function start()
    {
        $this->updateCookieParams();
        return parent::start();
    }

    /**
     * Renew session id and update session cookie
     *
     * @return $this
     */
    public function regenerateId()
    {
        $this->updateCookieParams();
        return parent::regenerateId();
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
