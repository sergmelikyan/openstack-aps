<?php

require "aps/2/runtime.php";
require "controllers/OS.php";

date_default_timezone_set("UTC");

function logme($title, $var = null) {
    error_log("\n\nOpen Stack ==> [$title]" . (is_null($var) ? "" : json_encode($var, true)));
}
