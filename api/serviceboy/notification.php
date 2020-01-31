<?php
require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
/*
 * notification.php
 * Notification list
 */

class Notification {

    public function __construct() {
        
    }

    public function index() {
		
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache');
		
        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "GET")) {

            if (!empty($_GET['staff_id'])) {

                $staff_id = (int) trim($_GET['staff_id']);
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

                $config = new API_Config();

                if ($config->check_valid_api_call(trim($token))) {
			*/
                    $con = new cleanto_db();
                    $conn = $con->connect();
                    
                    // BASE URL
                    $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                    $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';
                    
                    // Get all the notification lists against the customer
                    $sql = "SELECT * FROM ct_staff_notification WHERE staff_id='{$staff_id}' ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}";
                    $notifications = mysqli_query($conn, $sql);
                    
                    $json['success']['notifications'] = array();
                    
                    if (mysqli_num_rows($notifications) > 0) {
                        while ($notif = mysqli_fetch_object($notifications)) {
                            $json['success']['notifications'][] = array(
                                'id' => $notif->id,
                                'order_id' => $notif->order_id,
                                'staff_id' => $notif->staff_id,
                                'text' => $notif->text,
                                'is_read' => ($notif->is_read == 1) ? true : false,
                                'created_at' => $notif->created_at
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

$notification = new Notification();
$notification->index();