<?php
###############################################################################
// returns the base url using server type.
// if server type is https/http
//$base_url = "http://".$_SERVER['HTTP_HOST']."/"; 
$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
$base_url .= '://' . $_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
###########################################################################################
?>