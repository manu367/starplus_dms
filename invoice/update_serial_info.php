<?php
////// Function ID ///////
$fun_id = array(96);
require_once("../config/config.php");
////// Access check //////
// check only for locations
if($_SESSION["userid"])
{
	if(!access_check_master($link1, $fun_id, $_SESSION["userid"]))
	{
		exit;
	}
}
//////////////////////////
if($_POST){	
	if ($_POST['save']=='Save'){
		if($_POST['serialno']){	
			if($_POST['change_type']=="PARTCODE" || $_POST['change_type']=="OWNER" || $_POST['change_type']=="STOCK TYPE"){
				/// transcation parameter /////////////////////
				mysqli_autocommit($link1, false);
				$flag = true;
				$err_msg = "";
				////// get serial no. latest details
				$res_qry = mysqli_query($link1,"SELECT * FROM billing_imei_data WHERE imei1 ='".$_POST['serialno']."' ORDER BY id DESC");
				if(mysqli_num_rows($res_qry)>0){
					$row_qry = mysqli_fetch_assoc($res_qry);
					$partcode = $row_qry["prod_code"];
					$fromloc = $row_qry["from_location"];
					$toloc = $row_qry["to_location"];
					$owncode = $row_qry["owner_code"];
					$stocktype = $row_qry["stock_type"];
					$impdate = $row_qry["import_date"];
					$old_val = "";
					////// check change type
					if($_POST['change_type']=="PARTCODE"){
						$partcode = $_POST['change_val'];
						$old_val = $row_qry["prod_code"];
					}
					else if($_POST['change_type']=="OWNER"){
						$owncode = $_POST['change_val'];
						$old_val = $row_qry["owner_code"];
					}
					else if($_POST['change_type']=="STOCK TYPE"){
						$stocktype = $_POST['change_val'];
						$old_val = $row_qry["stock_type"];
					}else{
						//// nothing to do
					}
					//// insert into serial update data table
					$res_qry2 = mysqli_query($link1,"INSERT INTO update_serial_data SET serial_no='".$_POST['serialno']."', change_type='".$_POST['change_type']."', old_value='".$old_val."',  new_value='".$_POST['change_val']."', remark='".$_POST['remark']."', update_by = '".$_SESSION["userid"]."', update_date='".$datetime."', update_ip = '".$ip."'");
					if (!$res_qry2) {
						$flag = false;
						$err_msg = "ER2 : ".mysqli_error($link1).".";
					}
					$refid = mysqli_insert_id($link1);
					$refno = "SERDATAUPD-".$refid;
					//// insert into billing imei data
					$res_qry1 = mysqli_query($link1,"INSERT INTO billing_imei_data SET from_location='".$fromloc."', to_location='".$toloc."', owner_code='".$owncode."', prod_code='".$partcode."', doc_no = '".$refno."', imei1='".$_POST['serialno']."', stock_type = '".$stocktype."', transaction_date='".$today."', import_date='".$impdate."'");
					if (!$res_qry1) {
						$flag = false;
						$err_msg = "ER1 : ".mysqli_error($link1).".";
					}
					///// update serial no.
					if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM serial_stock WHERE serial_no='".$_POST['serialno']."'"))>0){
						$res_upd_ss = mysqli_query($link1,"UPDATE serial_stock SET location_code='".$owncode."', prod_code='".$partcode."', rem_qty='1', stock_type='".$stocktype."', ref_no='".$refno."', ref_date='".$today."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."' WHERE serial_no='".$_POST['serialno']."'");
						if (!$res_upd_ss) {
							$flag1 = false;
							$err_msg = "ER3 : ".mysqli_error($link1).".";
						}
					}else{
						$res_inst_ss = mysqli_query($link1,"INSERT INTO serial_stock SET location_code='".$owncode."', prod_code='".$partcode."', serial_no='".$_POST['serialno']."',inside_qty='1', rem_qty='1', stock_type='".$stocktype."', ref_no='".$refno."', ref_date='".$today."',import_date='".$impdate."', update_by='".$_SESSION["userid"]."', update_date='".$datetime."'");
						if (!$res_inst_ss) {
							$flag1 = false;
							$err_msg = "ER4 : ".mysqli_error($link1).".";
						}
					}								  
					////// insert in activity table////
					$flag=dailyActivity($_SESSION['userid'],$refno,"SERIAL DATA","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
					/////// check both master and data query executed //////////////////////////////////////////
					if ($flag) {
						mysqli_commit($link1);
						$msg = "Serial data is successfully updated w.r.f. ".$_POST['serialno'];
					} else {			
						mysqli_rollback($link1);
						$msg = "Request could not be processed. Please try again.".$err_msg;
					}
				}else{
					$msg = "Request could not be processed. Please serial no. not found in db.";
				}	
			}else{
				$msg = "Request could not be processed. Please select change type.";
			}
		}else{
			$msg = "Request could not be processed. Please enter serial no.";
		}
		//mysqli_close($link1);
		///// move to parent page
		header("location:update_serial_info.php?msg=".$msg."".$pagenav);
		exit;
	}
}
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
$(document).ready(function(){
	$("#frm3").validate({
		submitHandler: function (form) {
			let text = "Are you sure? you want to update this data";
			if (confirm(text) == true) {
				if (!this.wasSent) {
					this.wasSent = true;
					$(':submit', form).val('Please wait...')
							.attr('disabled', 'disabled')
							.addClass('disabled');
					spinner.show();
					form.submit();
				} else {
					return false;
				}
			}else{
				return false;
			}
		}
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
    		<h2 align="center"><i class="fa fa-barcode fa-lg"></i> Update Serial No. Info</h2><br/>
   	  		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
              <?php if($_REQUEST['msg']){?><br>
              <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
              <?php }?>
          		<form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post">
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Enter Serial Number<span class="red_small">*</span></label>
              				<div class="col-md-6">
                				<input type="search" class="form-control alphanumeric required" placeholder=""  name="serial_no" value="<?=$_REQUEST["serial_no"]?>">
              				</div>
              				<div class="col-md-2">
           						<input type="submit" class="btn <?=$btncolor?>" name="SHOW" id="" value="SHOW">       
           	  				</div>
            			</div>
          			</div>
        		</form>
		  		<?php
				if($_SESSION["userid"]=="admin"){
        		if($_POST['SHOW']){
		  			$sql = mysqli_query($link1,"SELECT * FROM billing_imei_data WHERE imei1 ='".$_POST['serial_no']."' ORDER BY id asc");
		  			$num_rows = mysqli_num_rows($sql);
		  			if($num_rows > 0){
					?>
                    <form name="frm3" id="frm3" method="post" onsubmit="openModal()">
            		<div class="alert alert-success alert-dismissible" role="alert">
                		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    		<span aria-hidden="true">&times;</span>
                  		</button>
                		<strong>Success <i class="fa fa-check-circle fa-lg"></i></strong>&nbsp;&nbsp;Entered serial number details are showing below.
            		</div>
		  			<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
       		  			<thead>
           		  			<tr class="<?=$tableheadcolor?>" >  
                    			<th>From Location</th>
                    			<th>To Location</th>
					  			<th>Product Code</th>
                                <th><?=$imeitag?></th>
                                <th>Ref. / Invoice No.</th>
                                <th>Ref. / Invoice Date</th>
                                <th>Ref. Type</th>
                                <th>Stock Type</th>
           		  			</tr>
       		  			</thead>
       		  			<tbody>
				  		<?php
						$k = 1;
                    	while($row = mysqli_fetch_assoc($sql)){
							////get document details
							$doc_date = "";
							$doc_type = "";
							$doc1 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date,type FROM billing_master where challan_no='".$row['doc_no']."'"));
							$doc_date = $doc1['entry_date'];
							if($doc1['type']=="CORPORATE"){ $doc_type="Sale Invoice Against PO";}else if($doc1['type']=="RETAIL"){ $doc_type="Sale Invoice";}else if($doc1['type']=="GRN"){ $doc_type="GRN";}else if($doc1['type']=="LP"){ $doc_type="Local Purchase";}else if($doc1['type']=="STN"){ $doc_type="STN";}else if($doc1['type']=="STN Distribution"){ $doc_type="STN";}else{$doc_type="";}
							if($doc1['entry_date']==""){
								$doc2 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM vendor_order_master where po_no='".$row['doc_no']."'"));
								$doc_date = $doc2['entry_date'];
								$doc_type="GRN";
								if($doc2['entry_date']==""){
									$doc3 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM opening_stock_master where doc_no='".$row['doc_no']."'"));
									$doc_date = $doc3['entry_date'];
									$doc_type="Opening Stock";
									if($doc3['entry_date']==""){
										$doc4 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM stockconvert_master where doc_no='".$row['doc_no']."'"));
										$doc_date = $doc4['entry_date'];
										$doc_type="Stock Convert";
										if($doc4['entry_date']==""){
											$expl_id = explode("-",$row['doc_no']);
											$doc5 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT DATE(update_date) as entry_date FROM update_serial_data where id='".$expl_id[1]."'"));
											$doc_type="Serial Data Update";
											$doc_date = $doc5['entry_date'];
										}
									}
								}
							}
						
                    	?>
                      		<tr <?php if($k==$num_rows){ echo "class='bg-warning'";}?>>
                          		<td><?php $fromLocation = str_replace("~",",",getLocationDetails($row['from_location'],"name,city,state",$link1)); if($fromLocation==""){ $fromLocation = str_replace("~",",",getVendorDetails($row['from_location'],"name,city,state",$link1));} echo $fromLocation.",".$row['from_location'];?></td>
                                <td>
								<?php  
								$billto=getLocationDetails($row['to_location'],"name,city,state",$link1);
                                $explodeval=explode("~",$billto);
                                if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($row['to_location'],"customername,city,state",$link1);}
                                echo str_replace("~",",",$toparty).",".$row['to_location'];
                                ?></td>
                                <td><?php echo $row['prod_code'];?></td>
                                <td><?php echo $row['imei1'];?></td>
                                <td><?php echo $row['doc_no'];?></td>
                                <td><?php echo $doc_date; //if($doc2['entry_date']){ echo $doc2['entry_date'];}else{ echo $doc1['entry_date'];}?></td>
                                <td><?=$doc_type?></td>
                                <td><?php echo $row['stock_type'];?></td>
                            </tr>
                  			<?php 
								$k++;
                    		}
							?>
                  		</tbody>  
			  		</table> 
                    <br/>
                    <br/>
                    <div class="form-group">
							<div class="col-md-12"><label class="col-md-4 control-label">Change Type<span class="red_small">*</span></label>
					 			<div class="col-md-4"> 
					  				<select name="change_type" id="change_type" class="form-control required" required>
					  					<option value="">--Please Select--</option>
                                        <option value="PARTCODE">PART CODE</option>
					  					<option value="OWNER">OWNER CODE</option>
                                        <option value="STOCK TYPE">STOCK TYPE (OK/DAMAGE)</option>
					  				</select>
				   				</div>
				 			</div>
			  			</div> 
                        <br/>
                        <br/>
                        <div class="form-group">
							<div class="col-md-12"><label class="col-md-4 control-label">Change Value<span class="red_small">*</span></label>
					 			<div class="col-md-4"> 
					  				<input type="text" name="change_val" id="change_val" class="form-control required alphanumric" required maxlength="20" autocomplete="off"/>
				   				</div>
				 			</div>
			  			</div> 
                        <br/>
                        <br/>
			 			<div class="form-group">
							<div class="col-md-12"><label class="col-md-4 control-label">Remark<span class="red_small">*</span></label>
				  				<div class="col-md-4"> 
				   					<textarea class="form-control required"  name="remark" id="remark" required style="resize:vertical"></textarea>
				  				</div>
							</div>
			   			</div>
                        
                        <br/>
                        <br/>
                        
                        <div class="form-group">
							<div class="col-md-12"><label class="col-md-4 control-label">&nbsp;</label>
				  				<div class="col-md-4">
                                	<input name="serialno" id="serialno" type="hidden" value="<?=$_POST['serial_no'];?>"/> 
				   					<input type="submit" class="btn<?=$btncolor?>" name="save" id="save" value="Save" <?php if($_POST['save']=='Save'){?>disabled<?php }?>>
				  				</div>
							</div>
			   			</div>
                        <div class="form-group">
							<div class="col-md-12 alert-danger">
                            	<i><strong>NOTE:</strong> Please follow the below instructions while updating serial no. details</i><br/><br/>
                                1. PART CODE Change -  this must be system generated like P00001/P00002/P00003.....<br/>
                                2. OWNER CODE Change -  this must be system generated like EABRHR001/EABRHR002/EABRUP001.....<br/>
                                3. STOCK TYPE Change -  this must be either OK or DAMAGE.
							</div>
			   			</div>
                    </form>          
			  		<?php
					}
					else{
					?>
				   	<div class="alert alert-danger alert-dismissible" role="alert">
                    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        <strong>Alert <i class="fa fa-exclamation fa-lg"></i></strong>&nbsp;&nbsp;Entered serial number does not exist in database.
        			</div>
					<?php
                	}
				}
				}else{
				?>
					<div class="alert alert-danger alert-dismissible" role="alert">
                    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        <strong>Alert <i class="fa fa-exclamation fa-lg"></i></strong>&nbsp;&nbsp;You are not authorized..
        			</div>
				<?php }
				?>
   	  			</div>
			</div>
		</div>
	</div>
</body>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</html>
