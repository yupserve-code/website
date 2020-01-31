<?php

/*
 * API Config
 */

class API_Config {

    public function __construct() {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function check_valid_api_call($api_token) {
        $sql = "SELECT * FROM ct_api_token WHERE token='{$api_token}'";
        $con = new cleanto_db();
        $conn = $con->connect();
        $result = mysqli_query($conn, $sql);
        return (mysqli_num_rows($result) > 0) ? true : false;
    }

}
