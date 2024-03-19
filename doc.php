<?php
require("vendor/autoload.php");


$openapi = \OpenApi\Generator::scan(['/Applications/MAMP/htdocs/sssd-2024-21002997/api']);


header('Content-Type: application/json');
echo $openapi->toJson();