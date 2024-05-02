<?php

namespace Sssd;

//include '../vendor/autoload.php';

use OpenApi\Annotations as OA;
use Flight as Flight;
use OTPHP\TOTP;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberType;



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
     *           required={"fullName", "username", "email", "password", "mobileNumber"},
     *           @OA\Property(property="fullName", type="string", format="text", example="Amina Srna"),
     *           @OA\Property(property="username", type="string", format="text", example="aminasrna123"),
     *           @OA\Property(property="email", type="email", format="text", example="someuser@example.com"),
     *           @OA\Property(property="password", type="string", format="text", example="123456"),
     *           @OA\Property(property="mobileNumber", type="string", format="text", example="0611112223"),
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
        $fullName = Flight::request()->data->fullName;
        $username = Flight::request()->data->username;
        $password = Flight::request()->data->password;
        $email = Flight::request()->data->email;
        $mobileNumber = Flight::request()->data->mobileNumber;

        
        // VALIDATIONS


        // USERNAME VALIDATION: Should be longer than 3 characters.

        if (mb_strlen($username) < 3 ) {
            Flight::json(["message" => "Username must be at least three characters long\n"], 400);
            return;
        }
      
        // Can only include alphanumeric characters (letters and numbers), no special characters or spaces are allowed.
        if (!ctype_alnum($username)) {
            echo "Username can only include alphanumeric characters, no special characters or spaces are allowed.\n";
            die;
        }
      
        // Validate against a list of “reserved” names (prevent admin etc..)
        $invalidUsernames = array("admin", "root", "superuser", "testuser",);
      
        if (in_array($username, $invalidUsernames)) {
            echo "You can not use reserved names. Invalid username\n";
            die;
        }
      
        // PASSWORD VALIDATION: Should be at least 8 characters long.
      
        if (mb_strlen($password)<8) {
            Flight::json(["message" => "Password must be at least 8 characters long"], 400);
            return;
        }

        // HIBP
        
        $sha1Password = strtoupper(sha1($password));
        $prefix = substr($sha1Password, 0, 5);
        $suffix = substr($sha1Password, 5);

        $ch = curl_init("https://api.pwnedpasswords.com/range/".$prefix);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            // Handle error; the request failed
            exit('Could not retrieve data from the API.');
        }

        if (str_contains($response, $suffix)) {
            echo "Password found. STOP";
            die;
        }
        
        // EMAIL ADDRESS VALIDATION:

        $validTlds = ['BA', 'COM', 'NET'];
        if (!$this->tldValidation($email, $validTlds)) {
            echo "Invalid TLD in email address";
            return;
        }

        if (!$this->mxValidation($email)) {
            echo "No valid MX records found for the email domain";
            return;
        }
      
        // PHONE NUMBER VALIDATION:
        if (!is_null($mobileNumber) && !$this->phoneValidation($mobileNumber)) {
            Flight::json(["message" => "Mobile number is not valid."], 400);
            return;
        }


        // A random secret will be generated from this.
        $otp = TOTP::generate();
        $secret = $otp->getSecret();
        echo "The OTP secret is: {$secret}\n";


        // Generate QR code URI
        $otp->setLabel($username); // Set label as username or any identifier
        $grCodeUri = $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=' . $secret . '&size=300x300&ecc=M',
            $secret
        );

        echo "<img src='{$grCodeUri}'>";

        // $code = Flight::request()->data->code;
        // if (!$otp->verify($code)) {
        //     Flight::json(["error" => "Invalid code provided"], 400);
        //     return;
        // }



        // ALL ABOUT DATABASE

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $connection = require __DIR__ . "/database.php";

        // Save the secret to the user table
        $connection = require __DIR__ . "/database.php";
        $sql = "INSERT INTO users (fullName, username, email, passwordHash, mobileNumber, secret) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            die("SQL error: " . $connection->error);
        }

        // Bind parameters and execute
        $result = $stmt->execute([$fullName, $username, $email, $passwordHash, $mobileNumber, $secret]);


        if ($result) {
            Flight::json(["message" => "User registered successfully"]);
        } else {
            Flight::json(["error" => "User registration failed"]);
        }

        $stmt = null;


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
     *           @OA\Property(property="password", type="string", format="password", example="srnaamina123"),
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
        $storedHash = '$2y$10$W9pDq1ACsNZYBUGoJ9fvSORD1KNz7NuYcBRApkM2ygRMuwO.or1su';
  
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

        // Retrieve hashed password from the database based on provided username or email
             
        $username = $data['username'];
        $email = $data['email'];

        // Database query to get the hashed password
        $connection = require __DIR__ . "/database.php";
        $sql = "SELECT passwordHash FROM users WHERE username = :username OR email = :email";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['username' => $username, 'email' => $email]);
        $storedHash = $stmt->fetchColumn();
        
        if (!$storedHash) {
            echo '{"error": "Invalid username/email."}';
            return;
        }
        
        // Verify the password against the hash
        if (password_verify($data['password'], $storedHash)) {
            echo '{"message": "Login successful."}';
        } else {
            echo '{"error": "Invalid password."}';
        }
        
    }

    private function phoneValidation($phoneNumber) {
        $phoneUtil = PhoneNumberUtil::getInstance();
        
        try {
            $numberProto = $phoneUtil->parse($phoneNumber, "BA"); 
            $numberType = $phoneUtil->getNumberType($numberProto);
            
            return $numberType === PhoneNumberType::MOBILE;
        } catch (\libphonenumber\NumberParseException $e) {
            return false;
        }
    }

    private function tldValidation($email) {
        $validTLDs = array('com', 'net', 'org', 'info', 'ba');
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false; 
        }
        $domainParts = explode('.', $parts[1]);
        $tld = end($domainParts);
        return in_array($tld, $validTLDs);
    }

    private function mxValidation($email) {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }
        $domain = $parts[1];

        return getmxrr($domain, $mx_details);
    }
}





  


