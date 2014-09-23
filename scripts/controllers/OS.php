<?php

require_once 'CurlManager.php';

class OS extends CurlManager {

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
        try {
            return isset($token->access->token->id);
        } catch (Exception $e) {
            return false;
        }
    }

    function getToken() {
        $data = array(
            "auth" => array("tenantName" => $this->user, "passwordCredentials" => array("username" => $this->user, "password" => $this->password))
        );
        return $this->send($this->url, 5000, "v2.0/tokens", "POST", $data);
    }

    function getSubnets() {
        $token = $this->getToken();
        $headers = array("X-Auth-Token: " . $token->access->token->id);
        return $this->send($this->url, 9696, "v2.0/subnets", "GET", array(), $headers);
    }

    function getProjects() {
        $token = $this->getToken();
        $headers = array("X-Auth-Token: " . $token->access->token->id);
        return $this->send($this->url, 5000, "v3/projects", "GET", array(), $headers);
    }

}
