<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_userdetails.php";
/*
 * Profile.php
 * Load customer profile
 */

class Profile {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();
		
        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "GET")) {

            if (!empty($_GET['customer_id'])) {

                $customer_id = (int) trim($_GET['customer_id']);
			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();

                    $objuserdetails = new cleanto_userdetails();
                    $objuserdetails->conn = $conn;

                    $customer_result = $objuserdetails->get_customer_app_details($customer_id);
                    
                    if (mysqli_num_rows($customer_result) > 0) {
                        
                        $customer_data = mysqli_fetch_object($customer_result);
                        
                        // BASE URL
                        $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                        $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';

                        $json['success']['customer_details'] = array(
                            'customer_id' => $customer_data->id,
                            'first_name' => $customer_data->first_name,
                            'last_name' => $customer_data->last_name,
                            'email' => $customer_data->user_email,
                            'mobile' => $customer_data->phone,
                            'address' => $customer_data->address,
                            'state' => $customer_data->state,
                            'city' => $customer_data->city,
                            'pincode' => $customer_data->zip
                        );
                        
                    } else {
                        $json['error']['message'] = "Customer not found";
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

$profile = new Profile();
$profile->index();