<?phprequire_once "../../objects/class_connection.php";require_once "../../class_configure.php";// API configrequire_once "../config.php";// API headerrequire_once "../header.php";require_once "../../objects/class_staff_commision.php";/* * completed_bookings.php * Completed Bookings */class Completed_Bookings {    public function __construct() {            }    public function index() {		header('Content-Type: application/json; charset=utf-8');		header('Cache-Control: no-cache');        $json = array();        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "GET")) {            if (!empty($_GET['staff_id'])) {                $staff_id = (int) trim($_GET['staff_id']);                if (!empty($_GET['page'])) {                     $page = (int) trim($_GET['page']);                } else {                    $page = 1;                }                // per page 10 items to be loaded                $per_page = 10;                // Offset to be inceremented on page scroll down                $offset = ($page - 1) * $per_page;               /*                $header = new API_Header();                $token = $header->get_auth_request_header_key();                $config = new API_Config();                if ($config->check_valid_api_call(trim($token))) {				*/                    $con = new cleanto_db();                    $conn = $con->connect();                                        $staff_commission = new cleanto_staff_commision();                    $staff_commission->conn = $conn;                                        // BASE URL                    $this->base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';                    $this->base_url .= '://' . $_SERVER['HTTP_HOST'] . '/';                                        // Get all the lists against the customer                    $booking_results = $staff_commission->get_staff_app_acompleted_bookings($staff_id, $per_page, $offset);                                        $json['success']['bookings'] = array();                                        if (mysqli_num_rows($booking_results) > 0) {                        while ($booking = mysqli_fetch_object($booking_results)) {                            $json['success']['bookings'][] = array(                                'id' => $booking->id,                                'order_id' => $booking->order_id,                                'status' => $booking->booking_status,                                'service_id' => $booking->service_id,                                'service_name' => $booking->service_name,                                'service_image' => ($booking->service_image == '') ? '' : $this->base_url . "assets/images/services/" . $booking->service_image,                                'order_date' => $booking->order_date,                                'date_time' => $booking->booking_date_time                            );                        }                    }                    			/*                } else {                    $json['error']['message'] = "Not authorized to access the API";                }			*/            } else {                $json['error']['message'] = "Parameters are missing";            }        } else {            $json['error']['message'] = "The request type is not allowed";        }        echo json_encode($json);    }}$my_bookings = new Completed_Bookings();$my_bookings->index();