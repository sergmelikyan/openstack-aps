<?php

/**
 * Description of CurlParentModel
 *
 * @author Xavier Casahuga Altimiras
 */
class CurlParentModel {

    protected $endpoint;
    protected $RESTTypeSend;
    protected $RESTTypeRecived;

    /**
     * 
     * @param type $endpoint Is the base URL to the API endpoint ex: http://endpoint.org
     * @param type $RESTTypeSend Is the type of data that we want to send ex: 
     * @param type $RESTTypeRecived
     */
    public function __construct($endpoint, $RESTTypeSend = 'plain', $RESTTypeRecived = 'json') {
        if (strlen($endpoint) == 0){
            throw new Exception("Endpoint URL has to be defined!");
        }
        $this->endpoint = $endpoint;
        
        if ($this->endpoint[strlen($this->endpoint) - 1] != "/") {
            $this->endpoint = $this->endpoint . "/";
        }

        $this->RESTTypeSend = $RESTTypeSend;
        $this->RESTTypeRecived = $RESTTypeRecived;
    }

    public function sendGetRequest($url, $getArguments = false, $header = false) {
        $formatGetArguments = $this->formatArguments($getArguments);
        return $this->sendXRequest($url, $formatGetArguments, false, $header);
    }

    public function sendPostRequest($url, $getArguments = false, $postArguments = false, $header = false) {
        $formatGetArguments = $this->formatArguments($getArguments);
        $formatPostArguments = ($this->RESTTypeSend === 'plain') ? $this->formatArguments($postArguments) : json_encode($postArguments);
        return $this->sendXRequest($url, $formatGetArguments, $formatPostArguments, $header);
    }

    private function formatArguments($arguments) {
        $formatArguments = "";
        $ampersand = "";
        if (!is_array($arguments)) {
            return "";
        }
        foreach ($arguments as $key => $value) {
            $formatArguments .= $ampersand . $key . "=" . $value;
            $ampersand = "&";
        }
        return $formatArguments;
    }

    private function sendXRequest($url, $getArguments = false, $postArguments = false, $header = false) {
        try {
            $ch = curl_init();
            if ($header) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }
            if ($postArguments) {
                curl_setopt($ch, CURLOPT_POST, (count(explode("&", $postArguments)) + 1));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postArguments);
            }
            $finalurl = ($getArguments) ? $url . "?" . $getArguments : $url;
            curl_setopt($ch, CURLOPT_URL, $this->endpoint . $finalurl);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            if (curl_errno($ch)) { //There is one error            
                return array("success" => false, "responseData" => curl_error($ch));
            }
            curl_close($ch);
            return array("success" => true,
                "responseData" =>
                ($this->RESTTypeRecived === 'json') ? json_decode($result, true) : $result);
        } catch (Exception $e) {
            return array("success" => false,
                "responseData" => $e->getMessage());
        }
    }

}
