<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
require_once "../config.php";
require_once "../../objects/class_services.php";
require_once "../../objects/class_setting.php";
require_once "../../objects/class_userdetails.php";

class Home {

    public $base_url, $connection;

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        //if ($_SERVER['REQUEST_METHOD'] == "POST") {

            if (isset($_POST['customer_id'])) {
                
                //$config = new API_Config();
                
                //if ($config->check_valid_api_call(trim($_POST['api_key']))) {

                    $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                    $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';

                    $cvars = new cleanto_myvariable();
                    $host = trim($cvars->hostnames);
                    $un = trim($cvars->username);
                    $ps = trim($cvars->passwords);
                    $db = trim($cvars->database);
                    
                    $customer_id = trim($_POST['customer_id']);
                    
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    $settings = new cleanto_setting();
                    $settings->conn = $conn;
                    
                    $objservice = new cleanto_services();
                    
                    $objservice->conn = $conn;
                    $services_data = $objservice->customer_app_home_services();
                    
                    $objuserdetails = new cleanto_userdetails();
                    $objuserdetails->conn = $conn;

                    $customer_result = $objuserdetails->get_customer_app_details($customer_id);
                    
                    $json['success']['customer_dtls'] = '';
                    
                    if (mysqli_num_rows($customer_result) > 0) {
                        $customer_data = mysqli_fetch_object($customer_result);
                        $json['success']['customer_dtls'] = array(
                            'customer_id' => $customer_data->id,
                            'name' => $customer_data->first_name . " " . $customer_data->last_name
                        );
                    }
                    
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
                //} else {
                //    $json['error']['message'] = "Not authorized to access the API";
                //}
            } else {
                $json['error']['message'] = "Parameters are missing";
            }
        //} else {
        //    $json['error']['message'] = "The request type is not allowed";
        //}

        echo json_encode($json);
    }

}

$home = new Home();
$home->index();