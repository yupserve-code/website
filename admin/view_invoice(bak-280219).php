<?php

ob_start();
session_start();
//include_once(dirname(__FILE__) . '/header.php');
//include(dirname(__FILE__) . '/user_session_check.php');
include(dirname(dirname(__FILE__)) . '/objects/class_connection.php');
include(dirname(dirname(__FILE__)) . '/assets/pdf/tfpdf/tfpdf.php');
include(dirname(dirname(__FILE__)) . '/objects/class_booking.php');
include(dirname(dirname(__FILE__)) . '/objects/class_setting.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services_methods.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services_methods_units.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services_addon.php');
include(dirname(dirname(__FILE__)) . '/objects/class_users.php');
include(dirname(dirname(__FILE__)) . '/objects/class_front_first_step.php');
include(dirname(dirname(__FILE__)) . '/objects/class_order_client_info.php');
include(dirname(dirname(__FILE__)) . '/objects/class_payments.php');
include(dirname(dirname(__FILE__)) . '/objects/class_general.php');


if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
    $protocol = 'https';
} else {
    $protocol = 'http';
}
$cur_dirname = basename(__DIR__);
if ($cur_dirname == 'public_html') {
    $cur_dirname = '';
}
$cur_dir = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], $cur_dirname)) . "/";
$dots = explode(".", $_SERVER['HTTP_HOST']);
if (sizeof($dots) > 2 && $dots[0] != 'www' && strlen($dots[1]) > 3) {
    define("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"].'/');
    define("BASE_URL", $protocol.'://'.$_SERVER['HTTP_HOST'].'/yupserve-application/');
    define("SITE_URL", $protocol . '://' . $_SERVER['HTTP_HOST'] . '/yupserve-application/');
    define("AJAX_URL",$protocol.'://'.$_SERVER['HTTP_HOST'].'/yupserve-application/assets/lib/');
    define("FRONT_URL",$protocol.'://'.$_SERVER['HTTP_HOST'].'/yupserve-application/front/');
} else {
    define("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"] .$cur_dir);
    define("BASE_URL", substr($cur_dir,0,-1));
    define("SITE_URL",$protocol.'://'.$_SERVER['HTTP_HOST'].$cur_dir);
    define("AJAX_URL",$protocol.'://'.$_SERVER['HTTP_HOST'].$cur_dir.'assets/lib/');
    define("FRONT_URL",$protocol.'://'.$_SERVER['HTTP_HOST'].$cur_dir.'front/'); 
}

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

$lang = $setting->get_option("ct_language");
$label_language_values = array();
$language_label_arr = $setting->get_all_labelsbyid($lang);

if ($language_label_arr[1] != "" || $language_label_arr[3] != "" || $language_label_arr[4] != "" || $language_label_arr[5] != "") {
    $default_language_arr = $setting->get_all_labelsbyid("en");
    if ($language_label_arr[1] != '') {
        $label_decode_front = base64_decode($language_label_arr[1]);
    } else {
        $label_decode_front = base64_decode($default_language_arr[1]);
    }
    if ($language_label_arr[3] != '') {
        $label_decode_admin = base64_decode($language_label_arr[3]);
    } else {
        $label_decode_admin = base64_decode($default_language_arr[3]);
    }
    if ($language_label_arr[4] != '') {
        $label_decode_error = base64_decode($language_label_arr[4]);
    } else {
        $label_decode_error = base64_decode($default_language_arr[4]);
    }
    if ($language_label_arr[5] != '') {
        $label_decode_extra = base64_decode($language_label_arr[5]);
    } else {
        $label_decode_extra = base64_decode($default_language_arr[5]);
    }

    $label_decode_front_unserial = unserialize($label_decode_front);
    $label_decode_admin_unserial = unserialize($label_decode_admin);
    $label_decode_error_unserial = unserialize($label_decode_error);
    $label_decode_extra_unserial = unserialize($label_decode_extra);

    $label_language_arr = array_merge($label_decode_front_unserial, $label_decode_admin_unserial, $label_decode_error_unserial, $label_decode_extra_unserial);
    foreach ($label_language_arr as $key => $value) {
        $label_language_values[$key] = urldecode($value);
    }
} else {
    $default_language_arr = $setting->get_all_labelsbyid("en");

    $label_decode_front = base64_decode($default_language_arr[1]);
    $label_decode_admin = base64_decode($default_language_arr[3]);
    $label_decode_error = base64_decode($default_language_arr[4]);
    $label_decode_extra = base64_decode($default_language_arr[5]);

    $label_decode_front_unserial = unserialize($label_decode_front);
    $label_decode_admin_unserial = unserialize($label_decode_admin);
    $label_decode_error_unserial = unserialize($label_decode_error);
    $label_decode_extra_unserial = unserialize($label_decode_extra);

    $label_language_arr = array_merge($label_decode_front_unserial, $label_decode_admin_unserial, $label_decode_error_unserial, $label_decode_extra_unserial);
    foreach ($label_language_arr as $key => $value) {
        $label_language_values[$key] = urldecode($value);
    }
}
/* new file include */
include(dirname(dirname(__FILE__)) . '/assets/lib/date_translate_array.php');

