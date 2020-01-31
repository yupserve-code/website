<?php

require_once "../../objects/class_connection.php";
require_once "../../class_configure.php";
// API config
require_once "../config.php";
// API header
require_once "../header.php";
require_once "../../objects/class_setting.php";
require_once "../../objects/class_general.php";
require_once "../../objects/class_front_first_step.php";
require_once "../../objects/class_dayweek_avail.php";
require_once "../../objects/class_gc_hook.php";
/*
 * load_date.php
 * Load Date
 */

class Load_Date {

    public function __construct() {
        
    }

    public function index() {

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache');

        $json = array();

        if (!empty($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == "POST")) {

            if (!empty($_POST['customer_id'])) {

                $customer_id = trim($_POST['customer_id']);

                /*
                  $header = new API_Header();
                  // API Token
                  $token = $header->get_auth_request_header_key();

                  $config = new API_Config();
                  // Check valid API call from valid resource
                  if ($config->check_valid_api_call(trim($token))) {

                 */

                // Database
                $con = new cleanto_db();
                $conn = $con->connect();
                // Google Calendar
                $gc_hook = new cleanto_gcHook();
                $gc_hook->conn = $conn;
                // First step to check
                $first_step = new cleanto_first_step();
                $first_step->conn = $conn;
                // Weekdays available for slot booking
                $week_day_avail = new cleanto_dayweek_avail();
                $week_day_avail->conn = $conn;
                // Settings
                $setting = new cleanto_setting();
                $setting->conn = $conn;

                $date_format = $setting->get_option('ct_date_picker_date_format');
                $time_interval = $setting->get_option('ct_time_interval');
                $time_slots_schedule_type = $setting->get_option('ct_time_slots_schedule_type');
                $advance_bookingtime = $setting->get_option('ct_min_advance_booking_time');
                $ct_service_padding_time_before = $setting->get_option('ct_service_padding_time_before');
                $ct_service_padding_time_after = $setting->get_option('ct_service_padding_time_after');
                $ct_calendar_firstDay = $setting->get_option('ct_calendar_firstDay');
                $booking_padding_time = $setting->get_option('ct_booking_padding_time');


                $t_zone_value = $setting->get_option('ct_timezone');
                $server_timezone = date_default_timezone_get();

                if (isset($t_zone_value) && $t_zone_value != '') {
                    $offset = $first_step->get_timezone_offset($server_timezone, $t_zone_value);
                    $timezonediff = $offset / 3600;
                } else {
                    $timezonediff = 0;
                }

                if (is_numeric(strpos($timezonediff, '-'))) {
                    $timediffmis = str_replace('-', '', $timezonediff) * 60;
                    $currDateTime_withTZ = strtotime("-" . $timediffmis . " minutes", strtotime(date('Y-m-d H:i:s')));
                } else {
                    $timediffmis = str_replace('+', '', $timezonediff) * 60;
                    $currDateTime_withTZ = strtotime("+" . $timediffmis . " minutes", strtotime(date('Y-m-d H:i:s')));
                }

                list($year, $month, $iNowDay) = explode('-', date('Y-m-d', $currDateTime_withTZ));

                $ct_max_advance_booking_time = $setting->get_option('ct_max_advance_booking_time');
                $datetime_withmaxtime = strtotime('+' . $ct_max_advance_booking_time . ' month', strtotime(date('Y-m-d', $currDateTime_withTZ)));

                $date = mktime(12, 0, 0, $month, 1, $year);
                $yearss = date("Y", $date);
                $monthss = date("m", $date);
                $monthssss = date("M", $date);

                $prevmonthlink = strtotime(date("Y-m-d", $date));
                $currrmonthlink = strtotime(date("Y-m-d", $currDateTime_withTZ));

                $daysInMonth = date("t", $date);
                /* calculate the position of the first day in the calendar (sunday = 1st column, etc) */
                if ($ct_calendar_firstDay == '1') {
                    $offset = date("N", $date);
                } else {
                    $offset = date("w", $date);
                }

                $rows = 1;

                $next_months = strtotime('+1 month', $date);
                $prev_months = strtotime('-1 month', $date);

                $json['success']['current_day'] = array(
                    'date' => date("d-m-Y", time()),
                    'month_name' => date("F", $date),
                    'year_name' => date("Y", $date)
                );

                // Weekdays
                if ($ct_calendar_firstDay == '0') {
                    $week_Days[] = 'Sun';
                    $week_Days[] = 'Sat';
                    $get_first_day_starting = 1;
                } else if ($ct_calendar_firstDay == '1') {
                    $week_Days[] = 'Sat';
                    $week_Days[] = 'Sun';
                    $get_first_day_starting = 2;
                }

                $week_Days[] = 'Mon';
                $week_Days[] = 'Tue';
                $week_Days[] = 'Wed';
                $week_Days[] = 'Thu';
                $week_Days[] = 'Fri';

                foreach ($week_Days as $week) {
                    $json['success']['weekdays'][] = array(
                        'day' => $week
                    );
                }

                // Admin dates and the slots provided
                $staff_id = 1;
                // For once in month by default and with 0% save
                $discount_id = 1;

                for ($days = 1; $days <= $daysInMonth; $days++) {
                    $json['success']['days'][] = array(
                        'day' => $days
                    );
                }

                $t_zone_value = $setting->get_option('ct_timezone');
                $server_timezone = date_default_timezone_get();
                if (isset($t_zone_value) && $t_zone_value != '') {
                    $offset = $first_step->get_timezone_offset($server_timezone, $t_zone_value);
                    $timezonediff = $offset / 3600;
                } else {
                    $timezonediff = 0;
                }

                if (is_numeric(strpos($timezonediff, '-'))) {
                    $timediffmis = str_replace('-', '', $timezonediff) * 60;
                    $currDateTime_withTZ = strtotime("-" . $timediffmis . " minutes", strtotime(date('Y-m-d H:i:s')));
                } else {
                    $timediffmis = str_replace('+', '', $timezonediff) * 60;
                    $currDateTime_withTZ = strtotime("+" . $timediffmis . " minutes", strtotime(date('Y-m-d H:i:s')));
                }

                $select_time = date('Y-m-d', strtotime(date("d-m-Y", time())));
                $start_date = date($select_time, $currDateTime_withTZ);

                /** Get Google Calendar Bookings * */
                $providerCalenderBooking = array();
                if ($gc_hook->gc_purchase_status() == 'exist') {
                    $gc_hook->google_cal_TwoSync_hook();
                }
                /** Get Google Calendar Bookings * */
                $time_interval = $setting->get_option('ct_time_interval');
                $time_slots_schedule_type = $setting->get_option('ct_time_slots_schedule_type');
                $advance_bookingtime = $setting->get_option('ct_min_advance_booking_time');
                $ct_service_padding_time_before = $setting->get_option('ct_service_padding_time_before');
                $ct_service_padding_time_after = $setting->get_option('ct_service_padding_time_after');

                $booking_padding_time = $setting->get_option('ct_booking_padding_time');
                $time_schedule = $first_step->get_day_time_slot_by_provider_id($time_slots_schedule_type, $start_date, $time_interval, $advance_bookingtime, $ct_service_padding_time_before, $ct_service_padding_time_after, $timezonediff, $booking_padding_time, $staff_id);

                $allbreak_counter = 0;
                $allofftime_counter = 0;
                $slot_counter = 0;

                $week_day_avail_count = $week_day_avail->get_data_for_front_cal();

                // Timeslot
                if (mysqli_num_rows($week_day_avail_count) > 0) {

                    if ($time_schedule['off_day'] != true && isset($time_schedule['slots']) && sizeof($time_schedule['slots']) > 0 && $allbreak_counter != sizeof($time_schedule['slots']) && $allofftime_counter != sizeof($time_schedule['slots'])) {

                        foreach ($time_schedule['slots'] as $slot) {

                            /* Checking in GC booked Slots START */
                            $curreslotstr = strtotime(date(date('Y-m-d H:i:s', strtotime($select_time . ' ' . $slot)), $currDateTime_withTZ));

                            $gccheck = 'N';

                            if (sizeof($providerCalenderBooking) > 0) {
                                for ($i = 0; $i < sizeof($providerCalenderBooking); $i++) {
                                    if ($curreslotstr >= $providerCalenderBooking[$i]['start'] && $curreslotstr < $providerCalenderBooking[$i]['end']) {
                                        $gccheck = 'Y';
                                    }
                                }
                            }
                            /* Checking in GC booked Slots END */

                            $ifbreak = 'N';
                            /* Need to check if the appointment slot come under break time. */
                            foreach ($time_schedule['breaks'] as $daybreak) {
                                if (strtotime($slot) >= strtotime($daybreak['break_start']) && strtotime($slot) < strtotime($daybreak['break_end'])) {
                                    $ifbreak = 'Y';
                                }
                            }

                            /* if yes its break time then we will not show the time for booking  */
                            if ($ifbreak == 'Y') {
                                $allbreak_counter++;
                                continue;
                            }

                            $ifofftime = 'N';

                            foreach ($time_schedule['offtimes'] as $offtime) {
                                if (strtotime(date("d-m-Y", time()) . ' ' . $slot) >= strtotime($offtime['offtime_start']) && strtotime(date("d-m-Y", time()) . ' ' . $slot) < strtotime($offtime['offtime_end'])) {
                                    $ifofftime = 'Y';
                                }
                            }

                            /* if yes its offtime time then we will not show the time for booking  */
                            if ($ifofftime == 'Y') {
                                $allofftime_counter++;
                                continue;
                            }

                            $final_slot = '';

                            $complete_time_slot = mktime(date('H', strtotime($slot)), date('i', strtotime($slot)), date('s', strtotime($slot)), date('n', strtotime($time_schedule['date'])), date('j', strtotime($time_schedule['date'])), date('Y', strtotime($time_schedule['date'])));

                            if ($setting->get_option('ct_hide_faded_already_booked_time_slots') == 'on' && (in_array($complete_time_slot, $time_schedule['booked'])) || $gccheck == 'Y') {
                                continue;
                            }

                            if ((in_array($complete_time_slot, $time_schedule['booked']) || $gccheck == 'Y') && ($setting->get_option('ct_allow_multiple_booking_for_same_timeslot_status') != 'Y')) {

                                if ($setting->get_option('ct_hide_faded_already_booked_time_slots') == "off") {
                                    if ($setting->get_option('ct_time_format') == 24) {
                                        $final_slot = date("H:i", strtotime($slot));
                                    } else {
                                        $final_slot = date("h:i A", strtotime($slot));
                                    }
                                }
                            } else {
                                $final_slot = date("h:i A", strtotime($slot));
                            }

                            if ($allbreak_counter != 0 && $allofftime_counter != 0) {
                                $json['error']['message'] = 'Timeslots not available';

                                /*
                                 * GET customer details
                                 */

                                $sql_cstm_dtls = "SELECT * FROM ct_users WHERE id='{$customer_id}'";
                                $res_cstm_dtls = mysqli_query($conn, $sql_cstm_dtls);
                                $row_cstm_dtls = mysqli_fetch_object($res_cstm_dtls);

                                $json['customer_dtls'] = array(
                                    'customer_id' => $customer_id,
                                    'phone' => $row_cstm_dtls->phone,
                                    'zip' => $row_cstm_dtls->zip,
                                    'address' => $row_cstm_dtls->address
                                );
                            }
                            if ($allbreak_counter == sizeof($time_schedule['slots']) && sizeof($time_schedule['slots']) != 0) {
                                $json['error']['message'] = 'Timeslots not available';

                                /*
                                 * GET customer details
                                 */

                                $sql_cstm_dtls = "SELECT * FROM ct_users WHERE id='{$customer_id}'";
                                $res_cstm_dtls = mysqli_query($conn, $sql_cstm_dtls);
                                $row_cstm_dtls = mysqli_fetch_object($res_cstm_dtls);

                                $json['customer_dtls'] = array(
                                    'customer_id' => $customer_id,
                                    'phone' => $row_cstm_dtls->phone,
                                    'zip' => $row_cstm_dtls->zip,
                                    'address' => $row_cstm_dtls->address
                                );
                            }
                            if ($allofftime_counter > sizeof($time_schedule['offtimes']) && sizeof($time_schedule['slots']) == $allofftime_counter) {
                                $json['error']['message'] = 'Timeslots not available';

                                /*
                                 * GET customer details
                                 */

                                $sql_cstm_dtls = "SELECT * FROM ct_users WHERE id='{$customer_id}'";
                                $res_cstm_dtls = mysqli_query($conn, $sql_cstm_dtls);
                                $row_cstm_dtls = mysqli_fetch_object($res_cstm_dtls);

                                $json['customer_dtls'] = array(
                                    'customer_id' => $customer_id,
                                    'phone' => $row_cstm_dtls->phone,
                                    'zip' => $row_cstm_dtls->zip,
                                    'address' => $row_cstm_dtls->address
                                );
                            }

                            if (empty($json['error'])) {
                                // Timeslot lists
                                $json['success']['time_slots'][] = array(
                                    'date' => date("d-m-Y", time()),
                                    'db_time' => date("H:i", strtotime($slot)),
                                    'slot' => $final_slot
                                );

                                /*
                                 * GET customer details
                                 */

                                $sql_cstm_dtls = "SELECT * FROM ct_users WHERE id='{$customer_id}'";
                                $res_cstm_dtls = mysqli_query($conn, $sql_cstm_dtls);
                                $row_cstm_dtls = mysqli_fetch_object($res_cstm_dtls);

                                $json['customer_dtls'] = array(
                                    'customer_id' => $customer_id,
                                    'phone' => $row_cstm_dtls->phone,
                                    'zip' => $row_cstm_dtls->zip,
                                    'address' => $row_cstm_dtls->address
                                );
                            }

                            $slot_counter++;
                        }
                    } else {
                        $json['error']['message'] = 'Timeslots not available';

                        /*
                         * GET customer details
                         */

                        $sql_cstm_dtls = "SELECT * FROM ct_users WHERE id='{$customer_id}'";
                        $res_cstm_dtls = mysqli_query($conn, $sql_cstm_dtls);
                        $row_cstm_dtls = mysqli_fetch_object($res_cstm_dtls);

                        $json['customer_dtls'] = array(
                            'customer_id' => $customer_id,
                            'phone' => $row_cstm_dtls->phone,
                            'zip' => $row_cstm_dtls->zip,
                            'address' => $row_cstm_dtls->address
                        );
                    }
                } else {
                    $json['error']['message'] = 'Timeslots not available';

                    /*
                     * GET customer details
                     */

                    $sql_cstm_dtls = "SELECT * FROM ct_users WHERE id='{$customer_id}'";
                    $res_cstm_dtls = mysqli_query($conn, $sql_cstm_dtls);
                    $row_cstm_dtls = mysqli_fetch_object($res_cstm_dtls);

                    $json['customer_dtls'] = array(
                        'customer_id' => $customer_id,
                        'phone' => $row_cstm_dtls->phone,
                        'zip' => $row_cstm_dtls->zip,
                        'address' => $row_cstm_dtls->address
                    );
                }

                /* 	
                  } else {
                  $json['error']['message'] = "Not authorized to access the API";
                  }
                 */
            } else {
                $json['error']['message'] = "Parameters are missing";
            }
        } else {
            $json['error']['message'] = "The request type is not allowed";
        }

        echo json_encode($json);
    }

}

$load_date = new Load_Date();
$load_date->index();
