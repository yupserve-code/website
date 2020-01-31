<?php
include(dirname(__FILE__) . '/header.php');
include(dirname(__FILE__) . '/user_session_check.php');
include(dirname(dirname(__FILE__)) . '/objects/class_booking.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services_methods.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services_methods_units.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services_addon.php');
//include(dirname(dirname(__FILE__)) . '/objects/class_setting.php');
include(dirname(dirname(__FILE__)) . '/objects/class_users.php');
include(dirname(dirname(__FILE__)) . '/objects/class_front_first_step.php');
include(dirname(dirname(__FILE__)) . '/objects/class_order_client_info.php');
include(dirname(dirname(__FILE__)) . '/objects/class_payments.php');
//include(dirname(dirname(__FILE__)) . '/objects/class_general.php');

$order_id = (int) trim($_GET['order_id']);

$database = new cleanto_db();
$conn = $database->connect();
$database->conn = $conn;

$booking = new cleanto_booking();
$service = new cleanto_services();
$setting = new cleanto_setting();
$first_step = new cleanto_first_step();
$user = new cleanto_users();
$order = new cleanto_order_client_info();
$payments = new cleanto_payments();
$general = new cleanto_general();
$smethod = new cleanto_services_methods();
$smunit = new cleanto_services_methods_units();
$saddon = new cleanto_services_addon();

