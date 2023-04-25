<?php

    namespace Develodesign\Punchout\Service;

    use Zend_Validate;

class OciService
{
    const REQUIRED_PARAMS = ['username','password','hook_url'];
    
    const CONFIGURED_REQUIRED_FIELDS = [
        '~okcode',
        '~caller',
        'oci_version'
    ];

    public function prepareOCIRequestBody(array $params): array
    {
        return array_combine(
            array_map('strtolower', array_keys($params)),
            array_values($params)
        );
    }
    
    /**
     * @param array $params
     *
     * @return false|array
     */
    public function validateRequest(array $params)
    {
        $resultReqExist = $this->requiredParamKeysExists(array_keys($params), self::REQUIRED_PARAMS);
        if ($resultReqExist !== true) {
            return false;
        }
        return $params;
    }
    
    /**
     * @param $param
     *
     * @return array|string
     */
    public function validateSetupConfiguredParam(array $params)
    {
        $errors = [];
            
        foreach (self::CONFIGURED_REQUIRED_FIELDS as $key) {
            if (!isset($params[$key])) {
                $errors [] = $key;
            }
        }
        if (count($errors) > 0) {
            return sprintf('%s, Field(s) are required to perform this action', implode(",", $errors));
        }
        return $params;
    }
    
    /**
     * @throws \Zend_Validate_Exception
     */
    public function isValidEmail(string $email)
    {
        return Zend_Validate::is($email, 'EmailAddress');
    }

    private function requiredParamKeysExists(array $params, array $requiredParams): bool
    {
        return count(array_intersect($params, $requiredParams)) === count($requiredParams);
    }
    
    public function getFirstLastName(string $name)
    {
        $nameArray = [];
        preg_match('/^(.+) ([^ ]+)$/', $name, $s);
        if (count($s) > 2) {
            $nameArray['first_name'] = $s[1];
            $nameArray['last_name'] = $s[2];
        } else {
            $nameArray['first_name'] = $name;
            $nameArray['last_name'] = 'Punchout User';
        }
        return $nameArray;
    }
}
