<?php
include(dirname(__FILE__) . '/header.php');
include(dirname(__FILE__) . '/user_session_check.php');
include(dirname(dirname(__FILE__)) . '/objects/class_booking.php');
include(dirname(dirname(__FILE__)) . '/objects/class_payments.php');
include(dirname(dirname(__FILE__)) . '/objects/class_staff_commision.php');
include(dirname(dirname(__FILE__)) . '/objects/class_services.php');

$con = new cleanto_db();
$conn = $con->connect();
$objpayment = new cleanto_payments();
$objpayment->conn = $conn;

$staffpayment = new cleanto_staff_commision();
$staffpayment->conn = $conn;

$objservice = new cleanto_services();
$objservice->conn = $conn;

$booking = new cleanto_booking();
$booking->conn = $conn;

$setting = new cleanto_setting();
$setting->conn = $conn;
$gettimeformat = $setting->get_option('ct_time_format'); /* CHECK FOR VC AND PARKING STATUS */
$global_vc_status = $setting->get_option('ct_vc_status');
$global_p_status = $setting->get_option('ct_p_status'); /* CHECK FOR VC AND PARKING STATUS END */
?>
<div class="container">
    <?php
    $sql_stat = "SELECT COUNT(*) AS total_appointments, 
        (SELECT COUNT(*) FROM ct_bookings b JOIN ct_services s ON b.service_id = s.id 
        WHERE b.booking_date_time = CURRENT_DATE) AS today_services_booked, 
        (CASE WHEN (SELECT SUM(p.net_amount) FROM ct_payments p JOIN ct_bookings b 
        ON b.order_id = p.order_id WHERE MONTH(b.booking_date_time) = MONTH(CURRENT_DATE)) 
        IS NULL THEN 0 ELSE (SELECT SUM(p.net_amount) FROM ct_payments p JOIN ct_bookings b 
        ON b.order_id = p.order_id 
        WHERE MONTH(b.booking_date_time) = MONTH(CURRENT_DATE)) END) AS total_sales, 
        (SELECT COUNT(*) FROM ct_bookings WHERE booking_Status = 'C' AND 
        MONTH(booking_date_time) = MONTH(CURRENT_DATE)) AS total_pending_tasks 
        FROM ct_bookings WHERE MONTH(booking_date_time) = MONTH(CURRENT_DATE)";
    $res_stat = mysqli_query($conn, $sql_stat);
    $row_stat = mysqli_fetch_object($res_stat);
    ?>
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6 res-600">
            <!-- small box -->
            <div class="small-box bg-aqua dashboard-grid">
                <div class="inner">
                    <h3><?php echo $row_stat->total_appointments; ?></h3>
                    <p>Monthly Appointments</p>
                </div>
                <div class="icon">
                    <i class="fa fa-book"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6 res-600">
            <!-- small box -->
            <div class="small-box bg-green dashboard-grid">
                <div class="inner">
                    <h3><?php echo $row_stat->total_sales; ?></h3>
                    <p>Monthly Sales</p>
                </div>
                <div class="icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6 res-600">
            <!-- small box -->
            <div class="small-box bg-yellow dashboard-grid">
                <div class="inner">
                    <h3><?php echo $row_stat->today_services_booked; ?></h3>
                    <p>Today Appointments</p>
                </div>
                <div class="icon">
                    <i class="fa fa-support"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6 res-600">
            <!-- small box -->
            <div class="small-box bg-red dashboard-grid">
                <div class="inner">
                    <h3><?php echo $row_stat->total_pending_tasks; ?></h3>
                    <p>Monthly Pending Tasks</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clipboard"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-8 col-sm-12">
            <div class="pad border-dashboard">
                <!-- Map will be created here -->
                <div id="curve_chart" style="width: 700px; height: 400px"></div>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-12">
            <!-- /.box-header -->
            <div class="box-body no-padding border-dashboard">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="pad">
                            <div id="piechart_3d" style="width: 250px; height: 400px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Recent Appointments</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table border-dashboard" style="margin-top: 10px;">
                        <thead>
                        <th style="width: 10px">#</th>
                        <th>Order ID</th>
                        <th>Service</th>
                        <th>Booking Date</th>
                        <th style="width: 40px">Status</th>
                        </thead>
                        <tbody>
                            <?php
                            $sql_srv = "SELECT b.*, s.title FROM ct_bookings b JOIN ct_services s ON b.service_id = s.id  WHERE MONTH(b.booking_date_time) = MONTH(CURRENT_DATE) GROUP BY b.order_id ORDER BY b.order_id DESC LIMIT 5";
                            $res_srvs = mysqli_query($conn, $sql_srv);
                            if (mysqli_num_rows($res_srvs) > 0) {
                                $cntr = 1;
                                while ($service = mysqli_fetch_object($res_srvs)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cntr; ?></td>
                                        <td><a href="view.php?order_id=<?php echo $service->order_id; ?>"><?php echo $service->order_id; ?></a></td>
                                        <td><?php echo $service->title; ?></td>
                                        <td><?php echo $service->booking_date_time; ?></td>
                                        <td>
                                            <?php
                                            if ($service->booking_status == 'CO') {
                                                $status = 'Completed';
                                            } else if ($service->booking_status == 'R') {
                                                $status = 'Rejected';
                                            } else if ($service->booking_status == 'C') {
                                                $status = 'Confirmed';
                                            } else if ($service->booking_status == 'CS') {
                                                $status = 'Cancelled by Staff';
                                            } else if ($service->booking_status == 'MN') {
                                                $status = 'No Show';
                                            } else if ($service->booking_status == 'CO') {
                                                $status = 'Completed';
                                            } else if ($service->booking_status == 'CC') {
                                                $status = 'Cancelled by Customer';
                                            } else if ($service->booking_status == 'A') {
                                                $status = 'Active';
                                            }
                                            ?>
                                            <?php echo $status; ?></td>
                                    </tr>
                                    <?php $cntr++;
                                }
                                ?>
                            <?php } else { ?>
                                <tr>No data to display</tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-4">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Recent Service Assigned</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table border-dashboard"  style="margin-top: 10px;">
                        <tr>
                            <th>Order ID</th>
                            <th>Service Boy</th>
                        </tr>
                        <?php
                        $sql_srv_asn = "SELECT b.order_id, u.fullname AS service_boy_name 
                            FROM ct_bookings b JOIN ct_admin_info u ON b.staff_ids = u.id 
                            WHERE MONTH(b.booking_date_time) = MONTH(CURRENT_DATE) AND u.role = 'staff' 
                            GROUP BY b.order_id ORDER BY b.order_id DESC LIMIT 5";

                        $res_srv_asn = mysqli_query($conn, $sql_srv_asn);
                        if (mysqli_num_rows($res_srv_asn) > 0) {
                            while ($service_asn = mysqli_fetch_object($res_srv_asn)) {
                                ?>
                                <tr>
                                    <td><a href="view.php?order_id=<?php echo $service_asn->order_id; ?>"><?php echo $service_asn->order_id; ?></a></td>
                                    <td><?php echo $service_asn->service_boy_name; ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>No data to display</tr>
                        <?php } ?>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</div>
<!-- /.col -->
<?php include(dirname(__FILE__) . '/footer.php'); ?>
<script>
    var ajax_url = '<?php echo AJAX_URL; ?>';
    var base_url = '<?php echo BASE_URL; ?>';
    var calObj = {'ajax_url': '<?php echo AJAX_URL; ?>'};
    var times = {'time_format_values': '<?php echo $gettimeformat; ?>'};
    var site_ur = {'site_url': '<?php echo SITE_URL; ?>'};
</script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
<script>
    $(document).ready(function () {

<?php
$sql_pie_stat = "SELECT COUNT(*) AS total_assigned, 
                (SELECT COUNT(*) FROM ct_bookings WHERE booking_status='CS' AND MONTH(booking_date_time) = MONTH(CURRENT_DATE)) AS total_cancelled,
                (SELECT COUNT(*) FROM ct_bookings WHERE booking_Status = 'CO' AND 
                MONTH(booking_date_time) = MONTH(CURRENT_DATE)) AS total_completed, 
                (SELECT COUNT(*) FROM ct_bookings WHERE booking_Status = 'R' AND 
                MONTH(booking_date_time) = MONTH(CURRENT_DATE)) AS total_rejected
                FROM ct_bookings WHERE booking_status='C' AND MONTH(booking_date_time) = MONTH(CURRENT_DATE)";
$res_pie_stat = mysqli_query($conn, $sql_pie_stat);
$row_pie_stat = mysqli_fetch_object($res_pie_stat);
?>

        // Piechart for Notification Report
        Highcharts.chart('piechart_3d', {
            chart: {
                type: 'pie',
                options3d: {
                    enabled: false,
                    alpha: 45,
                    beta: 0
                },
                dataLabels: {
                    enabled: false,
                    format: '{point.name}'
                },
                style: {
                    fontFamily: "'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif"
                }
            },
            title: {
                text: 'Bookings'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    depth: 35,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}',
                        distance: -50,
                        color: '#FFFFFF',
                        style: {
                            fontSize: '17px'
                        },
                        connectorColor: '#FFFFFF',
                        useHTML: true,
                        connectorWidth: 0,
                        formatter: function () {
                            return (this.point.y > 0) ? this.point.y : null;
                        }
                    },
                    showInLegend: true,
                    colors: ['#4dabf5', '#56CA7D', '#FF784E', '#EEAB10']
                }
            },
            exporting: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            series: [{
                    type: 'pie',
                    name: 'Appointments',
                    data: [
                        ['Assigned', <?php echo $row_pie_stat->total_assigned; ?>],
                        ['Completed', <?php echo $row_pie_stat->total_completed; ?>],
                        ['Rejected', <?php echo $row_pie_stat->total_rejected; ?>],
                        ['Cancelled', <?php echo $row_pie_stat->total_cancelled; ?>]
                    ]
                }],
            lang: {
                noData: "no data to display"
            }
        });


        <?php 
            $sql_line_data = "SELECT COUNT(*) AS week1,
                (SELECT COUNT(*) ct_bookings WHERE YEARWEEK(booking_date_time) = YEARWEEK(DATE_SUB(CURRENT_DATE(), INTERVAL 1 WEEK)) AND MONTH(booking_date_time) = MONTH(CURRENT_DATE())) AS week2,
                (SELECT COUNT(*) FROM ct_bookings WHERE YEARWEEK(booking_date_time) = YEARWEEK(DATE_SUB(CURRENT_DATE(), INTERVAL 2 WEEK)) AND MONTH(booking_date_time) = MONTH(CURRENT_DATE())) AS week3, 
                (SELECT COUNT(*) FROM ct_bookings b3 WHERE YEARWEEK(booking_date_time) = YEARWEEK(DATE_SUB(CURRENT_DATE(), INTERVAL 3 WEEK)) AND MONTH(booking_date_time) = MONTH(CURRENT_DATE())) 
                 AS week4
                 FROM ct_bookings WHERE YEARWEEK(booking_date_time) = YEARWEEK(CURRENT_DATE()) AND MONTH(booking_date_time) = MONTH(CURRENT_DATE())";
            
            $res_line_data = mysqli_query($conn, $sql_line_data);
            $row_line_data = mysqli_fetch_object($res_line_data);
        ?>
        
        Highcharts.chart('curve_chart', {
            chart: {
                type: 'area'
            },
            title: {
                text: 'No of Appointments'
            },
            subtitle: {
                text: 'Last 4 Weeks'
            },
            xAxis: {
                allowDecimals: false,
                categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                labels: {
                    formatter: function () {
                        if (this.value > 999) {
                            return this.value / 1000 + ' K';
                        } else {
                            return this.value;
                        }
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'No. of Appointments'
                },
                labels: {
                    formatter: function () {
                        if (this.value > 999) {
                            return this.value / 1000 + ' K';
                        } else {
                            return this.value;
                        }
                    }
                }
            },
            tooltip: {
                pointFormat: '<b>{point.y:,.0f}</b> Appointments'
            },
            plotOptions: {
                area: {
                    //stacking: 'normal',
                    lineColor: '#666666',
                    lineWidth: 1,
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            exporting: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Appointments',
                data: [<?php echo $row_line_data->week1; ?>, <?php echo $row_line_data->week2; ?>, <?php echo $row_line_data->week3; ?>, <?php echo $row_line_data->week4; ?>]
            }]
        });
    });
</script>