<?php
    include 'config.php';

    function authenticate(){
        $headers = getallheaders();
        if(isset($headers['Authorization'])){
            $apiKey = $headers['Authorization'];
            if($apiKey === API_KEY){
                return true;
            } else {
                http_response_code(401);
                echo json_encode(array("message" => "Unauthorized. Invalid API Key."));
                exit;
            }
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Unauthorized. No API Key provided."));
            exit;
        }
    }
?>