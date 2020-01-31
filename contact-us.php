<?php
include_once 'page_initialization.php';
include_once (dirname(__FILE__) . "/objects/class_connection.php");
include_once (dirname(__FILE__) . '/class_configure.php');
include_once (dirname(__FILE__) . "/header.php");
include_once (dirname(__FILE__) . "/objects/class_services.php");
include_once (dirname(__FILE__) . '/objects/class_setting.php');
?>
<?php
session_start();
$service_array = array("method" => array());
$_SESSION['ct_cart'] = $service_array;
$_SESSION['freq_dis_amount'] = '';
$_SESSION['ct_details'] = '';
$_SESSION['staff_id_cal'] = '';
$_SESSION['time_duration'] = 0;

$cvars = new cleanto_myvariable();
$host = trim($cvars->hostnames);
$un = trim($cvars->username);
$ps = trim($cvars->passwords);
$db = trim($cvars->database);

$con = @new cleanto_db();
$conn = $con->connect();

$settings = new cleanto_setting();
$settings->conn = $conn;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Contact Us | <?php echo $settings->get_option("ct_page_title"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/backgrounds/<?php echo $settings->get_option('ct_favicon_image'); ?>"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
        <link href="css/owl.carousel.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/chosen.min.css" rel="stylesheet" type="text/css"/>
        <?php include_once 'include/header-scripts-style.php'; ?>
    </head>
    <body>
        <?php include_once 'include/header.php'; ?>
        <!--Banner Start-->
        <section class="inner-page-banner">
            <img src="<?php echo $base_url; ?>images/contact-us-banner.jpg"/>
            <div class="page-title">
                <h1 class="banner-heading text-center">Contact Us</h1>
            </div>
        </section>        
        <!--Banner End-->
        <section class="section">
            <div class="container">
                <h1 class="banner-heading text-center"></h1>
                <div class="row">
                    <!-- Contact info -->
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                        <div class="contact-address wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s" data-wow-offset="100">
                            <div class="contact-list map">
                                <h1>LOCATION</h1>
                                <h2>36, Forest Park, Bhubaneswar - 751009</h2>
                            </div>
                            <div class="contact-list call-centerr">
                                <h1>CALL US</h1>
                                <h2> 0674 - 2435700 / 1800-123-3959</h2>
                            </div>
                            <div class="contact-list email-us">
                                <h1>EMAIL US</h1>
                                <h2>info@yupserve.com </h2>
                            </div>
                        </div>
                    </div>
                    <!-- /Contact info -->
                    <!-- Contact Map -->
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s" data-wow-offset="100">
                        <section>
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3743.061478269144!2d85.82246901439485!3d20.256284919075675!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a19a714c13f4a71%3A0x77709e075c80041a!2s36%2C+Forest+Park+Rd%2C+Forest+Park%2C+Bhubaneswar%2C+Odisha+751020!5e0!3m2!1sen!2sin!4v1552988828946" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
<!--                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14973.142922032994!2d85.81234797385602!3d20.246981346597597!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a19a70fc36a9169%3A0x71d4b8c7f4cc375c!2sBhubaneswar%2C+Odisha+751020!5e0!3m2!1sen!2sin!4v1551254597946" width="100%" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>-->
                        </section>
                    </div>
                    <!-- /Contact Map-->
                </div>
            </div>
        </section>
        <?php //include_once 'include/header-menu.php'; ?>
        <?php include_once 'include/footer.php'; ?>
        <div class="clearfix"></div>
        <?php include_once 'include/script.php'; ?>
    </body>
</html>