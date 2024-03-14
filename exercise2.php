<?php

class CurlUtil {

    #1 - GET Request with Custom Headers

    public function getRequestWithCustomHeaders($phone_number, $header)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://enyddz14apqjk.x.pipedream.net',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '{"messages":[{"destinations":[{"to":'.$phone_number.'}],"from":"Test","text":'.$header.'}]}',
            CURLOPT_HTTPHEADER => array(
                'X-Custom-Header: Value1',
                'Authorization: Bearer YourToken'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    //______________________________________________________________________________

    #2 - POST Request with JSON Data

    public function postRequestWithJSONData() {
        $data = array(
            'name' => 'John',
            'email' => 'john@example.com'
        );

        $json = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://enyddz14apqjk.x.pipedream.net',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    //______________________________________________________________________________

    #3 DELETE Request

    public function deleteRequest()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://enyddz14apqjk.x.pipedream.net',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_CUSTOMREQUEST => 'DELETE'
        ));
        $response = curl_exec($curl);

        if($response === false) {
            die(curl_error($curl));
        }
        
        curl_close($curl);
        return $response;
    }

    //______________________________________________________________________________

    #4 - PUT Request with Form Data

    public function putRequestWithFormData() {
        $filedata = array(
            'name' => 'Jane',
            'email' => 'jane@example.com'
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://enyddz14apqjk.x.pipedream.net',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $filedata,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: aplication/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    //______________________________________________________________________________

    #5 - PATCH Request with Custom User Agent

    public function patchRequestWithCustomUserAgent() {
        $data = array (
            'status' => 'active'
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://enyddz14apqjk.x.pipedream.net',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                'User-Agent: MyCustomUserAgent/1.0'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    //______________________________________________________________________________

    #6 - PUT Request with JSON Data and Custom Headers

    public function putRequestWithJSONAndCustomHeaders() {
        $data = array (
            'theme' => 'dark',
            'notifications' => 'enabled'
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://enyddz14apqjk.x.pipedream.net',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURL_SETOPT($curl, CURLOPT_SSL_VERIFYPEER, 0),
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'X-Request-ID: 789',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}

//______________________________________________________________________________

# let us create an instance so we can call all the function:
$response = new CurlUtil();

#1 - GET Request with Custom Headers
$response -> getRequestWithCustomHeaders('38761274724', 'Some header');

#2 - POST Request with JSON Data
$response -> postRequestWithJSONData();

#3 - DELETE Request
$response -> deleteRequest();

#4 - PUT Request with Form Data
$response -> putRequestWithFormData();

#5 - PATCH Request with Custom User Agent
$response -> patchRequestWithCustomUserAgent();

#6 - PUT Request with JSON Data and Custom Headers
$response -> putRequestWithJSONAndCustomHeaders();

?>