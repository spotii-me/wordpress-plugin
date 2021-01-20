<?php
/*
/* Spotii Authentications
*/
function spotiiAuth($th, $addon = "", $currency= null){

    $auth_url =  $th->auth . 'merchant/authentication/';
    if($th->enabled == "yes"){
        if ($currency == "SAR") {
            $public_key =  $th->testMode ? $th->testPublicKeySAR : $th->publicKeySAR;
            $private_key = $th->testMode ? $th->testPrivateKeySAR : $th->privateKeySAR;
        } else {
            $public_key =  $th->testMode ? $th->testPublicKeyAED : $th->publicKeyAED;
            $private_key = $th->testMode ? $th->testPrivateKeyAED : $th->privateKeyAED;
        }
        if(empty($public_key) || empty($private_key)){
            error_log("Keys does not exist [WP_Error_Spotii Authentication]: " . $response);
            throw new Exception(__('Keys does not exist'));
        }

        $headers = array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        );

        $body = array(
            'public_key' => $public_key,
            'private_key' => $private_key
        );

        $payload = array(
            'method' => 'POST',
            'headers' => $headers,
            'body' => wp_json_encode($body),
            'timeout' => 20
        );

        $response = wp_remote_post($auth_url, $payload);

        if (is_wp_error($response)) {
            error_log("Exception [WP_Error_Spotii Authentication]: " . $response);
            throw new Exception(__('Network connection issue'));
        }
        if (empty($response['body'])) {
            error_log("Response Body Empty [WP_Error_Spotii Authentication]: " . $response);
            throw new Exception(__('Empty response body'));
        }

        $response_body = $response['body'];
        $response_body_arr = json_decode($response_body, true);

        if (array_key_exists('token', $response_body_arr)) {
            $th->token = $response_body_arr['token'];
            return $response_body_arr['token'];
        } else {
            error_log("Error on authentication: " . $response_body);
        }
    }else{
        error_log("Response Body Empty [WP_Error_Spotii Authentication]: " . $response);
        throw new Exception(__('Plugin disabled'));
    }
}