$dateformat = $setting->get_option('ct_date_picker_date_format');
$symbol_position = $setting->get_option('ct_currency_symbol_position');
$symbol = $setting->get_option('ct_currency_symbol');
$decimal = $setting->get_option('ct_price_format_decimal_places');
$dateformat = $setting->get_option('ct_date_picker_date_format');
$time_format = $setting->get_option('ct_time_format');
/* Invoice Details */
$order_id = (int) trim($_GET['order_id']);
$booking->order_id = $order_id;
$bookings = $booking->get_details_for_invoice_client();

/* Business Id by location id */

$business_name = $setting->get_option('ct_company_name');
$business_email = $setting->get_option('ct_company_email');
$business_address = $setting->get_option('ct_company_address');
$business_city = $setting->get_option('ct_company_city');
$business_state = $setting->get_option('ct_company_state');
$business_zip = $setting->get_option('ct_company_zip_code');
$business_country = $setting->get_option('ct_company_country');
$business_logo = $setting->get_option('ct_company_logo');
$business_phone = $setting->get_option('ct_company_phone');
$business_gst = "#22AAAAA0000A1Z5";

$invoice_number = strtoupper(date('M', strtotime($bookings[2]))) . '-' . $order_id;
$invoice_date = date($dateformat, strtotime($bookings[2]));

/* Client info */
$booking->client_id = $bookings[4];
$cinfo = $booking->get_client_info();
$client_name = $cinfo[3];
$client_email = $cinfo[1];
$client_phone = 'N/A';
if (strlen($cinfo[5]) >= 6) {
    $client_phone = $cinfo[5];
}
$client_address = $cinfo[7];
$client_notes = $cinfo[10];
$client_city = $cinfo[8];
$client_state = $cinfo[9];
$client_zip = $cinfo[6];
$client_country = $cinfo[8];


/* Payment Info */
$payments->order_id = $order_id;
$payinfo = $payments->get_payment_details();

$payment_transaction_id = $payinfo[3];
$payment_amount = $payinfo[4];
$payment_discount = $payinfo[5];
$payment_taxes = $payinfo[6];
$payment_partial_amount = $payinfo[7];
$payment_date = $payinfo[8];
$payment_net_amount = $payinfo[9];
if ($payinfo[2] == 'Pay At Venue') {
    $payment_method = $label_language_values['cash'];
} else if ($payinfo[2] == 'Card Payment') {
    $payment_method = $label_language_values['card_payment'];
} else if ($payinfo[2] == 'Bank Transfer') {
    $payment_method = 'Bank Transfer';
} else if ($payinfo[2] == 'Paypal') {
    $payment_method = 'Paypal';
} else if ($payinfo[2] == 'Stripe-payment' || $payinfo[2] == 'Card-payment' || $payinfo[2] == '2checkout-payment') {
    $payment_method = 'Card Payment';
} else {
    $payment_method = '';
}


/* Booking Details */
$booking_info_details = array();

$booking->booking_id = $order_id;
$bookings_info = $booking->readall_bookings();

$all_booking_details = array();

