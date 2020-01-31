<?php

final class Sinfini_sms {

    public $Api_key;
    public $senderID;
    public $format;
    public $method;
    protected $sms_URL = "https://api-alerts.kaleyra.com/v4/";

    public function __construct() {
        $this->Api_key = 'Ad1751308e7bfc8a41b90fed6c6c909a1';
        $this->senderID = 'YUPSRV';
        $this->method = 'sms';
        $this->format = 'JSON';
    }

    private function SicURL($link) {
        $http = curl_init($link);
        // do your curl thing here
        curl_setopt($http, CURLOPT_RETURNTRANSFER, TRUE);
        $http_result = curl_exec($http);
        $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
        curl_close($http);

        return $http_result;
    }

    public function send($mobile, $message) {

        $data = $this->sms_URL . '?api_key=' . urlencode($this->Api_key);
        $data .= '&method=' . urlencode($this->method);
        $data .= '&message=' . urlencode($message);
        $data .= '&to=' . urlencode($mobile);
        $data .= '&sender=' . urlencode($this->senderID);

        $result = $this->SicURL($data);

        return $result;
    }

}

?>