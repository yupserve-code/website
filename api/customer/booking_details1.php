<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_booking.php";
/*
 * Booking Details
 */

class Booking_Details {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "GET")) {

            if (!empty($_GET['order_id'])) {

                $order_id = (int) trim($_GET['order_id']);

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

                    $order_result = $booking->get_customer_app_booking_details($order_id);

                    if (mysqli_num_rows($order_result) > 0) {

                        $order_data = mysqli_fetch_object($order_result);
                        // Order Details
                        $json['success']['booking_details']['order_details'] = array(
                            'id' => $order_data->id,
                            'order_id' => $order_data->order_id,
                            'order_date' => $order_data->order_date,
                            'booking_datetime' => $order_data->booking_date_time,
                            'service_id' => $order_data->service_id,
                            'service_title' => $order_data->service_title,
                            'booking_status' => $order_data->booking_status
                        );
                        // Customer details
                        // Get the booking address by customer end
                        $res_addr = $booking->get_order_client_info($order_id);
                        $row_addr = mysqli_fetch_object($res_addr);
                        
                        $address = unserialize(base64_decode($row_addr->client_personal_info));
                        
                        $json['success']['booking_details']['customer_details'] = array(
                            'customer_id' => $order_data->client_id,
                            'customer_name' => $order_data->customer_name,
                            'email' => $order_data->user_email,
                            'phone' =>$order_data->phone,
                            'zip' => $address['zip'],
                            'address' => $address['address'],
                            'state' => $address['state'],
                            'city' => $address['city'],
                        );
                        
                        // Service details
                        $service_results = $booking->get_customer_service_addon_bookings($order_id);
                        
                        if (mysqli_num_rows($service_results) > 0) {
                            while($sub_service = mysqli_fetch_object($service_results)) {
                                
                                $json['success']['booking_details']['sub_services'][] = array(
                                    'service_id' => $order_data->service_id,
                                    'service_title' => $order_data->service_title,
                                    'sub_service_id' => $sub_service->addons_service_id,
                                    'sub_service_title' => $sub_service->sub_service_name
                                );
                            }
                        }
                        
                        // Order Booking History
                        $json['success']['booking_details']['history'] = array();
                        
                        $booking_history = $booking->get_booking_history($order_id);
                        
                        if (mysqli_num_rows($booking_history) > 0) {
                            while ($row_history = mysqli_fetch_object($booking_history)) {
                                $json['success']['booking_details']['history'][] = array(
                                    'history_id' => $row_history->id,
                                    'order_id' => $row_history->order_id,
                                    'staff_id' => $row_history->staff_id,
                                    'customer_id' => $row_history->customer_id,
                                    'status' => $row_history->status,
                                    'created_at' => $row_history->created_at,
                                );
                            }
                        }
                        
                        // Service Boy Details
                        $json['success']['booking_details']['staff_dtls'] = '';
                        
                        $srv_boy_res = $booking->get_booking_assigned_staff($order_id);
                        
                        if (mysqli_num_rows($srv_boy_res) > 0) {
                            $srv_boy_dtls = mysqli_fetch_object($srv_boy_res);
                            
                            $sql_rating = "SELECT (CASE WHEN AVG(rating) IS NULL THEN 0 ELSE AVG(rating) AS staff_rating FROM ct_rating_review WHERE staff_id='{$srv_boy_dtls->staff_ids}'";
                            $rating_result = mysqli_query($conn, $sql_rating);
                            
                            if (mysqli_num_rows($rating_result) > 0) {
                                $s_rating = mysqli_fetch_object($rating_result);
                                $staff_ratings = $s_rating->staff_rating;
                            } else {
                                $staff_ratings = 0;
                            }
                            
                            $json['success']['booking_details']['staff_dtls'] = array(
                                'staff_id' => $srv_boy_dtls->staff_ids,
                                'staff_name' => $srv_boy_dtls->fullname,
                                'staff_phone' => $srv_boy_dtls->phone,
                                'rating' => $staff_ratings,
                                'image' => $srv_boy_dtls->image
                            );
                        }
                        
                        // OTP details
                        $json['success']['booking_details']['booking_otp'] = '';
                        
                        $otp_results = $booking->get_booking_otp_details($order_id);
                        
                        if (mysqli_num_rows($otp_results) > 0) {
                            $booking_otp = mysqli_fetch_object($otp_results);
                            
                            $json['success']['booking_details']['booking_otp'] = array(
                                'order_id' => $order_id,
                                'otp' => $booking_otp->otp,
                                'staff_id' => $booking_otp->staff_id,
                                'customer_id' => $booking_otp->customer_id,
                                'created_at' => $booking_otp->created_at,
                                'expired_at' => $booking_otp->expired_at,
                                'verified' => ($booking_otp->verified == 1) ? TRUE : FALSE
                            );
                            if ($booking_otp->verified == 1) {
                                $tsk_started = TRUE;
                            } else {
                                $tsk_started = FALSE;
                            }
                        } else {
                            $tsk_started = FALSE;
                        }
                        
                        $json['success']['booking_details']['extra_requirements'] = '';
                        
                        $json['success']['booking_details']['extra_images'] = array();
                        
                        $sql_tsk_upd = "SELECT * FROM ct_booking_extra_requirements WHERE order_id='{$order_id}'";
                        $task_upd_result = mysqli_query($conn, $sql_tsk_upd);
                        
                        if (mysqli_num_rows($task_upd_result) > 0) {
                            $row_upd_rqmnt = mysqli_fetch_object($task_upd_result);
                            
                            $json['success']['booking_details']['extra_requirements'] = array(
                                'id' => $row_upd_rqmnt->id,
                                'order_id' => $row_upd_rqmnt->order_id,
                                'staff_id' => $row_upd_rqmnt->staff_id,
                                'requirements' => $row_upd_rqmnt->requirements,
                                'customer_id' => $row_upd_rqmnt->customer_id,
                                'approved' => $row_upd_rqmnt->approved,
                                'rejected' => $row_upd_rqmnt->rejected,
                                'created_at' => $row_upd_rqmnt->created_at
                            );
                            
                            $sql_images = "SELECT * FROM ct_booking_extra_requirement_images WHERE order_id='{$order_id}'";
                            $res_images = mysqli_query($conn, $sql_images);
                            
                            if (mysqli_num_rows($res_images) > 0) {
                                
                                while($row_images = mysqli_fetch_object($res_images)) {
                                    
                                    $json['success']['booking_details']['extra_images'][] = array(
                                        'id' => $row_images->id,
                                        'requirement_id' => $row_images->requirement_id,
                                        'order_id' => $row_images->order_id,
                                        'image' => $row_images->image,
                                        'created_at' => $row_images->created_at
                                    );
                                }
                            }
                            
                            $tsk_upd_rqmnt = TRUE;
                            if ($row_upd_rqmnt->approved == 1) {
                                $tsk_upd_accepted = TRUE;
                                $tsk_upd_rejected = FALSE;
                            } else if ($row_upd_rqmnt->rejected == 1) {
                                $tsk_upd_accepted = FALSE;
                                $tsk_upd_rejected = TRUE;
                            } else {
                                $tsk_upd_accepted = FALSE;
                                $tsk_upd_rejected = FALSE;
                            }
                        } else {
                            $tsk_upd_rqmnt = FALSE;
                            $tsk_upd_accepted = FALSE;
                            $tsk_upd_rejected = FALSE;
                        }
                        
                        $sql_tsk_end = "SELECT * FROM ct_booking_task_end WHERE order_id='{$order_id}'";
                        
                        $task_end_result = mysqli_query($conn, $sql_tsk_end);
                        
                        if (mysqli_num_rows($task_end_result) > 0) {
                            $row_end = mysqli_fetch_object($task_end_result);
                            $tsk_end_rqst = TRUE;
                            if ($row_end->verified == 1) {
                                $tsk_end_accepted = TRUE;
                            } else {
                                $tsk_end_accepted = FALSE;
                            }
                        } else {
                            $tsk_end_rqst = FALSE;
                            $tsk_end_accepted = FALSE;
                        }
                        
                        $sql_tsk_pmnt = "SELECT * FROM ct_payments WHERE order_id='{$order_id}'";
                        
                        $tsk_pmnt_res = mysqli_query($conn, $sql_tsk_pmnt);
                        
                        if (mysqli_num_rows($tsk_pmnt_res) > 0) {
                            $row_pmnt = mysqli_fetch_object($tsk_pmnt_res);
                            if ($row_pmnt->staff_received == 1) {
                                $pmnt_rcvd = TRUE;
                            } else {
                                $pmnt_rcvd = FALSE;
                            }
                        } else {
                            $pmnt_rcvd = FALSE;
                        }
                        
                        $json['success']['booking_details']['task_stats'] = array(
                            'task_started' => $tsk_started,
                            'task_update_requested' => $tsk_upd_rqmnt,
                            'task_update_accepted' => $tsk_upd_accepted,
                            'task_update_rejected' => $tsk_upd_rejected,
                            'task_end_requested' => $tsk_end_rqst,
                            'task_end_accepted' => $tsk_end_accepted,
                            'payment_received' => $pmnt_rcvd
                        );
                        
                        $json['success']['booking_details']['invoice_dtls'] = '';
                        
                        $invoice_results = $booking->get_invoice_generated($order_id);
                        
                        if (mysqli_num_rows($invoice_results) > 0) {
                            $payment_total = $booking->get_app_invoice_details($order_id);
                            $row_pmnt_total = mysqli_fetch_object($payment_total);
                            
                            $json['success']['booking_details']['invoice_dtls'] = array(
                                'invoice_generated' => TRUE,
                                'payment_total' => ($row_pmnt_total->amount)
                            );
                        }
                        
                        // Payment details
                        $json['success']['booking_details']['payment_info'] = '';
                        
                        $payment_results = $booking->get_booking_payment_info($order_id);
                        
                        if (mysqli_num_rows($payment_results) > 0) {
                            $payment_info = mysqli_fetch_object($payment_results);
							
							$sql_inv1 = "SELECT * FROM ct_invoice WHERE order_id='{$order_id}'";
							$res_inv1 = mysqli_query($conn, $sql_inv1);
							$row_inv1 = mysqli_fetch_object($res_inv1);
                            
                            $json['success']['booking_details']['payment_info'] = array(
                                'order_id' => $order_id,
                                'payment_method' => $payment_info->payment_method,
                                'amount' => $payment_info->amount,
                                'taxes' => $payment_info->taxes,
                                'discount' => ($payment_info->discount + $row_inv1->default_discount_amount),
                                'partial_amount' => $payment_info->partial_amount,
                                'payment_date' => $payment_info->payment_date,
                                'net_amount' => ($payment_info->net_amount - $row_inv1->default_discount_amount),
                                'payment_status' => $payment_info->payment_status
                            );
                        }
                    } else {
                        $json['error']['message'] = "Order not found";
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

$booking_dtls = new Booking_Details();
$booking_dtls->index();