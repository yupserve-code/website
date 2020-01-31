<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_setting.php";
/*
 * verify_otp.php
 * Verify OTP
 */

class Verify_OTP {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['otp']) && preg_match('/^[0-9]{4}$/', $_POST['otp']) && !empty($_POST['staff_id'])) {

			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    $staff_id = trim($_POST['staff_id']);
                    $otp = trim($_POST['otp']);
                    
                    $sql = "SELECT * FROM ct_staff_otp WHERE staff_id='{$staff_id}'";
                    $result = mysqli_query($conn, $sql);
                    date_default_timezone_set('Asia/Kolkata');

                    $current_time = date('Y-m-d H:i:s', time());
                    
                    if (mysqli_num_rows($result) > 0) {
                        
                        $user_data = mysqli_fetch_object($result);
                        
                        if (($user_data->otp === $otp) && ($current_time < $user_data->expired_at)) {
                            
                            $current_time = date('Y-m-d H:i:s', time());
                            // Check if Device ID is there or not
                            if (!empty($_POST['device_id'])) {
                                $token = trim($_POST['device_id']);
                                // check if the customer_id data is already there in the table
                                $sql_device = "SELECT * FROM ct_staff_device_token WHERE staff_id='{$staff_id}'";
                                $res_device = mysqli_query($conn, $sql_device);
                                if (mysqli_num_rows($res_device) > 0) {
                                    $sql_dvc_upd = "UPDATE ct_staff_device_token SET token='{$token}', created_at='{$current_time}' WHERE staff_id='{$staff_id}'";
                                    mysqli_query($conn, $sql_dvc_upd);
                                } else {
                                    // INSERT into ct_staff_device_token
                                    $sql_dvc_ins = "INSERT INTO ct_staff_device_token (staff_id, token, created_at) VALUES ('{$staff_id}', '{$token}', '{$current_time}')";
                                    mysqli_query($conn, $sql_dvc_ins);
                                }
                            }
                            
                            $json['success']['message'] = 'Login successfully';
                            $json['success']['staff_details'] = array(
                                'staff_id' => $user_data->staff_id
                            );
                        } else {
                            $json['error']['message'] = "Invalid OTP";
                        }
                    } else {
                        $json['error']['message'] = "Invalid OTP";
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

$otp = new Verify_OTP();
$otp->index();