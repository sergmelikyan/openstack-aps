<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
function p($text){ echo "<pre>"; print_r($text); echo "</pre>"; }
require_once 'controllers/OSApi.php';
echo "Test api Open Stack<br>";

$api = new OSApi('http://176.74.221.57', 'admin', 'parallels0');

//date_default_timezone_set("Europe/Madrid");
// 99623ab4985b41a0ad00ba5f31e07fa0
/*
//$query[] = array("field" => "timestamp", "op" => "ge", "value" => date("Y-m-d\T00:00:00", strtotime("yesterday")));
//$query[] = array("field" => "timestamp", "op" => "lt", "value" => date("Y-m-d\T00:00:00", strtotime("today")));
//p($api->getMeter("vcpus", $query, null));
p($api->getMeterStatistics("memory", $query, array("project_id"), null));
p($api->getMeterStatistics("disk.root.size", $query, array("project_id"), null));
p($api->getMeterStatistics("ip.floeating", $qury, array("project_id"), null));
p($api->getMeterStatistics("network.outgoing.bytes", $query, array("project_id"), null));
*/
//p($api->getImageDetails('b1159911-257f-4b3e-b4bc-426a5016565d'));
//p($api->getFloatingIpPools('285014fd197244489dfe71cf9bd076f4'));
//p($api->allocateFloatingIp('234c0a5d41d7495588edfd45681a9e2c',"Mamasu network"));
//p($api->getPorts('8caf661ea9894e3489981a5f4a29000e'));
//p($api->createFloatingIp('8caf661ea9894e3489981a5f4a29000e','167851a4-8cc5-44fd-a7a1-8193a7678f9f'));


/*$query = array();
$query[] = array("field" => "project_id", "op" => "eq", "value" => '2cda956c4b68446ab0e9053a432b03a1');
p($api->getMeterStatistics("vcpus", $query, array("project_id"), null));*/

p(array("hoal", "hola2"));