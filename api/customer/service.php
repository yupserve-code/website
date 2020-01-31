<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
require_once "../config.php";
require_once "../header.php";
require_once "../../objects/class_services.php";
require_once "../../objects/class_setting.php";

/*
 * Service.php
 * To get all the service list
 */

class Service {

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "GET")) {

			/*
            if (new API_Header ()) {

                $header = new API_Header();

                $token = $header->get_auth_request_header_key();
                $check = new API_Config();
                if ($check->check_valid_api_call(trim($token))) {
			*/
                    if (!empty($_GET['page'])) {
                        $page = (int) trim($_GET['page']);
                    } else {
                        $page = 1;
                    }
                    // per page 10 items to be loaded
                    $per_page = 10;
                    // Offset to be inceremented on page scroll down
                    $offset = ($page - 1) * $per_page;

                    $con = new cleanto_db();
                    $conn = $con->connect();

                    $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                    $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';

                    $settings = new cleanto_setting();
                    $settings->conn = $conn;

                    $objservice = new cleanto_services();

                    $objservice->conn = $conn;
                    $services_data = $objservice->customer_app_services($per_page, $offset);

                    $json['success']['services'] = array();

                    if (mysqli_num_rows($services_data) > 0) {
                        while ($service = mysqli_fetch_array($services_data)) {
                            if ($settings->get_option('ct_company_service_desc_status') != "" &&
                                    $settings->get_option('ct_company_service_desc_status') == "Y") {

                                $json['success']['services'][] = array(
                                    'service_id' => $service['id'],
                                    'title' => $service['title'],
                                    'image' => ($service['image'] != '') ? $this->base_url . "assets/images/services/" . $service['image'] : ''
                                );
                            }
                        }
                    }
                /*
				} else {
                    $json['error']['message'] = "Not authorized to access the API";
                }
				*/
			/*
            } else {
                $json['error']['message'] = "Parameters are missing";
            }
			*/
			
        } else {
            $json['error']['message'] = "The request type is not allowed";
        }

        echo json_encode($json);
    }

}

$service = new Service();
$service->index();
