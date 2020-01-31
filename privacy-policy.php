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
        <title>Privacy Policy | <?php echo $settings->get_option("ct_page_title"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/backgrounds/<?php echo $settings->get_option('ct_favicon_image'); ?>"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
        <link href="css/owl.carousel.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/chosen.min.css" rel="stylesheet" type="text/css"/>
        <?php include_once 'include/header-scripts-style.php'; ?>		<style>		.list-privacy {			padding-left: 18px;		}		.list-privacy li {			    list-style: decimal;		}		</style>
    </head>
    <body>
        <?php include_once 'include/header.php'; ?>
        <!--Banner Start-->
        <section class="inner-page-banner">
            <img src="<?php echo $base_url; ?>images/about-us-banner.jpg"/>
            <div class="page-title">
                <h1 class="banner-heading text-center">Privacy Policy</h1>
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
							<h2 style="font-size: 24px; margin-bottom: 15px;">General</h2>
                            <p>The website <a href="http://yupserve.com">http://yupserve.com</a> and the YupServe Mobile Application is owned and operated by YupServe, whose registered office is at 22, Sweety Complex, GNG Square, Bhubaneswar -751020. This privacy policy is applies to the Website / App to comply with data protection legislation. This Privacy Policy regulates the processing of information relating to you and grants you various rights in respect of the information You provide us with.</p>
							
							<p>By accessing or using the Website / App, you agree to be bound by the terms and conditions of this Privacy Policy. This Privacy Policy is incorporated into and subject to the Terms of Service.</p>
							
							<p>For the purposes of this Privacy Policy, unless defined hereunder, all capitalised terms shall have the same meaning as ascribed to, in the Terms of use available at <a href="http://yupserve.com/term-and-conditions/">http://yupserve.com/term-and-conditions/</a>, unless the context otherwise requires.</p>
							
							<h2 style="font-size: 24px; margin-bottom: 15px;">User Information</h2>							<p>We use your user information for the following purposes:</p>
							<ul class="list-privacy">
								<li><p>To provide our services. This includes providing communications, promotions and information on events which may be of interest to you.</p></li>
								<li><p>To respond to any queries you may have and to communicate information to you. This includes sending you e-mail or other communications regarding updates of the Website / App, contacting you about your opinion on current services and/or potential services that may be offered.</p></li>
								<li><p>To operate and improve the Website / App in order to foster a positive user experience.</p></li>
								<li><p>To comply with the applicable laws.</p></li>							</ul>
							
							<h2 style="font-size: 24px; margin-bottom: 15px;">Service Providers</h2>
							<p>Information you share with us in any format, or post on our Website, would be provided to the service seekers, to enable them to evaluate you.</p>
							
							<h2 style="font-size: 24px; margin-bottom: 15px;">Customer Service</h2>
							<p>We may use your personal information to provide you better customer service. If we plan to publicly post any of your personal information on our or another Website, we would take your prior permission for same. YupServe is authorized to update customers, who have registered or called up the helpline, about offers and new services through email and SMS.</p>
							
							<h2 style="font-size: 24px; margin-bottom: 15px;">Keep Personal Information Secure</h2>
							<p>We provide secure transmission of personal information from your computer to our servers. Personal information collected by our Website is stored on secure servers.</p>

							<p>We would try to safeguard personal information provided by you to the best of our ability.</p>
							<p>Security and confidentiality of your password and account is entirely your responsibility. Also, you are the sole responsible person for any and all activities occurring under your account. You must inform us immediately in case of any unauthorized use of your account.</p>

							<p>Practices described in this Privacy Policy apply only to information gathered online at <a href="http://yupserve.com">http://yupserve.com</a>. It does not apply to any websites we link to, ads on our website or any partner websites.</p>

                            
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
