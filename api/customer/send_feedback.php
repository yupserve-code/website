<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../assets/lib/Firebase.php";
require_once "../../objects/class_booking.php";
/*
 * send_feedback.php
 * Send feedback by Customer against an order
 */

class Send_Feedback {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['customer_id']) && !empty($_POST['order_id'])) {
			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    $booking = new cleanto_booking();
                    $booking->conn = $conn;
                    
                    // POST values
                    $customer_id = trim($_POST['customer_id']);
                    $order_id = trim($_POST['order_id']);
                    $rating = trim($_POST['rating']);
                    $text = trim($_POST['feedback']);
                            
                    date_default_timezone_set('Asia/Kolkata');
                    
                    $current_time = date('Y-m-d H:i:s', time());
                    
                    // INSERT into feedback table
                    $sql_ins = "INSERT INTO ct_feedback (order_id, customer_id, status, created_at, rating, feedback) VALUES ('{$order_id}', '{$customer_id}', '0', '{$current_time}', '{$rating}', '{$text}')";
                    mysqli_query($conn, $sql_ins);
                    
                    $json['success']['message'] = 'Feedback submitted successfully';
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

$payment = new Send_Feedback();
$payment->index();