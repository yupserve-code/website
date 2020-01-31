<?php

/*
 * Header.php
 * Header file for API Requests
 */

class API_Header {

    public function __construct() {
        $this->get_auth_request_header();
    }

    public function get_auth_request_header() {
        $headers = apache_request_headers();
        return (isset($headers['api_key'])) ? true : false;
    }
    
    public function get_auth_request_header_key() {
        $headers = apache_request_headers();
        return (isset($headers['api_key'])) ? $headers['api_key'] : '';
    }

}
