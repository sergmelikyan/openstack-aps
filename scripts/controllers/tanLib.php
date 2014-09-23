<?php

//getToken();
//projects();
//validateToken();
subnets();



function sendPostData($url, $post, $headers, $method) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    curl_close($ch);  // Seems like good practice
    return $result;
}

function getToken() {
    $data = array(
        "auth" => array("tenantName" => "admin", "passwordCredentials" => array("username" => "admin", "password" => "parallels0"))
    );
    $headers = array('Content-Type: application/json');
    send("v2.0/tokens", $data, $headers, "POST", "5000");
}

function projects() {
    $data = array(
    );
    $headers = array('Content-Type: application/json', 'X-Auth-Token: 97775cb6177c4ddbade7d3af61b298e1');
    send("v3/projects", $data, $headers, "GET", "5000");
}

//function validateToken() {
//    $data = array(
//    );
//    $headers = array('Content-Type: application/json', 'X-Auth-Token: 97775cb6177c4ddbade7d3af61b298e1', 'belongsTo: 99623ab4985b41a0ad00ba5f31e07fa0');
//    send("v2.0/tokens/97775cb6177c4ddbade7d3af61b298e1", $data, $headers, "GET", "5000");
//}

function subnets() {
    $data = array(
    );
    $headers = array('Content-Type: application/json', 'X-Auth-Token: 97775cb6177c4ddbade7d3af61b298e1');
    send("v2.0/subnets", $data, $headers, "GET", "9696");
}
