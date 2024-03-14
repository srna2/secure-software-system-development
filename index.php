<?php

// If you're using Composer, require the autoloader.
require 'vendor/autoload.php';


// Then define a route and assign a function to handle the request.
Flight::route('/', function () {
  echo 'hello world!';
});



// - POST /register (should return “hello from register”)
// - POST /login (should return “hello from login”)


// Flight::route(' /register', function () {
//   $username = Flight::request()->data->username;
//   echo 'hello from' .$username. 'register!';

// });

//I had to change this route to login1 because in Postman it continuously executes this GET route instead of POST even though I change request option 

Flight::route(' /login1', function () {
  echo 'hello from login!';
});

Flight::route('POST /register', function() {

  $username = Flight::request()->data->username;
  $password = Flight::request()->data->password;
  $email = Flight::request()->data->email;
  $mobileNumber = Flight::request()->data->mobileNumber;

  //USERNAME VALIDATION:

  // Should be longer than 3 characters.
  if (mb_strlen($username) < 3 ) {
      Flight::json(["message" => "Username must be at least three characters long\n"], 400);
      return;
  }

  // Can only include alphanumeric characters (letters and numbers), no special characters or spaces are allowed.
  if (ctype_alnum($username)) {
      echo "Username is valid.\n";
  } else {
      echo "Username can only include alphanumeric characters, no special characters or spaces are allowed.\n";
  }

  //Validate against a list of “reserved” names (prevent admin etc..)

  $invalidUsernames = array("admin", "root", "superuser", "testuser",);

  if (in_array($username, $invalidUsernames)) {
    echo "You can not use reserved names. Invalid username\n";
  }

  //PASSWORD VALIDATION:

  //Should be at least 8 characters long.
  
  if (mb_strlen($password)<8) {
    Flight::json(["message" => "Password must be at least 8 characters long\n"], 400);
  } else {
    echo "Password is valid.\n";
  }

  //EMAIL ADDRESS VALIDATION:

  //Needs to follow a valid email format (example@domain.com).

  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Email address is valid.\n";
  } else {
    echo "Email address is not valid.\n";
  }

  //PHONE NUMBER VALIDATION:

  //Must be a mobile number

  if (preg_match('/^387\d{8,9}$/', $mobileNumber)) {
    echo "Mobile number is valid.\n";
  } else {
    echo "Mobile number is not valid.\n";
}

});

//LOGIN ENDPOINT

Flight::route('POST /login', function () {
  $data = Flight::request()->data;
  
  if (!isset($data['username']) && !isset($data['email'])) {
    Flight::json(["error" => "Username/email is required."], 400);
    return;
  }
  
  if (!isset($data['password'])) {
    Flight::json(["error" => "Password is required."], 400);
    return;
  }

  if (($data['username'] === "" && isset($data['password'])) || ($data['password'] === "" && isset($data['username']))) {
    Flight::json(["error" => "Username/email and password cannot be empty."], 400);
    return;
  }

  Flight::json(["message" => "Login successful."]);

});


// Finally, start the framework.
Flight::start();