<?phpinclude(dirname(dirname(__FILE__)) . '/config.php');class cleanto_db extends cleanto_myvariable {    public $hostname = "";    public $user = "";    public $password = "";    public $db_name = "";    public $connectobj = "";    public function connect() {        $this->hostname = $this->hostnames;        $this->user = $this->username;        $this->password = $this->passwords;        $this->db_name = $this->database;        if (is_numeric(strpos($this->hostname, ':'))) {            $hostnamewithcols = explode(":", $this->hostname);            $this->hostname = $hostnamewithcols[0];            $this->mysqlport = (int) $hostnamewithcols[1];            $conn = new mysqli($this->hostname, $this->user, $this->passwords, $this->db_name, $this->mysqlport);            if (!mysqli_query($conn, "select curtime()")) {                die(" No connection could be made because the target machine actively refused it.");            }        } else {            $conn = new mysqli($this->hostname, $this->user, $this->passwords, $this->db_name);        }        if (@mysqli_errno($conn)) {            echo 'Error:(' . mysqli_connect_errno() . ')' . mysqli_connect_error();        }        return $conn;    }    /* function for check of existing tables used in index.php */    public function check_existing_tables_index($table_conn) {        $result = mysqli_query($table_conn, "SHOW TABLES LIKE 'ct_admin_info'");        if ($result->num_rows != 1) {            return 'table_not_exist';        } else {            $val = mysqli_query($table_conn, "select * from `ct_admin_info` LIMIT 1");            if (mysqli_num_rows($val) == 0) {                return 'table_exist_but_no_data';            } else {                return 'table_data_both_exist';            }        }    }}?>