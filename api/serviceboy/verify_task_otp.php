<?phprequire_once "../../objects/class_connection.php";require_once "../../class_configure.php";// API configrequire_once "../config.php";// API headerrequire_once "../header.php";require_once "../../objects/class_setting.php";require_once "../../assets/lib/Firebase.php";/* * verify_task_otp.php * Verify Task OTP */class Verify_Task_OTP {    public function __construct() {            }    public function index() {				header('Content-Type: application/json; charset=utf-8');		header('Cache-Control: no-cache');        $json = array();                if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {            if (!empty($_POST['otp']) && preg_match('/^[0-9]{4}$/', $_POST['otp']) && !empty($_POST['staff_id']) && !empty($_POST['order_id'])) {                			/*	                $header = new API_Header();                $token = $header->get_auth_request_header_key();                $config = new API_Config();                if ($config->check_valid_api_call(trim($token))) {			*/                    $con = new cleanto_db();                    $conn = $con->connect();                                        $staff_id = trim($_POST['staff_id']);                    $order_id = trim($_POST['order_id']);                    $otp = trim($_POST['otp']);                    date_default_timezone_set('Asia/Kolkata');                    $current_time = date('Y-m-d H:i:s', time());                    $current_date = date('Y-m-d', time());                    // check that task should not be started before booking/order date_time                     $sql_chk = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'                                 AND DATE(booking_date_time) <= '{$current_date}'";                    $res_chk = mysqli_query($conn, $sql_chk);                    if (mysqli_num_rows($res_chk) < 1) {                        $json['error']['message'] = 'Cannot start the task before booking date';                    }                                        if (empty($json['error'])) {                        $sql_order = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";                        $res_order = mysqli_query($conn, $sql_order);                        $order_data = mysqli_fetch_object($res_order);                        $sql = "SELECT * FROM ct_booking_otp WHERE order_id='{$order_id}' AND staff_id='{$staff_id}' AND verified='0'";                        $result = mysqli_query($conn, $sql);                        if (mysqli_num_rows($result) > 0) {                            $otp_data = mysqli_fetch_object($result);                            //if (($otp_data->otp === $otp) && ($current_time < $otp_data->expired_at)) {								if ($otp_data->otp === $otp) {                                // UPDATE booking OTP table                                $sql_upd = "UPDATE ct_booking_otp SET verified='1' WHERE order_id='{$order_id}' AND staff_id='{$staff_id}'";                                mysqli_query($conn, $sql_upd);                                // INSERT into ct_booking_task_history table                                $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$staff_id}', '{$order_data->client_id}', 'Task Started', '{$current_time}')";                                mysqli_query($conn, $sql_ins);                                // Send notification to customer about task start                                $firebase = new Firebase();                                $firebase->set_api('AIzaSyDfRQ1UO-peN9EI2_QRe_MOZX67-XI9pz8');                                // Get the customer Device Token                                $sql_token = "SELECT token FROM ct_customer_device_token WHERE customer_id='{$order_data->client_id}'";                                $res_token = mysqli_query($conn, $sql_token);                                if (mysqli_num_rows($res_token)) {                                    $row_token = mysqli_fetch_object($res_token);                                }                                $message = "Task started for odrer #{$order_id}";                                // data array to be sent to firebase server                                $data = array(                                    'title' => 'Yupserve',                                    'request_id' => $order_id,                                    'message' => $message,                                    'action' => 'booking',                                );                                $notif_array = array(                                    'title' => 'Yupserve',                                    'request_id' => $order_id,                                    'message' => $message,                                    'action' => 'booking',                                    'click_action' => 'FCM_PLUGIN_ACTIVITY'                                );                                $firebase->send_to_single($row_token->token, $data, $notif_array);                                // INSERT into customer notification table                                $sql_ins_notif = "INSERT INTO ct_user_notification (user_id, order_id, text, is_read, created_at) VALUES('{$otp_data->customer_id}', '{$order_id}', '{$message}', '0', '{$current_time}')";                                mysqli_query($conn, $sql_ins_notif);                                $json['success']['message'] = 'Task started successfully';                            } else {                                $json['error']['message'] = "Invalid OTP";                            }                        } else {                            $json['error']['message'] = "Invalid OTP";                        }                    }				/*	                } else {                    $json['error']['message'] = "Not authorized to access the API";                }				*/            } else {                $json['error']['message'] = "Parameters are missing";            }        } else {            $json['error']['message'] = "The request type is not allowed";        }        echo json_encode($json);    }}$otp = new Verify_Task_OTP();$otp->index();