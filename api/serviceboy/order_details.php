<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_staff_commision.php";
require_once "../../objects/class_booking.php";
/*
 * Booking Details
 */

class Order_Details {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['order_id'])) {

                    $order_id = (int) trim($_POST['order_id']);
			
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    $query = "SELECT * FROM ct_bookings WHERE order_id = {$order_id}";
                    $order_result = mysqli_query($conn,$query);
                    //echo mysqli_info($conn);
                   if (mysqli_num_rows($order_result) > 0) {

                        $bookingAddons = mysqli_query($conn,"SELECT cba.addons_service_id,csa.addon_service_name FROM `ct_booking_addons` cba JOIN ct_services_addon csa ON cba.addons_service_id=csa.id WHERE cba.order_id = {$order_id} GROUP BY cba.addons_service_id");
                        if(mysqli_num_rows($bookingAddons)>0){
                            # - Coupon value fatching
                            $coupon_data = mysqli_query($conn,"SELECT * FROM ct_default_coupon WHERE id = 1");
                            $coupon_details = mysqli_fetch_object($coupon_data);
                            $couponID = $coupon_details->id;
                            $couponValue = $coupon_details->value;
                            $json['success']['promo_code'] = $couponValue;
                            $json['success']['sub_service'] = array();
                            # - Coupon value fatching
                            while($addpnsData = mysqli_fetch_array($bookingAddons)){
                                $sub_service[] = array("sub_service_id" => $addpnsData['addons_service_id'],"sub_service_name"=> $addpnsData['addon_service_name']); 
                                //$sub_service_id[]  = "sub_service_id" => $addpnsData['addons_service_id'];
                                //$sub_service_name[]  = $addpnsData['addon_service_name'];
                            }
                            $json['success']['sub_service'] = $sub_service;
                            //$json['success']['sub_service']['sub_service_id'] = $sub_service_id;
                            //$json['success']['sub_service']['sub_service_name'] = $sub_service_name;
                        }else{
                            $json['error']['message'] = "Order id not found.";
                        }
                    } else {
                        $json['error']['message'] = "Order id not found.";
                    }
            }else {  
                $json['error']['message'] = "Parameters are missing";
            }
        } else {
            $json['error']['message'] = "The request type is not allowed";
        }

        echo json_encode($json);
    }

}

$order_dtls = new Order_Details();
$order_dtls->index();