<?php
include_once (dirname(__FILE__) . "/objects/class_connection.php");
include_once (dirname(__FILE__) . '/class_configure.php');

	$cvars = new cleanto_myvariable();
	$host = trim($cvars->hostnames);
	$un = trim($cvars->username);
	$ps = trim($cvars->passwords);
	$db = trim($cvars->database);
	$con = @new cleanto_db();
	$conn = $con->connect();

	$json = array();
	$sql = "UPDATE ct_api_token SET site_launched = '0' WHERE id = '1'";
	$result = mysqli_query($conn, $sql);
	$json['success'] = TRUE;
	
	echo json_encode($json);
?>