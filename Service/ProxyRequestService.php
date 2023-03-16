<?php
    
    namespace Develodesign\Punchout\Service;
    
    class ProxyRequestService
    {
    
        public function getRequestParam($requestParam)
        {
            $param = [];
            if (is_string($requestParam)) {
                $param = json_decode(base64_decode($requestParam));
            }
            return $param;
        
        }
        
    }
