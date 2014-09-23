<?php

require_once 'CurlParentModel.php';

/**
 * Description of OpenStackV3Controller
 *
 * @author Jepi Humet Alsius
 */
class OpenStackV3Controller extends CurlParentModel {

    private $version = 'v3';

    public function __construct($endpoint) {
        parent::__construct($endpoint, 'json');
    }

    /**
     * Given response from API query is parsed in order to return a common struct 
     * with success status and responseData with error happened or information 
     * returned by function.
     * 
     * @param array $response
     * @return array {success: [true|false], responseData: [object|string]}
     */
    private function parseResponse($response) {
        if (isset($response['responseData']['error'])) {
            if (isset($response['responseData']['error']['message'])) {
                $message = $response['responseData']['error']['message'];
            } else {
                $message = $response['responseData'];
            }
            return array("success" => false, "responseData" => $message);
        } else {
            return $response;
        }
    }

    
    public function authenticateAndGenerateToken($username = "", $password = "") {
        $requestParams = array("auth" =>
            array(
                "passwordCredentials" =>
                array(
                    "username" => "$username",
                    "password" => "$password"
                )
            )
        );
        try {
            $responseIdentify = $this->sendPostRequest($this->version . '/tokens', false, $requestParams, array('Content-Type: application/json'));
            return $this->parseResponse($responseIdentify);
        } catch (Exception $e) {
            return array("success" => false, "responseData" => $e->getMessage());
        }
    }

    public function listIPPools($token) {
        try {
            $responseListIPPools = $this->sendPostRequest($this->version . '/subnets', false, array(), array('Content-Type: application/json', 'X-Auth-Token:' . $token));
            return $this->parseResponse($responseListIPPools);
        } catch (Exception $e) {
            return array("success" => false, "responseData" => $e->getMessage());
        }
    }

}
