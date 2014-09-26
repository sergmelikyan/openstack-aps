<?php

class CurlManager {

    /**
     * 
     * @param type $url
     * @param type $port
     * @param type $api
     * @param type $parameters
     * @param type $headers
     * @param type $method
     * @return type
     */
    protected function send($url, $port, $api, $method, $parameters = array(), $headers = array()) {
        $url_send = $url . ":" . $port . "/" . $api;
        $str_data = json_encode($parameters);
        $headers[] = 'Content-Type: application/json';
        $result = $this->sendPostData($url_send, $str_data, $headers, $method);
        return json_decode($result);
    }

    private function sendPostData($url, $post, $headers, $method) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) { //There is one error            
            return curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

}
