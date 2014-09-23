<?php

require_once 'CurlParentModel.php';

/**
 * Description of OpenStackController
 *
 * @author Xavier Casahuga Altimiras
 */
class OpenStackV2Controller extends CurlParentModel {

    private $version = 'v2.0';

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

    /**
     * Given $username, $password and $tenantName the API returns a valid token
     * 
     * @param string $username
     * @param string $password
     * @param string $tenantName
     * @return array {success: true, resposneData:
     *                                   {"access":{"token":{"issued_at":"2014-09-17T09:45:26.668873",
     *                                          "expires":"2014-09-17T10:45:26Z",
     *                                          "id":"b0e5cb0ab16842e688dd2d43359c9b74"},
     *                                  "serviceCatalog":[],
     *                                  "user":{"username":"admin",
     *                                          "roles_links":[],
     *                                          "id":"fd1673ac1cb247baa18004e494db28e7",
     *                                          "roles":[],
     *                                          "name":"admin"},
     *                                  "metadata":{"is_admin":0,"roles":[]}}};}
     */
    public function authenticateAndGenerateToken($username = "", $password = "") {
        $requestParams = array("auth" =>
            array(
                "passwordCredentials" => "$username",
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
