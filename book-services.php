<?php
$filename = './config.php';

$file = file_exists($filename);

if ($file) {

    if (!filesize($filename) > 0) {

        header('location:ct_install.php');
    } else {

        include(dirname(__FILE__) . "/objects/class_connection.php");

        $cvars = new cleanto_myvariable();

        $host = trim($cvars->hostnames);

        $un = trim($cvars->username);

        $ps = trim($cvars->passwords);

        $db = trim($cvars->database);

        $con = new cleanto_db();
        $conn = $con->connect();
        if (($conn->connect_errno == '0' && ($host == '' || $db == '')) || $conn->connect_errno != '0') {
            header('Location: ./config_index.php');
        }
    }
} else {
    echo "Config file does not exist";
}

session_start();
$service_array = array("method" => array());
$_SESSION['ct_cart'] = $service_array;
$_SESSION['freq_dis_amount'] = '';
$_SESSION['ct_details'] = '';
$_SESSION['staff_id_cal'] = '';
$_SESSION['time_duration'] = 0;

include_once 'page_initialization.php';
include(dirname(__FILE__) . '/class_configure.php');
include(dirname(__FILE__) . "/header.php");
include(dirname(__FILE__) . "/objects/class_general.php");
include(dirname(__FILE__) . "/objects/class_services.php");
include(dirname(__FILE__) . "/objects/class_users.php");
include(dirname(__FILE__) . '/objects/class_setting.php');
include(dirname(__FILE__) . '/objects/class_frequently_discount.php');
include(dirname(__FILE__) . '/objects/class_service_methods_design.php');
include(dirname(__FILE__) . "/objects/class_version_update.php");
include(dirname(__FILE__) . "/objects/class_payment_hook.php");
include(dirname(__FILE__) . "/objects/class_promo_code.php");
include(dirname(__FILE__) . "/objects/class_front_first_step.php");
include(dirname(__FILE__) . "/objects/class_services_addon.php");
include(dirname(__FILE__) . "/objects/class_services_addon_rates.php");
include(dirname(__FILE__) . "/objects/class_services_methods.php");
include(dirname(__FILE__) . "/objects/class_services_methods_units.php");

$cvars = new cleanto_myvariable();
$host = trim($cvars->hostnames);
$un = trim($cvars->username);
$ps = trim($cvars->passwords);
$db = trim($cvars->database);

$con = @new cleanto_db();
$conn = $con->connect();

if (($conn->connect_errno == '0' && ($host == '' || $db == '')) || $conn->connect_errno != '0') {
    header('Location: ' . BASE_URL . '/config_index.php');
    exit(0);
}

$check_existing_tables_index = $con->check_existing_tables_index($conn);
if ($check_existing_tables_index == 'table_not_exist' || $check_existing_tables_index == 'table_exist_but_no_data') {
    ?>
    <script type="text/javascript">
        var AdminloginCredentialObj = {'site_url': '<?php echo SITE_URL; ?>'};
        var AdminloginCredential_url = AdminloginCredentialObj.site_url;
        window.location = AdminloginCredential_url + "config_index.php";
    </script>
    <?php
}

$promo = new cleanto_promo_code();
$promo->conn = $conn;

$addons = new cleanto_services_addon();
$addons->conn = $conn;

$addons_rates = new cleanto_services_addon_rates();
$addons_rates->conn = $conn;

$first_step = new cleanto_first_step();
$first_step->conn = $conn;

$settings = new cleanto_setting();
$settings->conn = $conn;

$general = new cleanto_general();
$general->conn = $conn;

$symbol_position = $settings->get_option('ct_currency_symbol_position');
$decimal = $settings->get_option('ct_price_format_decimal_places');
// service ID
$service_id = (int) trim($_GET['service_id']);
// check service ID is numeric

if (!is_numeric($service_id)) {
    header('Location: ' . BASE_URL);
    exit(0);
}

// check that service_id exits in DB/Not
$sql_srv = "SELECT * FROM ct_services WHERE id='{$service_id}'";
$srv_res = mysqli_query($conn, $sql_srv);
if (mysqli_num_rows($srv_res) == 0) {
    header('Location: ' . BASE_URL);
    exit(0);
} else {
    $service_dtls = mysqli_fetch_object($srv_res);
}
/*
 * Language
 */
$language_names = array(
    "en" => urlencode("English (United States)"),
    "ary" => urlencode("العربية المغربية"),
    "ar" => urlencode("العربية"),
    "az" => urlencode("Azərbaycan dili"),
    "azb" => urlencode("گؤنئی آذربایجان"),
    "bg_BG" => urlencode("Български"),
    "bn_BD" => urlencode("বাংলা"),
    "bs_BA" => urlencode("Bosanski"),
    "ca" => urlencode("Català"),
    "ceb" => urlencode("Cebuano"),
    "cs_CZ" => urlencode("Čeština‎"),
    "cy" => urlencode("Cymraeg"),
    "da_DK" => urlencode("Dansk"),
    "de_CH_informal" => urlencode("Deutsch (Schweiz, Du)"),
    "de_DE_formal" => urlencode("Deutsch (Sie)"),
    "de_DE" => urlencode("Deutsch"),
    "de_CH" => urlencode("Deutsch (Schweiz)"),
    "el" => urlencode("Ελληνικά"),
    "en_CA" => urlencode("English (Canada)"),
    "en_GB" => urlencode("English (UK)"),
    "en_NZ" => urlencode("English (New Zealand)"),
    "en_ZA" => urlencode("English (South Africa)"),
    "en_AU" => urlencode("English (Australia)"),
    "eo" => urlencode("Esperanto"),
    "es_ES" => urlencode("Español"),
    "et" => urlencode("Eesti"),
    "eu" => urlencode("Euskara"),
    "fa_IR" => urlencode("فارسی"),
    "fi" => urlencode("Suomi"),
    "fr_FR" => urlencode("Français"),
    "gd" => urlencode("Gàidhlig"),
    "gl_ES" => urlencode("Galego"),
    "gu" => urlencode("ગુજરાતી"),
    "haz" => urlencode("هزاره گی"),
    "hi_IN" => urlencode("हिन्दी"),
    "hr" => urlencode("Hrvatski"),
    "hu_HU" => urlencode("Magyar"),
    "hy" => urlencode("Հայերեն"),
    "id_ID" => urlencode("Bahasa Indonesia"),
    "is_IS" => urlencode("Íslenska"),
    "it_IT" => urlencode("Italiano"),
    "ja" => urlencode("日本語"),
    "ka_GE" => urlencode("ქართული"),
    "ko_KR" => urlencode("한국어"),
    "lt_LT" => urlencode("Lietuvių kalba"),
    "lv" => urlencode("Latviešu valoda"),
    "mk_MK" => urlencode("Македонски јазик"),
    "mr" => urlencode("मराठी"),
    "ms_MY" => urlencode("Bahasa Melayu"),
    "my_MM" => urlencode("ဗမာစာ"),
    "nb_NO" => urlencode("Norsk bokmål"),
    "nl_NL" => urlencode("Nederlands"),
    "nl_NL_formal" => urlencode("Nederlands (Formeel)"),
    "nn_NO" => urlencode("Norsk nynorsk"),
    "oci" => urlencode("Occitan"),
    "pl_PL" => urlencode("Polski"),
    "pt_PT" => urlencode("Português"),
    "pt_BR" => urlencode("Português do Brasil"),
    "ro_RO" => urlencode("Română"),
    "ru_RU" => urlencode("Русский"),
    "sk_SK" => urlencode("Slovenčina"),
    "sl_SI" => urlencode("Slovenščina"),
    "sq" => urlencode("Shqip"),
    "sr_RS" => urlencode("Српски језик"),
    "sv_SE" => urlencode("Svenska"),
    "szl" => urlencode("Ślōnskŏ gŏdka"),
    "th" => urlencode("ไทย"),
    "tl" => urlencode("Tagalog"),
    "tr_TR" => urlencode("Türkçe"),
    "ug_CN" => urlencode("Uyƣurqə"),
    "uk" => urlencode("Українська"),
    "vi" => urlencode("Tiếng Việt"),
    "zh_TW" => urlencode("繁體中文"),
    "zh_HK" => urlencode("香港中文版"),
    "zh_CN" => urlencode("简体中文"),
);



/* NAME */

$objservice = new cleanto_services();

$objservice->conn = $conn;

$user = new cleanto_users();

$user->conn = $conn;

//$settings = new cleanto_setting();
//$settings->conn = $conn;

$frequently_discount = new cleanto_frequently_discount();

$frequently_discount->conn = $conn;

$objservice_method_design = new cleanto_service_methods_design();

$objservice_method_design->conn = $conn;



$payment_hook = new cleanto_paymentHook();

$payment_hook->conn = $conn;

$payment_hook->payment_extenstions_exist();

$purchase_check = $payment_hook->payment_purchase_status();



$objcheckversion = new cleanto_version_update();

$objcheckversion->conn = $conn;

$current = $settings->get_option('ct_version');

if ($current == "") {

    $objcheckversion->insert_option("ct_version", "1.1");
}

$current = $settings->get_option('ct_version');

if ($current < 1.1) {

    $settings->set_option("ct_version", "1.1");

    $objcheckversion->update1_1();
}

$current = $settings->get_option('ct_version');

if ($current < 1.2) {

    $settings->set_option("ct_version", "1.2");

    $objcheckversion->update1_2();
}

$current = $settings->get_option('ct_version');

if ($current < 1.3) {

    $settings->set_option("ct_version", "1.3");

    $objcheckversion->update1_3();
}

$current = $settings->get_option('ct_version');

if ($current < 1.4) {

    $settings->set_option("ct_version", "1.4");

    $objcheckversion->update1_4();
}

$current = $settings->get_option('ct_version');

if ($current < 1.5) {

    $settings->set_option("ct_version", "1.5");

    $objcheckversion->update1_5();
}

$current = $settings->get_option('ct_version');

if ($current < 1.6) {

    $settings->set_option("ct_version", "1.6");

    $objcheckversion->update1_6();
}

$current = $settings->get_option('ct_version');

if ($current < 2.0) {

    $settings->set_option("ct_version", "2.0");

    $objcheckversion->update2_0();
}

$current = $settings->get_option('ct_version');

if ($current < 2.1) {

    $settings->set_option("ct_version", "2.1");
}

$current = $settings->get_option('ct_version');

if ($current < 2.2) {

    $settings->set_option("ct_version", "2.2");

    $objcheckversion->update2_2();
}

$current = $settings->get_option('ct_version');

if ($current < 2.3) {

    $settings->set_option("ct_version", "2.3");

    $objcheckversion->update2_3();
}

$current = $settings->get_option('ct_version');

if ($current < 2.4) {

    $settings->set_option("ct_version", "2.4");

    $objcheckversion->update2_4();
}

$current = $settings->get_option('ct_version');

if ($current < 2.5) {

    $settings->set_option("ct_version", "2.5");

    $objcheckversion->update2_5();
}

$current = $settings->get_option('ct_version');

if ($current < 2.6) {

    $settings->set_option("ct_version", "2.6");

    $objcheckversion->update2_6();
}

$current = $settings->get_option('ct_version');

if ($current < 2.7) {

    $settings->set_option("ct_version", "2.7");

    $objcheckversion->update2_7();
}

$current = $settings->get_option('ct_version');

if ($current < 2.8) {

    $settings->set_option("ct_version", "2.8");

    $objcheckversion->update2_8();
}



$current = $settings->get_option('ct_version');

if ($current < 3.0) {

    $settings->set_option("ct_version", "3.0");

    $objcheckversion->update3_0();
}



$current = $settings->get_option('ct_version');

if ($current < 3.1) {

    $settings->set_option("ct_version", "3.1");
}



$current = $settings->get_option('ct_version');

if ($current < 3.2) {

    $settings->set_option("ct_version", "3.2");

    $objcheckversion->update3_2();
}



$current = $settings->get_option('ct_version');

if ($current < 3.3) {

    $settings->set_option("ct_version", "3.3");

    $objcheckversion->update3_3();
}



$current = $settings->get_option('ct_version');

if ($current < 4.0) {

    $settings->set_option("ct_version", "4.0");

    $objcheckversion->update4_0();
}



$current = $settings->get_option('ct_version');

if ($current < 4.1) {

    $settings->set_option("ct_version", "4.1");

    $objcheckversion->update4_1();
}

if ($current < 4.2) {

    $settings->set_option("ct_version", "4.2");

    $objcheckversion->update4_2();
}



$current = $settings->get_option('ct_version');

if ($current < 4.3) {

    $settings->set_option("ct_version", "4.3");

    $objcheckversion->update4_3();
}



$current = $settings->get_option('ct_version');

if ($current < 4.4) {

    $settings->set_option("ct_version", "4.4");

    $objcheckversion->update4_4();
}



$current = $settings->get_option('ct_version');

if ($current < 5.0) {

    $settings->set_option("ct_version", "5.0");

    $objcheckversion->update5_0();
}



$label_language_values = array();

if (isset($_SESSION['current_lang'])) {

    $lang = $_SESSION['current_lang'];

    $language_label_arr = $settings->get_all_labelsbyid($_SESSION['current_lang']);
} else {

    $lang = $settings->get_option("ct_language");

    $language_label_arr = $settings->get_all_labelsbyid($lang);
}

if ($language_label_arr[1] != "" || $language_label_arr[3] != "" || $language_label_arr[4] != "" || $language_label_arr[5] != "" || $language_label_arr[6] != "") {

    $default_language_arr = $settings->get_all_labelsbyid("en");

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

    if ($language_label_arr[6] != '') {

        $label_decode_front_form_errors = base64_decode($language_label_arr[6]);
    } else {

        $label_decode_front_form_errors = base64_decode($default_language_arr[6]);
    }



    $label_decode_front_unserial = unserialize($label_decode_front);

    $label_decode_admin_unserial = unserialize($label_decode_admin);

    $label_decode_error_unserial = unserialize($label_decode_error);

    $label_decode_extra_unserial = unserialize($label_decode_extra);

    $label_decode_front_form_errors_unserial = unserialize($label_decode_front_form_errors);



    $label_language_arr = array_merge($label_decode_front_unserial, $label_decode_admin_unserial, $label_decode_error_unserial, $label_decode_extra_unserial, $label_decode_front_form_errors_unserial);

    foreach ($label_language_arr as $key => $value) {

        $label_language_values[$key] = urldecode($value);
    }
} else {

    $default_language_arr = $settings->get_all_labelsbyid("en");



    $label_decode_front = base64_decode($default_language_arr[1]);

    $label_decode_admin = base64_decode($default_language_arr[3]);

    $label_decode_error = base64_decode($default_language_arr[4]);

    $label_decode_extra = base64_decode($default_language_arr[5]);

    $label_decode_front_form_errors = base64_decode($default_language_arr[6]);

    $label_decode_front_unserial = unserialize($label_decode_front);

    $label_decode_admin_unserial = unserialize($label_decode_admin);

    $label_decode_error_unserial = unserialize($label_decode_error);

    $label_decode_extra_unserial = unserialize($label_decode_extra);

    $label_decode_front_form_errors_unserial = unserialize($label_decode_front_form_errors);

    $label_language_arr = array_merge($label_decode_front_unserial, $label_decode_admin_unserial, $label_decode_error_unserial, $label_decode_extra_unserial, $label_decode_front_form_errors_unserial);

    foreach ($label_language_arr as $key => $value) {

        $label_language_values[$key] = urldecode($value);
    }
}
$frontimage = $settings->get_option('ct_front_image');
if ($frontimage != '') {
    $imagepath = SITE_URL . "assets/images/backgrounds/" . $frontimage;
} else {
    $imagepath = '';
}

$english_date_array = array(
    "January", "Jan", "February", "Feb", "March", "Mar", "April", "Apr", "May", "June", "Jun", "July", "Jul", "August", "Aug", "September", "Sep", "October", "Oct", "November", "Nov", "December", "Dec", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "su", "mo", "tu", "we", "th", "fr", "sa", "AM", "PM");

