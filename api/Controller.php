<?php

namespace Sssd;
include 'vendor/autoload.php';

use OpenApi\Annotations as OA;
use Flight as Flight;
use OTPHP\TOTP;

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
        if (!ctype_alnum($username)) {
            echo "Username can only include alphanumeric characters, no special characters or spaces are allowed.\n";
            die;
        }
      
        //Validate against a list of “reserved” names (prevent admin etc..)
      
        $invalidUsernames = array("admin", "root", "superuser", "testuser",);
      
        if (in_array($username, $invalidUsernames)) {
            echo "You can not use reserved names. Invalid username\n";
            die;
        }
      
        //PASSWORD VALIDATION:
      
        //Should be at least 8 characters long.
        
        if (mb_strlen($password)<8) {
            echo "Password must be at least 8 characters long\n";
            die;
        }

        //HIBP
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
        } else {
            echo "Password is not found in the pwned database.";
        }


        //EMAIL ADDRESS VALIDATION:
      
        //Needs to follow a valid email format (example@domain.com).
      
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Email address is not valid.\n";
            die;
        }
      
        //PHONE NUMBER VALIDATION:
      
        //Must be a mobile number
      
        if (!is_null($mobileNumber) && !preg_match('/^387\d{8,9}$/', $mobileNumber)) {
            echo "Mobile number is not valid.\n";
            die;
        }

        // A random secret will be generated from this.
        // You should store the secret with the user for verification.
        $otp = TOTP::generate();
        echo "The OTP secret is: {$otp->getSecret()}\n";
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
        $username = Flight::request()->data->username;
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

        // Verify the password against the hash
        if (password_verify($data['password'], $storedHash)) {
            echo 'Password is valid!';
        } else {
            echo 'Invalid password.';
            die;
        }

        if (!$user['firstlogin']) {
            $secret = $user['secretotp'];
            $otp = TOTP::createFromSecret($secret);
            $otp->setLabel('amina@sssdotp');
            $grCodeUri = $otp->getQrCodeUri(
                'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
                '[DATA]'
            );
            echo "<img src='{$grCodeUri}'>";
        
            // DISPLAY QR CODE
            echo "Scan the QR code to complete your first login: $grCodeUri";
        
            $updateStmt = $this->conn->prepare("UPDATE users SET firstlogin = 1 WHERE username = :username");
            $updateStmt->bindParam(':username', $username);
            $updateStmt->execute();
        
            return;
        
        } else {
            $input_otp = $data['otp'] ?? '';
            $secret = $user['secretotp'];
            $otp = TOTP::createFromSecret($secret);
            if ($otp->verify($input_otp)) {
                echo "Welcome back, " . $user['username'] . "!";
            } else {
                echo "Invalid OTP.";
            }
        }
        

        echo  "Login successful.";
    }
}