while ($row = mysqli_fetch_array($bookings_info)) {
    $all_booking_details[] = $row;
}
$service_price_sum = 0;
foreach ($all_booking_details as $book_info) {
    /* Service Details */

    $service->id = $book_info['service_id'];
    $s_info = $service->readone();
    $service_name = $s_info[1];


    /* Method detail */
    if ($book_info['method_id'] !== '' || $book_info['method_id'] !== null) {
        $smethod->id = $book_info['method_id'];
        $sminfo = $smethod->readone();
        $methodname = $sminfo[2];


        /* Unit Details */

        $smunit->units_id = $book_info['method_unit_id'];
        $sminfo = $smunit->readone();
        $unitname = $sminfo[3];
        $methodqty = $book_info['method_unit_qty'];
        $service_price = $general->ct_price_format_for_pdf($book_info['method_unit_qty_rate'], $symbol_position, $decimal);
    }

    $booking_info_details[] = array(
        "service_name" => $service_name,
        "unitname" => $unitname,
        "methodqty" => $methodqty,
        "service_price" => $service_price
    );
}

/* Addon's details */

$saddon->order_id = $order_id;
$sainfo = $saddon->addon_readall();
$all_addon_details = array();
//$sainfosize = count($sainfo);
if (mysqli_num_rows($sainfo) > 0) {
    while ($rows = mysqli_fetch_array($sainfo)) {
        $all_addon_details[] = $rows;
    }
    if (!empty($all_addon_details)) {
        foreach ($all_addon_details as $book_add_info) {

            $service->id = $book_add_info['service_id'];
            $s_info = $service->readone();
            $addon_service_name = $s_info[1];

            $saddon->id = $book_add_info['addons_service_id'];
            $addoninfo = $saddon->readone_single();
            $addonname = $addoninfo[2];
            $addonqty = $book_add_info['addons_service_qty'];

            // GET the pricing for each sub-service from proforma invoice table
            $sql_addons_prc = "SELECT * FROM ct_proforma_invoice WHERE order_id='{$order_id}' AND sub_service_id='{$book_add_info['addons_service_id']}'";
            $addons_srv_res = mysqli_query($conn, $sql_addons_prc);
            $row_addons_srv_prc = mysqli_fetch_object($addons_srv_res);
            $adons_service_rate = $row_addons_srv_prc->price;
			$adons_item_codes = $row_addons_srv_prc->item_code_type;
			$adons_tax_type = $row_addons_srv_prc->tax_type;
			$adons_tax_percent = $row_addons_srv_prc->tax_percentage;
			$adons_item_gst = $row_addons_srv_prc->gst;
			$adons_item_total = $row_addons_srv_prc->total;
            
            $addonprice = $general->ct_price_format_for_pdf($adons_service_rate, $symbol_position, $decimal);
            //$addonprice = $general->ct_price_format_for_pdf($book_add_info['addons_service_rate'], $symbol_position, $decimal);
			$adons_item_total = $general->ct_price_format_for_pdf($adons_item_total, $symbol_position, $decimal);
			$adons_item_gst = $general->ct_price_format_for_pdf($adons_item_gst, $symbol_position, $decimal);

            $booking_addon_details[] = array(
                "service_name" => $addon_service_name,
                "addonname" => $addonname,
                "addonqty" => $addonqty,
                "addonprice" => $addonprice,
				"adons_item_codes" => $adons_item_codes,
				"adons_tax_type" => $adons_tax_type,
				"adons_tax_percent" => $adons_tax_percent,
				"adons_item_gst" => $adons_item_gst,
				"adons_item_total" => $adons_item_total
				
            );
        }
    }
}


$backgroundimage = SITE_URL . "assets/images/background_image_client.jpeg";

if ($business_logo !== '' || $business_logo !== null) {
    $logo = SITE_URL . "assets/images/services/" . $business_logo;
} else {
    $logo = '';
}

$client_city_state = '';
if ($client_city != '' && $client_state != '') {
    $client_city_state = $client_city . "," . $client_state;
} elseif ($client_city != '' && $client_state == '') {
    $client_city_state = $client_city;
} elseif ($client_city == '' && $client_state != '') {
    $client_city_state = $client_state;
}

$pdf = new tFPDF();
$pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
$pdf->SetFont('DejaVu', '', 14);
$pdf->SetMargins(0, 0);
$pdf->SetTopMargin(0);
$pdf->SetAutoPageBreak(true, 0);
$pdf->AddPage();
$pdf->SetFillColor(242, 242, 242);
$pdf->SetTextColor(102, 103, 102);
$pdf->SetDrawColor(128, 255, 0);
$pdf->SetLineWidth(0);

$pdf->Cell(210, 297, '', 1, 1, 'C', true);

