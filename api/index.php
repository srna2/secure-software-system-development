<?php

require '../vendor/autoload.php';

use Sssd\Controller;


// Flight::route('/', function () {
//   echo 'hello world!';
// });


// Flight::route(' /register', function () {
//   $username = Flight::request()->data->username;
//   echo 'hello from' .$username. 'register!';

// });


Flight::route('POST /register', function() {
  $controller = new Controller();
  $controller -> register();

});

//LOGIN ENDPOINT

Flight::route('POST /login', function () {
  $controller = new Controller();
  $controller -> login();

});


// Finally, start the framework.
Flight::start();