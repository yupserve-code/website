<?php
require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_setting.php";
require_once "../../objects/class_general.php";
require_once "../../objects/class_front_first_step.php";
/*
 * review_booking.php
 * Review Booking
 */

class Review_Booking {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');

        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['customer_id']) && !empty($_POST['date']) && !empty($_POST['time_slot']) && !empty($_POST['service_id'])) {

			/*
                $header = new API_Header();
                // API Token
                $token = $header->get_auth_request_header_key();

                $config = new API_Config();
                // Check valid API call from valid resource
                if ($config->check_valid_api_call(trim($token))) {
					
			*/		
                    // Database
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                    $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';

                    $service_id = trim($_POST['service_id']);
                    $srv_sql = "SELECT * FROM ct_services WHERE id='{$service_id}'";
                    
                    $srv_res = mysqli_query($conn, $srv_sql);
                    
                    if (mysqli_num_rows($srv_res) > 0) {
                        $service = mysqli_fetch_object($srv_res);
                        
                        $json['success']['service_dtls'] = array(
                            'service_id' => $service->id,
                            'title' => $service->title,
                            'image' => ($service->image != '') ? $this->base_url . "assets/images/services/" . $service->image : ''
                        );
                    }

                    $sub_srv_arr = (array) json_decode($_POST['sub_services'], true);
                    
                    foreach ($sub_srv_arr as $sub_service) {
                        
                        $sql_sub_srv = "SELECT * FROM ct_services_addon WHERE id='{$sub_service['sub_service_id']}'";
                        $sub_srv_res = mysqli_query($conn, $sql_sub_srv);
                        
                        $sub_service = mysqli_fetch_object($sub_srv_res);
                        
                        $json['success']['sub_services'][] = array(
                            'sub_service_id' => $sub_service->id,
                            'title' => $sub_service->addon_service_name,
                            'image' => ($sub_service->image == '') ? $this->base_url . 'assets/images/addons-images/' . $sub_service->predefine_image : $this->base_url . 'assets/images/services/' . $sub_service->image
                        );
                    }

                    $json['success']['time_slot'] = array(
                        'date' => trim($_POST['date']),
                        'time_slot' => trim($_POST['time_slot'])
                    );
					
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

$review_booking = new Review_Booking();
$review_booking->index();
