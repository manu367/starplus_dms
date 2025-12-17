<?php
////// Function ID ///////
$fun_id = array("a"=>array(82));
require_once("../config/config.php");
require_once("../includes/serial_logic_function.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
	$("#frm1").validate();
	$('#myTable').dataTable({
		paging: false,
		searching: false,
		ordering:  false,
		info: false
	});
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
		<div class="col-sm-9">
    		<h2 align="center"><i class="fa fa-barcode fa-lg"></i> Serial No. Details</h2><br/><br/>
  		  	<div class="form-group"  id="page-wrap" style="margin-left:10px;">
       			<form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post">
       		  		<div class="form-group">
           				<div class="col-md-12"><label class="col-md-2 control-label">Enter Serial Number<span class="red_small">*</span></label>
       			  			<div class="col-md-4">
                            	<select name="serial_for" class="form-control">
                                	<option value="BTR"<?php if($_REQUEST["serial_for"]=="BTR"){ echo "selected";}?>>All Charged Battery</option>
                                    <option value="LTHIBTR"<?php if($_REQUEST["serial_for"]=="LTHIBTR"){ echo "selected";}?>>Lithium Ion Battery</option>
                                    <option value="ERBTRCHR"<?php if($_REQUEST["serial_for"]=="ERBTRCHR"){ echo "selected";}?>>E-Rickshaw Battery Charger</option>
                                    <option value="SOL"<?php if($_REQUEST["serial_for"]=="SOL"){ echo "selected";}?>>All Solar Product</option>
                                    <option value="SOLELC"<?php if($_REQUEST["serial_for"]=="SOLELC"){ echo "selected";}?>>Solar Product Electronics</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                				<input type="search" class="form-control alphanumeric required" placeholder="Enter serial no. here"  name="serial_no" value="<?=$_REQUEST["serial_no"]?>">
       			  			</div>
              				<div class="col-md-2">
           						<input type="submit" class="btn <?=$btncolor?>" name="SHOW" id="" value="SHOW">       
   	  			  			</div>
            			</div>
          			</div>
        		</form>
		  		<?php
        		if($_POST['SHOW']){
					/////// split serial no. to get all info
					$serialinfo = checkSerialNoLogic(strtoupper($_POST['serial_no']),$_POST['serial_for']);
					//print_r($serialinfo);
					if(is_array($serialinfo)){						
				?>
                <div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success <i class="fa fa-check-circle fa-lg"></i></strong>&nbsp;&nbsp;Entered serial number details are showing below.
                </div>
                	<?php 
					////// if serial no. search for all charged battery
					if($_REQUEST["serial_for"]=="BTR"){ 
						///// battery layout
						$bt_layout = getBatteryLayout($serialinfo["battery_layout"]);
						///// get manufacturing date
						$bt_mfd = getMfDate($serialinfo["mf_date"])." ".getMfMonth($serialinfo["mf_month"])." ".getMfYear($serialinfo["mf_year"]);
						///// get model info
						$bt_model = explode("~",getModelName($serialinfo["model_code"],$_POST['serial_for'],$link1));
						////// get segment
						$bt_segment = getSegment($serialinfo["segment"],$link1);
						////// get charging site
						$bt_chgsite = getChargingSite($serialinfo["charging_site"],$link1);
						////// get warranty
						$bt_ws = getWarrantySlab($serialinfo["warranty_slab"]);
					?>
                    <table  width="70%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr class="<?=$tableheadcolor?>" >  
                                <td colspan="2" align="center">You are looking serial no. <?=$_REQUEST["serial_no"]?> for Battery</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="25%"><strong>Product Category</strong></td>
                                <td width="45%"><?=$bt_model[3]?></td>
                            </tr>
                            <tr>
                                <td><strong>Product Sub Category</strong></td>
                                <td><?=$bt_model[2]?></td>
                            </tr>
                            <tr>
                                <td><strong>Brand</strong></td>
                                <td><?=$bt_model[1]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model Code</strong></td>
                                <td><?=$serialinfo["model_code"]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model</strong></td>
                                <td><?=$bt_model[0]?></td>
                            </tr>
                            <tr>
                                <td><strong>Manufacturing Date</strong></td>
                                <td><?=$bt_mfd?></td>
                            </tr>
                            <tr>
                                <td><strong>Charging Site</strong></td>
                                <td><?=$bt_chgsite?></td>
                            </tr>
                            <tr>
                                <td><strong>Segment</strong></td>
                                <td><?=$bt_segment?></td>
                            </tr>
                            <tr>
                                <td><strong>Capacity</strong></td>
                                <td><?=$bt_model[4]?></td>
                            </tr>
                            <tr>
                                <td><strong>Layout</strong></td>
                                <td><?=$bt_layout?></td>
                            </tr>
                            <tr>
                                <td><strong>Warranty</strong></td>
                                <td><?=$bt_ws?></td>
                            </tr>
                            <tr>
                                <td><strong>Running Serial No.</strong></td>
                                <td><?=$serialinfo["battery_serial"]?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php 
					}
					////// if serial no. search for lithium ion battery
					else if($_REQUEST["serial_for"]=="LTHIBTR"){ 
						///// battery GPS
						$bt_gps = getGPSInfo($serialinfo["battery_gps"],$link1);
						///// get manufacturing date
						$bt_mfd = getMfDate($serialinfo["mf_date"])." ".getMfMonth($serialinfo["mf_month"])." ".getMfYear($serialinfo["mf_year"]);
						///// get model info
						$bt_model = explode("~",getModelName($serialinfo["model_code"],$_POST['serial_for'],$link1));
						////// get segment
						$bt_segment = getSegment($serialinfo["segment"],$link1);
						////// get software info
						$bt_sw = getSoftwareName($serialinfo["can_software"],$link1);
						////// get warranty
						$bt_ws = getWarrantySlabForLithIon($serialinfo["warranty_slab"]);
					?>
                    <table  width="70%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr class="<?=$tableheadcolor?>" >  
                                <td colspan="2" align="center">You are looking serial no. <?=$_REQUEST["serial_no"]?> for Battery</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="25%"><strong>Product Category</strong></td>
                                <td width="45%"><?=$bt_model[3]?></td>
                            </tr>
                            <tr>
                                <td><strong>Product Sub Category</strong></td>
                                <td><?=$bt_model[2]?></td>
                            </tr>
                            <tr>
                                <td><strong>Brand</strong></td>
                                <td><?=$bt_model[1]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model Code</strong></td>
                                <td><?=$serialinfo["model_code"]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model</strong></td>
                                <td><?=$bt_model[0]?></td>
                            </tr>
                            <tr>
                                <td><strong>Manufacturing Date</strong></td>
                                <td><?=$bt_mfd?></td>
                            </tr>
                            <tr>
                                <td><strong>Software Name</strong></td>
                                <td><?=$bt_sw?></td>
                            </tr>
                            <tr>
                                <td><strong>Segment</strong></td>
                                <td><?=$bt_segment?></td>
                            </tr>
                            <tr>
                                <td><strong>Capacity</strong></td>
                                <td><?=$bt_model[4]?></td>
                            </tr>
                            <tr>
                                <td><strong>GPS</strong></td>
                                <td><?=$bt_gps?></td>
                            </tr>
                            <tr>
                                <td><strong>Warranty</strong></td>
                                <td><?=$bt_ws?></td>
                            </tr>
                            <tr>
                                <td><strong>Running Serial No.</strong></td>
                                <td><?=$serialinfo["battery_serial"]?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php 
					}
					////// if serial no. search for E-Rickshaw battery charger
					else if($_REQUEST["serial_for"]=="ERBTRCHR"){ 
						///// line code
						$chg_linecode = getProductionLine($serialinfo["product_linecode"]);
						///// get manufacturing month year
						$chg_mfd = getMfMonth($serialinfo["mf_month"])." ".getMfYear2($serialinfo["mf_year"]);
						///// get model info
						$chg_model = explode("~",getModelName($serialinfo["model_code"],$_POST['serial_for'],$link1));
						////// get segment
						$chg_segment = getSegment($serialinfo["segment"],$link1);
						////// get vendor info
						$chg_vendor = getVendorName($serialinfo["vendor_code"],$link1);
						////// get warranty
						$chg_ws = getWarrantySlabForERickshawChg($serialinfo["warranty_slab"]);
						////// get engineer change note code
						$chg_engchgnote = getEngChangeNote($serialinfo["engg_chgcode"],$link1);
					?>
                    <table  width="70%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr class="<?=$tableheadcolor?>" >  
                                <td colspan="2" align="center">You are looking serial no. <?=$_REQUEST["serial_no"]?> for E-Rickshaw charger</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="25%"><strong>Product Category</strong></td>
                                <td width="45%"><?=$chg_model[3]?></td>
                            </tr>
                            <tr>
                                <td><strong>Product Sub Category</strong></td>
                                <td><?=$chg_model[2]?></td>
                            </tr>
                            <tr>
                                <td><strong>Brand</strong></td>
                                <td><?=$chg_model[1]?></td>
                            </tr>
                            <tr>
                                <td><strong>Product Code</strong></td>
                                <td><?=$serialinfo["product_code"]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model</strong></td>
                                <td><?=$chg_model[0]?></td>
                            </tr>
                            <tr>
                                <td><strong>Manufacturing Month</strong></td>
                                <td><?=$chg_mfd?></td>
                            </tr>
                            <tr>
                                <td><strong>Vendor Name</strong></td>
                                <td><?=$chg_vendor?></td>
                            </tr>
                            <tr>
                                <td><strong>Segment</strong></td>
                                <td><?=$chg_segment?></td>
                            </tr>
                            <tr>
                                <td><strong>Production Line</strong></td>
                                <td><?=$chg_linecode?></td>
                            </tr>
                            <tr>
                                <td><strong>Engineering Change Note</strong></td>
                                <td><?=$chg_engchgnote?></td>
                            </tr>
                            <tr>
                                <td><strong>Last 2 Digits of PO</strong></td>
                                <td><?=$serialinfo["last_2digitpo"]?></td>
                            </tr>
                            <tr>
                                <td><strong>Warranty</strong></td>
                                <td><?=$chg_ws?></td>
                            </tr>
                            <tr>
                                <td><strong>Running Serial No.</strong></td>
                                <td><?=$serialinfo["charger_serial"]?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } else if($_REQUEST["serial_for"]=="SOL"){
						///// get manufacturing month
						$sol_mfm = getMfMonth($serialinfo["mf_month"]);
						///// get manufacturing year
						$sol_mfy = getMfYear($serialinfo["mf_year"]);
						///// get model info
						$sol_model = explode("~",getModelName($serialinfo["product_code"]."~".$serialinfo["range_code"]."~".$serialinfo["capacity"],$_POST['serial_for'],$link1));
						////// get segment
						$sol_segment = getSegment($serialinfo["segment"],$link1);
						//// get vendor name
						$sol_vend = getVendorName($serialinfo["vendor_code"],$link1);
						////// get warranty
						$sol_ws = getWarrantySlab($serialinfo["warranty_slab"]);
					
					?>
                    <table  width="70%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr class="<?=$tableheadcolor?>" >  
                                <td colspan="2" align="center">You are looking serial no. <?=$_REQUEST["serial_no"]?> for Solar</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="25%"><strong>Product Category</strong></td>
                                <td width="45%"><?=$sol_model[3]?></td>
                            </tr>
                            <tr>
                                <td><strong>Product Sub Category</strong></td>
                                <td><?=$sol_model[2]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model Code</strong></td>
                                <td><?=$serialinfo["product_code"]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model Name</strong></td>
                                <td><?=$sol_model[0]?></td>
                            </tr>
                            <tr>
                                <td><strong>Manufacturing Month</strong></td>
                                <td><?=$sol_mfm?></td>
                            </tr>
                            <tr>
                                <td><strong>Manufacturing Year</strong></td>
                                <td><?=$sol_mfy?></td>
                            </tr>
                            <tr>
                                <td><strong>Vendor</strong></td>
                                <td><?=$sol_vend?></td>
                            </tr>
                            <tr>
                                <td><strong>Segment</strong></td>
                                <td><?=$sol_segment?></td>
                            </tr>
                            <tr>
                                <td><strong>Warranty</strong></td>
                                <td><?=$sol_ws?></td>
                            </tr>
                            <tr>
                                <td><strong>Voltage Rating</strong></td>
                                <td><?=$sol_model[4]?></td>
                            </tr>
                            <tr>
                                <td><strong>Running Serial No.</strong></td>
                                <td><?=$serialinfo["solar_serial"]?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } else if($_REQUEST["serial_for"]=="SOLELC"){
						///// get manufacturing month
						$sol_mfm = getMfMonth($serialinfo["mf_month"]);
						///// get manufacturing year
						$sol_mfy = getMfYear2($serialinfo["mf_year"]);
						///// get model info
						$sol_model = explode("~",getModelName($serialinfo["product_code"]."~".$serialinfo["range_code"]."~"."",$_POST['serial_for'],$link1));
						////// get segment
						$sol_segment = getSegment($serialinfo["segment"],$link1);
						//// get vendor name
						$sol_vend = getVendorName($serialinfo["vendor_code"],$link1);
						////// get warranty
						//$sol_ws = getWarrantySlab($serialinfo["warranty_slab"]);
					
					?>
                    <table  width="70%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr class="<?=$tableheadcolor?>" >  
                                <td colspan="2" align="center">You are looking serial no. <?=$_REQUEST["serial_no"]?> for Solar</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="25%"><strong>Product Category</strong></td>
                                <td width="45%"><?=$sol_model[3]?></td>
                            </tr>
                            <tr>
                                <td><strong>Product Sub Category</strong></td>
                                <td><?=$sol_model[2]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model Code</strong></td>
                                <td><?=$serialinfo["product_code"]?></td>
                            </tr>
                            <tr>
                                <td><strong>Model Name</strong></td>
                                <td><?=$sol_model[0]?></td>
                            </tr>
                            <tr>
                                <td><strong>Manufacturing Month</strong></td>
                                <td><?=$sol_mfm?></td>
                            </tr>
                            <tr>
                                <td><strong>Manufacturing Year</strong></td>
                                <td><?=$sol_mfy?></td>
                            </tr>
                            <tr>
                                <td><strong>Vendor</strong></td>
                                <td><?=$sol_vend?></td>
                            </tr>
                            <tr>
                                <td><strong>Segment</strong></td>
                                <td><?=$sol_segment?></td>
                            </tr>
                            <tr>
                                <td><strong>Last 2 Digits of PO</strong></td>
                                <td><?=$serialinfo["last_2digitpo"]?></td>
                            </tr>
                            <tr>
                                <td><strong>Running Serial No.</strong></td>
                                <td><?=$serialinfo["solar_serial"]?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php }else{?>
                    <table  width="70%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr class="<?=$tableheadcolor?>" >  
                                <td align="center">You are looking serial no. <?=$_REQUEST["serial_no"]?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>No record found.</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
					}
                    }
                    else{
                    ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Alert <i class="fa fa-exclamation fa-lg"></i></strong>&nbsp;&nbsp;Entered serial number does not exist in database.
                    </div>
					<?php
                	}
				}
				?>
   	  			</div>
			</div>
		</div>
	</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
