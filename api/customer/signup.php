<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_services.php";
require_once "../../objects/class_setting.php";
require_once "../../objects/class_services_addon.php";
require_once "../../objects/class_general.php";
require_once "../../assets/lib/Sinfini_sms.php";
/*
 * signup.php
 * Signup
 */

class Signup {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) && !empty($_POST['mobile']) && preg_match('/^[0-9]{10}$/', $_POST['mobile']) && !empty($_POST['address']) && !empty($_POST['pincode']) && !empty($_POST['state']) && !empty($_POST['city'])) {

			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();

                    $first_name = trim($_POST['first_name']);
                    $last_name = trim($_POST['last_name']);
                    $email = trim($_POST['email']);
                    $mobile = trim($_POST['mobile']);
                    $address = trim($_POST['address']);
                    $pincode = trim($_POST['pincode']);
                    $phone = "+91" . $mobile;
                    $state = trim($_POST['state']);
                    $pwd_text = "12345678";
                    $password = md5($pwd_text);
                    $usertype = serialize(array('client'));
                    $city = trim($_POST['city']);
                    $date_time = date('Y-m-d H:i:s', time());

                    // check email already exists
                    $sql_email = "SELECT * FROM ct_users WHERE user_email='{$email}'";
                    $result_email = mysqli_query($conn, $sql_email);
                    if (mysqli_num_rows($result_email) > 0) {
                        $json['error']['message'] = "Email already exists";
                    }

                    // check phone already exists
                    $sql_mobile = "SELECT * FROM ct_users WHERE phone='{$phone}'";
                    $result_mobile = mysqli_query($conn, $sql_mobile);
                    if (mysqli_num_rows($result_mobile) > 0) {
                        $json['error']['message'] = "Mobile already exists";
                    }

                    if (empty($json['error'])) {
                        // INSERT into database
                        $sql_ins = "INSERT INTO ct_users (first_name, last_name, user_email, user_pwd, phone, zip, address, state, city, vc_status, p_status, status, usertype, cus_dt) VALUES ("
                                . "'{$first_name}', '{$last_name}', '{$email}', '{$password}', '{$phone}', '{$pincode}', '{$address}', '{$state}', '{$city}', '-', '-', 'E', '{$usertype}','{$date_time}'"
                                . ")";
                        mysqli_query($conn, $sql_ins);
                        if (mysqli_affected_rows($conn)) {
                            // Last Inserted ID
                            $user_id = mysqli_insert_id($conn);


                            //OTP Storage
                            $otp = $this->generate_otp(4);
                            // Save OTP Data
                            $created_at = date('Y-m-d H:i:s', time());
                            $extime = $this->getCurrentTime();
                            $query3 = mysqli_query($conn,"SELECT * FROM ct_user_reg_otp WHERE mobile_num ='{$phone}'");
							
                            if(mysqli_num_rows($query3) > 0){
								
                                $query = "UPDATE ct_user_reg_otp SET otp = '{$otp}',created_at='{$created_at}',expired_at='{$extime}' WHERE mobile_num = '{$phone}'";
								
                                mysqli_query($conn, $query);
								
                            } else {
								
                                $query = "INSERT into ct_user_reg_otp (`mobile_num`,`otp`, `verify_status`, `created_at`, `expired_at`) values('{$phone}','{$otp}',0,'{$created_at}','{$extime}')";
                                
                                mysqli_query($conn, $query);
                            }
							
                            $message = "Yup-Serve : Your OTP verification code is {$otp}";
                            $sms_api = new Sinfini_sms();
                            $sms_api->send($phone, $message);
                            // OTP Storage

                            $current_time = date('Y-m-d H:i:s', time());
                            // Check if Device ID is there or not
                            if (!empty($_POST['device_id'])) {
                                $token = trim($_POST['device_id']);
                                // INSERT into 
                                $sql_dvc_ins = "INSERT INTO ct_customer_device_token (customer_id, token, created_at) VALUES ('{$user_id}', '{$token}', '{$current_time}')";
                                mysqli_query($conn, $sql_dvc_ins);
                            }
                            
                            $json['success']['customer_details'] = array(
                                'customer_id' => $user_id
                            );
                            $json['success']['message'] = "Registered successfully";
                        } else {
                            $json['error']['message'] = "Not registered";
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

    public function generate_otp($n) {
        // all numeric digits 
        $generator = "1357902468";
        // Iterate for n-times and pick a single character 
        // from generator and append it to $result 
        $result = "";
        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }
        // Return result 
        return $result;
    }
	
	// GET 15 minute interval

    public function getCurrentTime() {

        // 15 Minute
        return date("Y-m-d H:i:s", time() + 900);

    }
}

$signup = new Signup();
$signup->index();
