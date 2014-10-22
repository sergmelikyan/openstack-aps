<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'controllers/OSApi.php';

echo "Test api Open Stack<br>";

$api = new OSApi('http://176.74.221.57', 'admin', 'parallels0');

//$createTenant = $api->createTenant();

//var_export($createTenant);


//var_export($api->createImage("Image1", 'c:shit'));
//var_export($api->listImages());
//var_export($api->updateImage('5230fb9c-275b-4808-80c3-93006188e447'));
//var_export($api->deleteImage('5230fb9c-275b-4808-80c3-93006188e447'));

