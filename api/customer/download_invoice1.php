<?php

// API config
require_once "../config.php";

require_once '../../objects/class_connection.php';
require_once '../../assets/pdf/tfpdf/tfpdf.php';
require_once '../../objects/class_booking.php';
require_once '../../objects/class_setting.php';
require_once '../../objects/class_services.php';
require_once '../../objects/class_services_methods.php';
require_once '../../objects/class_services_methods_units.php';
require_once '../../objects/class_services_addon.php';
require_once '../../objects/class_users.php';
require_once '../../objects/class_front_first_step.php';
require_once '../../objects/class_order_client_info.php';
require_once '../../objects/class_payments.php';
require_once '../../objects/class_general.php';
/*
 * download_invoice.php
 * Download Invoice
 */

class Download_Invoice {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/pdf; charset=utf-8');
		header('Cache-Control: no-cache');

        $json = array();
        
        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "GET")) {

            if (!empty($_GET['order_id']) && !empty($_GET['api_key'])) {

                $token = trim($_GET['api_key']);

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {

                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
                        $protocol = 'https';
                    } else {
                        $protocol = 'http';
                    }

                    $cur_dirname = basename(__DIR__);
                    if ($cur_dirname == 'public_html') {
                        $cur_dirname = '';
                    }
                    $cur_dir = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], $cur_dirname)) . "/";
                    $dots = explode(".", $_SERVER['HTTP_HOST']);
                    if (sizeof($dots) > 2 && $dots[0] != 'www' && strlen($dots[1]) > 3) {
                        //define("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"] . '/');
                        define("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"] . '/');
                        define("BASE_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
                        define("SITE_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
                        define("AJAX_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . '/assets/lib/');
                        define("FRONT_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . '/front/');
                    } else {
                        define("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"] . '/');
                        define("BASE_URL", substr($cur_dir, 0, -1));
                        define("SITE_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
                        define("AJAX_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . $cur_dir . 'assets/lib/');
                        define("FRONT_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . $cur_dir . 'front/');
                    }
                    
                    $order_id = (int) $_GET['order_id'];
                    
                    //echo ROOT_PATH;
                    $database = new cleanto_db();
                    $conn = $database->connect();
                    $database->conn = $conn;

                    $booking = new cleanto_booking();
                    $booking->conn = $conn;
                    
                    $sql_inv = "SELECT pdf_name FROM ct_invoice WHERE order_id='{$order_id}'";
                    $result_inv = mysqli_query($conn, $sql_inv);
                    if (mysqli_num_rows($result_inv) > 0) {
                        $row_invoice = mysqli_fetch_object($result_inv);
                        $json['success']['invoice'] = array(
                            'order_id' => $order_id,
                            'invoice' => SITE_URL . "admin/" . $row_invoice->pdf_name
                        );
                    } else {
                        $json['error']['message'] = "Invoice not generated";
                    }
                } else {
                    $json['error']['message'] = "Not authorized to access the API";
                }
            } else {
                $json['error']['message'] = "Parameters are missing";
            }
        } else {
            $json['error']['message'] = "Request type is not allowed";
        }
        
        echo json_encode($json);
    }

}

$invoice = new Download_Invoice();
$invoice->index();
