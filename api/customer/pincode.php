<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
require_once "../config.php";
require_once "../../objects/class_setting.php";
/*
 * Pincode check
 */

class Pincode_Check {

    public function check_pincode() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['api_key']) && !empty($_POST['pincode']) && preg_match('/^[0-9]{6}$/', $_POST['pincode'])) {
				
			/*	
				
                $config = new API_Config();
                if ($config->check_valid_api_call(trim($_POST['api_key']))) {
					
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    $settings = new cleanto_setting();
                    $settings->conn = $conn;

                    if ($settings->get_option("ct_postalcode_status") == 'Y') {
                        $sql = "SELECT * FROM ct_settings WHERE option_name='ct_postal_code' AND FIND_IN_SET(" . trim($_POST['pincode']) . ", postalcode)";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            $json['success']['message'] = "Service available in this pincode";
                        } else {
                            $json['error']['message'] = "Service not available in this pincode";
                        }
                    } else {
                        $json['error']['message'] = "Zipcode service is not enabled";
                    }
				/*
                } else {
                    $json['error']['message'] = "Not authorized to access the API";
                }
				*/
            } else {
                $json['error']['message'] = "Parameters are missing";
            }
        } else {
            $json['error']['message'] = "The request type is not allowed";
        }

        echo json_encode($json);
    }

}

$pincode = new Pincode_Check();
$pincode->check_pincode();
