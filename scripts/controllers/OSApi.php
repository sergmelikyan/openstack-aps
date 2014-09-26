<?php

require_once 'CurlManager.php';

class OSApi extends CurlManager {

    private $url = null;
    private $user = null;
    private $password = null;
    private $token = null;

    function __construct($url, $user, $password) {
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
    }

    function isReachable() {
        $token = $this->getToken();
        $return = array(
            'success' => false,
            'responseData' => ""
        );
        if (isset($token->access->token->id)) {
            $return['success'] = true;
        } else {
            if (isset($token->error->message)) {
                $return['responseData'] = $token->error->message;
            } else {
                $return['responseData'] = $token;
            }
        }

        return $return;
    }

    private function sendWithToken($url, $port, $api, $method, $parameters = array()) {
        $token = $this->getToken();
        $headers = array("X-Auth-Token: " . $token->access->token->id);
        return $this->send($url, $port, $api, $method, $parameters, $headers);
    }

    function getToken() {
        $data = array(
            "auth" => array("tenantName" => $this->user, "passwordCredentials" => array("username" => $this->user, "password" => $this->password))
        );
        return $this->send($this->url, 5000, "v2.0/tokens", "POST", $data);
    }

    function getSubnets() {
        return $this->sendWithToken($this->url, 9696, "v2.0/subnets", "GET");
    }

    function getNetworks() {
        return $this->sendWithToken($this->url, 9696, "v2.0/networks", "GET");
    }

    function getNetwork($networkId) {
        return $this->sendWithToken($this->url, 9696, "v2.0/networks/" . $networkId, "GET");
    }

    function getProjects() {
        return $this->sendWithToken($this->url, 5000, "v3/projects", "GET");
    }

}
