<?php
include(dirname(__FILE__) . '/header.php');
include(dirname(__FILE__) . '/user_session_check.php');
$setting = new cleanto_setting();
$setting->conn = $conn;
$gettimeformat = $setting->get_option('ct_time_format'); /* CHECK FOR VC AND PARKING STATUS */$global_vc_status = $setting->get_option('ct_vc_status');
$global_p_status = $setting->get_option('ct_p_status'); /* CHECK FOR VC AND PARKING STATUS END */
?>

<div id="cta-profile" class="panel tab-content">
    <div class="panel-body">
        <div class="ct-admin-profile-details tab-content col-md-12 col-sm-12 col-lg-12 col-xs-12">
            <!-- right side common menu for service -->
            <div id="personal-info-tab" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 tab-pane fade active in">
                <?php
                $order_id = (int) trim($_GET['order_id']);
                $sql_order = "SELECT b.*, s.title FROM ct_bookings b JOIN ct_services s ON b.service_id = s.id WHERE b.order_id='{$order_id}'";
                $res_order = mysqli_query($conn, $sql_order);
                if (mysqli_num_rows($res_order) > 0) {
                    $row_order = mysqli_fetch_object($res_order);
                    // if already present in ct_proforma_invoice table
                    $sql_pi = "SELECT * FROM ct_proforma_invoice WHERE order_id='{$order_id}'";
                    $res_order = mysqli_query($conn, $sql_pi);
                    if (mysqli_num_rows($res_order) > 0) {
                        header('Location: view_invoice.php?order_id=' . $order_id);
                        exit(0);
                    } else {
                        ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h4 class="header4">Create Invoice</h4>
                            <form id="invoice_form" name="invoice_form" method="post" action="save_invoice.php">
                                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>"/>
                                <div class="">
                                    <label for="fullname">Booked Service</label> : <?php echo $row_order->title; ?>
                                </div>
                                <?php
                                $sql_addons = "SELECT ba.*, sa.addon_service_name FROM ct_booking_addons ba JOIN ct_services_addon sa ON ba.addons_service_id = sa.id WHERE ba.order_id='{$order_id}'";
                                $res_addons = mysqli_query($conn, $sql_addons);
								$count = mysqli_num_rows($res_addons);
                                ?>
                                <table class="" style="width: 100%">
                                    <tbody>
                                        <?php while ($row_addons = mysqli_fetch_object($res_addons)) { ?>
                                        <input type="hidden" name="service_id[]" value="<?php echo $row_addons->service_id; ?>"/>
                                        <tr>
                                            <input type="hidden" name="sub_service_id[]" value="<?php echo $row_addons->addons_service_id; ?>"/>
                                            <td><?php echo $row_addons->addon_service_name; ?></td>
                                            <td class="price_dtls">
                                                <div class="form-group fl w100">
                                                    <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                        <label for="city">Price</label>
                                                        <input class="form-control addon_price valid decimal service_price" name="addon_price[]" id="addon_price_<?php echo $row_addons->addons_service_id; ?>" type="text" autocomplete="off" required=""/>
                                                    </div>
                                                    <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                        <label for="city">Code Type</label>
                                                        <select class="form-control addon_code_type" name="addon_code_type[]" id="addon_code_type_<?php echo $row_addons->addons_service_id; ?>" required="">
                                                            <option value="">Select Tax Code</option>
                                                            <option value="sac">SAC</option>
                                                            <option value="hsn">HSN</option>
                                                        </select>
                                                    </div>
                                                    <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                        <label for="city">Item Code</label>
                                                        <input type="text" class="form-control addon_item_code number_only" name="addon_item_code[]" id="addon_item_code_<?php echo $row_addons->addons_service_id; ?>" required=""/>
                                                    </div>
                                                    <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                        <label for="city">Tax Type</label>
                                                        <select class="form-control addon_tax_type" name="addon_tax_type[]" id="addon_tax_type_<?php echo $row_addons->addons_service_id; ?>" required="">
                                                            <option value="">Select Tax Type</option>
                                                            <option value="cgst">CGST</option>
                                                            <option value="sgst">SGST</option>
                                                            <option value="igst">IGST</option>
                                                        </select>
                                                    </div>
                                                    <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                        <label for="city">Tax (%)</label>
                                                        <input type="text" class="form-control addon_tax_percent number_only" name="addon_tax_percent[]" id="addon_tax_percent_<?php echo $row_addons->addons_service_id; ?>" required="" maxlength="2"/>
                                                    </div>
                                                    <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                        <label for="state">GST</label>
                                                        <input class="form-control addon_gst valid decimal" name="addon_gst[]" id="addon_gst_<?php echo $row_addons->addons_service_id; ?>" type="text" autocomplete="off" required=""/>
                                                    </div>
                                                    <div class="cta-col1-1 ct-w-50 mb-6 "style="">
                                                        <label for="state">Total</label>
                                                        <input class="form-control addon_total valid decimal" name="addon_total[]" id="addon_total_<?php echo $row_addons->addons_service_id; ?>" type="text" autocomplete="off" required=""/>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
										<input type="hidden" name="total_count" id="total_count" class="total_count" value="<?php echo $count;?>">
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <?php
                                $sql_xtras = "SELECT * FROM ct_booking_extra_requirements WHERE order_id='{$order_id}'";
                                $res_xtras = mysqli_query($conn, $sql_xtras);
								$num_rows = mysqli_num_rows($res_xtras);
                                ?>
                                <?php if ($num_rows > 0) { ?>
                                    <?php while($row_xtras = mysqli_fetch_object($res_xtras)){ ?>
									<?php if($row_xtras->rejected == '0'){?>
                                    <table class="" style="width: 100%">
                                        <input type="hidden" name="extras_id" value="<?php echo $row_xtras->id; ?>"/>
                                        <tbody>
                                            <tr>
                                                <td>Extra Requirements</td>
                                                <td>
                                                    <div class="form-group fl w100">
                                                        <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                            <label for="city">Price</label>
                                                            <input class="form-control value_city valid decimal" name="extras_price" id="extra_addon_price_<?php echo $num_rows; ?>" type="text" autocomplete="off" required=""/>
                                                        </div>
                                                        <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                            <label for="state">Item Code Type</label>
                                                            <select class="form-control value_state valid" name="extras_item_code_type" id="extras_item_code_type_<?php echo $row_xtras->id; ?>" type="text" autocomplete="off" required="">
                                                                <option value="">Select code type</option>
                                                                <option value="hsn">HSN</option>
                                                                <option value="sac">SAC</option>
                                                            </select>
                                                        </div>
                                                        <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                            <label for="state">Item Code</label>
                                                            <input class="form-control value_state valid" name="extras_item_code" id="extras_item_code_<?php echo $num_rows; ?>" type="text" autocomplete="off" required=""/>
                                                        </div>
                                                        <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                            <label for="state">Tax Type</label>
                                                            <select class="form-control value_state valid" name="extras_tax_type" id="addon_tax_type_<?php echo $num_rows; ?>" type="text" autocomplete="off" required="">
                                                                <option value="">Select tax type</option>
                                                                <option value="cgst">CGST</option>
                                                                <option value="sgst">SGST</option>
                                                                <option value="igst">IGST</option>
                                                            </select>
                                                        </div>
                                                        <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                            <label for="state">Tax Percentage</label>
                                                            <input class="form-control value_state valid number_only_tax" name="extras_tax_prcnt" id="extra_addon_tax_prcent_<?php echo $num_rows; ?>" type="text" autocomplete="off" required=""/>
                                                        </div>
                                                        <div class="cta-col1-1 ct-w-50 mb-6" style="margin-right: 20px;">
                                                            <label for="state">GST</label>
                                                            <input class="form-control value_state valid decimal" name="extras_gst" id="extra_addon_gst_<?php echo $num_rows; ?>" type="text" autocomplete="off" required=""/>
                                                        </div>
                                                        <div class="cta-col1-1 ct-w-50 mb-6 "style="">
                                                            <label for="state">Total</label>
                                                            <input class="form-control value_state valid decimal" name="extras_total" id="extra_addon_total_<?php echo $num_rows; ?>" type="text" autocomplete="off" required=""/>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table> 
									<?php } ?>
									<?php $num_rows++;} ?>
									<input type="hidden" name="num_rows" id="num_rows" class="num_rows" value="<?php echo $num_rows;?>">
									<?php } ?>
                                <div class="form-group cb">
								
                                    <button type="submit" data-id="<?php echo $_SESSION['ct_adminid']; ?>" class="btn btn-success ct-btn-width" style="margin-left: 100px;" id="submit"><?php echo $label_language_values['save']; ?></button>
                                </div>
                            </form>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <?php
                    header('Location: calendar.php');
                    exit(0);
                    ?>
                <?php } ?>
            </div>
        </div> <!-- end personal infomation -->
    </div>
</div>
<?php include(dirname(__FILE__) . '/footer.php'); ?>
<script>
    $(document).ready(function() {
        //Restrict number input only using class number_only in the input field
        $('input.number_only').keyup(function (e) {
            if (/\D/g.test(this.value)) {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
				
            }else{
			var total = $('#total_count').val();
			for(var FieldCount=1;FieldCount<=total;FieldCount++)
			{
				var initial_amt=$('input[id^="addon_price_'+ FieldCount +'"]').val();			
				if( initial_amt=='' ) initial_amt='0';
				initial_amt = parseFloat(initial_amt);
				var gst_price = $('input[id^="addon_tax_percent_'+ FieldCount +'"]').val();
				if( gst_price=='' ) gst_price='0';
				gst_price = parseFloat(gst_price);
				
				if( initial_amt && gst_price)
				{
					var gst_amount = initial_amt*gst_price/100;
					var gross_amount = parseFloat(initial_amt)+parseFloat(gst_amount);
				}
				
				gst_amount 	= gst_amount.toFixed(2);;
				gross_amount = gross_amount.toFixed(2);
				
				$('input[id^="addon_gst_'+ FieldCount +'"]').val(gst_amount);
				$('input[id^="addon_total_'+ FieldCount +'"]').val(gross_amount);
			}	
			}
        });
		
		
		
		$('input.number_only_tax').keyup(function (e) {
            if (/\D/g.test(this.value)) {
                // Filter non-digits from input value.
                this.value = this.value.replace(/\D/g, '');
				
            }else{
			var num_rows = $('#num_rows').val();
			for(var NumCount=1;NumCount<=num_rows;NumCount++)
			{
				var initial_amt=$('input[id^="extra_addon_price_'+ NumCount +'"]').val();			
				if( initial_amt=='' ) initial_amt='0';
				initial_amt = parseFloat(initial_amt);
				var gst_price = $('input[id^="extra_addon_tax_prcent_'+ NumCount +'"]').val();
				if( gst_price=='' ) gst_price='0';
				gst_price = parseFloat(gst_price);
				
				if( initial_amt && gst_price)
				{
					var gst_amount = initial_amt*gst_price/100;
					var gross_amount = parseFloat(initial_amt)+parseFloat(gst_amount);
				}
				
				gst_amount 	= gst_amount.toFixed(2);;
				gross_amount = gross_amount.toFixed(2);
				
				$('input[id^="extra_addon_gst_'+ NumCount +'"]').val(gst_amount);
				$('input[id^="extra_addon_total_'+ NumCount +'"]').val(gross_amount);
			}	
			}
        });
		
        //Restrict decimal input only using class number_only in the input field
		var total_no = $('#total_count').val();
		for(var Count=1;Count<=total_no;Count++)
		{
		   $('input[id^="addon_price_'+ Count +'"]').keyup(function () {
				
				if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
					this.value = this.value.replace(/[^0-9\.]/g, '');	
				
				}
				$('input[id^="addon_tax_percent_'+ Count +'"]').val('');
				$('input[id^="addon_gst_'+ Count +'"]').val('');
				$('input[id^="addon_total_'+ Count +'"]').val('');
				
			});
		}
   
        $('.form-control').each(function () { 
            $(this).rules("add", {
                required: true,
                messages: {
                    required : 'This field is required',
                }
            });
        });
		
		
		$('input.number_only').keyup(function() {
			alert( "Handler for .keyup() called." );
		});
		
    });
	
	/*$("input.decimal").keyup(function () {
            if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
                this.value = this.value.replace(/[^0-9\.]/g, '');
            }
			var total = $('#total_count').val();
			for(var Count=1;Count<=total;Count++)
			{
				$('input[id^="addon_tax_percent_'+ Count +'"]').val('');
				$('input[id^="addon_gst_'+ Count +'"]').val('');
				$('input[id^="addon_total_'+ Count +'"]').val('');
			}	
        });
	*/
	
	


</script>