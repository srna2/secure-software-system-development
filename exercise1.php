<?php

class TextMessage {

    public function sendMessage($phone_number, $code) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://e1m383.api.infobip.com/sms/2/text/advanced',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"messages":[{"destinations":[{"to":'.$phone_number.'}],"from":"InfoSMS","text":'.$code.'}]}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: App d13023899beddeab8e628146943a2e8c-f7d26d92-c56d-4892-beab-6e53ed1d7dbf',
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        echo $response;    
    
    
    }        
}

# Now, let us call this function, so we can use it multiple times with different phone numbers and codes:
$response = new TextMessage();
$response->sendMessage('38761274724','1234');


?>