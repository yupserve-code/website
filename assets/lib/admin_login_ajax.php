<?php  session_start();include(dirname(dirname(dirname(__FILE__))) . '/header.php');include(dirname(dirname(dirname(__FILE__))) . "/objects/class_connection.php");include(dirname(dirname(dirname(__FILE__))) . "/objects/class_login_check.php");include(dirname(dirname(dirname(__FILE__))) . "/objects/class_adminprofile.php");include(dirname(dirname(dirname(__FILE__))) . '/objects/class.phpmailer.php');include(dirname(dirname(dirname(__FILE__))).'/objects/class_setting.php');include(dirname(dirname(dirname(__FILE__))).'/objects/class_front_first_step.php');$con = new cleanto_db();$conn = $con->connect();$settings = new cleanto_setting();$settings->conn = $conn;$first_step=new cleanto_first_step();$first_step->conn=$conn;if($settings->get_option('ct_smtp_authetication') == 'true'){	$mail_SMTPAuth = '1';	if($settings->get_option('ct_smtp_hostname') == "smtp.gmail.com"){		$mail_SMTPAuth = 'Yes';	}	}else{	$mail_SMTPAuth = '0';	if($settings->get_option('ct_smtp_hostname') == "smtp.gmail.com"){		$mail_SMTPAuth = 'No';	}}$mail = new cleanto_phpmailer();$mail->Host = $settings->get_option('ct_smtp_hostname');$mail->Username = $settings->get_option('ct_smtp_username');$mail->Password = $settings->get_option('ct_smtp_password');$mail->Port = $settings->get_option('ct_smtp_port');$mail->SMTPSecure = $settings->get_option('ct_smtp_encryption');$mail->SMTPAuth = $mail_SMTPAuth;$objlogin = new cleanto_login_check();$objlogin->conn = $conn;$objadmininfo = new cleanto_adminprofile();$objadmininfo->conn = $conn;$company_email=$settings->get_option('ct_company_email');$company_name=$settings->get_option('ct_company_name');if (isset($_POST['checkadmin'])) {    $name = trim(strip_tags(mysqli_real_escape_string($conn,$_POST['name'])));    $password = md5($_POST['password']);    $objlogin->remember = $_POST['remember'];    $objlogin->cookie_passwords = $_POST['password'];    $t = $objlogin->checkadmin($name, $password);} elseif (isset($_POST['logout'])) {    session_destroy();} elseif (isset($_GET['resetpassword'])) {    $newpass = $_GET['password'];    $id = $_GET['userid'];    $objlogin->resetpassword($id, $newpass);} elseif (isset($_POST['action']) && $_POST['action'] == 'forget_password') {    $email = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['email'])));    $query = "SELECT id as user_id FROM ct_admin_info WHERE email='{$email}' AND role='admin'";    $result = mysqli_query($conn, $query);        if (mysqli_num_rows($result) >= 1) {        $row = mysqli_fetch_row($result);        $userid = $row;        date_default_timezone_set('Asia/Kolkata');	$current_time = date('Y-m-d H:i:s');        //$ency_code = base64_encode(strtotime($current_time));        $ency_code = base64_encode(base64_encode($userid) . '#' . strtotime("+120 minutes", strtotime($current_time)));        $to = $_POST['email'];        $subject = "Forget Password";        $from = $settings->get_option('ct_company_email');        $body = '<html>	<head>		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />		<title>Welcome to ' . $settings->get_option('ct_company_name'). '</title>	</head>	<body>		<div style="margin: 0;padding: 0;font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;box-sizing: border-box;">			<div style="display: block !important;max-width: 600px !important;margin: 0 auto !important;clear: both !important;">				<table style="border: 1px solid #c2c2c2;width: 100%;float: left;margin: 30px 0px;-webkit-border-radius: 5px;-moz-border-radius: 5px;-o-border-radius: 5px;border-radius: 5px;">					<tbody>						<tr>							<td>								<div style="padding: 25px 30px;background: #fff;float: left;width: 90%;display: block;">									<div style="border-bottom: 1px solid #e6e6e6;float: left;width: 100%;display: block;">										<h3 style="color: #606060;font-size: 20px;margin: 15px 0px 0px;font-weight: 400;">Dear Admin,</h3><br />                                                                                <p style="color: #606060;font-size: 15px;margin: 10px 0px 15px;">You have requested for reset password request.</p>										<p style="color: #606060;font-size: 15px;margin: 10px 0px 15px;">Forgot your password - <a href="' . SITE_URL . 'admin/forgot-password_admin.php?code=' . $ency_code . '" >Reset it here</a></p>									</div>									<div style="padding: 15px 0px;float: left;width: 100%;">										<h5 style="color: #606060;font-size: 13px;margin: 10px 0px px;">Regards,</h5>										<h6 style="color: #606060;font-size: 14px;font-weight: 600;margin: 10px 0px 15px;">' . $settings->get_option('ct_company_name') . '</h6>									</div>								</div>							</td>						</tr>					</tbody>				</table>			</div>		</div>	</body></html>';        if($settings->get_option('ct_smtp_hostname') != '' && $settings->get_option('ct_email_sender_name') != '' && $settings->get_option('ct_email_sender_address') != '' && $settings->get_option('ct_smtp_username') != '' && $settings->get_option('ct_smtp_password') != '' && $settings->get_option('ct_smtp_port') != ''){            $mail->IsSMTP();        }else{            $mail->IsMail();        }        $mail->SMTPDebug  = 0;        $mail->IsHTML(true);        $mail->From = $company_email;        $mail->FromName = $company_name;        $mail->Sender = $company_email;        $mail->AddAddress($to,"Admin");        $mail->Subject = $subject;        $mail->Body = $body;        $mail->send();	$mail->ClearAllRecipients();        echo "Reset password link sent to your registered email address";    } else {        echo "not";    }} elseif (isset($_POST['action']) && $_POST['action'] == 'reset_new_password') {    //$objadmininfo->id = $_SESSION['user_id'];    //$objadmininfo->password = $_POST['retype_new_reset_pass'];    //$reset_new_pass = $objadmininfo->update_password();    $password = md5($_POST['retype_new_reset_pass']);    mysqli_query($conn, "UPDATE ct_admin_info SET password='{$password}' WHERE role='admin'");    echo "Password reset successfully";    unset($_SESSION['fp_admin']);    unset($_SESSION['fp_user']);    exit;}?>