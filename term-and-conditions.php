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
        <title>Terms & Conditions | <?php echo $settings->get_option("ct_page_title"); ?></title>
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
                <h1 class="banner-heading text-center">Terms & Conditions</h1>
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
                            <p>This Terms of Use agreement was last updated: February 28th 2019. This Terms of Use agreement is effective as of: February 28th 2019.</p>

                            <p>We strongly recommend that you go through the Terms
of use mentioned on this page attentively before browsing the content on the
other pages of the website or conducting any activity on it.</p>

                            <p>PLEASE READ THE TERMS OF USE THOROUGHLY AND CAREFULLY. The terms and conditions set forth below ("Terms of Use") and the Privacy Policy (as defined below) constitute a legally-binding agreement between YupServe operating from its Bhubaneswar Office and you. These Terms of Use contain provisions that define your limits, legal rights and obligations with respect to your use of and participation in the YupServe website and mobile application, including the classified advertisements, forums, various email functions and Internet links, and all content and YupServe services available through the domain and sub-domains of YupServe located at http://yupserve.com/.com  and the online transactions between those users of the Website who are offering services and those users of the Website who are obtaining services (each, a "Service User") through the Website (such services, collectively, the "Services"). The Terms of Use described below incorporate the Privacy Policy and apply to all users of the Website, including users who are also contributors of video content, information, private and public messages, advertisements, and other materials or Services on the Website.</p>

                            <p>The Website is owned and operated by YupServe.</p>

<p>You acknowledge that the Website serves as a venue for the online distribution and publication of user submitted information between Service Professionals and Service Users, and, by using, visiting, registering for, and/or otherwise participating in this Website, including the Services presented, promoted, and displayed on the Website, and by clicking on "I have read and agree to the terms of use," you hereby certify that: You are either a Service Professional or a prospective Service User, You have the authority to enter into these Terms of Use, You authorize the transfer of payment for Services requested through the use of the Website, and You agree to be bound by all terms and conditions of these Terms of Use and any other documents incorporated by reference herein. </p>
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
