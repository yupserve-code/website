<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
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

            if (!empty($_GET['staff_id'])) {

                $staff_id = (int) trim($_GET['staff_id']);

			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();

                    $sql = "SELECT * FROM ct_admin_info WHERE role='staff' AND id='{$staff_id}'";
                    $staff_result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($staff_result) > 0) {
                        
                        $staff_data = mysqli_fetch_object($staff_result);
                        
                        // BASE URL
                        $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                        $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';

                        $json['success']['customer_details'] = array(
                            'staff_id' => $staff_data->id,
                            'name' => $staff_data->fullname,
                            'email' => $staff_data->email,
                            'mobile' => $staff_data->phone
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
