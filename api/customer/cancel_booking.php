<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../assets/lib/Firebase.php";
/*
 * accept_end_task.php
 * Accept End Task by Customer 
 */

class Cancel_Booking {

    public function __construct() {
        
    }

    public function index() {

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache');

        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['customer_id']) && !empty($_POST['order_id']) && !empty($_POST['remark'])) {

                /*
                  $header = new API_Header();

                  $token = $header->get_auth_request_header_key();

                  $config = new API_Config();

                  if ($config->check_valid_api_call(trim($token))) {

                 */

                $con = new cleanto_db();
                $conn = $con->connect();
                // POST values
                $customer_id = trim($_POST['customer_id']);
                $order_id = trim($_POST['order_id']);
                $remark = trim($_POST['remark']);

                // GET the customer_id from bookings table
                $sql_check = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}' AND staff_ids <> ''";
                $res_check = mysqli_query($conn, $sql_check);

                if (mysqli_num_rows($res_check) > 0) {
                    $json['error']['message'] = "Booking has been assigned to Service Partner. Cannot be cancelled";
                } else {
                    // Message
                    $message = "Customer cancelled the booking for order #{$order_id}";

                    date_default_timezone_set('Asia/Kolkata');

                    $current_time = date('Y-m-d H:i:s', time());
                    // GET the customer_id from bookings table
                    $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
                    $res_sel = mysqli_query($conn, $sql_sel);
                    // order data
                    $order_data = mysqli_fetch_object($res_sel);

                    // UPDATE table ct_bookings and set booking_status to 'CC' completed
                    $sql_book_upd = "UPDATE ct_bookings SET booking_status='CC', cancel_remark='{$remark}' 
                                    WHERE order_id='{$order_id}'";
                    mysqli_query($conn, $sql_book_upd);

                    // INSERT into task booking history
                    $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$order_data->staff_ids}', '{$customer_id}', '{$message}', '{$current_time}')";
                    mysqli_query($conn, $sql_ins);

                    // Send Notification to the customer
                    $firebase = new Firebase();
                    $firebase->set_api('AIzaSyDfRQ1UO-peN9EI2_QRe_MOZX67-XI9pz8');

                    // Get the staff Device Token
                    $sql_token = "SELECT token FROM ct_staff_device_token WHERE staff_id='{$order_data->staff_ids}'";
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

                    // send push notification
                    $firebase->send_to_single($row_token->token, $data, $notif_array);

                    // INSERT into staff notification table
                    $sql_ins_notif = "INSERT INTO ct_staff_notification (staff_id, order_id, text, is_read, created_at) VALUES('{$order_data->staff_ids}', '{$order_id}', '{$message}', '0', '{$current_time}')";
                    mysqli_query($conn, $sql_ins_notif);

                    // INSERT into admin notification
                    $sql_adm_not = "INSERT INTO ct_admin_notification (order_id, text, created_at) VALUES ('{$order_id}', '{$message}', '{$current_time}')";
                    mysqli_query($conn, $sql_adm_not);

                    $json['success']['message'] = 'Booking has been cancelled successfully';
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

$cancel_task = new Cancel_Booking();
$cancel_task->index();
