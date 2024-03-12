<?php

namespace Punchout\Cookie2\Stdlib\Cookie;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

//Check if PHP already supports the samesite option (PHP 7.3+)- but Magento version is not, check if M2 is below EE/CE 2.4.3
$shouldDeclareMethods = (version_compare(PHP_VERSION, "7.3.0", ">=")
    && !method_exists(\Magento\Framework\Stdlib\Cookie\CookieMetadata::class,'getSameSite')
);

//It's a kind of patch for Magento
if ($shouldDeclareMethods) {
    abstract class PhpCookieManagerAbstract extends PhpCookieManager
    {
        /**
         * Set a value in a cookie with the given $name $value pairing.
         *
         * @param string $name
         * @param string $value
         * @param array $metadataArray
         * @return void
         * @throws FailureToSendException If cookie couldn't be sent to the browser.
         * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
         * @throws InputException If the cookie name is empty or contains invalid characters.
         */
        protected function setCookie($name, $value, array $metadataArray)
        {
            $expire = $this->computeExpirationTime($metadataArray);

            $this->checkAbilityToSendCookie($name, $value);

            $phpSetcookieSuccess = setcookie(
                $name,
                $value,
                [
                    'expires' => $expire,
                    'path' => $this->extractValue(CookieMetadata::KEY_PATH, $metadataArray, ''),
                    'domain' => $this->extractValue(CookieMetadata::KEY_DOMAIN, $metadataArray, ''),
                    'secure' => $this->extractValue(CookieMetadata::KEY_SECURE, $metadataArray, false),
                    'httponly' => $this->extractValue(CookieMetadata::KEY_HTTP_ONLY, $metadataArray, false),
                    'samesite' => $this->extractValue('samesite', $metadataArray, 'Lax')
                ]
            );

            if (!$phpSetcookieSuccess) {
                $params['name'] = $name;
                if ($value == '') {
                    throw new FailureToSendException(
                        new Phrase('The cookie with "%name" cookieName couldn\'t be deleted.', $params)
                    );
                } else {
                    throw new FailureToSendException(
                        new Phrase('The cookie with "%name" cookieName couldn\'t be sent. Please try again later.', $params)
                    );
                }
            }
        }

        /** Copy Paste of all related private methods */
        /**
         * Retrieve the size of a cookie.
         * The size of a cookie is determined by the length of 'name=value' portion of the cookie.
         *
         * @param string $name
         * @param string $value
         * @return int
         */
        private function sizeOfCookie($name, $value)
        {
            // The constant '1' is the length of the equal sign in 'name=value'.
            return strlen($name) + 1 + strlen($value);
        }

        /**
         * Determines whether or not it is possible to send the cookie, based on the number of cookies that already
         * exist and the size of the cookie.
         *
         * @param string $name
         * @param string|null $value
         * @return void if it is possible to send the cookie
         * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
         * @throws InputException If the cookie name is empty or contains invalid characters.
         */
        private function checkAbilityToSendCookie($name, $value)
        {
            if ($name == '' || preg_match("/[=,; \t\r\n\013\014]/", $name)) {
                throw new InputException(
                    new Phrase(
                        'Cookie name cannot be empty and cannot contain these characters: =,; \\t\\r\\n\\013\\014'
                    )
                );
            }

            $numCookies = count($_COOKIE);

            if (!isset($_COOKIE[$name])) {
                $numCookies++;
            }

            $sizeOfCookie = $this->sizeOfCookie($name, $value);

            if ($numCookies > static::MAX_NUM_COOKIES) {
//                $this->logger->warning(
//                    new Phrase('Unable to send the cookie. Maximum number of cookies would be exceeded.'),
//                    array_merge($_COOKIE, ['user-agent' => $this->httpHeader->getHttpUserAgent()])
//                );
            }

            if ($sizeOfCookie > static::MAX_COOKIE_SIZE) {
                throw new CookieSizeLimitReachedException(
                    new Phrase(
                        'Unable to send the cookie. Size of \'%name\' is %size bytes.',
                        [
                            'name' => $name,
                            'size' => $sizeOfCookie,
                        ]
                    )
                );
            }
        }

        /**
         * Determines the expiration time of a cookie.
         *
         * @param array $metadataArray
         * @return int in seconds since the Unix epoch.
         */
        private function computeExpirationTime(array $metadataArray)
        {
            if (isset($metadataArray[PhpCookieManager::KEY_EXPIRE_TIME])
                && $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME] < time()
            ) {
                $expireTime = $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME];
            } else {
                if (isset($metadataArray[CookieMetadata::KEY_DURATION])) {
                    $expireTime = $metadataArray[CookieMetadata::KEY_DURATION] + time();
                } else {
                    $expireTime = PhpCookieManager::EXPIRE_AT_END_OF_SESSION_TIME;
                }
            }

            return $expireTime;
        }

        /**
         * Determines the value to be used as a $parameter.
         * If $metadataArray[$parameter] is not set, returns the $defaultValue.
         *
         * @param string $parameter
         * @param array $metadataArray
         * @param string|boolean|int|null $defaultValue
         * @return string|boolean|int|null
         */
        private function extractValue($parameter, array $metadataArray, $defaultValue)
        {
            if (array_key_exists($parameter, $metadataArray)) {
                return $metadataArray[$parameter];
            } else {
                return $defaultValue;
            }
        }
    }
} else {
    abstract class PhpCookieManagerAbstract extends PhpCookieManager
    {
    }
}
