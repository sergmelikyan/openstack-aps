<?php

require "aps/2/runtime.php";
require "controllers/OS.php";

function logme($var) {
    error_log("\nOpen Stack ==> " . print_r($var, true));
}
