<?phpinclude(dirname(__FILE__) . '/header.php');include(dirname(__FILE__) . '/user_session_check.php');include(dirname(dirname(__FILE__)) . '/objects/class_booking.php');$setting = new cleanto_setting();$setting->conn = $conn;$booking = new cleanto_booking();$booking->conn = $conn;$gettimeformat = $setting->get_option('ct_time_format'); /* CHECK FOR VC AND PARKING STATUS */$global_vc_status = $setting->get_option('ct_vc_status');$global_p_status = $setting->get_option('ct_p_status'); /* CHECK FOR VC AND PARKING STATUS END */?><style>    #html5-watermark {        display:none !important;    }    .invoice_row:nth-child(1) .delete-row{        display: none;    }</style><div id="cta-profile" class="panel tab-content">    <div class="panel panel-default">        <div class="panel-heading">            <h1 class="panel-title">Create Invoice</h1>        </div>    </div>    <div class="panel-body">        <div class="ct-admin-profile-details tab-content col-md-12 col-sm-12 col-lg-12 col-xs-12">            <!-- right side common menu for service -->            <div id="personal-info-tab" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-pane fade active in"><?php$order_id = (int) trim($_GET['order_id']);$sql_order = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) AS customer_name, u.user_email, u.phone, s.title FROM ct_bookings b JOIN ct_services s ON b.service_id = s.id JOIN ct_users u ON b.client_id = u.id WHERE b.order_id='{$order_id}'";$res_order = mysqli_query($conn, $sql_order);if (mysqli_num_rows($res_order) > 0) {    $row_order = mysqli_fetch_object($res_order);    // if already present in ct_proforma_invoice table    $sql_pi = "SELECT * FROM ct_proforma_invoice WHERE order_id='{$order_id}'";    $res_order = mysqli_query($conn, $sql_pi);    if (mysqli_num_rows($res_order) > 0) {        header('Location: view_invoice.php?order_id=' . $order_id);        exit(0);    } else {        ?>                        <div class="row">                            <div class="col-md-6">                                <div class="panel panel-default">                                    <div class="panel-heading">                                        <h3 class="panel-title"><i class="fa fa-calendar"></i> Appointment Details</h3>                                    </div>                                    <table class="table">                                        <tbody>                                            <tr>                                                <td>Appointment ID</td>                                                <td><?php echo $row_order->order_id; ?></td>                                            </tr>                                            <tr>                                                <td>Order Date</td>                                                <td><?php echo $row_order->booking_date_time; ?></td>                                            </tr>                                            <tr>                                                <td>Status</td>                                                <?php                                                if ($row_order->booking_status == 'CO') {                                                    $status = 'Completed';                                                } else if ($row_order->booking_status == 'R') {                                                    $status = 'Rejected';                                                } else if ($row_order->booking_status == 'C') {                                                    $status = 'Confirmed';                                                } else if ($row_order->booking_status == 'CS') {                                                    $status = 'Cancelled by Staff';                                                } else if ($row_order->booking_status == 'MN') {                                                    $status = 'No Show';                                                } else if ($row_order->booking_status == 'CO') {                                                    $status = 'Completed';                                                } else if ($row_order->booking_status == 'CC') {                                                    $status = 'Cancelled by Customer';                                                } else if ($row_order->booking_status == 'A') {                                                    $status = 'Active';                                                }                                                ?>                                                 <td><?php echo $status; ?></td>                                            </tr>                                                <?php                                                $sql_addons2 = "SELECT ba.*, sa.addon_service_name FROM ct_booking_addons ba JOIN ct_services_addon sa ON ba.addons_service_id = sa.id WHERE ba.order_id='{$order_id}'";                                                $res_addons2 = mysqli_query($conn, $sql_addons2);                                                ?>                                            <tr>                                                <td>Service</td>                                                <td><?php echo $row_order->title; ?></td>                                            </tr>                                            <tr>                                                <td>Add Ons</td>                                                <td>                                                <?php while ($row_addons3 = mysqli_fetch_object($res_addons2)) { ?>                                                    <?php echo $row_addons3->addon_service_name; ?>,                                                 <?php } ?>                                                </td>                                            </tr>                                        </tbody>                                    </table>                                </div>                            </div>                            <div class="col-md-6">                                <div class="panel panel-default">                                    <div class="panel-heading">                                        <h3 class="panel-title"><i class="fa fa-briefcase"></i> Extra Requirements Details</h3>                                    </div>                                    <?php                                        $sql_exr = "SELECT * FROM ct_booking_extra_requirements WHERE order_id='{$order_id}'";                                        $res_exr = mysqli_query($conn, $sql_exr);                                    ?>                                        <?php if (mysqli_num_rows($res_exr) > 0) { ?>                                    <?php $row_exr = mysqli_fetch_object($res_exr); ?>                                    <table class="table">                                        <tbody>                                            <tr>                                                <td>Extra Requirements</td>                                                <td><?php echo $row_exr->requirements; ?></td>                                            </tr>                                            <tr>                                                <td>Approved</td>                                                <td><?php echo ($row_exr->approved == 1) ? "YES" : "NO"; ?></td>                                            </tr>                                            <tr>                                                <td>Rejected</td>                                                <td><?php echo ($row_exr->rejected == 1) ? "YES" : "NO"; ?></td>                                            </tr>                                            <tr>                                                <?php                                                $sql_exr_img = "SELECT * FROM ct_booking_extra_requirement_images WHERE order_id='{$order_id}'";                                                $res_exr_img = mysqli_query($conn, $sql_exr_img);                                                ?>                                                <?php if (mysqli_num_rows($res_exr_img) > 0) { ?>                                                    <td>Image</td>            <?php while ($row_exr_img = mysqli_fetch_object($res_exr_img)) { ?>                                                        <td>                                                            <a href="<?php echo $row_exr_img->image; ?>" class="html5lightbox" data-width="520" data-height="480" data-group="mygroup" data-thumbnail="<?php echo $row_exr_img->image; ?>">                                                                <img src="<?php echo $row_exr_img->image; ?>" width="100" height="100" alt="Extra Image"/>                                                            </a>                                                        </td>                                                    <?php } ?>                                                <?php } ?>                                            </tr>                                        </tbody>                                    </table>                                    <?php } else {?>                                        <p class="text-center">No data available</p>                                    <?php } ?>                                </div>                            </div>                        </div>                        <div class="row">                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                <form id="invoice_form" name="invoice_form" method="post" action="save_invoice.php">                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>"/>                                    <?php                                    $sql_addons = "SELECT ba.*, sa.addon_service_name FROM ct_booking_addons ba JOIN ct_services_addon sa ON ba.addons_service_id = sa.id WHERE ba.order_id='{$order_id}'";                                    $res_addons = mysqli_query($conn, $sql_addons);                                    $count = mysqli_num_rows($res_addons);                                    $num = 1;                                    ?>                                    <table class="">                                        <tbody>        <?php while ($row_addons = mysqli_fetch_object($res_addons)) { ?>                                                <tr class="sub_services">                                                    <td><b><?php echo $row_addons->addon_service_name; ?></b></td>                                                </tr>                                                <tr class="invoice_pricing_list">                                                    <td class="price_dtls" style="position: relative;">                                                        <div class="form-group fl w100 invoice_row">                                                            <div class="row-cont" style="position:relative;float: left">                                                                <input type="hidden" name="service_id[]" value="<?php echo $row_addons->service_id; ?>"/>                                                                <input type="hidden" name="sub_service_id[]" value="<?php echo $row_addons->addons_service_id; ?>"/>                                                                <div class="cta-col2-1 ct-w-50 mb-6" style="margin-right: 20px;">                                                                    <label for="city">Item Desc</label>                                                                    <input type="text" class="form-control addon_item_desc" name="addon_item_desc[]" id="addon_item_desc_<?php echo $num; ?>" required=""/>                                                                </div>                                                                <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">                                                                    <label for="city">Item Code</label>                                                                    <input type="text" class="form-control addon_item_code number_only" name="addon_item_code[]" id="addon_item_code_<?php echo $num; ?>" required="" maxlength="6"/>                                                                </div>                                                                <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">                                                                    <label for="city">Price (Rs)</label>                                                                    <input class="form-control addon_price valid decimal service_price number_only" name="addon_price[]" id="addon_price_<?php echo $num; ?>" type="text" autocomplete="off" required="" maxlength="5"/>                                                                </div>                                                                <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">                                                                    <label for="city">Quantity</label>                                                                    <input class="form-control addon_qty valid number_only" name="addon_qty[]" id="addon_qty_<?php echo $num; ?>" type="text" autocomplete="off" required="" maxlength="3" value="1"/>                                                                </div>                                                                <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">                                                                    <label for="city">Total</label>                                                                    <input class="form-control addon_qty_total valid number_only" name="addon_qty_total[]" id="addon_qty_total_<?php echo $num; ?>" type="text" autocomplete="off" required="" maxlength="6" readonly/>                                                                </div>                                                                <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">                                                                    <label for="city">Discount (%)</label>                                                                    <input type="text" class="form-control addon_discount number_only" name="addon_discount[]" id="addon_discount_<?php echo $num; ?>" required="" maxlength="3" max="100"/>                                                                </div>                                                                <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">                                                                    <label for="city">GST (%)</label>                                                                    <select class="form-control addon_tax_type" name="addon_tax_type[]" id="addon_tax_type_<?php echo $num; ?>" required="">                                                                        <option value="">Select GST Percent</option>                                                                        <?php                                                                        $sql_tax_type = "SELECT * FROM ct_gst_percent";                                                                        $res_tax_type = mysqli_query($conn, $sql_tax_type);                                                                        ?>                                                                        <?php if (mysqli_num_rows($res_tax_type) > 0) { ?>                                                                            <?php while ($row_tax_type = mysqli_fetch_object($res_tax_type)) { ?>                                                                                <option value="<?php echo $row_tax_type->id; ?>"><?php echo $row_tax_type->percent; ?></option>                                                                            <?php } ?>                                                                        <?php } ?>                                                                    </select>                                                                </div>                                                                <div class="cta-col1-1 ct-w-50 mb-6 "style="">                                                                    <label for="state">Amount (Rs)</label>                                                                    <input class="form-control addon_total valid decimal number_only" name="addon_total[]" id="addon_total_<?php echo $num; ?>" type="text" autocomplete="off" required="" readonly/>                                                                </div>                                                                <a href="javascript:void(0);" style="position: absolute;top: 20px;right: 15px;" class="btn btn-danger pull-right delete-row" id=""><i class="fa fa-minus"></i></a>                                                            </div>                                                        </div>                                                        <a href="javascript:void(0);" style="position: absolute;top: 20px;right: 15px;" class="btn btn-success pull-right add-row" id=""><i class="fa fa-plus"></i></a>                                                    </td>                                                </tr>                                            <?php } ?>                                        </tbody>                                    </table>                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 default_coupon">                                        <?php                                         $sql_cpn = "SELECT * FROM ct_default_coupon WHERE id='1'";                                        $res_cpn = mysqli_query($conn, $sql_cpn);                                        $dflt_cpn_value = mysqli_fetch_object($res_cpn);                                        ?>                                        <div class="cpn_txt" style="font-weight: bold; float: right;">Promocode : <?php echo $dflt_cpn_value->value; ?> %</div>                                    </div>                                    <div class="form-group cb">                                        <button type="submit" data-id="<?php echo $_SESSION['ct_adminid']; ?>" class="btn btn-success ct-btn-width" id="submit" style="margin-top: 10px;"><?php echo $label_language_values['save']; ?></button>                                    </div>                                </form>                            </div>                        </div>    <?php } ?><?php } else { ?>    <?php    header('Location: calendar.php');    exit(0);    ?><?php } ?>            </div>        </div> <!-- end personal infomation -->    </div></div><?php include(dirname(__FILE__) . '/footer.php'); ?><script type="text/javascript" src="<?php echo SITE_URL; ?>assets/html5lightbox/html5lightbox.js"></script><script>    $(document).ready(function () {        $(".add-row").click(function () {            var data = $(this).closest('.price_dtls').find('.invoice_row').html();            $(this).closest('.price_dtls').append(data);        });        // Find and remove selected table rows        $("body").on("click", ".delete-row", function () {            $(this).closest('.row-cont').remove();        });        //Restrict number input only using class number_only in the input field        $('input.number_only').keyup(function (e) {            if (/\D/g.test(this.value)) {                // Filter non-digits from input value.                this.value = this.value.replace(/\D/g, '');            }        });        // calculate price with quantity        $("body").on('change', '.addon_price', function(event) {            var rate = $(this).closest(".row-cont").find('.addon_price').val();            var qty = $(this).closest(".row-cont").find('.addon_qty').val();            var qty_total = $(this).closest(".row-cont").find('.addon_qty_total').val();            var dscnt = $(this).closest(".row-cont").find('.addon_discount').val();            var gst_prcnt_id = $(this).closest(".row-cont").find(".addon_tax_type option:selected").val();            var gst_prcnt_val = $(this).closest(".row-cont").find(".addon_tax_type option:selected").text();            var total_amount = $(this).closest(".row-cont").find('.addon_qty_total').val();                        var amount = 0;            if (rate != '' && qty != '' && total_amount == '') {                var total_amount_qty = calculate_qty(rate, qty);                $(this).closest(".row-cont").find('.addon_qty_total').val(total_amount_qty);            } else if (rate != '' && qty != '' && qty_total != '' && dscnt != '' && gst_prcnt_id != '' && gst_prcnt_val != '') {                var total_amount_qty = calculate_qty(rate, qty);                $(this).closest(".row-cont").find('.addon_qty_total').val(total_amount_qty);                amount = calculate_gst(total_amount_qty, dscnt, gst_prcnt_val);                $(this).closest(".row-cont").find('.addon_total').val(amount);            }        });        $("body").on('change', '.addon_qty', function(event) {            var rate = $(this).closest(".row-cont").find('.addon_price').val();            var qty = $(this).closest(".row-cont").find('.addon_qty').val();            var qty_total = $(this).closest(".row-cont").find('.addon_qty_total').val();            var dscnt = $(this).closest(".row-cont").find('.addon_discount').val();            var gst_prcnt_id = $(this).closest(".row-cont").find(".addon_tax_type option:selected").val();            var gst_prcnt_val = $(this).closest(".row-cont").find(".addon_tax_type option:selected").text();            var total_amount = $(this).closest(".row-cont").find('.addon_qty_total').val();            var amount = 0;            if (rate != '' && qty != '' && total_amount == '') {                var total_amount_qty = calculate_qty(rate, qty);                $(this).closest(".row-cont").find('.addon_qty_total').val(total_amount_qty);            } else if (rate != '' && qty != '' && qty_total != '' && dscnt != '' && gst_prcnt_id != '' && gst_prcnt_val != '') {                var total_amount_qty = calculate_qty(rate, qty);                $(this).closest(".row-cont").find('.addon_qty_total').val(total_amount_qty);                amount = calculate_gst(total_amount_qty, dscnt, gst_prcnt_val);                $(this).closest(".row-cont").find('.addon_total').val(amount);            }        });                $("body").on('change','.addon_tax_type', function(event) {            var rate = $(this).closest(".row-cont").find('.addon_price').val();            var qty = $(this).closest(".row-cont").find('.addon_qty').val();            var qty_total = $(this).closest(".row-cont").find('.addon_qty_total').val();            var dscnt = $(this).closest(".row-cont").find('.addon_discount').val();            var gst_prcnt_id = $(this).closest(".row-cont").find(".addon_tax_type option:selected").val();            var gst_prcnt_val = $(this).closest(".row-cont").find(".addon_tax_type option:selected").text();            var amount = 0;            if (rate != '' && qty != '') {                var total_amount_qty = calculate_qty(rate, qty);                $(this).closest(".row-cont").find('.addon_qty_total').val(total_amount_qty);            }            if (rate != '' && qty != '' && qty_total != '' && dscnt != '' && gst_prcnt_id != '' && gst_prcnt_val != '') {                amount = calculate_gst(qty_total, dscnt, gst_prcnt_val);                $(this).closest(".row-cont").find('.addon_total').val(amount);            }        });        $("body").on('change', '.addon_discount', function(event) {            var rate = $(this).closest(".row-cont").find('.addon_price').val();            var qty = $(this).closest(".row-cont").find('.addon_qty').val();            var qty_total = $(this).closest(".row-cont").find('.addon_qty_total').val();            var dscnt = $(this).closest(".row-cont").find('.addon_discount').val();            var gst_prcnt_id = $(this).closest(".row-cont").find(".addon_tax_type option:selected").val();            var gst_prcnt_val = $(this).closest(".row-cont").find(".addon_tax_type option:selected").text();            var amount = 0;            if (rate != '' && qty != '') {                var total_amount_qty = calculate_qty(rate, qty);                $(this).closest(".row-cont").find('.addon_qty_total').val(total_amount_qty);            }            if (rate != '' && qty != '' && qty_total != '' && dscnt != '' && gst_prcnt_id != '' && gst_prcnt_val != '') {                amount = calculate_gst(qty_total, dscnt, gst_prcnt_val);                $(this).closest(".row-cont").find('.addon_total').val(amount);            }        });//        $('.form-control').each(function () { //            $(this).rules("add", {//                required: true,//                messages: {//                    required : 'This field is required',//                }//            });//        });//        $('input.number_only').keyup(function() {//            alert( "Handler for .keyup() called." );//        });    });        function calculate_gst(rate, discnt_prcnt, gst_prcnt) {        var total_amount = 0;        var discount = (rate * discnt_prcnt) / 100;        var gst_amount = ((rate - discount) / 100) * gst_prcnt;        total_amount += parseFloat(gst_amount + (rate - discount));        return total_amount;    }        function calculate_total_amount_change(rate, discnt_prcnt, gst_prcnt) {        var total_amount = 0;        var discount = (rate * discnt_prcnt) / 100;        var gst_amount = ((rate - discount) / 100) * gst_prcnt;        total_amount += parseFloat(gst_amount + (rate - discount));        return total_amount;    }        function calculate_qty(rate, qty) {        var total_amount = 0;        var amount = (rate * qty);        total_amount += parseFloat(amount);        return total_amount;    }</script>