//$pdf->Image($logo, 20, 15, 20); /* Logo */
$pdf->SetFont('DejaVu', '', 12);
$pdf->Text(10, 10, $business_name);
$pdf->SetFont('DejaVu', '', 9);
$pdf->Text(10, 15, $business_address);
$pdf->Text(10, 20, $business_city . "," . $business_state);
$pdf->Text(10, 25, $business_country);
$pdf->Text(10, 30, $business_email);
$pdf->Text(10, 35, $business_phone);
$pdf->Text(10, 40, $business_gst);

$pdf->SetFont('DejaVu', '', 13);
$pdf->Text(80, 10, "BILL TO");

$pdf->SetFont('DejaVu', '', 10);
$pdf->Text(80, 15, ucwords($client_name));

$pdf->SetFont('DejaVu', '', 9);

/* here first no.is position from left and second is from top ok */
$pdf->Text(80, 20, $client_address);
$pdf->Text(80, 25, $client_city_state);
$pdf->Text(80, 33, $client_phone);
$pdf->Text(80, 38, $client_email);

$pdf->SetFont('DejaVu', '', 13);
$pdf->Text(155, 10, $label_language_values['invoice']);
$pdf->SetFont('DejaVu', '', 9);
$pdf->Text(155, 15, "#" . strtoupper(date('M', strtotime($invoice_date))) . "-" . sprintf("%04d", $order_id));
$pdf->Text(155, 20, $label_language_values['payment_method']." : Pay Localy");
$pdf->Text(155, 25, $label_language_values['invoice_date']." : ".$invoice_date);
$pdf->Text(155, 30, $label_language_values['booked_on'] . " : ");
/* for booking date and time */
$book_times;
if ($time_format == 24) {
    $book_times = date("H:i", strtotime($bookings[1]));
} else {
    $book_times = str_replace($english_date_array, $selected_lang_label, date("h:i A", strtotime($bookings[1])));
}
$datevar = date($dateformat, strtotime($bookings[1]));
$pdf->Text($pdf->GetStringWidth(($label_language_values['booked_on'])) + 160, 30, date($dateformat, strtotime($bookings[1])));
$pdf->Text($pdf->GetStringWidth(($label_language_values['booked_on'])) + $pdf->GetStringWidth(($datevar)) + 160, 30, $book_times);





/*$pdf->SetFont('DejaVu', '', 30);
$pdf->SetTextColor(55, 55, 55);
$pdf->Text(30, 60, $label_language_values['invoice']);
$pdf->SetFont('DejaVu', '', 22);
$pdf->Text(31, 75, "#" . strtoupper(date('M', strtotime($invoice_date))) . "-" . sprintf("%04d", $order_id));*/

/*$pdf->SetFont('DejaVu', '', 13);
$pdf->SetTextColor(255, 255, 255);
$pdf->Text(98, 60, $label_language_values['invoice_date']);
 $pdf->Text(120 - $pdf->GetStringWidth("Invoice Date")/2,77,"Invoice Date"); 
$pdf->Text(147 - $pdf->GetStringWidth("Invoice Due Date")/2,77,"Invoice Due Date"); 
$pdf->Text(160, 60, $label_language_values['payment_method']);
 $pdf->Text(185 - $pdf->GetStringWidth("Payment Method")/2,77,"Payment Method"); */

/*$pdf->SetFont('DejaVu', '', 11);
$pdf->SetTextColor(255, 255, 255);
$pdf->Text(100, 68, $invoice_date);
 $pdf->Text(109 - $pdf->GetStringWidth(date($dateformat,strtotime($bookings[3])))/2,82,$invoice_date); 
 $pdf->Text(147 - $pdf->GetStringWidth(date($dateformat,strtotime($bookings[3])))/2,82,$invoice_date); 
if ($payment_method == 'Bank Transfer') {
    $pdf->Text(173 - ($pdf->GetStringWidth($payment_method) / 2), 68, strtoupper($payment_method));
} else {
    $pdf->Text(177 - ($pdf->GetStringWidth($payment_method) / 2), 68, strtoupper($payment_method));
}
 $pdf->Text(181 - $pdf->GetStringWidth(strtoupper($payment_method))/2,82,strtoupper($payment_method)); */
 
$edge=2; // Gap between line and border 

//$pdf->Line($edge, $edge,$width-$edge,$edge); // Horizontal line at top	


$pdf->SetDrawColor(0,0,0);
$pdf->Line(0, 50, 210, 50); // 20mm from each edge

