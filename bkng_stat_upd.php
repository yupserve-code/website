<?php

require_once 'objects/class_connection.php';
require_once 'objects/class_booking.php';
require_once 'assets/lib/Firebase.php';

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['action'] == 'task_upd_accepted')) {
    $json = array();
    $database = new cleanto_db();
    $conn = $database->connect();
    $database->conn = $conn;

    $booking = new cleanto_booking();

    // POST values
    $customer_id = trim($_POST['customer_id']);
    $order_id = trim($_POST['order_id']);
    // 1 for Accepted, 0 for rejected
    $status = trim($_POST['status']);
    // Message
    if ($status == 'accepted') {
        $message = "Update requirements accepted for order #{$order_id}";
    }

    date_default_timezone_set('Asia/Kolkata');

    $current_time = date('Y-m-d H:i:s', time());
// GET the customer_id from bookings table
    $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
    $res_sel = mysqli_query($conn, $sql_sel);
// order data
    $order_data = mysqli_fetch_object($res_sel);
// If customer accepted, SET approved to 1
    if ($status == 'accepted') {
        // UPDATE table ct_booking_extra_requirements and set approved status to 1
        $sql_upd = "UPDATE ct_booking_extra_requirements SET approved='1' WHERE order_id='{$order_id}'";
    }
    mysqli_query($conn, $sql_upd);

// INSERT into task booking history
    $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$order_data->staff_ids}', '{$customer_id}', '{$message}', '{$current_time}')";
    mysqli_query($conn, $sql_ins);

// Send Notification to the customer
    $firebase = new Firebase();
    $firebase->set_api('AIzaSyDfRQ1UO-peN9EI2_QRe_MOZX67-XI9pz8');

// Get the customer Device Token
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
    $_SESSION['message'] = 'Update requirements accepted by customer';
    $json['success']['message'] = 'Update requirements accepted by customer';
    echo json_encode($json);
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['action'] == 'task_upd_rejected')) {
    $json = array();
    $database = new cleanto_db();
    $conn = $database->connect();
    $database->conn = $conn;

    $booking = new cleanto_booking();

    // POST values
    $customer_id = trim($_POST['customer_id']);
    $order_id = trim($_POST['order_id']);
    // 1 for Accepted, 0 for rejected
    $status = trim($_POST['status']);
    // Message
    if ($status == 'rejected') {
        $message = "Update requirements rejected for order #{$order_id}";
    }

    date_default_timezone_set('Asia/Kolkata');

    $current_time = date('Y-m-d H:i:s', time());
    // GET the customer_id from bookings table
    $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
    $res_sel = mysqli_query($conn, $sql_sel);
    // order data
    $order_data = mysqli_fetch_object($res_sel);
    // If customer accepted, SET approved to 1
    if ($status == 'rejected') {
        // UPDATE table ct_booking_extra_requirements and set approved status to 1
        $sql_upd = "UPDATE ct_booking_extra_requirements SET rejected='1' WHERE order_id='{$order_id}'";
    }
    mysqli_query($conn, $sql_upd);

    // INSERT into task booking history
    $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$order_data->staff_ids}', '{$customer_id}', '{$message}', '{$current_time}')";
    mysqli_query($conn, $sql_ins);

    // Send Notification to the customer
    $firebase = new Firebase();
    $firebase->set_api('AIzaSyDfRQ1UO-peN9EI2_QRe_MOZX67-XI9pz8');

    // Get the customer Device Token
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
    
    $_SESSION['message'] = 'Update requirements rejected by customer';
    $json['success']['message'] = 'Update requirements rejected by customer';
    echo json_encode($json);
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['action'] == 'task_end_accepted')) {
    $json = array();
    $database = new cleanto_db();
    $conn = $database->connect();
    $database->conn = $conn;

    $booking = new cleanto_booking();

    // POST values
    $customer_id = trim($_POST['customer_id']);
    $order_id = trim($_POST['order_id']);
    // 1 for Accepted, 0 for rejected
    $status = trim($_POST['status']);
    // Message
    if ($status == 'accepted') {
        $message = "task end request accepted for order #{$order_id}";
    }

    date_default_timezone_set('Asia/Kolkata');

    $current_time = date('Y-m-d H:i:s', time());
    
// GET the customer_id from bookings table
    $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
    $res_sel = mysqli_query($conn, $sql_sel);
// order data
    $order_data = mysqli_fetch_object($res_sel);
    // UPDATE table ct_bookings and set booking_status to 'CO' completed
    $sql_book_upd = "UPDATE ct_bookings SET booking_status='CO' WHERE order_id='{$order_id}'";
    mysqli_query($conn, $sql_book_upd);
    
    // UPDATE table ct_booking_task_end and set verified status to 1
    $sql_upd = "UPDATE ct_booking_task_end SET verified='1', updated_at='{$current_time}' WHERE order_id='{$order_id}'";
    mysqli_query($conn, $sql_upd);
    
    
// INSERT into task booking history
    $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '{$order_data->staff_ids}', '{$customer_id}', '{$message}', '{$current_time}')";
    mysqli_query($conn, $sql_ins);

// Send Notification to the customer
    $firebase = new Firebase();
    $firebase->set_api('AIzaSyDfRQ1UO-peN9EI2_QRe_MOZX67-XI9pz8');

// Get the customer Device Token
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

    // INSERT into admin notification
    $sql_adm_not = "INSERT INTO ct_admin_notification (order_id, text, created_at) VALUES ('{$order_id}', '{$message}', '{$current_time}')";
    mysqli_query($conn, $sql_adm_not);
    
    $_SESSION['message'] = 'Task end request accepted by customer';
    
    $json['success']['message'] = 'Task end request accepted by customer';
    echo json_encode($json);
}
?>