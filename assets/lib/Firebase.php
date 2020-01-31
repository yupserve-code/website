<?php 
 
class Firebase {
    
    public $api_key;
    
    public function __construct() {
        
    }
    
    public function set_api($api_key) {
        $this->api_key = $api_key;
    }

    // Send to multiple
    public function send($registration_ids, $message) {
        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
        );
        
        return $this->sendPushNotification($fields);
    }
    
    // Send to single
    public function send_to_single($registration_ids, $message, $notif_array) {
        $fields = array(
            'registration_ids' => array($registration_ids),
            'notification' => $notif_array,
            'data' => $message,
            'priority' => 'high'
        );
        
        return $this->sendPushNotification($fields);
    }
    
    /*
    * This function will make the actuall curl request to firebase server
    * and then the message is sent 
    */
    private function sendPushNotification($fields) {
        
        //firebase server url to send the curl request
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        //building headers for the request
        $headers = array(
            'Authorization: key=' . $this->api_key,
            'Content-Type: application/json'
        );
 
        //Initializing curl to open a connection
        $ch = curl_init();
 
        //Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);
 
        //adding headers 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        //adding the fields in json format 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        //finally executing the curl request 
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        //Now close the connection
        curl_close($ch);
 
        //and return the result 
        return $result;
    }
}