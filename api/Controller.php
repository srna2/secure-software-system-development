<?php

namespace Sssd;

use OpenApi\Annotations as OA;
use Flight as Flight;

class Controller {

    /**
     * @OA\POST(
     *   path="/register",
     *   summary="Register User",
     *   description="Register User",
     *   tags={"Users"},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Provide All Info Below",
     *       @OA\JsonContent(
     *           required={"full_name", "username", "email", "password", "phone_number"},
     *           @OA\Property(property="full_name", type="string", format="text", example="Amina Srna"),
     *           @OA\Property(property="username", type="string", format="text", example="user"),
     *           @OA\Property(property="email", type="email", format="text", example="someuser@example.com"),
     *           @OA\Property(property="password", type="string", format="text", example="123456"),
     *           @OA\Property(property="phone_number", type="string", format="text", example="0611112223"),
     *       ),
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="User registered",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="false"),
     *           @OA\Property(property="message", type="string", example="User registered")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="User Not registered",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="true"),
     *           @OA\Property(property="message", type="string", example="User Not register")
     *       )
     *   )
     * )
     */
    
    public function register() {
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
      
        if (!is_null($mobileNumber) && preg_match('/^387\d{8,9}$/', $mobileNumber)) {
            echo "Mobile number is valid.\n";
        } else {
            echo "Mobile number is not valid.\n";
        }
    }

    /**
     * @OA\POST(
     *   path="/login",
     *   summary="Login User",
     *   description="Login User",
     *   tags={"Users"},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Provide All Info Below",
     *       @OA\JsonContent(
     *           required={"email","password"},
     *           @OA\Property(property="email", type="string", format="email", example="someuser@example.com"),
     *           @OA\Property(property="password", type="string", format="password", example="123456"),
     *       ),
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="User login",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="false"),
     *           @OA\Property(property="message", type="string", example="User register")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="User Not loged in",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="true"),
     *           @OA\Property(property="message", type="string", example="User Not loged in")
     *       )
     *   )
     * )
     */

    public function login() {
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

    }

}
