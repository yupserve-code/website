<?php

require_once 'objects/class_connection.php';
require_once 'objects/class_booking.php';
require_once 'assets/lib/Firebase.php';

if (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['action'] == 'cancel_order')) {
    $json = array();
    $database = new cleanto_db();
    $conn = $database->connect();
    $database->conn = $conn;

    $booking = new cleanto_booking();

    // POST values
    $customer_id = trim($_POST['customer_id']);
    $order_id = trim($_POST['order_id']);
    // 1 for Accepted, 0 for rejected
    $remark = trim($_POST['remark']);
    $message = "Customer has cancelled the booking for order #{$order_id}";

    date_default_timezone_set('Asia/Kolkata');

    $current_time = date('Y-m-d H:i:s', time());
    // GET the customer_id from bookings table
    $sql_sel = "SELECT * FROM ct_bookings WHERE order_id='{$order_id}'";
    $res_sel = mysqli_query($conn, $sql_sel);
    // order data
    $order_data = mysqli_fetch_object($res_sel);
    
    // UPDATE bookings table
    $sql_upd = "UPDATE ct_bookings SET booking_status='CC', cancel_remark='{$remark}' WHERE order_id='{$order_id}'";
    mysqli_query($conn, $sql_upd);

// INSERT into task booking history
    $sql_ins = "INSERT INTO ct_booking_task_history (order_id, staff_id, customer_id, status, created_at) VALUES ('{$order_id}', '', '{$customer_id}', '{$message}', '{$current_time}')";
    mysqli_query($conn, $sql_ins);

    $_SESSION['message'] = 'Booking has been cancelled by customer';
    $json['success']['message'] = 'Booking has been cancelled by customer';
    echo json_encode($json);
}
?>