$selected_lang_label = array(
    ucfirst(strtolower($label_language_values['january'])),
    ucfirst(strtolower($label_language_values['jan'])),
    ucfirst(strtolower($label_language_values['february'])),
    ucfirst(strtolower($label_language_values['feb'])),
    ucfirst(strtolower($label_language_values['march'])),
    ucfirst(strtolower($label_language_values['mar'])),
    ucfirst(strtolower($label_language_values['april'])),
    ucfirst(strtolower($label_language_values['apr'])),
    ucfirst(strtolower($label_language_values['may'])),
    ucfirst(strtolower($label_language_values['june'])),
    ucfirst(strtolower($label_language_values['jun'])),
    ucfirst(strtolower($label_language_values['july'])),
    ucfirst(strtolower($label_language_values['jul'])),
    ucfirst(strtolower($label_language_values['august'])),
    ucfirst(strtolower($label_language_values['aug'])),
    ucfirst(strtolower($label_language_values['september'])),
    ucfirst(strtolower($label_language_values['sep'])),
    ucfirst(strtolower($label_language_values['october'])),
    ucfirst(strtolower($label_language_values['oct'])),
    ucfirst(strtolower($label_language_values['november'])),
    ucfirst(strtolower($label_language_values['nov'])),
    ucfirst(strtolower($label_language_values['december'])),
    ucfirst(strtolower($label_language_values['dec'])),
    ucfirst(strtolower($label_language_values['sun'])),
    ucfirst(strtolower($label_language_values['mon'])),
    ucfirst(strtolower($label_language_values['tue'])),
    ucfirst(strtolower($label_language_values['wed'])),
    ucfirst(strtolower($label_language_values['thu'])),
    ucfirst(strtolower($label_language_values['fri'])),
    ucfirst(strtolower($label_language_values['sat'])),
    ucfirst(strtolower($label_language_values['su'])),
    ucfirst(strtolower($label_language_values['mo'])),
    ucfirst(strtolower($label_language_values['tu'])),
    ucfirst(strtolower($label_language_values['we'])),
    ucfirst(strtolower($label_language_values['th'])),
    ucfirst(strtolower($label_language_values['fr'])),
    ucfirst(strtolower($label_language_values['sa'])),
    strtoupper($label_language_values['am']),
    strtoupper($label_language_values['pm']));
