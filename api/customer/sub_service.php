<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
require_once "../config.php";
require_once "../header.php";
require_once "../../objects/class_services.php";
require_once "../../objects/class_setting.php";
require_once "../../objects/class_services_addon.php";
require_once "../../objects/class_general.php";

/*
 * Sub-Service.php
 * To get all the sub-service list
 */

class Sub_Service {

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "GET")) {

            if (!empty($_GET['service_id'])) {

                if (!empty($_GET['page'])) { 
                    $page = (int) trim($_GET['page']);
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
                $check = new API_Config();
                if ($check->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();

                    $addons = new cleanto_services_addon();
                    $addons->conn = $conn;

                    $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                    $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';

                    $settings = new cleanto_setting();
                    $settings->conn = $conn;

                    $symbol_position = $settings->get_option('ct_currency_symbol_position');
                    $decimal = $settings->get_option('ct_price_format_decimal_places');

                    $general = new cleanto_general();
                    $general->conn = $conn;

                    $addons->service_id = trim($_GET['service_id']);
                    $addons_data = $addons->get_customer_app_sub_service($per_page, $offset);

                    $json['success']['services'] = array();
                    
                    if (mysqli_num_rows($addons_data) > 0) {
                        while ($service = mysqli_fetch_array($addons_data)) {
                            if ($service['image'] == '') {
                                $image = $this->base_url . 'assets/images/addons-images/' . $service['predefine_image'];
                            } else {
                                $image = $this->base_url . 'assets/images/services/' . $service['image'];
                            }

                            $json['success']['services'][] = array(
                                'service_id' => $service['service_id'],
                                'sub_service_id' => $service['id'],
                                'title' => $service['addon_service_name'],
                                //'price' => $general->ct_price_format($service['base_price'], $symbol_position, $decimal),
                                'image' => $image
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

$service = new Sub_Service();
$service->index();
