<?php
include(dirname(__FILE__) . '/header.php');
include(dirname(__FILE__) . '/user_session_check.php');
include(dirname(dirname(__FILE__)) . '/objects/class_booking.php');
$setting = new cleanto_setting();
$setting->conn = $conn;
$booking = new cleanto_booking();
$booking->conn = $conn;

$gettimeformat = $setting->get_option('ct_time_format'); /* CHECK FOR VC AND PARKING STATUS */$global_vc_status = $setting->get_option('ct_vc_status');
$global_p_status = $setting->get_option('ct_p_status'); /* CHECK FOR VC AND PARKING STATUS END */
?>
<?php 
    if (isset($_POST['submit'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];
        // UDPATE in the feedback table
        $query_upd = "UPDATE ct_feedback SET status='{$status}' WHERE order_id='{$order_id}'";
        mysqli_query($conn, $query_upd);
    }
?>
<style>
    #html5-watermark {
        display:none !important;
    }
    .invoice_row:nth-child(1) .delete-row{
        display: none;
    }

    .star-rating {
        font-family: 'FontAwesome';
        //margin: 50px auto;
    }
    .star-rating > fieldset {
        border: none;
        display: inline-block;
    }
    .star-rating > fieldset:not(:checked) > input {
        position: absolute;
        top: -9999px;
        clip: rect(0, 0, 0, 0);
    }
    .star-rating > fieldset:not(:checked) > label {
        float: right;
        width: 1em;
        padding: 0 0.05em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 120%;
        color: #e8b82b;
    }
    .star-rating > fieldset:not(:checked) > label:before {
        content: '\f006  ';
    }
    .star-rating > fieldset:not(:checked) > label:hover,
    .star-rating > fieldset:not(:checked) > label:hover ~ label {
        color: #1abc9c;
        text-shadow: 0 0 3px #1abc9c;
    }
    .star-rating > fieldset:not(:checked) > label:hover:before,
    .star-rating > fieldset:not(:checked) > label:hover ~ label:before {
        content: '\f005  ';
    }
    .star-rating > fieldset > input:checked ~ label:before {
        content: '\f005  ';
    }
    .star-rating > fieldset > label:active {
        position: relative;
        top: 2px;
    }
    body {
        //background: #262829;
        color: #95a5a6;
        font-family: 'Raleway';
        text-align: center;
    }
    body p {
        font-size: 1.6em;
        margin: auto;
        width: 80%;
    }
    body a {
        color: #16a085;
    }
