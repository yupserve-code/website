<?php
require_once '../../objects/class_connection.php';
require_once '../../objects/class_booking.php';
require_once '../../assets/lib/Firebase.php';
?>
<?php
$database = new cleanto_db();
$conn = $database->connect();
$database->conn = $conn;

$booking = new cleanto_booking();
$booking->conn = $con;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$postdata = $_POST;
$msg = '';
if (isset($postdata ['key'])) {
    $key = $postdata['key'];
    //$salt = $postdata['salt'];
    $txnid = $postdata['txnid'];
    
    $amount = $postdata['amount'];
    $productInfo = $postdata['productinfo'];
    $firstname = $postdata['firstname'];
    $email = $postdata['email'];
    $udf5 = $postdata['udf5'];
    $mihpayid = $postdata['mihpayid'];
    $status = $postdata['status'];
    $resphash = $postdata['hash'];
    //Calculate response hash to verify	
//    $keyString = $key . '|' . $txnid . '|' . $amount . '|' . $productInfo . '|' . $firstname . '|' . $email . '|||||' . $udf5 . '|||||';
//    $keyArray = explode("|", $keyString);
//    $reverseKeyArray = array_reverse($keyArray);
//    $reverseKeyString = implode("|", $reverseKeyArray);
//    $CalcHashString = strtolower(hash('sha512', $salt . '|' . $status . '|' . $reverseKeyString));

    if ($status == 'success') {
        $msg = "Transaction Successful and Hash Verified...";
        //Do success order processing here...
        // POST values
        $txn_id = $txnid;
        $order_id = $txnid;
        
        // Message
        $message = "Cutomer paid for order #{$order_id}";

        date_default_timezone_set('Asia/Kolkata');

        $current_time = date('Y-m-d H:i:s', time());
        // GET the customer_id from bookings table
        $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
        $res_sel = mysqli_query($conn, $sql_sel);
        // order data
        $order_data = mysqli_fetch_object($res_sel);

        // INSERT into task booking history
        $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$order_data->staff_ids}', '{$order_data->client_id}', '{$message}', '{$current_time}')";
        mysqli_query($conn, $sql_ins);
        
        $payment_method = "Online";

        $current_date = date('Y-m-d', time());

        $amount = 0;
        $discount = 0;
        $tax = 0;
        $partial_amnt = 0;
        $net_amnt = 0;
        $freq_dscnt = 'O';
        $freq_dscnt_amnt = 0;
        $total_amount = 0;
        $total_tax = 0;

        $invoice_results = $booking->get_proforma_invoice_details($order_id);

        if (mysqli_num_rows($invoice_results) > 0) {
            $payment_total = $booking->get_payment_details($order_id);
            $row_pmnt_total = mysqli_fetch_object($payment_total);

            // xtra requirements
            $xtras_info = $booking->get_extra_requirements_invoice_details($order_id);
            $xtras_total = 0;
            if (mysqli_num_rows($xtras_info) > 0) {
                $row_xtras_info = mysqli_fetch_object($xtras_info);
                $xtras_total .= $row_xtras_info->total;
                $total_amount .= $row_xtras_info->price;
                $total_tax .= $row_xtras_info->gst;
            }

            $amount .= ($row_pmnt_total->amount + $total_amount);
            $tax .= ($row_pmnt_total->tax + $total_tax);
            $net_amnt .= ($row_pmnt_total->total + $xtras_total);
        }

        // INSERT Into ct_payments table
        $sql_ins_pmnt = "INSERT INTO ct_payments (
                        order_id, payment_method, transaction_id, amount, discount, taxes, 
                        partial_amount, payment_date, net_amount, lastmodify, frequently_discount,
                        frequently_discount_amount, recurrence_status, payment_status
                        ) 
                     VALUES (
                     '{$order_id}', '{$payment_method}', '{$txn_id}', '{$amount}', '{$discount}', '{$tax}',
                     '{$partial_amnt}', '{$current_date}', '{$net_amnt}', '{$current_time}', '{$freq_dscnt}',
                     '{$freq_dscnt_amnt}', 'N', 'Completed'
                     )";
        mysqli_query($conn, $sql_ins_pmnt);

        // Send Notification to the customer
        $firebase = new Firebase();
        $firebase->set_api('AIzaSyDfRQ1UO-peN9EI2_QRe_MOZX67-XI9pz8');

        // Get the staff Device Token
        $sql_token = "SELECT token FROM ct_staff_device_token WHERE staff_id='{$order_data->staff_ids}'";
        $res_token = mysqli_query($conn, $sql_token);
        if (mysqli_num_rows($res_token)) {
            $row_token = mysqli_fetch_object($res_token);
        }

        // data array to be sent to firebase server
        $data = array(
            'title' => 'Yupserve',
            'request_id' => $order_id,
            'message' => $message,
            'action' => 'booking',
        );

        $notif_array = array(
            'title' => 'Yupserve',
            'request_id' => $order_id,
            'message' => $message,
            'action' => 'booking',
            'click_action' => 'FCM_PLUGIN_ACTIVITY'
        );
        
        // send push notification
        $firebase->send_to_single($row_token->token, $data, $notif_array);

        // INSERT into staff notification table
        $sql_ins_notif = "INSERT INTO ct_staff_notification (staff_id, order_id, text, is_read, created_at) VALUES('{$order_data->staff_ids}', '{$order_id}', '{$message}', '0', '{$current_time}')";
        mysqli_query($conn, $sql_ins_notif);
        
        $json['success']['message'] = "Payment processed successfully";
        
    } else {
        //tampered or failed
        $json['error']['message'] = "Payment not processed";
    }
} else {
    exit(0);
}
?>