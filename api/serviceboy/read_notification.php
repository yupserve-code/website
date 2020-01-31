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

            if (!empty($_POST['notif_id']) && !empty($_POST['staff_id'])) {
			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();

                    $staff_id = trim($_POST['staff_id']);
                    $notif_id = trim($_POST['notif_id']);

                    $sql = "UPDATE ct_staff_notification SET is_read='1' WHERE id='{$notif_id}' AND staff_id='{$staff_id}'";
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