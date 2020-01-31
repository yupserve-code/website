<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
/*
 * read_notification.php
 * Read Notification
 */

class Read_Notification {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['notification_id']) && !empty($_POST['customer_id'])) {
				
			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();

                    $customer_id = trim($_POST['customer_id']);
                    $notification_id = trim($_POST['notification_id']);

                    $sql = "UPDATE ct_user_notification SET is_read='1' WHERE id='{$notification_id}' AND user_id='{$customer_id}'";
                    mysqli_query($conn, $sql);
                    if (mysqli_affected_rows($conn)) {
                        $json['success']['message'] = "Read successfully";
                    } else {
                        $json['error']['message'] = "Not read";
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

$notification = new Read_Notification();
$notification->index();