$pdf->SetFont('DejaVu', '', 11);
$pdf->SetTextColor(55, 55, 55);
$pdf->Text(10,56,"No"); 
$pdf->Text(30, 56, $label_language_values['service_name']);
$pdf->Text(65, 56, "HSN/SAC");
$pdf->Text(100,56,"Unit Price"); 
$pdf->Text(130,56,"GST"); 
$pdf->Text(150,56,"TAX"); 
/*$pdf->Text(150, 107, $label_language_values['qty']);
 $pdf->Text(150,107,""); */
$pdf->Text(179, 56, $label_language_values['price']);

$pdf->SetDrawColor(0,0,0);
$pdf->Line(0, 60, 210, 60); 


$addondetails_startpoint = 75;
$pdf->SetFont('DejaVu', '', 9);
$pdf->Text(10, 70, '1');
$pdf->Text(30, 70, $booking_info_details[0]['service_name']);

$pdf->SetFont('DejaVu', '', 9);
foreach ($booking_info_details as $book_detail) {
    if ($book_detail['unitname'] != "") {
        $pdf->Text(22, $addondetails_startpoint, $book_detail['unitname']);
        $pdf->Text(150, $addondetails_startpoint, $book_detail['methodqty']);
       // $pdf->Text(150,$addondetails_startpoint,$book_detail['service_price']);
		//$pdf->Text(123 - $pdf->GetStringWidth($book_detail["service_price"]), $addondetails_sttpoint, $book_detail['service_price']);
        //$pdf->Text(190 - $pdf->GetStringWidth($book_detail["service_price"]), $addondetails_startpoint, $book_detail['service_price']);
        $addondetails_startpoint = $addondetails_startpoint + 5;
    }
}

$addondetails_sttpoint = 0;
if (!empty($booking_addon_details)) {
    $addondetails_sttpoint = $addondetails_startpoint + 10;
    $pdf->SetFont('DejaVu', '', 11);
    $pdf->Text(30, $addondetails_sttpoint - 5, "Add-ons");
    $pdf->SetFont('DejaVu', '', 9);

    foreach ($booking_addon_details as $booking_addon) {

        /* $pdf->Text(20,$addondetails_sttpoint,$booking_addon['service_name']); */
        $pdf->Text(30, $addondetails_sttpoint, $booking_addon['addonname']);
        /*$pdf->Text(150, $addondetails_sttpoint, $booking_addon['addonqty']);
         $pdf->Text(150,$addondetails_sttpoint,$booking_addon['methodqty']); 
         $pdf->Text(180,$addondetails_startpoint,$book_detail['service_price']);*/
		$pdf->Text(70 - $pdf->GetStringWidth($booking_addon["adons_item_codes"]), $addondetails_sttpoint, strtoupper($booking_addon['adons_item_codes']));
		$pdf->Text(113 - $pdf->GetStringWidth($booking_addon["addonprice"]), $addondetails_sttpoint, $booking_addon['addonprice']);
		$pdf->Text(132 - $pdf->GetStringWidth($booking_addon["adons_item_gst"]), $addondetails_sttpoint, $booking_addon['adons_item_gst']);
		$pdf->Text(155 - $pdf->GetStringWidth($booking_addon["adons_tax_percent"]), $addondetails_sttpoint, $booking_addon['adons_tax_percent']."%");
        $pdf->Text(193 - $pdf->GetStringWidth($booking_addon["adons_item_total"]), $addondetails_sttpoint, $booking_addon['adons_item_total']);

        $addondetails_sttpoint = $addondetails_sttpoint + 5;
    }
}

$sql_xtra_reqmnts = "SELECT ri.* FROM ct_extra_requirements_invoice ri 
                    JOIN ct_booking_extra_requirements er ON ri.order_id = er.order_id";
$res_xtra_reqmnts = mysqli_query($conn, $sql_xtra_reqmnts);

if (mysqli_num_rows($res_xtra_reqmnts) > 0) {
    $row_xtra_reqmnts = mysqli_fetch_object($res_xtra_reqmnts);
    $pdf->Text(22, $addondetails_sttpoint, 'Extra Requirements');
    $pdf->Text(190 - $pdf->GetStringWidth($general->ct_price_format_for_pdf($row_xtra_reqmnts->price, $symbol_position, $decimal)), $addondetails_sttpoint, $general->ct_price_format_for_pdf($row_xtra_reqmnts->price, $symbol_position, $decimal));
}

$pdf->SetFont('DejaVu', '', 10);


