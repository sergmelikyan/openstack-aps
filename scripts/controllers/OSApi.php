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

    private function sendWithToken($url, $port, $api, $method, $parameters = array(), $header = null) {
        $token = $this->getToken();
        $headers[] = "X-Auth-Token: " . $token->access->token->id;
        if(!empty($header)) {
            $headers[] = $header;
        }
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
    
    function createImage($name, $location = null, $id = null, $visibility = null, $tags = null) {
        $data = array('name' => $name);
        if(!empty($id)) {
            $data['id'] = $id;
        }
        if(!empty($visibility)) {
            $data['visibility'] = $visibility;
        }
        if(!empty($tags)) {
            $data['tags'] = $tags;
        }
        return $this->sendWithToken($this->url, 9292, "/v2/images", "POST", $data, "Location:$location;");
    }
    
    function listImages() {
        return $this->sendWithToken($this->url, 9292, "/v2/images", "GET");
    }
    
    function getImageDetails($image_id) {
        return $this->send($this->url, 9292, "/v2/images/".$image_id, "GET");
    }
    
//    function updateImage($image_id) {
//        $data = array("tags" => array('test1', 'test2'));
//        return $this->send($this->url, 9292, "/v2/images/".$image_id, "PATCH");
//    }
    
    function deleteImage($image_id) {
        return $this->sendWithToken($this->url, 9292, "/v2/images".$image_id, "DELETE");
    }

}
