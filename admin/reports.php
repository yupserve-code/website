<?php
include(dirname(__FILE__) . '/header.php');
include(dirname(dirname(__FILE__)) . "/objects/class_payments.php");
include(dirname(dirname(__FILE__)) . "/objects/class_staff_commision.php");
include(dirname(__FILE__) . '/user_session_check.php');
include(dirname(dirname(__FILE__)) . "/objects/class_adminprofile.php");

$con = new cleanto_db();
$conn = $con->connect();
$objpayment = new cleanto_payments();
$objpayment->conn = $conn;

$staffpayment = new cleanto_staff_commision();
$staffpayment->conn = $conn;

$admin_profile = new cleanto_adminprofile();
$admin_profile->conn = $conn;

/* general setting object */
$general = new cleanto_general();
$general->conn = $conn;
$settings = new cleanto_setting();
$settings->conn = $conn;
$symbol_position = $settings->get_option('ct_currency_symbol_position');
$decimal = $settings->get_option('ct_price_format_decimal_places');
?>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            Payments<i class="fa fa-money"></i>
        </div>
        <div class="col-md-4">
            Customers<i class="fa fa-user-o"></i>
        </div>
        <div class="col-md-4">
            Service Assigned<i class="fa fa-sellsy"></i>
        </div>
    </div>
</div>
<?php include(dirname(__FILE__) . '/footer.php'); ?>
<script type="text/javascript">
    var ajax_url = '<?php echo AJAX_URL; ?>';
    var servObj = {'site_url': '<?php echo SITE_URL . 'assets/images/business/'; ?>'};
    var imgObj = {'img_url': '<?php echo SITE_URL . 'assets/images/'; ?>'};
</script>