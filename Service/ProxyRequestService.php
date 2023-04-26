<?php

namespace Develodesign\Punchout\Service;

class ProxyRequestService
{
    public function getRequestParam($requestParam)
    {
        $param = [];
        if (is_string($requestParam)) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $param = json_decode(base64_decode($requestParam));
            // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
        }
        return $param;
    }

    public function isTokenExpired(string $tokenExpiryDate): bool
    {
        $tokenExpiryDate = new \DateTime($tokenExpiryDate);
        return $tokenExpiryDate > $this->getCurrentTimeStamp();
    }

    public function getCurrentTimeStamp()
    {
        $timestamp = new \DateTime('now', new \DateTimeZone('UTC'));
        $timestamp->format('Y-m-d H:i:s');
        return $timestamp;
    }
}
