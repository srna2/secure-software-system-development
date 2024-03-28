<?php

//this code is to find hash


// Your user's password
$password = "srnaamina";
// Creating a password hash
$hash = password_hash($password, PASSWORD_DEFAULT);
// Store this hash in your user database
echo $hash;