?>
<!Doctype html>
<head>
    <title><?php echo $settings->get_option("ct_page_title"); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/backgrounds/<?php echo $settings->get_option('ct_favicon_image'); ?>"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/owl.carousel.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo BASE_URL; ?>css/chosen.min.css" rel="stylesheet" type="text/css"/>
    <?php include_once 'include/header-scripts-style.php'; ?>
    <?php if ($settings->get_option('ct_seo_meta_description') != '') { ?>
        <meta name="description" content="<?php echo $settings->get_option('ct_seo_meta_description'); ?>">
    <?php } ?>
    <?php if ($settings->get_option('ct_seo_og_title') != '') { ?>
        <meta property="og:title" content="<?php echo $settings->get_option('ct_seo_og_title'); ?>" />
    <?php } ?>
    <?php if ($settings->get_option('ct_seo_og_type') != '') { ?>
        <meta property="og:type" content="<?php echo $settings->get_option('ct_seo_og_type'); ?>" />
    <?php } ?>
    <?php if ($settings->get_option('ct_seo_og_url') != '') { ?>
        <meta property="og:url" content="<?php echo $settings->get_option('ct_seo_og_url'); ?>" />
    <?php } ?>
    <?php if ($settings->get_option('ct_seo_og_image') != '') { ?>
        <meta property="og:image" content="<?php echo SITE_URL; ?>assets/images/og_tag_img/<?php echo $settings->get_option('ct_seo_og_image'); ?>" />
    <?php } ?>
    <?php
    if ($settings->get_option('ct_google_analytics_code') != '') {
        ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $settings->get_option('ct_google_analytics_code'); ?>"></script>
        <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {

            dataLayer.push(arguments);

        }

        gtag('js', new Date());

        gtag('config', '<?php echo $settings->get_option('ct_google_analytics_code'); ?>');

        </script>

        <?php
    }
    ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ct-main.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ct-common.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tooltipster.bundle.min.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tooltipster-sideTip-shadow.min.css" type="text/css" media="all" />

    <?php if (in_array($lang, array('ary', 'ar', 'azb', 'fa_IR', 'haz'))) { ?>	

        <!-- Front RTL style -->

        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/cta-front-rtl.css" type="text/css" media="all" />

        <?php
    }

    $check_zip_code = explode(",", $settings->get_option('ct_bf_zip_code'));

    $dateFormat = $settings->get_option('ct_date_picker_date_format');

    function date_format_js($date_Format) {
        $chars = array(
            // Day
            'd' => 'DD',
            'j' => 'DD',
            // Month
            'm' => 'MM',
            'M' => 'MMM',
            'F' => 'MMMM',
            // Year
            'Y' => 'YYYY',
            'y' => 'YYYY',
        );

        return strtr((string) $date_Format, $chars);
    }
    ?>

    <script>
        var ct_postalcode_statusObj = {'ct_postalcode_status': '<?php echo $settings->get_option('ct_postalcode_status'); ?>', 'zip_show_status': '<?php echo $check_zip_code[0]; ?>'};
        var date_format_for_js = '<?php echo date_format_js($dateFormat); ?>';
        var scrollable_cartObj = {'scrollable_cart': '<?php echo $settings->get_option('ct_cart_scrollable'); ?>'};
    </script>
    <?php
    $ct_frontend_fonts_val = $settings->get_option('ct_frontend_fonts');

    if ($ct_frontend_fonts_val == 'Molle') {
        ?>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Molle:400i" />

        <?php
    } else if ($ct_frontend_fonts_val == 'Coda Caption') {
        ?>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Coda+Caption:800" />

        <?php
    } else {
        ?>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $ct_frontend_fonts_val; ?>:300,400,700" />

        <?php
    }
    ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/login-style.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ct-responsive.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/ct-reset.min.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/jquery-ui.min.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/intlTelInput.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/jquery-ui.theme.min.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/font-awesome/css/font-awesome.min.css" type="text/css" media="all">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/line-icons/simple-line-icons.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/daterangepicker.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/bootstrap/bootstrap.css" type="text/css" media="all" />

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/star_rating.min.css" type="text/css" media="all">

    <?php if ($settings->get_option('ct_stripe_payment_form_status') == 'on') { ?>

        <script src="https://js.stripe.com/v2/" type="text/javascript"></script>	

    <?php } ?>

    <?php if ($settings->get_option('ct_2checkout_status') == 'Y') { ?>

        <script src="https://www.2checkout.com/checkout/api/2co.min.js" type="text/javascript"></script>

    <?php } ?>

    <script src="<?php echo BASE_URL; ?>/assets/js/jquery-2.1.4.min.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/jquery.mask.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/moment.min.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/daterangepicker.js" type="text/javascript"></script>

    <!--Debasis Behera-->

    <script src="<?php echo $base_url; ?>js/bootstrap.min.js" type="text/javascript"></script>

    <script src="<?php echo $base_url; ?>js/jquery.waypoints.min.js" type="text/javascript"></script>

    <script src="<?php echo $base_url; ?>js/owl.carousel.min.js" type="text/javascript"></script>

    <script src="<?php echo $base_url; ?>js/chosen.jquery.js" type="text/javascript"></script>

    <script src="<?php echo $base_url; ?>js/scrollreveal.min.js" type="text/javascript"></script>

    <script src="<?php echo $base_url; ?>js/wow.min.js" type="text/javascript"></script>

    <script src="<?php echo $base_url; ?>js/custom-script.js" type="text/javascript"></script>    

    <!--Debasis Behera-->

    <?php include(dirname(__FILE__) . '/extension/ct-common-front-extension-js.php'); ?>

    <script src="<?php echo BASE_URL; ?>/assets/js/ct-common-jquery.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/tooltipster.bundle.min.js" type="text/javascript"></script>

    <?php include(dirname(__FILE__) . "/admin/language_js_objects.php"); ?>

    <script src="<?php echo BASE_URL; ?>/assets/js/jquery-ui.min.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/jquery.nicescroll.min.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/intlTelInput.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/jquery.payment.min.js" type="text/javascript"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/star_rating_min.js" type="text/javascript"></script>

    <!-- **Google - Fonts** -->

    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,700,800" rel="stylesheet"> 

    <script src="<?php echo BASE_URL; ?>/assets/js/jquery.validate.min.js"></script>

    <style>

        .error {

            color: red;

        }

        #ct, a, h1, h2, h3, h4, h5, h6, span, p, div, label, li, ul {

            font-family: <?php echo $settings->get_option('ct_frontend_fonts'); ?>  !important;

        }

    </style>

    <?php if ($imagepath != '') { ?>

        <style>

            #ct .ct-fixed-background {

                background-image: url(<?php echo $imagepath; ?>) !important;

            }

        </style>

    <?php } else { ?>

        <style>

            #ct .ct-fixed-background {

                background: #ffffff !important;

            }

        </style>

    <?php } ?>

    <?php
    if ($settings->get_option('ct_cart_scrollable') == 'N') {

        $ct_cart_scrollable_position = 'relative !important';
        ?>

        <style>#ct .not-scroll-custom{ margin-top: 0 !important; }</style>

        <?php
    } else {

        $ct_cart_scrollable_position = 'relative';
    }
    ?>

    <?php
    echo "<style>

	/* primary color */

		.cleanto{

			color: " . $settings->get_option('ct_text_color') . " !important;

		}

		.cleanto .ct-link.ct-mybookings{

			color:" . $settings->get_option('ct_text_color_on_bg') . " !important;

			background:" . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .ct-link.ct-mybookings:hover{

			color:" . $settings->get_option('ct_text_color_on_bg') . " !important;

			background:" . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .ct-main-left .ct-list-header .ct-logged-in-user a.ct-link,

		.cleanto .ct-complete-booking-main .ct-link,

		.cleanto .ct-discount-coupons a.ct-apply-coupon.ct-link{

			color: " . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .ct-link:hover,

		.cleanto .ct-main-left .ct-list-header .ct-logged-in-user a.ct-link:hover,

		.cleanto .ct-complete-booking-main .ct-link:hover,

		.cleanto .ct-discount-coupons a.ct-apply-coupon.ct-link:hover{

			color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto a,

		.cleanto .ct-link,

		.cleanto .ct-addon-count .ct-btn-group .ct-btn-text{

			color: " . $settings->get_option('ct_text_color') . " !important;

		}

		.cleanto a.ct-back-to-top i.icon-arrow-up,

		.cleanto .calendar-wrapper .calendar-header a.next-date:hover .icon-arrow-right:before,

		.cleanto .calendar-wrapper .calendar-header a.previous-date:hover .icon-arrow-left:before{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		.cleanto .calendar-body .ct-week:hover a span,

		.cleanto .ct-extra-services-list ul.addon-service-list li .ct-addon-ser:hover .addon-price{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		.cleanto #ct-type-2 .service-selection-main .ct-services-dropdown .ct-service-list:hover,

		.cleanto #ct-type-method .ct-services-method-dropdown .ct-service-method-list:hover,

		.cleanto .common-selection-main .common-data-dropdown .data-list:hover{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

			background:" . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .selected-is:hover,

		.cleanto #ct-type-2 .service-is:hover,

		.cleanto #ct-type-method .service-method-is:hover{

			border-color:" . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .ct-extra-services-list ul.addon-service-list li .ct-addon-ser:hover span:before{

			border-top-color:" . $settings->get_option('ct_primary_color') . " !important;

		}

		

		.cleanto .calendar-wrapper .calendar-header a.next-date:hover,

		.cleanto .calendar-wrapper .calendar-header a.previous-date:hover,

		.cleanto .calendar-body .ct-week:hover{

			background:" . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .calendar-body .ct-show-time .time-slot-container ul li.time-slot{

			background:" . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .calendar-body .dates .ct-week.by_default_today_selected.active_today span,

		.cleanto .calendar-body .ct-show-time .time-slot-container ul li.time-slot,

		.cleanto .calendar-body .dates .ct-week.active span {

			color:" . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		.cleanto .calendar-header a.previous-date,

		.cleanto .calendar-header a.next-date{

			color:" . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		

		.cleanto .ct-custom-checkbox  ul.ct-checkbox-list label:hover span,

		.cleanto .ct-custom-radio ul.ct-radio-list label:hover span{

			border:1px solid " . $settings->get_option('ct_secondary_color') . " !important;

		}

		#ct-login .ct-main-forget-password .ct-info-btn,

		.cleanto button,

		.cleanto #ct-front-forget-password .ct-front-forget-password .ct-info-btn,	

		.cleanto .ct-button{

			color:" . $settings->get_option('ct_text_color_on_bg') . " !important;

			background:" . $settings->get_option('ct_primary_color') . " !important;

			border: 2px solid " . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .ct-display-coupon-code .ct-coupon-value{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

			background:" . $settings->get_option('ct_secondary_color') . " !important;

		}

		/* for front date legends */

		.cleanto .calendar-body .ct-show-time .time-slot-container .ct-slot-legends .ct-available-new {

			background: " . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .calendar-body .ct-show-time .time-slot-container .ct-slot-legends .ct-selected-new{

			background: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		/* seconday color */

		.nicescroll-cursors{

			background-color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

				

	    .cleanto .calendar-body .dates .ct-week.active,

	    .cleanto .calendar-body .ct-show-time.shown{

	    	background: " . $settings->get_option('ct_secondary_color') . " !important;

	    }

	/* background color all css  HOVER */

		.ct-white-color a{

			color: #FFFFFF !important;

			background: #FFFFFF !important;

		}

		.cleanto .ct-selected,

		.cleanto .ct-selected-data,

		.cleanto .ct-provider-list ul.provders-list li input[type='radio']:checked + lable span,

		.cleanto .ct-list-services ul.services-list li input[type='radio']:checked + lable span,

		.cleanto .ct-extra-services-list ul.addon-service-list li input[type='checkbox']:checked label span,

		.cleanto .ct-discount-list ul.ct-discount-often li input[type='radio']:checked + .ct-btn-discount,

		.cleanto #ct-tslots .ct-date-time-main .time-slot-selection-main .time-slot.ct-selected,

		.cleanto .ct-button:hover,

		.cleanto-login .ct-main-forget-password .ct-info-btn:hover,

		.cleanto #ct-front-forget-password .ct-front-forget-password .ct-info-btn:hover,

		.cleanto  input[type='submit']:hover,

		.cleanto  input[type='reset']:hover,

		.cleanto  input[type='button']:hover,

		.cleanto  button:hover{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

			background: " . $settings->get_option('ct_secondary_color') . " !important;

			border-color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .ct-step-heading{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

			background: " . $settings->get_option('ct_secondary_color') . " !important;

			border-color: " . $settings->get_option('ct_secondary_color') . " !important;

			border-radius: 2px;

			box-shadow: 0 4px 4px " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .promocodes{

		   color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

		   background: " . $settings->get_option('ct_secondary_color') . " !important;

		   border-color: " . $settings->get_option('ct_secondary_color') . " !important;

		  }

		.cleanto #ct-price-scroll{

			border-color: " . $settings->get_option('ct_primary_color') . " !important;

			box-shadow: 0 4px 4px #ccc !important;

			position: " . $ct_cart_scrollable_position . ";

		}

		

		.cleanto .ct-cart-wrapper .ct-cart-label-total-amount,

		.cleanto .ct-cart-wrapper .ct-cart-total-amount{

			color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		

		.cleanto .ct-list-services ul.services-list li input[type='radio']:checked + .ct-service ,

		.cleanto .ct-provider-list ul.provders-list li input[type='radio']:checked + .ct-provider ,

		.cleanto .ct-extra-services-list ul.addon-service-list li input[type='checkbox']:checked + .ct-addon-ser {

			border-color: " . $settings->get_option('ct_secondary_color') . " !important;

			box-shadow: 0 0 10px 1px " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .ct-extra-services-list ul.addon-service-list li input[type='checkbox']:checked + .ct-addon-ser span:before{

			border-top-color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .ct-extra-services-list ul.addon-service-list li input[type='checkbox']:checked + .ct-addon-ser .addon-price{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		

		.cleanto .border-c:hover,

		.cleanto .ct-list-services ul.services-list li .ct-service:hover,

		.cleanto .ct-list-services ul.addon-service-list li .ct-addon-ser:hover,

		.cleanto #ct-meth-unit-type-2.ct-meth-unit-count .bedroom-box .ct-bedroom-btn:hover,

		.cleanto #ct-meth-unit-type-2.ct-meth-unit-count .bathroom-box .ct-bathroom-btn:hover,

		.cleanto #ct-duration-main.ct-service-duration .ct-duration-list .duration-box .ct-duration-btn:hover,

		.cleanto .ct-extra-services-list .ct-addon-extra-count .ct-common-addon-list .ct-addon-box .ct-addon-btn:hover,

		.cleanto .ct-discount-list ul.ct-discount-often li .ct-btn-discount:hover,

		.cleanto .ct-custom-radio ul.ct-radio-list label:hover span,

		.cleanto .ct-custom-checkbox  ul.ct-checkbox-list label:hover span{

			border-color: " . $settings->get_option('ct_primary_color') . " !important;

			

		}

		

		

		.cleanto .ct-custom-checkbox  ul.ct-checkbox-list input[type='checkbox']:checked + label span{

			border: 1px solid " . $settings->get_option('ct_secondary_color') . " !important;

			background: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .ct-custom-radio ul.ct-radio-list input[type='radio']:checked + label span{

			border:5px solid " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto #ct-meth-unit-type-2.ct-meth-unit-count .bedroom-box .ct-bedroom-btn.ct-bed-selected,

		.cleanto #ct-meth-unit-type-2.ct-meth-unit-count .bathroom-box .ct-bathroom-btn.ct-bath-selected,

		.cleanto #ct-duration-main.ct-service-duration .ct-duration-list .duration-box .ct-duration-btn.duration-box-selected,

		.cleanto .ct-extra-services-list .ct-addon-extra-count .ct-common-addon-list .ct-addon-box .ct-addon-selected{

			background: " . $settings->get_option('ct_secondary_color') . " !important;

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

			border-color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		

		.cleanto .ct-button.ct-btn-abs,

		.cleanto .calendar-header,

		.cleanto .panel-login .panel-heading .col-xs-6,

		.cleanto a.ct-back-to-top {

			background-color: " . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto a.ct-back-to-top:hover,

		.cleanto .weekdays{

			background-color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		

		.cleanto .calendar-body .dates .ct-week.by_default_today_selected{

			background-color: " . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .calendar-body .dates .ct-week.by_default_today_selected a span{

			color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		

		.cleanto .calendar-body .dates .ct-week.selected_date.active{

			background-color: " . $settings->get_option('ct_secondary_color') . " !important;

			border-bottom: thin solid " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .calendar-body .ct-show-time .time-slot-container ul li.time-slot:hover,

		.cleanto .calendar-body .ct-show-time .time-slot-container ul li.time-slot.ct-booked,

		.cleanto .calendar-body .ct-show-time.shown{

			background-color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		

		

		.cleanto #ct-meth-unit-type-2.ct-meth-unit-count .bedroom-box .ct-bedroom-btn.ct-bed-selected,

		.cleanto #ct-meth-unit-type-2.ct-meth-unit-count .bathroom-box .ct-bathroom-btn.ct-bath-selected,

		.cleanto #ct-duration-main.ct-service-duration .ct-duration-list .duration-box .ct-duration-btn.duration-box-selected,

		.cleanto .ct-extra-services-list .ct-addon-extra-count .ct-common-addon-list .ct-addon-box .ct-addon-selected{

			/* background: " . $settings->get_option('ct_secondary_color') . " !important; */

		}

		

		/* hover inputs */

		.cleanto input[type='text']:hover,

		.cleanto input[type='password']:hover,

		.cleanto input[type='email']:hover,

		.cleanto input[type='url']:hover,

		.cleanto input[type='tel']:hover,

		.cleanto input[type='number']:hover,

		.cleanto input[type='range']:hover,

		.cleanto input[type='date']:hover,

		.cleanto textarea:hover,

		.cleanto select:hover,

		.cleanto input[type='search']:hover,

		.cleanto input[type='submit']:hover,

		.cleanto input[type='button']:hover{

			border-color: " . $settings->get_option('ct_primary_color') . " !important;

		}

		

		/* Focus inputs */

		.cleanto input[type='text']:focus,

		.cleanto input[type='password']:focus,

		.cleanto input[type='email']:focus,

		.cleanto input[type='url']:focus,

		.cleanto input[type='tel']:focus,

		.cleanto input[type='number']:focus,

		.cleanto input[type='range']:focus,

		.cleanto input[type='date']:focus,

		.cleanto textarea:focus,

		.cleanto select:focus,

		.cleanto input[type='search']:focus,

		.cleanto input[type='submit']:focus,

		.cleanto input[type='button']:focus{

			border-color: " . $settings->get_option('ct_secondary_color') . " !important;

			/* box-shadow: 0 0 0 1.5px " . $settings->get_option('ct_secondary_color') . " inset !important; */

		}

		.cleanto .ct-tooltip-link {color: " . $settings->get_option('ct_secondary_color') . " !important;}

	    /* for custom css option */

		" . $settings->get_option('ct_custom_css') . "

		

		.cleanto .ct_method_tab-slider--nav .ct_method_tab-slider-tabs {

		  background: " . $settings->get_option('ct_primary_color') . " !important;

		}

		.cleanto .ct_method_tab-slider--nav .ct_method_tab-slider-tabs:after {

		  background: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.cleanto .ct_method_tab-slider--nav .ct_method_tab-slider-trigger {

		  color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		.cleanto .ct_method_tab-slider--nav .ct_method_tab-slider-trigger.active {

		  color: " . $settings->get_option('ct_text_color_on_bg') . " !important;

		}

		.ct-list-services ul.services-list li input[type=\"radio\"]:checked + .ct-service::after{

			background-color: " . $settings->get_option('ct_secondary_color') . " !important;

		}

		.rating-md{

			font-size: 1.5em !important ;

			display: table;

			margin: auto;

		}

	</style>";
    ?>

    <script>

        jQuery(document).ready(function () {

            var $sidebar = jQuery("#ct-price-scroll"),
                    $window = jQuery(window),
                    offset = $sidebar.offset(),
                    topPadding = 250;

            fulloffset = jQuery("#ct").offset();



            $window.scroll(function () {

                var color = jQuery('#color_box').val();

                jQuery("#ct-price-scroll").css({'box-shadow': '0px 0px 1px ' + color + '', 'position': 'absolute', 'margin-left': '15px;'});

            });

        });

    </script>

    <script type="text/javascript">

        function myFunction() {

            var input = document.getElementById('coupon_val');

            var div = document.getElementById('display_code');

            div.innerHTML = input.value;

        }

    </script>

</head>

<body>

    <?php include_once 'include/header.php'; ?>

    <!--Side Fixed Offer Part End-->

    <div class="container ct-wrapper cleanto" id="ct"> <!-- main wrapper -->

        <div class="ct-fixed-background"></div>

        <!-- loader -->

        <?php if ($settings->get_option("ct_loader") == 'css' && $settings->get_option("ct_custom_css_loader") != '') { ?>

            <div class="ct-loading-main" align="center">

                <?php echo $settings->get_option("ct_custom_css_loader"); ?>

            </div>

        <?php } elseif ($settings->get_option("ct_loader") == 'gif' && $settings->get_option("ct_custom_gif_loader") != '') { ?>

            <div class="ct-loading-main" align="center">

                <img style="margin-top:18%;" src="<?php echo BASE_URL; ?>/assets/images/gif-loader/<?php echo $settings->get_option("ct_custom_gif_loader"); ?>"></img>

            </div>

        <?php } else { ?>

            <div class="ct-loading-main">

                <div class="loader">Loading...</div>

            </div>

        <?php } ?>

        <?php
        if ($settings->get_option("ct_special_offer") == "Y") {
            ?>

            <div class="promocodes" id="promocodes"><?php echo $settings->get_option("ct_special_offer_text"); ?></div>

            <?php
        }
        ?>

        <!--        <div class="right-bg-image"></div>

                <div class="left-bg-image"></div>-->

        <div class="ct-main-wrapper">

            <div class="order-heading text-center">Book Now</div>

            <div class="ct_container">

                <!-- left side main booking form -->

                <?php
                /* added for display flags start */

                //$langs_selects = $settings->count_lang();
                //if ($settings->get_option("ct_front_language_selection_dropdown") == "Y" && $langs_selects > 1) {
                ?>

                <!--                    <div class="ct-sm-12 np">

                                        <span class="pull-left ct-link np" style="text-decoration: none;"><?php // echo $label_language_values['set_language'];                    ?>

                                            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/flags/flags.min.css" type="text/css" media="all" />

                <?php
                // $langs_select = $settings->get_all_languages();
                // $langs_array = array('en' => 'us', 'ary' => 'ma', 'ar' => 'ar', 'az' => 'az', 'azb' => 'az', 'bg_BG' => 'bg', 'bn_BD' => 'bn', 'bs_BA' => 'bs', 'ca' => 'catalonia', 'ceb' => 'ph', 'cs_CZ' => 'cz', 'cy' => 'cy', 'da_DK' => 'dk', 'de_CH_informal' => 'de', 'de_DE_formal' => 'de', 'de_DE' => 'de', 'de_CH' => 'de', 'el' => 'gr', 'en_CA' => 'ca', 'en_GB' => 'gb', 'en_NZ' => 'nz', 'en_ZA' => 'za', 'en_AU' => 'au', 'eo' => 'sa', 'es_ES' => 'es', 'et' => 'et', 'eu' => 'eu', 'fa_IR' => 'ir', 'fi' => 'fi', 'fr_FR' => 'fr', 'gd' => 'gd', 'gl_ES' => 'gl', 'gu' => 'in', 'haz' => 'pe', 'hi_IN' => 'in', 'hr' => 'hr', 'hu_HU' => 'hu', 'hy' => 'pe', 'id_ID' => 'id', 'is_IS' => 'is', 'it_IT' => 'it', 'ja' => 'jp', 'ka_GE' => 'ge', 'ko_KR' => 'kr', 'lt_LT' => 'lt', 'lv' => 'lv', 'mk_MK' => 'mk', 'mr' => 'in', 'ms_MY' => 'my', 'my_MM' => 'mm', 'nb_NO' => 'no', 'nl_NL' => 'nl', 'nl_NL_formal' => 'nl', 'nn_NO' => 'no', 'oci' => 'es', 'pl_PL' => 'pl', 'pt_PT' => 'pt', 'pt_BR' => 'br', 'ro_RO' => 'ro', 'ru_RU' => 'ru', 'sk_SK' => 'sk', 'sl_SI' => 'si', 'sq' => 'al', 'sr_RS' => 'rs', 'sv_SE' => 'se', 'szl' => 'pl', 'th' => 'th', 'tl' => 'ph', 'tr_TR' => 'tr', 'ug_CN' => 'az', 'uk' => 'ua', 'vi' => 'vi', 'zh_TW' => 'tw', 'zh_HK' => 'hk', 'zh_CN' => 'cn');
                // while ($res = mysqli_fetch_array($langs_select)) {
                // if ($res['language_status'] == 'Y') {
                ?>

                                                    <a href="javascript:void(0);" class="select_language_view" data-langs="<?php // echo $res['language'];                  ?>" title="<?php // echo urldecode($language_names[$res['language']]);                  ?>"><img src="<?php echo SITE_URL; ?>assets/flags/blank.gif" class="flag flag-<?php // echo $langs_array[$res['language']];                  ?>" /></a>

                <?php
                // } else {
                //}
                // }
                // 
                ?>

                                        </span>

                                    </div>-->

                <?php
                //}

                /* added for display flags end */
                ?>

                <?php
                /* added for display flags start */

                $langs_selects = $settings->count_lang();

                if ($settings->get_option("ct_front_language_selection_dropdown") == "Y" && $langs_selects > 1) {
                    ?>

                    <div class="row">

                        <div class="ct-main-left col-lg-12 col-md-12 col-sm-12 br-5 np mt-30 mb-30">

                        <?php } else { ?>

                            <div class="ct-main-left ct-sm-12 ct-md-12 ct-xs-12 mt-30 br-5 np mb-30">

                            <?php } ?>

                            <?php if ($settings->get_option("ct_postalcode_status") == 'Y') { ?>

                                <div class="left-div">
                                    <?php
                                    $sql_service = "SELECT * FROM ct_services WHERE id='{$service_id}'";
                                    $res_service = mysqli_query($conn, $sql_service);
                                    if (mysqli_num_rows($res_service) > 0) {
                                        $row_service = mysqli_fetch_object($res_service);
                                        ?>
                                        <div class="form-heading">Selected Service Category: <?php echo $row_service->title; ?></div>
                                    <?php } ?>
                                    <div class="ct-list-services ct-common-box">

                                        <div class="ct-list-header">

                                            <h3 class="header3"><?php echo $label_language_values['where_would_you_like_us_to_provide_service']; ?></h3>

                                                    <!--<p class="ct-sub">Choose your service and property size</p>-->

                                        </div>

                                        <div class="ct-address-area-main">
                                            <div class="ct-postal-code">
                                                <h6 class="header6">Please Enter Pincode
                                                    <?php if ($settings->get_option("ct_front_tool_tips_status") == 'on' && $settings->get_option("ct_front_tool_tips_postal_code") != '') { ?>
                                                        <a class="ct-tooltip" href='#' title="<?php echo $settings->get_option("ct_front_tool_tips_postal_code"); ?>"><i class="fa fa-info-circle fa-lg"></i></a>
                                                    <?php } ?></h6>
                                                <div class="ct-md-6 ct-sm-6 ct-xs-12 remove_show_error_class">
                                                    <?php
                                                    $postalcode_placeholder = explode(',', $settings->get_option_postal("ct_postal_code"));
                                                    ?>
                                                    <input type="text" class="ct-postal-input number_only" name="ct_postal_code" id="ct_postal_code" autocomplete="off" placeholder="Enter pincode" value="" maxlength="6"/>
                                                    <label class="postal_code_error error"></label>
                                                    <label class="postal_code_available"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- end area based -->
                                <!-- Start service list -->
                                <div class="ct-list-services ct-common-box fl hide_allsss">
                                    <input id="total_cart_count" type="hidden" name="total_cart_count" value='1'/>
                                    <div class="ct-scroll-meth-unit"></div>
                                    <div class="services-method-list-dropdown fl show_methods_after_service_selection show_single_service_method" id="ct-type-method">
                                        <div class="service-method-selection-main">
                                            <div class="ct-services-method-dropdown s_method_names">
                                            </div>
                                        </div>
                                    </div>
                                    <label class="empty_cart_error" id="empty_cart_error"></label>
                                    <label class="no_units_in_cart_error" id="no_units_in_cart_error"></label>
                                    <input type='hidden' id="no_units_in_cart_err" value=''>
                                    <input type='hidden' id="no_units_in_cart_err_count" value=''>
                                    <!-- hrs selected  -->
                                    <div class="ct-service-duration ct-md-12 ct-sm-12 s_m_units_design_1" id="ct-duration-main">
                                        <div class="ct-inner-box border-c">
                                            <div class="fl ct-md-12 mt-5 mb-15 np duration_hrs"></div>
                                            <!-- end duration hrs  -->
                                        </div>
                                    </div>
                                    <!-- 1. bedroom and bathroom counting dropdown -->
                                    <div class="ct-meth-unit-count ct-md-12 ct-sm-12 np ct_hidden fl s_m_units_design_2" id="ct-meth-unit-type-1">
                                        <div class="ct-inner-box border-c ser_design_2_units"></div>
                                    </div>
                                    <!-- 1.end dropdown list bathroom bedroom -->
                                    <!-- 2. boxed bathroom bedroom  -->
                                    <div class="ct-meth-unit-count ct-md-12 ct-sm-12 np s_m_units_design_3" id="ct-meth-unit-type-2">
                                        <div class="ct-inner-box border-c ser_design_3_units"></div>
                                    </div>
                                    <!-- 2. end boxed bathroom bedroom -->
                                    <div class="ct-meth-unit-count ct-md-12 ct-sm-12 s_m_units_design_4" id="ct-meth-unit-type-3">
                                        <div class="ct-inner-box border-c ">
                                            <div class="fl ct-bedrooms ct-btn-group ct-md-12 mt-5 mb-15 np">
                                                <div class="ct-inner-box border-c ser_design_4_units"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end service list -->

                                <!-- display booked service -->

                                <!-- end display booked service -->

                                <!-- start sub-service list -->
                                <div class="ct-extra-services-list ct-common-box add_on_lists res-min-reg">
                                    <label id="service_not_selected_error"></label>
                                    <?php
                                    $addons->service_id = $service_id;
                                    $addons_data = $addons->readall_from_service();
                                    if (mysqli_num_rows($addons_data) > 0) {
                                        ?>
                                        <script>
                                            $(document).ready(function () {
                                                $('.ct-tooltip-addon').tooltipster({
                                                    animation: 'grow',
                                                    delay: 20,
                                                    theme: 'tooltipster-shadow',
                                                    trigger: 'hover'
                                                });
                                            });
                                        </script>
                                        <div class="ct-list-header">
                                            <h3 class="header3">Services</h3>
                                            <?php if ($settings->get_option("ct_front_tool_tips_status") == 'on' && $settings->get_option("ct_front_tool_tips_addons_services") != '') { ?>
                                                <a class="ct-tooltip-addon" href="#" data-toggle="tooltip" title="<?php echo $settings->get_option("ct_front_tool_tips_addons_services"); ?>."><i class="fa fa-info-circle fa-lg"></i></a>
                                            <?php } ?>
<!--                                            <p class="ct-sub" style="display: none;"><?php echo $label_language_values['for_initial_cleaning_only_contact_us_to_apply_to_recurrings']; ?></p>-->
                                        </div>
                                        <?php if ($settings->get_option('ct_addons_default_design') == 1) { ?>
                                            <ul class="addon-service-list fl remove_addonsss row">
                                                <?php
                                                if (mysqli_num_rows($addons_data) > 0) {

                                                    while ($adonsdata = mysqli_fetch_array($addons_data)) {

                                                        $addons_rates->addon_service_id = $adonsdata['service_id'];

                                                        $addonrates_data = $addons_rates->readone_from_serviceid();

                                                        /* CHANGED BY ME FROM Y TO N */
                                                        $mmnameee = 'ad_unit' . $adonsdata['id'];
                                                        ?>
                                                        <li class="ct-sm-6 ct-md-4 ct-lg-2 ct-xs-12 mb-15">
                                                                <input type="checkbox" name="addon-checkbox" class="addon-checkbox add_addon_in_cart_for_multipleqty" data-status="2" data-duration_value="-1" data-id="<?php echo $adonsdata['id']; ?>" id="ct-addon-<?php echo $adonsdata['id']; ?>" data-rate="<?php echo $adonsdata['base_price']; ?>" data-service_id="<?php echo $adonsdata['service_id']; ?>" data-method_id="0" data-method_name="<?php echo $adonsdata['addon_service_name']; ?>" data-units_id="<?php echo $adonsdata['id']; ?>" data-type="<?php echo "addon"; ?>" data-mnamee="<?php echo $mmnameee; ?>" />
                                                                <label class="ct-addon-ser border-c ct_addon_ser<?php echo $adonsdata['id']; ?>" for="ct-addon-<?php echo $adonsdata['id']; ?>"><span></span>
                                                                    <div class="addon-price" style="display: none;"><?php echo $general->ct_price_format($adonsdata['base_price'], $symbol_position, $decimal); ?></div>
                                                                    <div class="ct-addon-img">
                                                                        <img src="
                                                                             <?php
                                                                             if ($adonsdata['image'] == '' && $adonsdata['predefine_image'] == '') {
                                                                                 echo SITE_URL . '/assets/images/services/default.png';
                                                                             } else if ($adonsdata['image'] == '') {
                                                                                 echo SITE_URL . '/assets/images/addons-images/' . $adonsdata['predefine_image'];
                                                                             } else {
                                                                                 echo SITE_URL . '/assets/images/services/' . $adonsdata['image'];
                                                                             }
                                                                             ?>" />
                                                                    </div>
                                                                </label>
                                                                <div class="addon-name fl ta-c"><?php echo $adonsdata['addon_service_name']; ?></div>
                                                            </li>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p class="ct-sub">Services Not Available</p>
                                                <?php } ?>
                                            </ul>
                                            <div class="addons_counting"></div>
                                        <?php } else { ?>
                                            <ul class="addon-service-list fl remove_addonsss row">
                                                <?php
                                                if (mysqli_num_rows($addons_data) > 0) {
                                                    while ($adonsdata = mysqli_fetch_array($addons_data)) {
                                                        $addons_rates->addon_service_id = $adonsdata['service_id'];
                                                        $addonrates_data = $addons_rates->readone_from_serviceid();
                                                        /* CHANGED BY ME FROM Y TO N */
                                                        $mmnameee = 'ad_unit' . $adonsdata['id'];
                                                        
                                                            ?>
                                                            <li class="ct-sm-6 ct-md-4 ct-lg-2 ct-xs-12 mb-15">
                                                                <input type="checkbox" name="addon-checkbox" class="addon-checkbox add_addon_in_cart_for_multipleqty" data-status="2" data-duration_value="-1" data-id="<?php echo $adonsdata['id']; ?>" id="ct-addon-<?php echo $adonsdata['id']; ?>" data-rate="<?php echo $adonsdata['base_price']; ?>" data-service_id="<?php echo $adonsdata['service_id']; ?>" data-method_id="0" data-method_name="<?php echo $adonsdata['addon_service_name']; ?>" data-units_id="<?php echo $adonsdata['id']; ?>" data-type="<?php echo "addon"; ?>" data-mnamee="<?php echo $mmnameee; ?>" />
                                                                <label class="ct-addon-ser border-c ct_addon_ser<?php echo $adonsdata['id']; ?>" for="ct-addon-<?php echo $adonsdata['id']; ?>"><span></span>
                                                                    <div class="addon-price" style="display: none;"><?php echo $general->ct_price_format($adonsdata['base_price'], $symbol_position, $decimal); ?></div>
                                                                    <div class="ct-addon-img">
                                                                        <img src="<?php
                                                                        if ($adonsdata['image'] == '' && $adonsdata['predefine_image'] == '') {
                                                                            echo SITE_URL . '/assets/images/services/default.png';
                                                                        } else if ($adonsdata['image'] == '') {
                                                                            echo SITE_URL . '/assets/images/addons-images/' . $adonsdata['predefine_image'];
                                                                        } else {
                                                                            echo SITE_URL . '/assets/images/services/' . $adonsdata['image'];
                                                                        }
                                                                        ?>" /></div>
                                                                </label>
                                                                <div class="addon-name fl ta-c"><?php echo $adonsdata['addon_service_name']; ?></div>
                                                            </li>
                                                            <?php } ?>
                                                    <?php } else { ?>
                                                    <p class="ct-sub">Services Not Available</p>
                                                    <?php } ?>
                                            </ul>
                                            <?php
                                        }
                                    } else {
                                        echo "Services Not Available";
                                    }
                                    ?>								
                                    <label class="service_not_selected_error"></label>
                                </div>
                                <!-- end sub-service list --> 
                                <!-- Module third area based -->
                                <!--                                <div class="ct-list-services ct-common-box s_m_units_design_5 ser_design_5_units" style="display: none;">
                                
                                                                </div>-->

                                <!-- end area based -->



                                <!-- end module third area based -->

                                <!--                                <div class="ct-extra-services-list ct-common-box add_on_lists hide_allsss_addons" style="display: none;">
                                
                                                                </div>-->



                                <!-- start discount. By default discount is Zero -->

                                <input type="radio" name="frequently_discount_radio" checked="checked" data-id='1' data-discount_type="P" data-discount_value="0" class="cart_frequently_discount" id="discount-often-1" data-name="Once" style="display: none;"/>

                                <!-- end discount -->

                                <div class="ct-provider-list ct-common-box">

                                    <div class="ct-list-header">

                                        <h3 class="header3 show_select_staff_title" style="display:none;"><?php echo $label_language_values['please_select_provider']; ?></h3>

                                        <ul class="provders-list">

                                        </ul>

                                    </div>

                                </div>

                                <!-- date time selection -->

                                <div class="ct-date-time-main ct-common-box hide_allsss">

                                    <div class="ct-list-header">

                                        <h3 class="header3 calendar-title"><?php echo $label_language_values['when_would_you_like_us_to_come']; ?>

                                            <?php if ($settings->get_option("ct_front_tool_tips_status") == 'on' && $settings->get_option("ct_front_tool_tips_time_slots") != '') { ?>

                                                <a class="ct-tooltip" href="#" data-toggle="tooltip" title="<?php echo $settings->get_option("ct_front_tool_tips_time_slots"); ?>"><i class="fa fa-info-circle fa-lg"></i></a>	

                                            <?php } ?>

                                        </h3>

                                    </div>

                                    <div class="row">

    <input type="hidden" name="service_value" id="service_value" value="<?php echo $service_id; ?>">
    
                                        <div class="ct-md-12 ct-sm-12 ct-xs-12 ct-datetime-select-main">

                                            <div class="ct-datetime-select clearfix">

                                                

                                                <label class="date_time_error" id="date_time_error_id" for="complete_bookings"></label>



                                                <div class="calendar-wrapper cal_info">



                                                </div>

                                                <!-- end calendar-wrapper -->

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- date and time slots end  -->

                                <?php if ($settings->get_option('ct_recurrence_booking_status') == 'Y') { ?>

                                    <div class="bi-terms-agree ct-common-box clearfix">

                                        <div class="ct-custom-checkbox clearfix">

                                            <ul class="ct-checkbox-list recu-book">

                                                <li>

                                                    <input type="checkbox" name="recurrence-booking" class="input-radio"

                                                           id="recurrence-booking"/>

                                                    <label for="recurrence-booking" class="">

                                                        <span></span>

                                                        <?php echo $label_language_values['Recurrence_booking']; ?>

                                                    </label>

                                                </li>

                                            </ul>  

                                        </div>

                                    </div>

                                    <div class="recurrence_type_dropdown ct-common-box" style="display:none;">					

                                        <div class="ct_recurrence_type_dropdown_1"></div>

                                        <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row" style="padding-left: 0px;">

                                            <label><?php echo $label_language_values['Recurrence_Type']; ?></label>

                                            <select class="ct_recurrence_type_dropdown ct_recurrence_type">

                                                <option class="ct_recurrence_type_dropdown_option" value="daily"><?php echo $label_language_values['Daily']; ?><i class="fa fa-angle-down"></i></option>

                                                <option value="weekly"><?php echo $label_language_values['weekly']; ?></option>

                                                <option value="monthly"><?php echo $label_language_values['monthly']; ?></option>

                                                <option value="biweekly"><?php echo $label_language_values['bi_weekly']; ?></option>

                                                <option value="bimonthly"><?php echo $label_language_values['Bi_Monthly']; ?></option>

                                                <option value="fortnightly"><?php echo $label_language_values['Fortnightly']; ?></option>

                                            </select>

                                        </div>

                                        <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">

                                            <label><?php echo $label_language_values['end_date']; ?>:</label>

                                            <input type="text" readonly class="form-control ct_recurrence_end_date" />

                                        </div>

                                    </div>

                                <?php } ?>	

                                <!-- personal details -->
                                <div class="ct-user-info-main ct-common-box existing_user_details hide_allsss res-min-reg">
                                    <div class="ct-list-header">
                                        <h3 class="header3"><?php echo $label_language_values['your_personal_details']; ?>
                                            <?php if ($settings->get_option("ct_front_tool_tips_status") == 'on' && $settings->get_option("ct_front_tool_tips_personal_details") != '') { ?>
                                                <a class="ct-tooltip" href="#" data-toggle="tooltip" title="<?php echo $settings->get_option("ct_front_tool_tips_personal_details"); ?>"><i class="fa fa-info-circle fa-lg"></i></a>	
                                            <?php } ?>
                                        </h3>
                                        <p class="ct-sub"><?php echo $label_language_values['please_provide_your_address_and_contact_details']; ?></p>
                                        <div class="ct-logged-in-user client_logout">
                                            <p class="welcome_msg_after_login pull-left"><?php echo $label_language_values['you_are_logged_in_as']; ?> <span class='fname'></span> <span class='lname'></span></p>
                                            <a href="javascript:void(0)" class="ct-link ml-10" id="logout" data-id="<?php
                                            if (isset($_SESSION['ct_login_user_id'])) {
                                                echo $_SESSION['ct_login_user_id'];
                                            }
                                            ?>" title="<?php echo $label_language_values['log_out']; ?>"><?php echo $label_language_values['log_out']; ?></a>
                                        </div>
                                    </div>
                                    <div class="ct-main-details">
                                        <div class="ct-login-exist clearfix" id="ct-login">
                                            <div class="ct-custom-radio">
                                                <ul class="row ct-radio-list hide_radio_btn_after_login">
                                                    <?php
                                                    if ($settings->get_option('ct_existing_and_new_user_checkout') == 'on' && $settings->get_option('ct_guest_user_checkout') == 'on') {
                                                        ?>
                                                        <li class="ct-exiting-user col-md-4 col-sm-6 ct-xs-12">
                                                            <input id="existing-user" type="radio" class="input-radio existing-user user-selection" name="user-selection" value="Existing User"/>
                                                            <label for="existing-user" class=""><span></span><?php echo $label_language_values['existing_user']; ?></label>
                                                        </li>
                                                        <li class="ct-new-user col-md-4 col-sm-6 ct-xs-12">

                                                            <input id="new-user" type="radio" checked="checked" class="input-radio new-user user-selection" name="user-selection" value="New-User"/>

                                                            <label for="new-user" class=""><span></span><?php echo $label_language_values['new_user']; ?>
                                                            </label>
                                                        </li>
                                                        <li class="ct-guest-user ct-md-4 ct-sm-6 ct-xs-12">
                                                            <input id="guest-user" type="radio" class="input-radio guest-user user-selection" name="user-selection" value="Guest-User"/>
                                                            <label for="guest-user" class=""><span></span><?php echo $label_language_values['guest_user']; ?></label>
                                                        </li>
                                                        <?php
                                                    } else if ($settings->get_option('ct_existing_and_new_user_checkout') == 'off' && $settings->get_option('ct_guest_user_checkout') == 'on') {
                                                        ?>
                                                        <li class="ct-guest-user ct-md-4 ct-sm-6 ct-xs-12" style='display:none;'>

                                                            <input id="guest-user" type="radio" class="input-radio guest-user user-selection" checked="checked"  name="user-selection" value="Guest-User"/>

                                                            <label for="guest-user" class=""><span></span><?php echo $label_language_values['guest_user']; ?></label>

                                                        </li>						

                                                        <?php
                                                    } else if ($settings->get_option('ct_existing_and_new_user_checkout') == 'on' && $settings->get_option('ct_guest_user_checkout') == 'off') {
                                                        ?>

                                                        <li class="ct-exiting-user ct-md-4 ct-sm-6 ct-xs-12"  style="padding-left: 0px;">

                                                            <input id="existing-user" type="radio" class="input-radio existing-user user-selection" name="user-selection" value="Existing User"/>

                                                            <label for="existing-user" class=""><span></span><?php echo $label_language_values['existing_user']; ?></label>

                                                        </li>

                                                        <li class="ct-new-user ct-md-4 ct-sm-6 ct-xs-12">

                                                            <input id="new-user" type="radio" checked="checked" class="input-radio new-user user-selection" name="user-selection" value="New-User"/>

                                                            <label for="new-user" class=""><span></span><?php echo $label_language_values['new_user']; ?>
                                                            </label>
                                                        </li>

                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="ct-login-existing ct_hidden">
                                                <form id="user_login_form" class="clearfix" method="POST">
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row hide_login_email" style="padding-left: 0px;">
                                                        <label for="ct-user-name">Mobile</label>
                                                        <input type="text" class="add_show_error_class_for_login error number_only" name="ct_user_name" id="ct-user-name" onkeydown="if (event.keyCode == 13)
                                                                    document.getElementById('login_existing_user').click()"/>
                                                    </div>
<!--                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row hide_password" style="padding-right: 0px;">

<label for="ct-user-pass">OTP</label>

<input type="password" class="add_show_error_class_for_login error" name="ct_user_pass" id="ct-user-pass" onkeydown="if (event.keyCode == 13)

document.getElementById('login_existing_user').click()"/>

</div>-->
                                                    <label class="login_unsuccessfull"></label>
                                                    <div class="ct-md-12 ct-xs-12 mb-15 hide_login_btn" style="padding-left: 0px;">
                                                        <input type="hidden" value='not' id="check_login_click" />
                                                        <a href="javascript:void(0)" class="ct-button" id="login_existing_user" title="<?php echo $label_language_values['log_in']; ?>"><?php echo $label_language_values['log_in']; ?></a>
<!--                                                        <a href="javascript:void(0)" id="ct_forget_password" class="ct-link" title="<?php echo $label_language_values['forget_password']; ?>?"><?php echo $label_language_values['forget_password']; ?></a>-->
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="ct-login-otp ct_hidden">
                                                <form id="user_otp_form" class="clearfix" method="POST">
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row hide_password" style="margin-left: -11px;">
                                                        <label for="ct-user-pass">OTP</label>
                                                        <input type="text" class="add_show_error_class_for_login error number_only" name="ct_user_otp" id="ct-user-otp" onkeydown="if (event.keyCode == 13)
                                                                    document.getElementById('ct_login_otp').click()"/>
                                                    </div>
                                                    <label class="otp_error" style="margin-left: 4px"></label>
                                                    <label class="otp_success" style="margin-left: 4px"></label>
                                                    <a href="javascript:void(0)" id="ct_login_otp" class="ct-button" style="position: relative;top: 15px;left: 10px;">Verify</a>
                                                    <a href="javascript:void(0)" id="ct_resend_otp" class="" style="position: relative;top: 15px;left: 10px;">Resend OTP</a>
                                                </form>
                                            </div>
                                        </div>            
                                        <input type="hidden" id="color_box" data-id="<?php echo $settings->get_option('ct_secondary_color'); ?>" value="<?php echo $settings->get_option('ct_secondary_color'); ?>"/>

                <?php if(isset($_SESSION['ct_login_user_id'])){ 
                    $style = 'style="display: none"';
                }else{
                    $style = '';
                } ?>
                <form method="post" id="mobileVerifyForm" <?php echo $style; ?> action="" >
                    <input type="hidden" name="isSent" id="isSent">
                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row" id="mobileDiv">
                        <label for="ct-email">Mobile</label>
                        <input type="tel" maxlength="12" name="mobile_number" id="mobile_number" class="add_show_error_class error"/>
                        <span style="color: red;" id="error_message"></span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                        <!-- <input type="submit" id="verifyMobile" name="verifyMobile" class="btn btn-primary"/> -->
                        <a href="javascript:void(0)" class="ct-button" id="verifyMobile" title="Log In">Submit</a>
                    </div>
                </form>

                 <form method="post" id="otpVerifyForm" action="" style="display: none;">
                    <input type="hidden" name="isVerify" id="isVerify">
                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row" id="otpDiv">
                        <p style="color: green;">An OTP sent to your register phone number</p>
                        <label for="ct-email">OTP</label>
                        <input type="text" name="mobile_otp" id="mobile_otp" class="add_show_error_class error"/>
                        <span style="color: red;" id="error_message_otp"></span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                        <a href="javascript:void(0)" class="ct-button" id="verifyOTP" title="Verify">Verify</a>
                        <a href="javascript:void(0)" class="ct-button" id="resendOTP" title="Resend">Resend</a>
                        <!-- <input type="submit" id="verifyOTP" name="verifyOTP" class="btn btn-primary"/> -->
                    </div>
                </form>

					<?php if (!empty($_SESSION['ct_login_user_id'])) { ?>
                                        <form id="user_details_form" class="clearfix" method="post" style="">

                                            <div class="ct-peronal-details">
                                                <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_guest_user_preferred_email">
                                                    <label for="ct-email-guest"><?php echo $label_language_values['preferred_email']; ?>
                                                    </label>
                                                    <input type="text" name="ct_email_guest" class="add_show_error_class error" id="ct-email-guest" />
                                                </div>
                                                <?php
                                                $fn_check = explode(",", $settings->get_option("ct_bf_first_name"));

                                                if ($fn_check[0] == 'on') {
                                                    ?>
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                        <label for="ct-first-name"><?php echo $label_language_values['first_name']; ?></label>
                                                        <input type="text" name="ct_first_name" class="add_show_error_class error" id="ct-first-name" />
                                                    </div>
                                                <?php } else {
                                                    ?>
                                                    <input type="hidden" name="ct_first_name" id="ct-first-name" class="add_show_error_class error" value=""/>
                                                <?php }
                                                ?>
                                                <?php
                                                $ln_check = explode(",", $settings->get_option("ct_bf_last_name"));
                                                if ($ln_check[0] == 'on') {
                                                    ?>
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                        <label for="ct-last-pass"><?php echo $label_language_values['last_name']; ?></label>
                                                        <input type="text" class="add_show_error_class error" name="ct_last_name" id="ct-last-name"/>
                                                    </div>
                                                <?php } else {
                                                    ?>
                                                    <input type="hidden" name="ct_last_name" id="ct-last-name" class="add_show_error_class error" value=""/>
                                                <?php } ?>
                                                <?php
                                                $phone_check = explode(",", $settings->get_option("ct_bf_phone"));

                                                if ($phone_check[0] == 'on') {
                                                    ?>
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                        <label for="ct-user-phone"><?php echo $label_language_values['phone']; ?></label>
                                                        <input type="tel" value="" id="ct-user-phone" class="add_show_error_class error number_only" name="ct_user_phone" disabled/>
                                                    </div>
                                                <?php } else { ?>
                                                    <input type="hidden" name="ct_user_phone" id="ct-user-phone" class="add_show_error_class error" value=""/>
                                                <?php } ?>
                                                <!--                                            <div class="ct-new-user-details remove_preferred_password_and_preferred_email">-->
                                                <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                    <label for="ct-email"><?php echo $label_language_values['preferred_email']; ?></label>
                                                    <input type="text" name="ct_email" id="ct-email" class="add_show_error_class error"/>
                                                </div>
<!--                                                <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
<label for="ct-preffered-pass"><?php echo $label_language_values['preferred_password']; ?></label>
<input type="password" name="ct_preffered_pass" id="ct-preffered-pass" class="add_show_error_class error"/>
</div>-->
<!--</div>-->
                                                <?php
                                                $address_check = explode(",", $settings->get_option("ct_bf_address"));

                                                if ($address_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">

                                                        <label for="ct-street-address"><?php echo $label_language_values['street_address']; ?></label>

                                                        <input type="text" name="ct_street_address" id="ct-street-address" class="add_show_error_class error" />

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_street_address" id="ct-street-address" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                $zip_check = explode(",", $settings->get_option("ct_bf_zip_code"));

                                                if ($zip_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_zip_code_class">

                                                        <label for="ct-zip-code">Pincode</label>

                                                        <input type="text" name="ct_zip_code" id="ct-zip-code" class="add_show_error_class error number_only" maxlength="6"/>

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_zip_code" id="ct-zip-code" class="add_show_error_class error" value="" maxlength="6"/>

                                                <?php }
                                                ?>

                                                <?php
                                                $city_check = explode(",", $settings->get_option("ct_bf_city"));

                                                if ($city_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_city_class">

                                                        <label for="ct-city"><?php echo $label_language_values['city']; ?></label>

                                                        <input type="text" name="ct_city" id="ct-city" class="add_show_error_class error" />

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_city" id="ct-city" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                $state_check = explode(",", $settings->get_option("ct_bf_state"));

                                                if ($state_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_state_class" style="state-margin">

                                                        <label for="ct-state"><?php echo $label_language_values['state']; ?></label>

                                                        <input type="text" name="ct_state" id="ct-state" class="add_show_error_class error" />

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_state" id="ct-state" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                $notes_check = explode(",", $settings->get_option("ct_bf_notes"));

                                                if ($notes_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-12 ct-xs-12 ct-form-row" style="padding-left: 0px;padding-right: 0px;">

                                                        <label for="ct-notes"><?php echo $label_language_values['special_requests_notes']; ?></label>

                                                        <textarea id="ct-notes" class="add_show_error_class error" rows="10"></textarea>

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" id="ct-notes" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                if ($settings->get_option('ct_vc_status') == "Y") {
                                                    ?>

                                                    <div class="ct-custom-radio ct-options-new ct-md-6 ct-sm-6 ct-xs-12 mb-15">

                                                        <label><?php echo $label_language_values['do_you_have_a_vaccum_cleaner']; ?></label>

                                                        <ul class="ct-radio-list">

                                                            <li>

                                                                <input id="vaccum-yes" type="radio" checked="checked" class="input-radio vc_status" name="vacuum-cleaner" value="Vacuum-Yes"/>

                                                                <label for="vaccum-yes"><span></span><?php echo $label_language_values['yes']; ?></label>

                                                            </li>

                                                            <li>

                                                                <input id="vaccum-no" type="radio" class="input-radio vc_status" name="vacuum-cleaner" value="Vacuum-No"/>

                                                                <label for="vaccum-no"><span></span><?php echo $label_language_values['no']; ?></label>

                                                            </li>

                                                        </ul>

                                                    </div>

                                                <?php } ?>

                                                <?php
                                                if ($settings->get_option('ct_p_status') == "Y") {
                                                    ?>

                                                    <div class="ct-custom-radio ct-options-new ct-md-6 ct-sm-6 ct-xs-12 mb-10">

                                                        <label><?php echo $label_language_values['do_you_have_parking']; ?></label>

                                                        <ul class="ct-radio-list">

                                                            <li>

                                                                <input id="parking-yes" type="radio" checked="checked" class="input-radio p_status" name="parking" value="Parking-Yes"/>

                                                                <label for="parking-yes"><span></span><?php echo $label_language_values['yes']; ?></label>

                                                            </li>

                                                            <li>

                                                                <input id="parking-no" type="radio" class="input-radio p_status" name="parking" value="Parking-No"/>

                                                                <label for="parking-no"><span></span><?php echo $label_language_values['no']; ?></label>

                                                            </li>

                                                        </ul>

                                                    </div>

                                                <?php } ?>

                                                <?php if ($settings->get_option('ct_company_willwe_getin_status') != "" && $settings->get_option('ct_company_willwe_getin_status') == "Y") { ?>

                                                    <div class="ct-options-new ct-md-12 ct-xs-12 mb-10 ct-form-row">

                                                        <label><?php echo $label_language_values['how_will_we_get_in']; ?></label>



                                                        <div class="ct-option-select">

                                                            <select class="ct-option-select" id="contact_status">

                                                                <option value="I'll be at home"><?php echo $label_language_values['i_will_be_at_home']; ?></option>

                                                                <option value="Please call me"><?php echo $label_language_values['please_call_me']; ?></option>

                                                                <option value="The key is with the doorman"><?php echo $label_language_values['the_key_is_with_the_doorman']; ?></option>

                                                                <option value="Other"><?php echo $label_language_values['other']; ?></option>

                                                            </select>

                                                        </div>

                                                        <div class="ct-option-others pt-10 ct_hidden">

                                                            <input type="text" name="other_contact_status" class="add_show_error_class error" id="other_contact_status" />

                                                        </div>

                                                    </div>

                                                <?php } ?>

                                                <?php
                                                if ($settings->get_option('ct_appointment_details_display') == 'on' && ($address_check[0] == 'on' || $zip_check[0] == 'on' || $city_check[0] == 'on' || $state_check[0] == 'on')) {
                                                    ?>					  

                                                    <div class="ct-md-12 ct-xs-12 ct-form-row np app-det">

                                                        <h3 class="header3 pull-left"><?php echo $label_language_values['appointment_details']; ?></h3>

                                                        <div class="pull-left ml-10">

                                                            <div class="ct-custom-checkbox">

                                                                <ul class="ct-checkbox-list">

                                                                    <li>

                                                                        <input type="checkbox" id="retype_status" /> 

                                                                        <label for="retype_status" class="">

                                                                            (<?php echo $label_language_values['same_as_above']; ?>) &nbsp;<span></span>

                                                                        </label>

                                                                    </li>

                                                                </ul>

                                                            </div>

                                                        </div>

                                                        <div class="cb"></div>



                                                        <?php
                                                        if ($address_check[0] == 'on') {
                                                            ?>

                                                            <div class="ct-md-12 ct-xs-12 ct-form-row">

                                                                <label for="app-notes"><?php echo $label_language_values['appointment_address']; ?></label>

                                                                <input type="text" id="app-street-address" name="app_street_address" class="add_show_error_class error" >

                                                            </div>

                                                        <?php } else { ?>

                                                            <input type="hidden" name="app_street_address" id="app-street-address" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                        <?php if ($zip_check[0] == 'on') { ?>

                                                            <div class="ct-md-4 ct-sm-4 ct-xs-12 ct-form-row app-dett">

                                                                <label for="app-zip-code">Appointment Pincode</label>

                                                                <input type="text" name="app_zip_code" id="app-zip-code" class="add_show_error_class error"  <?php
                                                                if ($settings->get_option('ct_postalcode_status') == 'Y') {

                                                                    echo "readonly";
                                                                }
                                                                ?>/>

                                                            </div>

                                                        <?php } else {
                                                            ?>

                                                            <input type="hidden" name="app_zip_code" id="app-zip-code" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                        <?php
                                                        if ($city_check[0] == 'on') {
                                                            ?>

                                                            <div class="ct-md-4 ct-sm-4 ct-xs-12 ct-form-row">

                                                                <label for="app-city"><?php echo $label_language_values['appointment_city']; ?></label>

                                                                <input type="text" name="app_city" id="app-city" class="add_show_error_class error" />

                                                            </div>

                                                        <?php } else { ?>

                                                            <input type="hidden" name="app_city" id="app-city" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                        <?php if ($state_check[0] == 'on') { ?>

                                                            <div class="ct-md-4 ct-sm-4 ct-xs-12 ct-form-row">

                                                                <label for="app-state"><?php echo $label_language_values['appointment_state']; ?></label>

                                                                <input type="text" name="app_state" id="app-state" class="add_show_error_class error" />

                                                            </div>

                                                        <?php } else { ?>

                                                            <input type="hidden" name="app_state" id="app-state" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                    </div>

                                                <?php } ?>	

                                            </div>

                                        </form>
					<?php } else { ?>
										<form id="user_details_form" class="clearfix" method="post" style="display: none;">

                                            <div class="ct-peronal-details">
                                                <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_guest_user_preferred_email">
                                                    <label for="ct-email-guest"><?php echo $label_language_values['preferred_email']; ?>
                                                    </label>
                                                    <input type="text" name="ct_email_guest" class="add_show_error_class error" id="ct-email-guest" />
                                                </div>
                                                <?php
                                                $fn_check = explode(",", $settings->get_option("ct_bf_first_name"));

                                                if ($fn_check[0] == 'on') {
                                                    ?>
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                        <label for="ct-first-name"><?php echo $label_language_values['first_name']; ?></label>
                                                        <input type="text" name="ct_first_name" class="add_show_error_class error" id="ct-first-name" />
                                                    </div>
                                                <?php } else {
                                                    ?>
                                                    <input type="hidden" name="ct_first_name" id="ct-first-name" class="add_show_error_class error" value=""/>
                                                <?php }
                                                ?>
                                                <?php
                                                $ln_check = explode(",", $settings->get_option("ct_bf_last_name"));
                                                if ($ln_check[0] == 'on') {
                                                    ?>
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                        <label for="ct-last-pass"><?php echo $label_language_values['last_name']; ?></label>
                                                        <input type="text" class="add_show_error_class error" name="ct_last_name" id="ct-last-name"/>
                                                    </div>
                                                <?php } else {
                                                    ?>
                                                    <input type="hidden" name="ct_last_name" id="ct-last-name" class="add_show_error_class error" value=""/>
                                                <?php } ?>
                                                <?php
                                                $phone_check = explode(",", $settings->get_option("ct_bf_phone"));

                                                if ($phone_check[0] == 'on') {
                                                    ?>
                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                        <label for="ct-user-phone"><?php echo $label_language_values['phone']; ?></label>
                                                        <input type="tel" value="" id="ct-user-phone" class="add_show_error_class error number_only" name="ct_user_phone" disabled/>
                                                    </div>
                                                <?php } else { ?>
                                                    <input type="hidden" name="ct_user_phone" id="ct-user-phone" class="add_show_error_class error" value=""/>
                                                <?php } ?>
                                                <!--                                            <div class="ct-new-user-details remove_preferred_password_and_preferred_email">-->
                                                <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
                                                    <label for="ct-email"><?php echo $label_language_values['preferred_email']; ?></label>
                                                    <input type="text" name="ct_email" id="ct-email" class="add_show_error_class error"/>
                                                </div>
<!--                                                <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">
<label for="ct-preffered-pass"><?php echo $label_language_values['preferred_password']; ?></label>
<input type="password" name="ct_preffered_pass" id="ct-preffered-pass" class="add_show_error_class error"/>
</div>-->
<!--</div>-->
                                                <?php
                                                $address_check = explode(",", $settings->get_option("ct_bf_address"));

                                                if ($address_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row">

                                                        <label for="ct-street-address"><?php echo $label_language_values['street_address']; ?></label>

                                                        <input type="text" name="ct_street_address" id="ct-street-address" class="add_show_error_class error" />

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_street_address" id="ct-street-address" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                $zip_check = explode(",", $settings->get_option("ct_bf_zip_code"));

                                                if ($zip_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_zip_code_class">

                                                        <label for="ct-zip-code">Pincode</label>

                                                        <input type="text" name="ct_zip_code" id="ct-zip-code" class="add_show_error_class error number_only" maxlength="6"/>

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_zip_code" id="ct-zip-code" class="add_show_error_class error" value="" maxlength="6"/>

                                                <?php }
                                                ?>

                                                <?php
                                                $city_check = explode(",", $settings->get_option("ct_bf_city"));

                                                if ($city_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_city_class">

                                                        <label for="ct-city"><?php echo $label_language_values['city']; ?></label>

                                                        <input type="text" name="ct_city" id="ct-city" class="add_show_error_class error" />

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_city" id="ct-city" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                $state_check = explode(",", $settings->get_option("ct_bf_state"));

                                                if ($state_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-6 ct-sm-6 ct-xs-12 ct-form-row remove_state_class" style="state-margin">

                                                        <label for="ct-state"><?php echo $label_language_values['state']; ?></label>

                                                        <input type="text" name="ct_state" id="ct-state" class="add_show_error_class error" />

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" name="ct_state" id="ct-state" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                $notes_check = explode(",", $settings->get_option("ct_bf_notes"));

                                                if ($notes_check[0] == 'on') {
                                                    ?>

                                                    <div class="ct-md-12 ct-xs-12 ct-form-row" style="padding-left: 0px;padding-right: 0px;">

                                                        <label for="ct-notes"><?php echo $label_language_values['special_requests_notes']; ?></label>

                                                        <textarea id="ct-notes" class="add_show_error_class error" rows="10"></textarea>

                                                    </div>

                                                <?php } else {
                                                    ?>

                                                    <input type="hidden" id="ct-notes" class="add_show_error_class error" value=""/>

                                                <?php }
                                                ?>

                                                <?php
                                                if ($settings->get_option('ct_vc_status') == "Y") {
                                                    ?>

                                                    <div class="ct-custom-radio ct-options-new ct-md-6 ct-sm-6 ct-xs-12 mb-15">

                                                        <label><?php echo $label_language_values['do_you_have_a_vaccum_cleaner']; ?></label>

                                                        <ul class="ct-radio-list">

                                                            <li>

                                                                <input id="vaccum-yes" type="radio" checked="checked" class="input-radio vc_status" name="vacuum-cleaner" value="Vacuum-Yes"/>

                                                                <label for="vaccum-yes"><span></span><?php echo $label_language_values['yes']; ?></label>

                                                            </li>

                                                            <li>

                                                                <input id="vaccum-no" type="radio" class="input-radio vc_status" name="vacuum-cleaner" value="Vacuum-No"/>

                                                                <label for="vaccum-no"><span></span><?php echo $label_language_values['no']; ?></label>

                                                            </li>

                                                        </ul>

                                                    </div>

                                                <?php } ?>

                                                <?php
                                                if ($settings->get_option('ct_p_status') == "Y") {
                                                    ?>

                                                    <div class="ct-custom-radio ct-options-new ct-md-6 ct-sm-6 ct-xs-12 mb-10">

                                                        <label><?php echo $label_language_values['do_you_have_parking']; ?></label>

                                                        <ul class="ct-radio-list">

                                                            <li>

                                                                <input id="parking-yes" type="radio" checked="checked" class="input-radio p_status" name="parking" value="Parking-Yes"/>

                                                                <label for="parking-yes"><span></span><?php echo $label_language_values['yes']; ?></label>

                                                            </li>

                                                            <li>

                                                                <input id="parking-no" type="radio" class="input-radio p_status" name="parking" value="Parking-No"/>

                                                                <label for="parking-no"><span></span><?php echo $label_language_values['no']; ?></label>

                                                            </li>

                                                        </ul>

                                                    </div>

                                                <?php } ?>

                                                <?php if ($settings->get_option('ct_company_willwe_getin_status') != "" && $settings->get_option('ct_company_willwe_getin_status') == "Y") { ?>

                                                    <div class="ct-options-new ct-md-12 ct-xs-12 mb-10 ct-form-row">

                                                        <label><?php echo $label_language_values['how_will_we_get_in']; ?></label>



                                                        <div class="ct-option-select">

                                                            <select class="ct-option-select" id="contact_status">

                                                                <option value="I'll be at home"><?php echo $label_language_values['i_will_be_at_home']; ?></option>

                                                                <option value="Please call me"><?php echo $label_language_values['please_call_me']; ?></option>

                                                                <option value="The key is with the doorman"><?php echo $label_language_values['the_key_is_with_the_doorman']; ?></option>

                                                                <option value="Other"><?php echo $label_language_values['other']; ?></option>

                                                            </select>

                                                        </div>

                                                        <div class="ct-option-others pt-10 ct_hidden">

                                                            <input type="text" name="other_contact_status" class="add_show_error_class error" id="other_contact_status" />

                                                        </div>

                                                    </div>

                                                <?php } ?>

                                                <?php
                                                if ($settings->get_option('ct_appointment_details_display') == 'on' && ($address_check[0] == 'on' || $zip_check[0] == 'on' || $city_check[0] == 'on' || $state_check[0] == 'on')) {
                                                    ?>					  

                                                    <div class="ct-md-12 ct-xs-12 ct-form-row np app-det">

                                                        <h3 class="header3 pull-left"><?php echo $label_language_values['appointment_details']; ?></h3>

                                                        <div class="pull-left ml-10">

                                                            <div class="ct-custom-checkbox">

                                                                <ul class="ct-checkbox-list">

                                                                    <li>

                                                                        <input type="checkbox" id="retype_status" /> 

                                                                        <label for="retype_status" class="">

                                                                            (<?php echo $label_language_values['same_as_above']; ?>) &nbsp;<span></span>

                                                                        </label>

                                                                    </li>

                                                                </ul>

                                                            </div>

                                                        </div>

                                                        <div class="cb"></div>



                                                        <?php
                                                        if ($address_check[0] == 'on') {
                                                            ?>

                                                            <div class="ct-md-12 ct-xs-12 ct-form-row">

                                                                <label for="app-notes"><?php echo $label_language_values['appointment_address']; ?></label>

                                                                <input type="text" id="app-street-address" name="app_street_address" class="add_show_error_class error" >

                                                            </div>

                                                        <?php } else { ?>

                                                            <input type="hidden" name="app_street_address" id="app-street-address" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                        <?php if ($zip_check[0] == 'on') { ?>

                                                            <div class="ct-md-4 ct-sm-4 ct-xs-12 ct-form-row app-dett">

                                                                <label for="app-zip-code">Appointment Pincode</label>

                                                                <input type="text" name="app_zip_code" id="app-zip-code" class="add_show_error_class error"  <?php
                                                                if ($settings->get_option('ct_postalcode_status') == 'Y') {

                                                                    echo "readonly";
                                                                }
                                                                ?>/>

                                                            </div>

                                                        <?php } else {
                                                            ?>

                                                            <input type="hidden" name="app_zip_code" id="app-zip-code" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                        <?php
                                                        if ($city_check[0] == 'on') {
                                                            ?>

                                                            <div class="ct-md-4 ct-sm-4 ct-xs-12 ct-form-row">

                                                                <label for="app-city"><?php echo $label_language_values['appointment_city']; ?></label>

                                                                <input type="text" name="app_city" id="app-city" class="add_show_error_class error" />

                                                            </div>

                                                        <?php } else { ?>

                                                            <input type="hidden" name="app_city" id="app-city" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                        <?php if ($state_check[0] == 'on') { ?>

                                                            <div class="ct-md-4 ct-sm-4 ct-xs-12 ct-form-row">

                                                                <label for="app-state"><?php echo $label_language_values['appointment_state']; ?></label>

                                                                <input type="text" name="app_state" id="app-state" class="add_show_error_class error" />

                                                            </div>

                                                        <?php } else { ?>

                                                            <input type="hidden" name="app_state" id="app-state" class="add_show_error_class error" value=""/>

                                                        <?php } ?>

                                                    </div>

                                                <?php } ?>	

                                            </div>

                                        </form>
					<?php } ?>
                                    </div>

                                    <!-- main details end -->

                                </div>

                                <!-- end personal details -->

                                <!-- payment details -->

                                <div class="ct-common-box hide_allsss">

                                    <!-- Promocodes -->

                                    <?php if ($settings->get_option('ct_show_coupons_input_on_checkout') == 'on') { ?>

<!--                                        <div class="ct-discount-coupons mb-20">

<div class="ct-form-rown">

<div class="ct-coupon-input ct-md-6 ct-sm-12 ct-xs-12 mt-10 mb-15 np" style="padding-left: 0px;">

<input id="coupon_val" type="text" name="coupon_apply"

class="ct-coupon-input-text hide_coupon_textbox"

placeholder="<?php echo $label_language_values['have_a_promocode']; ?>" maxlength="22" onchange="myFunction()"/>

<a href="javascript:void(0);" class="ct-apply-coupon ct-link hide_coupon_textbox" name="apply-coupon" id="apply_coupon"><?php echo $label_language_values['apply']; ?></a>

<?php if ($settings->get_option("ct_front_tool_tips_status") == 'on' && $settings->get_option("ct_front_tool_tips_promocode") != '') { ?>

<a class="ct-tooltip" href="#" data-toggle="tooltip" title="<?php echo $settings->get_option("ct_front_tool_tips_promocode"); ?>"><i class="fa fa-info-circle fa-lg"></i></a>	

<?php } ?>

<label class="ct-error ofh coupon_invalid_error"></label>

display coupon 

<div class="ct-display-coupon-code">

<div class="ct-form-rown">

<div class="ct-column ct-md-7 ct-xs-12 ofh">

<label><?php echo $label_language_values['applied_promocode']; ?></label>

</div>

<div class="ct-coupon-value-main ct-md-5 ct-xs-12">

<span class="ct-coupon-value border-2" id="display_code"></span>

<img id="ct-remove-applied-coupon"

src="<?php echo SITE_URL; ?>/assets/images/ct-close.png"

class="reverse_coupon" title="<?php echo $label_language_values['remove_applied_coupon']; ?>"/>

</div>

</div>

</div>

</div>

</div>

</div>-->

                                    <?php } ?>

<!--                                    <div class="ct-list-header">

<h3 class="header3"><?php echo $label_language_values['preferred_payment_method']; ?>
<?php if ($settings->get_option("ct_front_tool_tips_status") == 'on' && $settings->get_option("ct_front_tool_payment_method") != '') { ?>
<a class="ct-tooltip" href="#" data-toggle="tooltip" title="<?php echo $settings->get_option("ct_front_tool_payment_method"); ?>"><i class="fa fa-info-circle fa-lg"></i></a>
<?php } ?>

</h3>

</div>-->
        <div class="ct-main-payments fl">
            <div class="payments-container f-l" id="ct-payments">
                <label class="ct-error-msg"><?php echo $label_language_values['please_select_one_payment_method']; ?></label>
                <label class="ct-error-msg ct-paypal-error" id="paypal_error"></label>
                <div class="ct-custom-radio ct-payment-methods f-l">
                    <ul class="ct-radio-list ct-all-pay-methods">
                        <?php if ($settings->get_option('ct_pay_locally_status') == 'on') { ?>

                            <!--                                                        <li class="ct-md-3 ct-sm-6 ct-xs-12" id="pay-at-venue">-->

                            <input type="hidden" name="payment-methods" value="pay at venue" class="input-radio payment_gateway" id="pay-cash"/>

                                        <!--<label for="pay-cash" class="locally-radio"><span></span><?php echo $label_language_values['pay_locally']; ?></label>

                                    </li>-->

                        <?php } ?>

                        <!-- bank transfer -->

                        <?php if ($settings->get_option('ct_bank_transfer_status') == 'Y' && ($settings->get_option('ct_bank_name') != '' || $settings->get_option('ct_account_name') != '' || $settings->get_option('ct_account_number') != '' || $settings->get_option('ct_branch_code') != '' || $settings->get_option('ct_ifsc_code') != '' || $settings->get_option('ct_bank_description') != '')) { ?>

                            <li class="ct-md-3 ct-sm-6 ct-xs-12" id="ct-bank-transer">

                                <input type="radio" name="payment-methods" value="bank transfer" class="input-radio bank_transfer payment_gateway" id="bank-transer"  />

                                <label for="bank-transer" class="locally-radio"><span></span><?php echo $label_language_values['bank_transfer']; ?></label>

                            </li>

                        <?php } ?>

                        <?php
                        if ($settings->get_option('ct_paypal_express_checkout_status') == 'on') {
                            ?>

                            <li class="ct-md-3 ct-sm-6 ct-xs-12" id="pay-at-venue">

                                <input type="radio" name="payment-methods" value="paypal"

                                       class="input-radio payment_gateway" id="pay-paypal" checked="checked" />

                                <label for="pay-paypal"  class="locally-radio"><span></span><?php echo $label_language_values['paypal']; ?><img src="<?php echo SITE_URL; ?>/assets/images/cards/paypal.png" class="ct-paypal-image" alt="PayPal"></label>

                            </li>

                        <?php }
                        ?>



                        <?php
                        if ($settings->get_option('ct_payumoney_status') == 'Y') {
                            ?>



                            <li class="ct-md-3 ct-sm-6 ct-xs-12" id="pay-at-venue">

                                <input type="radio" name="payment-methods" value="payumoney"

                                       class="input-radio payment_gateway" id="payumoney" checked="checked" />

                                <label for="payumoney"  class="locally-radio"><span></span> <?php echo $label_language_values['payumoney']; ?></label>

                            </li>

                        <?php }
                        ?>

                        <?php if ($settings->get_option('ct_authorizenet_status') == 'on' && $settings->get_option('ct_stripe_payment_form_status') != 'on' && $settings->get_option('ct_2checkout_status') != 'Y') { ?>

                            <!-- new added -->

                            <li class="ct-md-3 ct-sm-6 ct-xs-12" id="card-payment">

                                <input type="radio" name="payment-methods" value="card-payment" class="input-radio payment_gateway cccard" id="pay-card" checked="checked"/>

                                <label for="pay-card" class="card-radio"><span></span><?php echo $label_language_values['card_payment']; ?></label>

                            </li>

                        <?php } ?>

                        <?php if ($settings->get_option('ct_authorizenet_status') != 'on' && $settings->get_option('ct_stripe_payment_form_status') == 'on' && $settings->get_option('ct_2checkout_status') != 'Y') { ?>

                            <!-- new added -->

                            <li class="ct-md-3 ct-sm-6 ct-xs-12" id="card-payment">

                                <input type="radio" name="payment-methods" value="stripe-payment" class="input-radio payment_gateway cccard" id="pay-card" checked="checked"/>

                                <label for="pay-card" class="card-radio"><span></span><?php echo $label_language_values['card_payment']; ?></label>

                            </li>

                        <?php } ?>

                        <?php if ($settings->get_option('ct_authorizenet_status') != 'on' && $settings->get_option('ct_stripe_payment_form_status') != 'on' && $settings->get_option('ct_2checkout_status') == 'Y') { ?>

                            <!-- new added -->

                            <li class="ct-md-3 ct-sm-6 ct-xs-12" id="card-payment">

                                <input type="radio" name="payment-methods" value="2checkout-payment" class="input-radio payment_gateway cccard" id="pay-card" checked="checked"/>

                                <label for="pay-card" class="card-radio"><span></span><?php echo $label_language_values['card_payment']; ?></label>

                            </li>

                        <?php } ?>

                        <!-- Payment Start -->

                        <?php
                        if (sizeof($purchase_check) > 0) {

                            foreach ($purchase_check as $key => $val) {

                                if ($val == 'Y') {

                                    echo $payment_hook->payment_payment_selection_hook($key);
                                }
                            }
                        }
                        ?>

                        <!-- Payment End -->

                    </ul>

                </div>

            </div>

            <div id="ct-pay-methods" class="payment-method-container f-l">

                <div class="card-type-center f-l">

                    <div class="common-payment-style ct_hidden" <?php
                    if ($settings->get_option('ct_authorizenet_status') == 'on' || $settings->get_option('ct_stripe_payment_form_status') == 'on' || $settings->get_option('ct_2checkout_status') == 'Y') {

                        echo " style='display:block;' ";
                    } else if (sizeof($purchase_check) > 0) {

                        $check_pay = 'N';

                        $display_check = '';

                        foreach ($purchase_check as $key => $val) {

                            if ($val == 'Y') {

                                if ($payment_hook->payment_display_cardbox_condition_hook($key) == true) {

                                    if ($display_check == '') {

                                        $display_check = " style='display:block;' ";

                                        $check_pay = 'Y';
                                    } else if ($display_check == " style='display:none;' ") {

                                        $display_check = " style='display:block;' ";

                                        $check_pay = 'Y';
                                    }
                                } else {

                                    if ($display_check == '') {

                                        $display_check = " style='display:none;' ";

                                        $check_pay = 'Y';
                                    } else if ($display_check == " style='display:block;' ") {

                                        $display_check = " style='display:none;' ";

                                        $check_pay = 'Y';
                                    }
                                }
                            }
                        }

                        echo $display_check;
                    }
                    ?> >

                        <div class="payment-inner">

                            <?php if ($settings->get_option('ct_2checkout_status') == 'Y') { ?>

                                <input id="token" name="token" type="hidden" value="">

                            <?php } ?>

                            <div id="card-payment-fields">

                                <div class="ct-md-12 ct-xs-12 ct-header-bg">

                                    <h4 class="header4"><?php echo $label_language_values['card_details']; ?></h4>

                                    <img src="<?php echo SITE_URL; ?>/assets/images/cards/card-images.png" class="ct-stripe-image float-right" alt="Stripe" />

                                </div>

                                <div class="ct-md-12">

                                    <label id="ct-card-payment-error" class="ct-error-msg ct-payment-error"><?php echo $label_language_values['invalid_card_number']; ?><?php echo $label_language_values['expiry_date_or_csv']; ?></label>  

                                </div>

                                <div class="ct-md-9 ct-sm-9 ct-xs-12 ct-card-details">

                                    <div class="ct-form-row ct-md-12 ct-xs-12">

                                        <label><?php echo $label_language_values['card_number']; ?></label>

                                        <i class="icon-credit-card icons"></i>

                                        <input class="cc-number ct-card-number" maxlength="20" size="20" data-stripe="number" type="tel">

                                        <span class="card" aria-hidden="true"></span>

                                    </div>

                                    <div class="ct-form-row ct-md-8 ct-sm-8 ct-xs-12 ct-exp-mnyr">

                                        <label><?php echo $label_language_values['expiry']; ?><?php echo $label_language_values['mm_yyyy']; ?></label>

                                        <i class="icon-calendar icons"></i>

                                        <input data-stripe="exp-month" class="cc-exp-month ct-exp-month" maxlength="2" type="tel" placeholder="<?php echo date('m'); ?>" />/

                                        <input data-stripe="exp-year" class="cc-exp-year ct-exp-year" maxlength="4" type="tel" placeholder="<?php echo date('Y'); ?>" />

                                    </div>

                                    <div class="ct-form-row ct-md-4 ct-sm-4 ct-xs-12 ct-stripe-cvc">

                                        <label><?php echo $label_language_values['cvc']; ?></label>

                                        <i class="icon-lock icons"></i>

                                        <input type="password" placeholder="●●●" maxlength="4" size="4" data-stripe="cvc" class="cc-cvc ct-cvc-code" type="tel"/>

                                    </div>

                                </div>

                                <div class="ct-md-3 ct-sm-3 ct-xs-12 ct-lock-image">

                                    <div class="ct-lock-img"></div>

                                </div>



                            </div>

                        </div>

                    </div>

                </div>

            </div> 	

            <!--  bank details popup -->

            <div id="ct-bank-transfer-box" class="payment-method-container f-l">

                <div class="card-type-center f-l">

                    <div class="common-payment-style-bank-transfer ct_hidden">

                        <div class="payment-inner">

                            <div id="card-payment-fields" style="">

                                <div class="ct-md-12 ct-xs-12 ct-header-bg">

                                    <h4 class="header4"><?php echo $label_language_values['bank_details']; ?></h4>

                                </div>

                                <div class="ct-md-12">

                                    <table>

                                        <tbody>

                                            <?php if ($settings->get_option('ct_bank_name') != "") {
                                                ?>

                                                <tr class="dc_acc_name">

                                                    <th><strong><?php echo $label_language_values['bank_name']; ?></strong></th>

                                                    <td><span class="amount"><?php echo $settings->get_option('ct_bank_name'); ?></span></td>

                                                </tr>

                                                <?php
                                            }

                                            if ($settings->get_option('ct_account_name') != "") {
                                                ?>

                                                <tr class="dc_acc_name">

                                                    <th><strong><?php echo $label_language_values['account_name']; ?></strong></th>

                                                    <td><span class="amount"><?php echo $settings->get_option('ct_account_name'); ?></span></td>

                                                </tr>

                                                <?php
                                            }

                                            if ($settings->get_option('ct_account_number') != "") {
                                                ?>

                                                <tr class="dc_acc_number">

                                                    <th><strong><?php echo $label_language_values['account_number']; ?></strong></th>
                                                    <td><span class="amount"><?php echo $settings->get_option('ct_account_number'); ?></span></td>
                                                </tr>
                                                <?php
                                            }
                                            if ($settings->get_option('ct_branch_code') != "") {
                                                ?>
                                                <tr class="dc_branch_code">
                                                    <th><strong><?php echo $label_language_values['branch_code']; ?></strong></th>
                                                    <td><span class="amount"><?php echo $settings->get_option('ct_branch_code'); ?></span></td>
                                                </tr>
                                                <?php
                                            }
                                            if ($settings->get_option('ct_ifsc_code') != "") {
                                                ?>
                                                <tr class="dc_ifc_code">
                                                    <th><strong><?php echo $label_language_values['ifsc_code']; ?></strong></th>
                                                    <td><span class="amount"><?php echo $settings->get_option('ct_ifsc_code'); ?></span></td>
                                                </tr>
                                                <?php
                                            }
                                            if ($settings->get_option('ct_bank_description') != "") {
                                                ?>
                                                <tr class="dc_ifc_code">
                                                    <th><strong><?php echo $label_language_values['bank_description']; ?></strong></th>
                                                    <td><span class="amount"><?php echo $settings->get_option('ct_bank_description'); ?></span></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end payment detials -->
    <div class="ct-list-header">
        <p class="ct-sub-complete-booking"></p>
    </div>
    <?php if ($settings->get_option('ct_cancelation_policy_status') == 'Y') { ?>
        <div class="ct-complete-booking ct-md-12">
            <h5 class="ct-cancel-booking"><?php echo $label_language_values['cancellation_policy']; ?></h5>
            <div class="ct-cancel-policy">
                <p><?php echo $settings->get_option('ct_cancel_policy_header'); ?></p>
                <span class="show-more-toggler ct-link"><?php echo $label_language_values['show_more']; ?></span>
                <ul class="bullet-more">
                    <li><?php echo $settings->get_option('ct_cancel_policy_textarea'); ?></li>
                </ul>
            </div>
        </div>
    <?php } ?>
    <?php if ($settings->get_option('ct_allow_terms_and_conditions') == 'Y' || $settings->get_option('ct_allow_privacy_policy') == 'Y') { ?>
	
	<?php if (!empty($_SESSION['ct_login_user_id'])) { ?>
        <div class="bi-terms-agree" style="" id="termCondition">
            <div class="ct-custom-checkbox">
                <ul class="ct-checkbox-list">
                    <li>
                        <input type="checkbox" name="accept-conditions" class="input-radio" id="accept-conditions"/>
                        <label for="accept-conditions" class="">
                            <span></span>
                            <?php echo $label_language_values['i_have_read_and_accepted_the']; ?>
                            <?php if ($settings->get_option('ct_allow_terms_and_conditions') == 'Y' && $settings->get_option('ct_allow_privacy_policy') == 'N') { ?>
                                <a href="<?php
                                if ($settings->get_option('ct_terms_condition_link') != '') {
                                    echo $settings->get_option('ct_terms_condition_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_terms_condition_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link">
                                    <?php echo $label_language_values['terms_and_condition']; ?>
                                </a>.
                            <?php } else if ($settings->get_option('ct_allow_terms_and_conditions') == 'N' && $settings->get_option('ct_allow_privacy_policy') == 'Y') { ?>
                                <a href="<?php
                                if ($settings->get_option('ct_privacy_policy_link') != '') {
                                    echo $settings->get_option('ct_privacy_policy_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_privacy_policy_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link"><?php echo $label_language_values['privacy_policy']; ?></a>.
                            <?php } else { ?>
                                <a href="<?php
                                if ($settings->get_option('ct_terms_condition_link') != '') {
                                    echo $settings->get_option('ct_terms_condition_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_terms_condition_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link"><?php echo $label_language_values['terms_and_condition']; ?></a>
                                <?php echo $label_language_values['and']; ?>
                                <a href="<?php
                                if ($settings->get_option('ct_privacy_policy_link') != '') {
                                    echo $settings->get_option('ct_privacy_policy_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_privacy_policy_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link"><?php echo $label_language_values['privacy_policy']; ?></a>.
                               <?php } ?>
                        </label>
                    </li>
                </ul>
            </div>
            <label class="terms_and_condition"></label>
        </div>
	<?php } else { ?>
		<div class="bi-terms-agree" style="display: none;" id="termCondition">
            <div class="ct-custom-checkbox">
                <ul class="ct-checkbox-list">
                    <li>
                        <input type="checkbox" name="accept-conditions" class="input-radio" id="accept-conditions"/>
                        <label for="accept-conditions" class="">
                            <span></span>
                            <?php echo $label_language_values['i_have_read_and_accepted_the']; ?>
                            <?php if ($settings->get_option('ct_allow_terms_and_conditions') == 'Y' && $settings->get_option('ct_allow_privacy_policy') == 'N') { ?>
                                <a href="<?php
                                if ($settings->get_option('ct_terms_condition_link') != '') {
                                    echo $settings->get_option('ct_terms_condition_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_terms_condition_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link">
                                    <?php echo $label_language_values['terms_and_condition']; ?>
                                </a>.
                            <?php } else if ($settings->get_option('ct_allow_terms_and_conditions') == 'N' && $settings->get_option('ct_allow_privacy_policy') == 'Y') { ?>
                                <a href="<?php
                                if ($settings->get_option('ct_privacy_policy_link') != '') {
                                    echo $settings->get_option('ct_privacy_policy_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_privacy_policy_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link"><?php echo $label_language_values['privacy_policy']; ?></a>.
                            <?php } else { ?>
                                <a href="<?php
                                if ($settings->get_option('ct_terms_condition_link') != '') {
                                    echo $settings->get_option('ct_terms_condition_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_terms_condition_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link"><?php echo $label_language_values['terms_and_condition']; ?></a>
                                <?php echo $label_language_values['and']; ?>
                                <a href="<?php
                                if ($settings->get_option('ct_privacy_policy_link') != '') {
                                    echo $settings->get_option('ct_privacy_policy_link');
                                } else {
                                    echo 'javascript:void(0)';
                                }
                                ?>" <?php if ($settings->get_option('ct_privacy_policy_link') != '') { ?> target="-BLANK" <?php } ?> class="ct-link"><?php echo $label_language_values['privacy_policy']; ?></a>.
                               <?php } ?>
                        </label>
                    </li>
                </ul>
            </div>
            <label class="terms_and_condition"></label>
        </div>
		<?php } ?>
    <?php } ?>
	
	<?php if (!empty($_SESSION['ct_login_user_id'])) { ?>
    <div class="ta-center fl comp-bok" style="" id="submitButton">
        <?php if ($settings->get_option("ct_loader") == 'css' && $settings->get_option("ct_custom_css_loader") != '') { ?>
            <div class="ct-loading-main-complete_booking" align="center">
                <?php echo $settings->get_option("ct_custom_css_loader"); ?>
            </div>
        <?php } elseif ($settings->get_option("ct_loader") == 'gif' && $settings->get_option("ct_custom_gif_loader") != '') { ?>
            <div class="ct-loading-main-complete_booking" align="center">
                <img style="margin-top:18%;" src="<?php echo BASE_URL; ?>/assets/images/gif-loader/<?php echo $settings->get_option("ct_custom_gif_loader"); ?>"></img>
            </div>
        <?php } else { ?>
            <div class="ct-loading-main-complete_booking">
                <div class="loader">Loading...</div>
            </div>
        <?php } ?>			
        <p class="complete-message">We will confirm your service request within 24 hours. Thank you very much!</p>
        <a href="javascript:void(0)" type='submit' data-currency_symbol="<?php echo $settings->get_option('ct_currency_symbol'); ?>" id='complete_bookings' class="ct-button ct-btn-big ct_remove_id"><?php echo $label_language_values['complete_booking']; ?></a>
    </div>
	<?php } else { ?>
	<div class="ta-center fl comp-bok" style="display: none;" id="submitButton">
        <?php if ($settings->get_option("ct_loader") == 'css' && $settings->get_option("ct_custom_css_loader") != '') { ?>
            <div class="ct-loading-main-complete_booking" align="center">
                <?php echo $settings->get_option("ct_custom_css_loader"); ?>
            </div>
        <?php } elseif ($settings->get_option("ct_loader") == 'gif' && $settings->get_option("ct_custom_gif_loader") != '') { ?>
            <div class="ct-loading-main-complete_booking" align="center">
                <img style="margin-top:18%;" src="<?php echo BASE_URL; ?>/assets/images/gif-loader/<?php echo $settings->get_option("ct_custom_gif_loader"); ?>"></img>
            </div>
        <?php } else { ?>
            <div class="ct-loading-main-complete_booking">
                <div class="loader">Loading...</div>
            </div>
        <?php } ?>			
        <p class="complete-message">We will confirm your service request within 24 hours. Thank you very much!</p>
        <a href="javascript:void(0)" type='submit' data-currency_symbol="<?php echo $settings->get_option('ct_currency_symbol'); ?>" id='complete_bookings' class="ct-button ct-btn-big ct_remove_id"><?php echo $label_language_values['complete_booking']; ?></a>
    </div>
	<?php } ?>
</div>
</div>
                        <!-- left side end -->
                        <!-- right side cart -->
                        <div class="col-lg-4 col-md-4 col-sm-4 mt-30 mb-30 br-5 pull-right hide_allsss" style="display: none;">
                            <div class="owl-carousel book-now-slider">
                                <div class="item">
                                    <img src="<?php echo $base_url; ?>images/yup-serve-order-form-img-home.jpg"/>
                                </div>
                                <div class="item">
                                    <img src="<?php echo $base_url; ?>images/yup-serve-order-form-img-for-bathroom.jpg"/>
                                </div>
                                <div class="item">
                                    <img src="<?php echo $base_url; ?>images/yup-serve-order-form-img3.jpg"/>
                                </div>
                                <div class="item">
                                    <img src="<?php echo $base_url; ?>images/yup-serve-order-form-img2.jpg"/>
                                </div>                                
                            </div>
                        </div>
                        <div class="ct-main-right ct-sm-4 ct-md-4 ct-xs-12 br-5 pull-right hide_allsss" style="display: none;">
                            <div class="fl">
                                <div class="main-inner-container border-c ct-price-scroll" id="ct-price-scroll">
                                    <div class="ct-step-heading"><h3 class="header3"><?php echo $label_language_values['booking_summary']; ?></h3></div>
                                    <div class="ct-cart-wrapper f-l" id="">
                                        <div class="ct-summary">
                                            <div class="ct-image">
                                                <img src="<?php echo SITE_URL; ?>/assets/images/icon-service.png" alt="">
                                            </div>
                                            <p class="ct-text sel-service"><?php echo $service_dtls->title; ?></p>
                                        </div>
                                        <div class="ct-summary hidedatetime_value">
                                            <div class="ct-image">
                                                <img src="<?php echo SITE_URL; ?>/assets/images/icon-calendar.png" alt="">
                                            </div>
                                            <p class="ct-text sel-datetime"><span class='cart_date' data-date_val=""></span><span class="space_between_date_time"> @ </span><span class='cart_time' data-time_val=""></span></p>
                                        </div>
                                        <div class="ct-summary">
                                            <div class="ct-image f_dis_img">
                                                <img src="<?php echo SITE_URL; ?>/assets/images/icon-frequency.png" alt="">
                                            </div>
                                            <p class="ct-text sel-datetime f_discount_name"></p>
                                        </div>
                                        <div class="ct-summary hideduration_value <?php
                                        if ($settings->get_option('ct_show_time_duration') == 'N') {
                                            echo "force_hidden";
                                        }
                                        ?>">
                                            <div class="ct-image total_time_duration">
                                                <img src="<?php echo SITE_URL; ?>/assets/images/icon-timer.png" alt="">
                                            </div>
                                            <p class="ct-text total_time_duration_text"></p>
                                        </div>
                                        <div class="ct-form-rown ct-addons-list-main">
                                            <div class="step_heading f-l"><h6 class="header6 ct-item-list"><?php echo $label_language_values['cart_items']; ?></h6>
                                            </div>
                                            <div class="cart-items-main f-l">
                                                <label class="cart_empty_msg"><?php echo $label_language_values['cart_is_empty']; ?></label>
                                                <ul class="ct-addon-items-list cart_item_listing"></ul>
                                            </div>
                                        </div>
                                        <div class="ct-form-rown">
                                            <div class="ct-cart-label-common ofh"><?php echo $label_language_values['sub_total']; ?></div>
                                            <div class="ct-cart-amount-common ofh">
                                                <span class="ct-sub-total cart_sub_total"></span>
                                            </div>
                                        </div>
                                        <?php
                                        $count_f_dis = $frequently_discount->readall_front();
                                        if (mysqli_num_rows($count_f_dis) > 0) {
                                            ?>
                                            <div class="ct-form-rown freq_discount_display">
                                                <div class="ct-cart-label-common ofh"><?php echo ucwords(strtolower($label_language_values['frequently_discount'])); ?></div>
                                                <div class="ct-cart-amount-common ofh">
                                                    <span class="ct-frequently-discount frequent_discount"></span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if ($settings->get_option('ct_show_coupons_input_on_checkout') == 'on') {
                                            ?>
                                            <div class="ct-form-rown coupon_display">
                                                <div class="ct-cart-label-common ofh"><?php echo $label_language_values['coupon_discount']; ?></div>
                                                <div class="ct-cart-amount-common ofh">
                                                    <span class="ct-coupon-discount cart_discount"></span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if ($settings->get_option('ct_tax_vat_status') == 'Y') {
                                            ?>
                                            <div class="ct-form-rown">
                                                <div class="ct-cart-label-common ofh"><?php echo $label_language_values['tax']; ?></div>
                                                <div class="ct-cart-amount-common ofh">
                                                    <span class="ct-tax-amount cart_tax"></span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if ($settings->get_option('ct_partial_deposit_status') == 'Y') {
                                            ?>
                                            <div class="ct-form-rown partial_amount_hide_on_load mb-15">
                                                <div class="ct-partial-amount-wrapper border-c border-2">
                                                    <div class="ct-partial-amount-message">
                                                        <?php echo $settings->get_option('ct_partial_deposit_message'); ?>
                                                    </div>
                                                    <div class="ct-form-rown">
                                                        <div class="ct-cart-label-common ofh"><?php echo $label_language_values['partial_deposit']; ?></div>
                                                        <div class="ct-cart-amount-common ofh">
                                                            <span class="ct-partial-deposit partial_amount"></span>
                                                        </div>
                                                    </div>
                                                    <div class="ct-form-rown">
                                                        <div class="ct-cart-label-common ofh"><?php echo $label_language_values['remaining_amount']; ?></div>
                                                        <div class="ct-cart-amount-common ofh">
                                                            <span class="ct-remaining-amount remain_amount"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="ct-clear"></div>
                                        <div id="ct-line"></div>
                                        <div class="ct-form-rown">
                                            <div class="ct-cart-label-total-amount ofh"><?php echo $label_language_values['total']; ?></div>
                                            <div class="ct-cart-total-amount ofh">
                                                <span class="ct-total-amount cart_total"></span>
                                            </div>
                                        </div>
                                        <div class="ct-clear"></div>
                                        <!-- discount coupons -->
                                    </div>
                                    <!-- cart wrapper end here -->
                                </div>
                            </div>
                        </div>
                        <!-- right side card end -->
                        </form>
                        <a href="javascript:void(0)" class="ct-back-to-top br-2"><i class="icon-arrow-up icons"></i></a>
                        <?php
                        if (sizeof($purchase_check) > 0) {
                            foreach ($purchase_check as $key => $val) {
                                if ($val == 'Y') {
                                    echo $payment_hook->payment_form_hook($key);
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <!-- end container -->
            </div>
            <!-- forget password -->
            <div class="main">
                <div id="ct-front-forget-password">
                    <div class="vertical-alignment-helper">
                        <div class="vertical-align-center">
                            <div class="ct-front-forget-password visible">
                                <div class="form-container">
                                    <div class="tab-content">
                                        <form id="forget_pass" name="" method="POST">
                                            <h1 class="forget-password"><?php echo $label_language_values['reset_password']; ?></h1>
                                            <h4><?php echo $label_language_values['enter_your_email_and_we_send_you_instructions_on_resetting_your_password']; ?></h4>
                                            <div class="form-group fl mt-15">
                                                <label for="userEmail"><i class="icon-envelope-alt"></i><?php echo $label_language_values['email']; ?></label>
                                                <input type="email" class="add_show_error_class error" id="rp_user_email" name="rp_user_email" placeholder="<?php echo $label_language_values['registered_email']; ?>">
                                            </div>
                                            <label class="forget_pass_correct"></label>
                                            <label class="forget_pass_incorrect"></label>
                                            <div class="clearfix"></div>
                                            <a class="btn ct-info-btn btn-lg ct-xs-12" href="javascript:void(0)" id="reset_pass"><?php echo $label_language_values['send_mail']; ?></a>
                                            <div class="clearfix">
                                                <a class="btn btn-link ct-xs-12" id="ct_login_user" href="javascript:void(0)"><?php echo $label_language_values['back_to_login']; ?></a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once 'include/footer.php'; ?>
        <div class="clearfix"></div>
        <script>
            var baseurlObj = {'base_url': '<?php echo BASE_URL; ?>', 'stripe_publishkey': '<?php echo $settings->get_option('ct_stripe_publishablekey'); ?>', 'stripe_status': '<?php echo $settings->get_option('ct_stripe_payment_form_status'); ?>'};
            var siteurlObj = {'site_url': '<?php echo SITE_URL; ?>'};
            var ajaxurlObj = {'ajax_url': '<?php echo AJAX_URL; ?>'};
            var fronturlObj = {'front_url': '<?php echo FRONT_URL; ?>'};
            var termsconditionObj = {'terms_condition': '<?php echo $settings->get_option('ct_allow_terms_and_conditions'); ?>'};
            var privacypolicyObj = {'privacy_policy': '<?php echo $settings->get_option('ct_allow_privacy_policy'); ?>'};
<?php
if ($settings->get_option('ct_thankyou_page_url') == '') {
    $thankyou_page_url = SITE_URL . 'front/thankyou.php';
} else {
    $thankyou_page_url = $settings->get_option('ct_thankyou_page_url');
}
$phone = explode(",", $settings->get_option('ct_bf_phone'));
$check_password = explode(",", $settings->get_option('ct_bf_password'));
$check_fn = explode(",", $settings->get_option('ct_bf_first_name'));
$check_ln = explode(",", $settings->get_option('ct_bf_last_name'));
$check_addresss = explode(",", $settings->get_option('ct_bf_address'));
$check_zip_code = explode(",", $settings->get_option('ct_bf_zip_code'));
$check_city = explode(",", $settings->get_option('ct_bf_city'));
$check_state = explode(",", $settings->get_option('ct_bf_state'));
$check_notes = explode(",", $settings->get_option('ct_bf_notes'));
$check_notes = explode(",", $settings->get_option('ct_bf_notes'));

$ct_currency_symbol = $settings->get_option('ct_currency_symbol');
$ct_currency_symbol_position = $settings->get_option('ct_currency_symbol_position');
$ct_price_format_decimal_places = $settings->get_option('ct_price_format_decimal_places');
?>
            var currency_symbol = '<?php echo $ct_currency_symbol; ?>';
            var currency_symbol_position = '<?php echo $ct_currency_symbol_position; ?>';
            var price_format_decimal_places = '<?php echo $ct_price_format_decimal_places; ?>';
            var thankyoupageObj = {'thankyou_page': '<?php echo $thankyou_page_url; ?>'};
            var phone_status = {'statuss': '<?php echo $phone[0]; ?>', 'required': '<?php echo $phone[1]; ?>', 'min': '<?php echo $phone[2]; ?>', 'max': '<?php echo $phone[3]; ?>'};
            var check_password = {'statuss': '<?php echo $check_password[0]; ?>', 'required': '<?php echo $check_password[1]; ?>', 'min': '<?php echo $check_password[2]; ?>', 'max': '<?php echo $check_password[3]; ?>'};
            var check_fn = {'statuss': '<?php echo $check_fn[0]; ?>', 'required': '<?php echo $check_fn[1]; ?>', 'min': '<?php echo $check_fn[2]; ?>', 'max': '<?php echo $check_fn[3]; ?>'};
            var check_ln = {'statuss': '<?php echo $check_ln[0]; ?>', 'required': '<?php echo $check_ln[1]; ?>', 'min': '<?php echo $check_ln[2]; ?>', 'max': '<?php echo $check_ln[3]; ?>'};
            var check_addresss = {'statuss': '<?php echo $check_addresss[0]; ?>', 'required': '<?php echo $check_addresss[1]; ?>', 'min': '<?php echo $check_addresss[2]; ?>', 'max': '<?php echo $check_addresss[3]; ?>'};
            var check_zip_code = {'statuss': '<?php echo $check_zip_code[0]; ?>', 'required': '<?php echo $check_zip_code[1]; ?>', 'min': '<?php echo $check_zip_code[2]; ?>', 'max': '<?php echo $check_zip_code[3]; ?>'};
            var check_city = {'statuss': '<?php echo $check_city[0]; ?>', 'required': '<?php echo $check_city[1]; ?>', 'min': '<?php echo $check_city[2]; ?>', 'max': '<?php echo $check_city[3]; ?>'};
            var check_state = {'statuss': '<?php echo $check_state[0]; ?>', 'required': '<?php echo $check_state[1]; ?>', 'min': '<?php echo $check_state[2]; ?>', 'max': '<?php echo $check_state[3]; ?>'};
            var check_notes = {'statuss': '<?php echo $check_notes[0]; ?>', 'required': '<?php echo $check_notes[1]; ?>', 'min': '<?php echo $check_notes[2]; ?>', 'max': '<?php echo $check_notes[3]; ?>'};
<?php
$nacode = explode(',', $settings->get_option("ct_company_country_code"));
$allowed = $settings->get_option("ct_phone_display_country_code");
?>
            var countrycodeObj = {'numbercode': '<?php echo $nacode[0]; ?>', 'alphacode': '<?php echo $nacode[1]; ?>', 'countrytitle': '<?php echo $nacode[2]; ?>', 'allowed': '<?php echo $allowed; ?>'};
            var subheaderObj = {'subheader_status': '<?php echo $settings->get_option('ct_subheaders'); ?>'};
            var twocheckout_Obj = {'sellerId': '<?php echo $settings->get_option('ct_2checkout_sellerid'); ?>', 'publishKey': '<?php echo $settings->get_option('ct_2checkout_publishkey'); ?>', 'twocheckout_status': '<?php echo $settings->get_option('ct_2checkout_status'); ?>'};
            var appoint_details = {'status': '<?php echo $settings->get_option('ct_appointment_details_display'); ?>'};
<?php
$is_login_user = "N";
if (isset($_SESSION['ct_login_user_id'])) {
    $is_login_user = "Y";
}
?>
            var is_login_user = '<?php echo $is_login_user; ?>';
        </script>
    </div>
</body>
<script>
    $(document).ready(function () {
        // Restrict number input only using class number_only in the input field
        $('input.number_only').keyup(function (e) {
            if (/\D/g.test(this.value)) {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
            }
        });
        
        
       // $("#user_details_form").validate();
       
       $("#verifyMobile").on("click",function(verifyMobile){
            verifyMobile.preventDefault();
            var mobile = $("#mobile_number").val();
            if(isNaN(mobile) || mobile==""){
                $("#error_message").html("Please enter a valid number");
            }else{
                if(!$('#mobile_number').val().match('[0-9]{10}'))  {
                    $("#error_message").html("Please put 10 digit mobile number");
                    //alert("Please put 10 digit mobile number");
                    //return;
                } else{
                    //alert("hii");
                    $.post("<?php echo BASE_URL."/front/mobileVerify.php"; ?>",{
                        mobile:mobile,
                        part:"mobile",
                    },function success(data){
                        var check = $.trim(data);
                        if(check == "A"){
                            $("#error_message").html("Mobile number already exist.");
                        }
                        if(check == "X"){
                            $("#error_message").html("You can send three times in 30 minutes.");
                            //alert("OTP is invalid.");
                        }
                        if(check == "S"){
                            $('#mobileVerifyForm').hide();
                            //$('#verifyMobile').attr("class","verifyOTP");
                            
                            $('#isSent').val("sent");
                            $('#otpVerifyForm').show();
                        }
                        //alert(data);
                    });
                } 
            }
       });
       // - Mobile OTP END

       // - Verify OTP --termCondition  submitButton
       $("#verifyOTP").on("click",function(verifyMobile){
            verifyMobile.preventDefault();
            var otp = $("#mobile_otp").val();
            if(isNaN(otp) || otp==""){
                $("#error_message_otp").html("Please enter a valid number");
            }else{
                if(!$('#mobile_otp').val().match('[0-9]{4}'))  {
                    $("#error_message_otp").html("Please put 4 digit mobile number");
                    //alert("Please put 10 digit mobile number");
                    //return;
                } else{
                    //alert("hii");
                    $.post("<?php echo BASE_URL."/front/mobileVerify.php"; ?>",{
                        mobile:otp,
                        part:"otp",
                    },function success(data){
                        var check = $.trim(data);
                        if(check == "I"){
                            $("#error_message_otp").html("OTP is invalid.");
                            //alert("OTP is invalid.");
                        }
                        if(check == "X"){
                            $("#error_message_otp").html("You can send three times in 30 minutes.");
                            //alert("OTP is invalid.");
                        }
                        if(check == "S"){
                            var mobile = $("#mobile_number").val();
                            $("#ct-user-phone").val(mobile);
                            
                            $('#isVerify').val("verify");
                            //alert("valid");
                            $('#mobileVerifyForm').hide();
                            //$('#verifyMobile').attr("class","verifyOTP");
                            $('#otpVerifyForm').hide();
                            $('#user_details_form').show();
                            $('#termCondition').show();
                            $('#submitButton').show();
                        }
                        //alert(data);
                    });
                } 
            }
       });
       /*
        *On page load
       */
       // - Resend OTP
       $("#resendOTP").on("click",function(){
            var mobile = $("#mobile_number").val();
            $.post("<?php echo BASE_URL."/front/mobileVerify.php"; ?>",{
                mobile:mobile,
                part:"resend",
            },function success(data){
                var check = $.trim(data);
                if(check == "A"){
                    $("#error_message_otp").html("OTP is invalid.");
                }
                if(check == "X"){
                    $("#error_message_otp").html("You can send three times in 30 minutes.");
                }
                if(check == "S"){
                    $("#error_message_otp").html("OTP sent. Please check your phone.");
                    /*$('#isVerify').val("verify");
                    $('#mobileVerifyForm').hide();
                    $('#otpVerifyForm').hide();
                    $('#user_details_form').show();
                    $('#termCondition').show();
                    $('#submitButton').show();*/
                }
                //alert(data);
            });
       });

    $(".new-user").on("change", function () {
        if ($('.new-user').is(':checked')) {
			debugger
            var isSent = $('#isSent').val();
            var isVerify = $('#isVerify').val();
            if(isSent){
                if(isVerify){
                    //$('#user_details_form').show();
                    //$('#termCondition').show();
                    //$('#submitButton').show();
                }else{
                   $('#otpVerifyForm').show();
                }
            }else{
               $('#mobileVerifyForm').show();                
            }
        }
    });
    });

    jQuery("#user_details_form").validate();
</script>
</html>