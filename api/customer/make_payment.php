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
 * make_payment.php
 * Make payment by Customer 
 */

class Make_Payment {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['customer_id']) && !empty($_POST['order_id']) && !empty($_POST['payment_method'])) {

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
                    // Message
                    $message = "Cutomer paid for order #{$order_id}";
                    
                    date_default_timezone_set('Asia/Kolkata');
                    
                    $current_time = date('Y-m-d H:i:s', time());
                    // GET the customer_id from bookings table
                    $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
                    $res_sel = mysqli_query($conn, $sql_sel);
                    // order data
                    $order_data = mysqli_fetch_object($res_sel);
                    
                    // INSERT into task booking history
                    $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$order_data->staff_ids}', '{$customer_id}', '{$message}', '{$current_time}')";
                    mysqli_query($conn, $sql_ins);
                    
                    if (trim($_POST['payment_method']) == 'cash') {
                        $payment_method = "Pay At Venue";
                    } else {
                        $payment_method = "Online";
                    }
                    
                    $current_date = date('Y-m-d', time());
                    
                    $amount = 0;
                    $discount = 0;
                    $tax = 0;
                    $partial_amnt = 0;
                    $net_amnt = 0;
                    $freq_dscnt = 'O';
                    $freq_dscnt_amnt = 0;
                    $total_amount = 0;
                    $total_tax = 0;
                    
                    $invoice_results = $booking->get_invoice_generated($order_id);
                    
                    if (mysqli_num_rows($invoice_results) > 0) {
                        $payment_total = $booking->get_payment_details($order_id);
                        $row_pmnt_total = mysqli_fetch_object($payment_total);
						
						$sql_inv1 = "SELECT * FROM ct_invoice WHERE order_id='{$order_id}'";
						$res_inv1 = mysqli_query($conn, $sql_inv1);
						$row_inv1 = mysqli_fetch_object($res_inv1);
						
                        $amount .= $row_pmnt_total->total;
                        $tax .= $row_pmnt_total->total_tax;
						$discount = $row_pmnt_total->total_discount;
                        $net_amnt .= $row_pmnt_total->amount;
                    }
                    
                    // INSERT Into ct_payments table
                    $sql_ins_pmnt = "INSERT INTO ct_payments (
                        order_id, payment_method, transaction_id, amount, discount, taxes, 
                        partial_amount, payment_date, net_amount, lastmodify, frequently_discount,
                        frequently_discount_amount, recurrence_status, payment_status
                        ) 
                     VALUES (
                     '{$order_id}', '{$payment_method}', '', '{$amount}', '{$discount}', '{$tax}',
                     '{$partial_amnt}', '{$current_date}', '{$net_amnt}', '{$current_time}', '{$freq_dscnt}',
                     '{$freq_dscnt_amnt}', 'N', 'Completed'
                     )";
                    mysqli_query($conn, $sql_ins_pmnt);
                    
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
                    
                    $json['success']['message'] = 'Payment processed successfully';
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

$payment = new Make_Payment();
$payment->index();