if ($addondetails_sttpoint == 0) {
    $addondetails_sttpoint = $addondetails_startpoint;
}
/* $pdf->Text(160,170,"Frequently Discount"); */
$pdf->Text(155 - $pdf->GetStringWidth($label_language_values['sub_total']), 260, $label_language_values['sub_total']);
if ($payinfo[11] == 'O') {
    $fd = "Once";
} elseif ($payinfo[11] == 'W') {
    $fd = "Weekly";
} elseif ($payinfo[11] == 'B') {
    $fd = "Bi-Weekly";
} elseif ($payinfo[11] == 'M') {
    $fd = "Monthly";
} else {
    $fd = "None";
}

$pdf->Text(100,230,"Price"); 
$pdf->Text(130,230,"Total GST"); 
$pdf->Text(150,230,"TAX"); 
/*$pdf->Text(150, 107, $label_language_values['qty']);
 $pdf->Text(150,107,""); */
$pdf->Text(179, 225, $label_language_values['price']);

$pdf->SetDrawColor(0,0,0);
$pdf->Line(0, 235 , 210, 235); // 20mm from each edge

$printamount = $general->ct_price_format_for_pdf($payment_amount, $symbol_position, $decimal);
$printtaxes = $general->ct_price_format_for_pdf($payment_taxes, $symbol_position, $decimal);
$printdiscount = '-' . $general->ct_price_format_for_pdf($payment_discount, $symbol_position, $decimal);
//$printfrequency = '-' . $general->ct_price_format_for_pdf($payinfo[12], $symbol_position, $decimal);
$printnetamount = $general->ct_price_format_for_pdf($payment_net_amount, $symbol_position, $decimal);

$pdf->SetFont('DejaVu', '', 10);
$pdf->Text(100,240, $printamount);
$pdf->Text(130,240, $printtaxes);
$pdf->Text(150,240, "40");
$pdf->Text(179,240, $printnetamount);


//$pdf->Text(155 - $pdf->GetStringWidth($label_language_values['frequently_discount'] . "(" . $fd . ")"), $addondetails_sttpoint + 10, $label_language_values['frequently_discount'] . "(" . $fd . ")");
$pdf->Text(155 - $pdf->GetStringWidth($label_language_values['coupon_discount']), 265, $label_language_values['coupon_discount']);
$pdf->Text(155 - $pdf->GetStringWidth($label_language_values['tax']), 270, $label_language_values['tax']);



$pdf->SetFont('DejaVu', '', 10);
$pdf->Text(190 - $pdf->GetStringWidth($printamount), 260, $printamount);
//$pdf->Text(190 - $pdf->GetStringWidth($printfrequency), $addondetails_sttpoint + 20, $printfrequency);
$pdf->Text(190 - $pdf->GetStringWidth($printdiscount), 265, $printdiscount);
$pdf->Text(190 - $pdf->GetStringWidth($printtaxes), 270, $printtaxes);

$pdf->SetFont('DejaVu', '', 13);

$pdf->Text(155 - $pdf->GetStringWidth($label_language_values['total']), 275, $label_language_values['total']);
$pdf->Text(188 - $pdf->GetStringWidth($printtaxes), 275, $printnetamount);

$pdf->SetFont('DejaVu', '', 12);
$pdf->SetTextColor(102, 103, 102);

/* 	$pdf->Text(23,217,"Payment Information");
  $pdf->SetFont('DejaVu','',8);
  $pdf->Text(23,222,"Please pay for the service on or before ".date($dateformat,strtotime($bookings[3]))); */



$pdf->SetDrawColor(0,0,0);
$pdf->Line(0, 250, 210, 250); // 20mm from each edge

$pdf->SetFont('DejaVu', '', 11);
$pdf->Text(23, 260, "Authorized Signatory");
$pdf->SetFont('DejaVu', '', 13);
$pdf->Text(30, 270, $business_name);
// file path to be saved in server folder for later viewing of invoice
$file = 'pdf/yupserve' . '-' . $invoice_number . '.pdf';

$query = "UPDATE `ct_proforma_invoice` SET `pdf_name`='{$file}' WHERE `order_id`='{$order_id}'";
$result = mysqli_query($conn, $query);

ob_start();
// first send the output to server folder for permanent saving the document
$pdf->Output($file, "F");
//$pdf->Output($file, "D");
$pdf->Output($file, "I");
exit(0);
?>