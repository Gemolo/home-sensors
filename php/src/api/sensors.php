<?php

use HomeSensors\PushUtils;

if($curl = curl_init('gpio:5000')) {
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    if(is_string($response)) {
        header("Content-Type: application/json");
        echo $response;
        exit(0);
    }
}
http_response_code(502);