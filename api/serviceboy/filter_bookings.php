<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_staff_commision.php";
/*
 * filter_bookings.php
 * Filter Bookings
 */

class Filter_Bookings {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['staff_id'])) {

                $staff_id = (int) trim($_POST['staff_id']);
                if (!empty($_POST['page'])) {
                    $page = (int) trim($_POST['page']);
                } else {
                    $page = 1;
                }
                // per page 10 items to be loaded
                $per_page = 10;
                // Offset to be inceremented on page scroll down
                $offset = ($page - 1) * $per_page;
                
			/*	
                $header = new API_Header();

                $token = $header->get_auth_request_header_key();

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    $objuserdetails = new cleanto_staff_commision();
                    $objuserdetails->conn = $conn;
                    // BASE URL
                    $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                    $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';
                    
                    
                    $where = " WHERE staff_ids='{$staff_id}'";
                    
                    if (!empty($_POST['order_id'])) {
                        $order_id = trim($_POST['order_id']);
                        $where .= " AND b.order_id='{$order_id}'";
                    }
                    
                    if (!empty($_POST['service_id'])) {
                        $service_id = trim($_POST['service_id']);
                        $where .= " AND b.service_id='{$service_id}'";
                    }
                    
                    if (!empty($_POST['status'])) {
                        $status = trim($_POST['status']);
                        $where .= " AND b.booking_status='{$status}'";
                    }
                    
                    if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
                        $from_dt = trim($_POST['from_date']);
                        $to_dt = trim($_POST['to_date']);
                        $where .= " AND b.order_date >= '{$from_dt}' AND b.order_date <= '{$to_dt}'";
                    }
                    
                    $json['success']['bookings'] = array();
                    
                    // Get all the lists against the customer
                    $sql="SELECT b.id, b.order_id, b.service_id, b.order_date, b.booking_date_time, 
                        b.booking_status, s.title AS service_name, s.image AS service_image 
                        FROM ct_bookings b JOIN ct_services s ON b.service_id = s.id ";
                    $sql .= $where;
                    $sql .= " GROUP BY b.order_id ORDER BY b.id DESC 
                            LIMIT {$per_page} OFFSET {$offset}";
                            
                    $booking_results = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($booking_results) > 0) {
                        while ($booking = mysqli_fetch_object($booking_results)) {
                            $json['success']['bookings'][] = array(
                                'id' => $booking->id,
                                'order_id' => $booking->order_id,
                                'status' => $booking->booking_status,
                                'service_id' => $booking->service_id,
                                'service_name' => $booking->service_name,
                                'service_image' => ($booking->service_image == '') ? '' : $this->base_url . "assets/images/services/" . $booking->service_image,
                                'order_date' => $booking->order_date,
                                'date_time' => $booking->booking_date_time
                            );
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

}

$my_bookings = new Filter_Bookings();
$my_bookings->index();