</style>
<div id="cta-profile" class="panel tab-content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title">View Feedback</h1>
        </div>
    </div>
    <div class="panel-body">
        <div class="ct-admin-profile-details tab-content col-md-12 col-sm-12 col-lg-12 col-xs-12">
            <!-- right side common menu for service -->
            <div id="personal-info-tab" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-pane fade active in">
                <?php
                $order_id = (int) trim($_GET['order_id']);

                $sql_order = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) AS customer_name, adm.fullname, u.user_email, u.phone, s.title FROM ct_bookings b JOIN ct_services s ON b.service_id = s.id JOIN ct_users u ON b.client_id = u.id JOIN ct_admin_info adm ON b.staff_ids = adm.id WHERE b.order_id='{$order_id}'";

                $res_order = mysqli_query($conn, $sql_order);
                if (mysqli_num_rows($res_order) > 0) {
                    $row_order = mysqli_fetch_object($res_order);
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="fa fa-calendar"></i> Appointment Details</h3>
                                </div>
                                <table class="table">
                                    <tbody style="text-align: left;">
                                        <tr>
                                            <td>Appointment ID</td>
                                            <td><?php echo $row_order->order_id; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Order Date</td>
                                            <td><?php echo $row_order->booking_date_time; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <?php
                                            if ($row_order->booking_status == 'CO') {
                                                $status = 'Completed';
                                            } else if ($row_order->booking_status == 'R') {
                                                $status = 'Rejected';
                                            } else if ($row_order->booking_status == 'C') {
                                                $status = 'Confirmed';
                                            } else if ($row_order->booking_status == 'CS') {
                                                $status = 'Cancelled by Staff';
                                            } else if ($row_order->booking_status == 'MN') {
                                                $status = 'No Show';
                                            } else if ($row_order->booking_status == 'CO') {
                                                $status = 'Completed';
                                            } else if ($row_order->booking_status == 'CC') {
                                                $status = 'Cancelled by Customer';
                                            } else if ($row_order->booking_status == 'A') {
                                                $status = 'Active';
                                            }
                                            ?> 
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <?php
                                        $sql_addons2 = "SELECT ba.*, sa.addon_service_name FROM ct_booking_addons ba JOIN ct_services_addon sa ON ba.addons_service_id = sa.id WHERE ba.order_id='{$order_id}'";
                                        $res_addons2 = mysqli_query($conn, $sql_addons2);
                                        ?>
                                        <tr>
                                            <td>Service</td>
                                            <td><?php echo $row_order->title; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Add Ons</td>
                                            <td>
                                                <?php while ($row_addons3 = mysqli_fetch_object($res_addons2)) { ?>
                                                    <?php echo $row_addons3->addon_service_name; ?>, 
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><i class="fa fa-comment-o"></i> Feedback Details</h3>
                                </div>
                                <?php
                                $sql_feedback = "SELECT * FROM ct_feedback WHERE order_id='{$order_id}'";
                                $res_feedback = mysqli_query($conn, $sql_feedback);
                                $row_feedback = mysqli_fetch_object($res_feedback);
                                ?>
                                <table class="table">
                                    <tbody style="text-align: left;">
                                        <tr>
                                            <td>Staff</td>
                                            <td><?php echo $row_order->fullname; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Rating</td>
                                            <td>
                                                <div class="star-rating">
                                                    <fieldset>
                                                        <input type="radio" id="star5" name="rating" value="5" <?php echo ($row_feedback->rating == 5) ? 'checked' : ''; ?> /><label for="star5" title="Outstanding">5 stars</label>
                                                        <input type="radio" id="star4" name="rating" value="4" <?php echo ($row_feedback->rating == 4) ? 'checked' : ''; ?>/><label for="star4" title="Very Good">4 stars</label>
                                                        <input type="radio" id="star3" name="rating" value="3" <?php echo ($row_feedback->rating == 3) ? 'checked' : ''; ?>/><label for="star3" title="Good">3 stars</label>
                                                        <input type="radio" id="star2" name="rating" value="2" <?php echo ($row_feedback->rating == 2) ? 'checked' : ''; ?>/><label for="star2" title="Poor">2 stars</label>
                                                        <input type="radio" id="star1" name="rating" value="1" <?php echo ($row_feedback->rating == 1) ? 'checked' : ''; ?>/><label for="star1" title="Very Poor">1 star</label>
                                                    </fieldset>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Feedback</td>
                                            <td><?php echo ($row_feedback->feedback); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td>
                                                <form method="post" action="view_feedback.php?order_id=<?php echo $order_id; ?>" name="">
                                                    <input type="hidden" value="<?php echo $order_id; ?>" name="order_id"/>
                                                    <select name="status">
                                                        <option value="1" <?php echo ($row_feedback->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                        <option value="0" <?php echo ($row_feedback->status == 0) ? 'selected' : ''; ?>>Inactive</option>
                                                    </select>
                                                    <button name="submit" class="btn-sm btn btn-success">Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div> <!-- end personal infomation -->
    </div>
</div>
<?php include(dirname(__FILE__) . '/footer.php'); ?>
<script type="text/javascript" src="<?php echo SITE_URL; ?>assets/html5lightbox/html5lightbox.js"></script>
<script>
    $(document).ready(function () {

    });
</script>