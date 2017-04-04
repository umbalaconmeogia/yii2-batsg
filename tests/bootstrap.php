<?php
// ensure we get report on all possible php errors
error_reporting(E_ALL); // same meaning as error_reporting(-1)
// Beside, we could use error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once(__DIR__ . '/../vendor/autoload.php');