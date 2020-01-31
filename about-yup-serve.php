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
        <title>About YupServe | <?php echo $settings->get_option("ct_page_title"); ?></title>
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
            <img src="<?php echo $base_url; ?>images/about-us-banner.jpg"/>
            <div class="page-title">
                <h1 class="banner-heading text-center">About YupServe</h1>
            </div>
        </section>        
        <!--Banner End-->

        <section class="section">
            <div class="container">
                <h1 class="banner-heading text-center"></h1>
                <div class="row">

                    <!-- Contact info -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inner-paragraph">
                            <p>YupServe gets curated service providers to your doorstep for various
home services ranging from cleaning, pest control, repairs, handyman jobs.
We do this so that you can spend your time following your passions and doing
things you actually love rather than running around for mundane tasks.
Customer satisfaction to us is paramount. We ensure that all our service
partners are background verified and well experienced.
Regular feedbacks and quality control checks ensure that your
service requests are fulfilled on time and with 100% accountability.</p>
                        </div>
                    </div>
                    <!-- /Contact info -->

                </div>
            </div>
        </section>

        <?php //include_once 'include/header-menu.php'; ?>

        <?php include_once 'include/footer.php'; ?>
        <div class="clearfix"></div>
        <?php include_once 'include/script.php'; ?>
    </body>
</html>
