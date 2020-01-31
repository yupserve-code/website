<?phprequire_once "../../objects/class_connection.php";require_once "../../class_configure.php";// API configrequire_once "../config.php";// API headerrequire_once "../header.php";require_once "../../objects/class_setting.php";require_once "../../objects/class_general.php";require_once "../../assets/lib/Sinfini_sms.php";/* * Login.php * Login functionalities */class Login {    public function index() {				header('Content-Type: application/json; charset=utf-8');		header('Cache-Control: no-cache');		        $json = array();        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {            if (!empty($_POST['mobile']) && preg_match('/^[0-9]{10}$/', $_POST['mobile'])) {			/*                $header = new API_Header();                $token = $header->get_auth_request_header_key();                $config = new API_Config();                if ($config->check_valid_api_call(trim($token))) {			*/                    $con = new cleanto_db();                    $conn = $con->connect();                    $ph_mobile = trim($_POST['mobile']);                    //$mobile = "+91" . $ph_mobile;                    $mobile = $ph_mobile;                    $sql = "SELECT * FROM ct_admin_info WHERE phone='{$mobile}' AND role='staff'";                    $result = mysqli_query($conn, $sql);                    // check if mobile exists                    if (mysqli_num_rows($result) > 0) {                        // user data                        $user_data = mysqli_fetch_object($result);                        $user_id = $user_data->id;                        // OTP                        $otp = $this->generate_otp(4);                        $message = "Yup-Serve : Your OTP verification code is {$otp}";                        // SMS API                        $sms_api = new Sinfini_sms();                        // send SMS using API                        $sms_api->send($ph_mobile, $message);                        date_default_timezone_set('Asia/Kolkata');                        $created_at = date('Y-m-d H:i:s', time());                        $expired_at = $this->getCurrentTime();                        // check that user already exists                        $otp_sql = "SELECT * FROM ct_staff_otp WHERE staff_id='{$user_id}'";                        $result_otp = mysqli_query($conn, $otp_sql);                        // check that row exists in OTP table                        if (mysqli_num_rows($result_otp) > 0) {                            // UPDATE OTP table                            $sql_upd = "UPDATE ct_staff_otp SET otp='{$otp}', created_at='{$created_at}', expired_at='{$expired_at}' WHERE staff_id='{$user_id}'";                            mysqli_query($conn, $sql_upd);                        } else {                            // INSERT into OTP table                            $sql_ins = "INSERT INTO ct_staff_otp (staff_id, otp, created_at, expired_at) VALUES ("                                    . "'{$user_id}', '{$otp}', '{$created_at}', '{$expired_at}'"                                    . ")";                            mysqli_query($conn, $sql_ins);                        }                        $json['success']['staff_details'] = array(                            'staff_id' => $user_id                        );                        $json['success']['message'] = 'OTP sent successfully';                    } else {                        $json['error']['message'] = "Staff not registered";                    }				/*                } else {                    $json['error']['message'] = "Not authorized to access the API";                }				*/            } else {                $json['error']['message'] = "Parameters are missing";            }        } else {            $json['error']['message'] = "The request type is not allowed";        }        echo json_encode($json);    }    // Function to generate OTP     public function generate_otp($n) {        // all numeric digits         $generator = "1357902468";        // Iterate for n-times and pick a single character         // from generator and append it to $result         $result = "";        for ($i = 1; $i <= $n; $i++) {            $result .= substr($generator, (rand() % (strlen($generator))), 1);        }        // Return result         return $result;    }    // GET 15 minute interval    public function getCurrentTime() {        // 15 Minute        return date("Y-m-d H:i:s", time() + 900);    }}$login = new Login();$login->index();