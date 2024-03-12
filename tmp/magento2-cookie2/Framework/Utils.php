<?php

namespace Punchout\Cookie2\Framework;

class Utils
{
    static public function wrapCookieMetadata(array $metadataArray)
    {
        $metadataArray['secure'] = true;
        $metadataArray['path'] = empty($metadataArray['path']) ? '/' : $metadataArray['path'];

        //Magento\Framework\Stdlib\Cookie
        if (version_compare(PHP_VERSION, "7.3.0", ">=")) {
            $metadataArray['samesite'] = 'None';
        } elseif (strpos($metadataArray['path'], 'SameSite') === false) {
            $metadataArray['path'] .= '; SameSite=None';
        }

        return $metadataArray;
    }
    
    static public function wrapSessionOptions(array $options)
    {
        $options["session.cookie_secure"] = true;
    
        if (version_compare(PHP_VERSION, "7.3.0", ">=")) {
            $options['session.cookie_samesite'] = 'None';
        } elseif (isset($options['session.cookie_path']) && strpos(strtolower($options['session.cookie_path']), 'samesite') === false) {
            $options['session.cookie_path'] .= '; SameSite=None';
        } elseif (!isset($options['session.cookie_path'])) {
            $options['session.cookie_path'] .= '/; SameSite=None';
        }

        return $options;
    }
}
