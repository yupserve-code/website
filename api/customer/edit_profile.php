<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_setting.php";
require_once "../../objects/class_general.php";
/*
 * edit_profile.php
 * Edit Profile
 */

class Edit_Profile {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['customer_id']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) && !empty($_POST['mobile']) && preg_match('/^[0-9]{10}$/', $_POST['mobile']) && !empty($_POST['address']) && !empty($_POST['pincode']) && preg_match('/^[0-9]{6}$/', $_POST['pincode']) && !empty($_POST['state']) && !empty($_POST['city'])) {


				/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
				*/
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    $customer_id = trim($_POST['customer_id']);
                    $first_name = trim($_POST['first_name']);
                    $last_name = trim($_POST['last_name']);
                    $email = trim($_POST['email']);
                    $mobile = trim($_POST['mobile']);
                    $address = trim($_POST['address']);
                    $pincode = trim($_POST['pincode']);
                    $phone = "+91" . $mobile;
                    $state = trim($_POST['state']);
                    $city = trim($_POST['city']);
                    
                    // check if another user exists using the same mobile number
                    $sql_chk = "SELECT * FROM ct_users WHERE phone='{$phone}' AND id <> '{$customer_id}'";
                    $chk_res = mysqli_query($conn, $sql_chk);
                    if (mysqli_num_rows($chk_res) > 0) {
                        $json['error']['message'] = "Mobile number exists for another customer";
                    }
                    
                    // check if another user exists using the same mobile number
                    $sql_chk2 = "SELECT * FROM ct_users WHERE user_email='{$email}' AND id <> '{$customer_id}'";
                    $chk_res2 = mysqli_query($conn, $sql_chk2);
                    if (mysqli_num_rows($chk_res2) > 0) {
                        $json['error']['message'] = "Email exists for another customer";
                    }

                    if (empty($json['error'])) {
                        // UPDATE into database users table
                        $sql_upd = "UPDATE ct_users SET first_name='{$first_name}', last_name='{$last_name}', user_email='{$email}', phone='{$phone}', zip='{$pincode}', address='{$address}', state='{$state}', city='{$city}'  WHERE id='{$customer_id}'";

                        mysqli_query($conn, $sql_upd);
                        if (mysqli_affected_rows($conn)) {
                            $json['success']['message'] = "Profile updated successfully";
                        } else {
                            $json['error']['message'] = "Profile not updated";
                        }
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

$edit_profile = new Edit_Profile();
$edit_profile->index();