$service->conn = $conn;
$booking->conn = $conn;
$setting->conn = $conn;
$first_step->conn = $conn;
$user->conn = $conn;
$order->conn = $conn;
$payments->conn = $conn;
$smethod->conn = $conn;
$smunit->conn = $conn;
$general->conn = $conn;
$saddon->conn = $conn;
?>
<div class="panel-body">
    <?php
    $order_result = $booking->get_customer_app_booking_details($order_id);
    if (mysqli_num_rows($order_result) > 0) {
        $order_data = mysqli_fetch_object($order_result);
        ?>
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-calendar"></i> Appointment Details</h3>
                    </div>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Appointment ID</td>
                                <td><?php echo $order_data->order_id; ?></td>
                            </tr>
                            <tr>
                                <td>Appointment Date</td>
                                <td><?php echo $order_data->order_date; ?></td>
                            </tr>
                            <tr>
                                <td>Appointment Date</td>
                                <td><?php echo $order_data->booking_date_time; ?></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <?php
                                if ($order_data->booking_status == 'CO') {
                                    $status = 'Completed';
                                } else if ($order_data->booking_status == 'R') {
                                    $status = 'Rejected';
                                } else if ($order_data->booking_status == 'C') {
                                    $status = 'Confirmed';
                                } else if ($order_data->booking_status == 'CS') {
                                    $status = 'Cancelled by Staff';
                                } else if ($order_data->booking_status == 'MN') {
                                    $status = 'No Show';
                                } else if ($order_data->booking_status == 'CO') {
                                    $status = 'Completed';
                                } else if ($order_data->booking_status == 'CC') {
                                    $status = 'Cancelled by Customer';
                                } else if ($order_data->booking_status == 'A') {
                                    $status = 'Active';
                                }
                                ?> 
                                <td><?php echo $status; ?></td>
                            <tr>
                                <td>Service Booked</td>
                                <td><?php echo $order_data->service_title; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-user"></i> Customer Details</h3>
                    </div>
                    <table class="table">
                        <?php
                        // Get the booking address by customer end
                        $res_addr = $booking->get_order_client_info($order_id);
                        $row_addr = mysqli_fetch_object($res_addr);
                        $address = unserialize(base64_decode($row_addr->client_personal_info));
                        ?>
                        <tbody>
                            <tr>
                                <td><?php echo $order_data->customer_name; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $order_data->user_email; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $order_data->phone; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $address['address']; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $address['zip']; ?>, <?php echo $address['state']; ?>, <?php echo $address['city']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-briefcase"></i> Staff Details</h3>
                    </div>
                    <?php
                    // Service Boy Details
                    $json['success']['booking_details']['staff_dtls'] = '';

                    $srv_boy_res = $booking->get_booking_assigned_staff($order_id);

                    if (mysqli_num_rows($srv_boy_res) > 0) {
                        $srv_boy_dtls = mysqli_fetch_object($srv_boy_res);
                        ?>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Staff Name</td>
                                    <td><?php echo $srv_boy_dtls->fullname; ?></td>
                                </tr>
                                <tr>
                                    <td>Staff Mobile</td>
                                    <td><?php echo $srv_boy_dtls->phone; ?></td>
                                </tr>
                                <?php
                                // OTP details
                                $otp_results = $booking->get_booking_otp_details($order_id);
                                if (mysqli_num_rows($otp_results) > 0) {
                                    $booking_otp = mysqli_fetch_object($otp_results);
                                    ?>
                                        <tr>
                                            <td>OTP</td>
                                            <td><?php echo $booking_otp->otp; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Verified</td>
                                            <td><?php echo ($booking_otp->verified == 1) ? 'Yes' : 'No'; ?></td>
                                        </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle"></i> Addons (#<?php echo $order_data->order_id; ?>)</h3>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="text-left">Service</td>
                            <td class="text-left">Add ons</td>
                            <td class="text-right">Quantity</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Service details
                        $service_results = $booking->get_customer_service_addon_bookings($order_id);

                        if (mysqli_num_rows($service_results) > 0) {
                            while ($sub_service = mysqli_fetch_object($service_results)) {
                                ?>
                                <tr>
                                    <td class="text-left"><?php echo $order_data->service_title; ?></td>
                                    <td class="text-left"><?php echo $sub_service->sub_service_name; ?></td>
                                    <td class="text-right"><?php echo $sub_service->addons_service_qty; ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle"></i> Extra Requirements (#<?php echo $order_data->order_id; ?>)</h3>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="text-left">Requirements</td>
                            <td class="text-left">Approved</td>
                            <td class="text-right">Rejected</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_tsk_upd = "SELECT * FROM ct_booking_extra_requirements WHERE order_id='{$order_id}'";
                        $task_upd_result = mysqli_query($conn, $sql_tsk_upd);

                        if (mysqli_num_rows($task_upd_result) > 0) {
                            $row_upd_rqmnt = mysqli_fetch_object($task_upd_result);
                            ?>
                            <tr>
                                <td class="text-left" width="300"><?php echo $row_upd_rqmnt->requirements; ?></td>
                                <td class="text-left"><?php echo ($row_upd_rqmnt->approved == 1) ? 'Yes' : 'No'; ?></td>
                                <td class="text-right"><?php echo ($row_upd_rqmnt->rejected == 1) ? 'Yes' : 'No'; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        $payment_results = $booking->get_booking_payment_info($order_id);
        if (mysqli_num_rows($payment_results) > 0) {
            $payment_info = mysqli_fetch_object($payment_results);
            ?>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-money"></i> Payment Info</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Payment Method</td>
                                    <td><?php echo $payment_info->payment_method; ?></td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td><?php echo $payment_info->amount; ?></td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td><?php echo $payment_info->taxes; ?></td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td><?php echo $payment_info->discount; ?></td>
                                </tr>
                                <tr>
                                    <td>Payment Date</td>
                                    <td><?php echo $payment_info->payment_date; ?></td>
                                </tr>
                                <tr>
                                    <td>Total Amount</td>
                                    <td><?php echo $payment_info->net_amount; ?></td>
                                </tr>
                                <tr>
                                    <td>Payment Status</td>
                                    <td><?php echo $payment_info->payment_status; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-history"></i> History</h3>
            </div>
            <div class="panel-body">
                <div id="history">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="text-left">Date Added</td>
                                    <td class="text-left">Status</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $booking_history = $booking->get_booking_history($order_id);
                                if (mysqli_num_rows($booking_history) > 0) {
                                    while ($row_history = mysqli_fetch_object($booking_history)) {
                                        ?>
                                        <tr>
                                            <td class="text-left"><?php echo $row_history->created_at; ?></td>
                                            <td class="text-left"><?php echo $row_history->status; ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>