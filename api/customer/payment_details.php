<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_booking.php";
/*
 * Payment Details
 */

class Payment_Details {

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
                            'service_title' => $order_data->service_title
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
                        
                        $json['success']['booking_details']['invoice_dtls'] = '';
                        
                        $invoice_results = $booking->get_invoice_generated($order_id);
                        
                        if (mysqli_num_rows($invoice_results) > 0) {
                            $payment_total = $booking->get_app_invoice_details($order_id);
                            $row_pmnt_total = mysqli_fetch_object($payment_total);
                            
							$sql_inv11 = "SELECT * FROM ct_invoice WHERE order_id='{$order_id}'";
							$res_inv11 = mysqli_query($conn, $sql_inv11);
							$row_inv11 = mysqli_fetch_object($res_inv11);
							
                            $json['success']['booking_details']['invoice_dtls'] = array(
                                'invoice_generated' => TRUE,
                                'payment_total' => ($row_pmnt_total->amount - $row_inv11->default_discount_amount)
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

$booking_dtls = new Payment_Details();
$booking_dtls->index();