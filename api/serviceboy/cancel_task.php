<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../assets/lib/Firebase.php";
/*
 * cancel_task.php
 * Cancel Task by Service Boy 
 */

class Cancel_Task {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['staff_id']) && is_numeric($_POST['order_id'])) {

			/*
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    // POST values
                    $staff_id = trim($_POST['staff_id']);
                    $order_id = trim($_POST['order_id']);
                    // Remark
                    $remark = trim($_POST['remark']);
                    // Message
                    $message = "Task has been cancelled by service provider for order #{$order_id}";
                    
                    date_default_timezone_set('Asia/Kolkata');
                    
                    $current_time = date('Y-m-d H:i:s', time());
                    // GET the customer_id from bookings table
                    $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
                    $res_sel = mysqli_query($conn, $sql_sel);
                    // order data
                    $order_data = mysqli_fetch_object($res_sel);
                    // UPDATE the ct_bookings table regarding the cancellation process
                    // SET status to "CS" as it defined as in the system for Cancelled By Service Provider 
                    $sql_upd = "UPDATE ct_bookings SET booking_status='CS' WHERE order_id='{$order_id}'";
                    mysqli_query($conn, $sql_upd);
                    // INSERT into task booking history
                    $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$staff_id}', '{$order_data->client_id}', '{$message}', '{$current_time}')";
                    mysqli_query($conn, $sql_ins);
                    
                    // Send Notification to the customer
                    $firebase = new Firebase();
                    $firebase->set_api('AIzaSyDfRQ1UO-peN9EI2_QRe_MOZX67-XI9pz8');

                    // Get the customer Device Token
                    $sql_token = "SELECT token FROM ct_customer_device_token WHERE customer_id='{$order_data->client_id}'";
                    $res_token = mysqli_query($conn, $sql_token);
                    if (mysqli_num_rows($res_token)) {
                       $row_token = mysqli_fetch_object($res_token);
                    }

                    // data array to be sent to firebase server
                    $data = array(
                        'title' => 'Yupserve',
                        'request_id' => $order_id,
                        'message' => $message,
                        'action' => 'booking',
                    );
                    
                    $notif_array = array(
                        'title' => 'Yupserve',
                        'request_id' => $order_id,
                        'message' => $message,
                        'action' => 'booking',
                        'click_action' => 'FCM_PLUGIN_ACTIVITY'
                    );
                    
                    $firebase->send_to_single($row_token->token, $data, $notif_array);

                    // INSERT into customer notification table
                    $sql_ins_notif = "INSERT INTO ct_user_notification (user_id, order_id, text, is_read, created_at) VALUES('{$order_data->client_id}', '{$order_id}', '{$message}', '0', '{$current_time}')";
                     mysqli_query($conn, $sql_ins_notif);
                    
                    $json['success']['message'] = 'Task has been cancelled successfully';
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

$cancel_task = new Cancel_Task();
$cancel_task->index();
