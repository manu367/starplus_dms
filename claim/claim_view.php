<?php
////// Function ID ///////
$fun_id = array("u"=>array(133,134),"a"=>array(105));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

$docid=base64_decode($_REQUEST['id']);
$pgnm=base64_decode($_REQUEST['pgnm']);
if($pgnm){ $backlink = $pgnm;}else{$backlink = "claim_list";}

$sql_master = "SELECT * FROM claim_master WHERE claim_no='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= siteTitle ?></title>
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<style type="text/css">
 .stepwizard-step p {
  margin-top: 10px;
}
.stepwizard-row {
  display: table-row;
}
.stepwizard {
  display: table;
  width: 100%;
  position: relative;
}
.stepwizard-step button[disabled] {
  opacity: 1 !important;
  filter: alpha(opacity=100) !important;
}
.stepwizard-row:before {
  top: 14px;
  bottom: 0;
  position: absolute;
  content: " ";
  width: 100%;
  height: 1px;
  background-color: #ccc;
  z-order: 0;
}
.stepwizard-step {
  display: table-cell;
  text-align: center;
  position: relative;
  /*width: 70px*/
}

.stepwizard-step p {
  position: absolute;
  width: 100%;
  text-align: center;
}
@-webkit-keyframes spinner-border{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes spinner-border{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}.spinner-border{display:inline-block;width:2rem;height:2rem;vertical-align:-.125em;border:.25em solid currentcolor;border-right-color:transparent;border-radius:50%;-webkit-animation:.75s linear infinite spinner-border;animation:.75s linear infinite spinner-border}.spinner-border-sm{width:1rem;height:1rem;border-width:.2em}@-webkit-keyframes spinner-grow{0%{-webkit-transform:scale(0);transform:scale(0)}50%{opacity:1;-webkit-transform:none;transform:none}}@keyframes spinner-grow{0%{-webkit-transform:scale(0);transform:scale(0)}50%{opacity:1;-webkit-transform:none;transform:none}}.spinner-grow{display:inline-block;width:2rem;height:2rem;vertical-align:-.125em;background-color:currentcolor;border-radius:50%;opacity:0;-webkit-animation:.75s linear infinite spinner-grow;animation:.75s linear infinite spinner-grow}.spinner-grow-sm{width:1rem;height:1rem}@media (prefers-reduced-motion:reduce){.spinner-border,.spinner-grow{-webkit-animation-duration:1.5s;animation-duration:1.5s}}
</style>

</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-clipboard"></i> Claim Details</h2><br/>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          			<form  name="form1" class="form-horizontal" action="" method="post" id="form1" enctype="multipart/form-data">
                    <div class="panel-group">
    				<div class="panel panel-info">
        				<div class="panel-heading">Party Information</div>
         				<div class="panel-body">
        				<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Party Name</label>
              					<div class="col-md-7">
               						<?=str_replace("~"," , ",getAnyDetails($row_master["party_id"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Claim Type</label>
              					<div class="col-md-7">
          							<?=$row_master["claim_type"]?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6 alert-warning"><label class="col-md-5">Claim No.</label>
              					<div class="col-md-7">
               						<?=$row_master["claim_no"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Status</label>
              					<div class="col-md-7">
          							<?=$row_master["status"]?>
      							</div>
            				</div>
          				</div>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Entry By</label>
              					<div class="col-md-7">
               						<?=getAnyDetails($row_master["entry_by"],"name","username","admin_users",$link1)." ".$row_master["entry_by"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Entry Date</label>
              					<div class="col-md-7">
          							<?=$row_master["entry_date"]." ".$row_master["entry_time"]?>
      							</div>
            				</div>
          				</div>
                        <?php if($row_master["update_by"]){?>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Edited By</label>
              					<div class="col-md-7">
               						<?=getAnyDetails($row_master["update_by"],"name","username","admin_users",$link1)." ".$row_master["update_by"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Edited Date</label>
              					<div class="col-md-7">
          							<?=$row_master["update_date"]." ".$row_master["update_time"]?>
      							</div>
            				</div>
          				</div>
                        <?php }?>
                        <div class="form-group">
            				<div class="col-md-6 alert-success"><label class="col-md-5">Requested Claim Amount</label>
              					<div class="col-md-7">
               						<?=$row_master["total_amount"]?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Claim Budget</label>
              					<div class="col-md-7">
          							<?php
									$str = "";
									$res_clm_bgt = mysqli_query($link1,"SELECT budget_year, budget_yearly FROM claim_budget WHERE party_id='".$row_master["party_id"]."' AND  claim_type='".$row_master["claim_type"]."' AND status='1'");
									while($row_clm_bgt = mysqli_fetch_assoc($res_clm_bgt)){
										if($str){
											$str .= ", ".$row_clm_bgt["budget_yearly"]." (<b>Year:</b> ".$row_clm_bgt["budget_year"].")";
										}else{
											$str = $row_clm_bgt["budget_yearly"]." (<b>Year:</b> ".$row_clm_bgt["budget_year"].")";
										}
									}
									echo $str;
									?>
      							</div>
            				</div>
          				</div>
                        <?php
						$res_apppend = mysqli_query($link1,"SELECT process_id,last_updatedate FROM approval_status_matrix WHERE ref_no='".$row_master["claim_no"]."' AND current_status='Pending'");
						$row_apppend = mysqli_fetch_assoc($res_apppend);
						if(mysqli_num_rows($res_apppend)>0){
						?>
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5">Approval Pending By</label>
              					<div class="col-md-7">
               						<?php
									$res_mappedusr = mysqli_query($link1,"SELECT GROUP_CONCAT(uid) AS mapusr FROM access_location WHERE location_id='".$row_master["party_id"]."' AND status='Y'");
									$row_mappedusr = mysqli_fetch_assoc($res_mappedusr);		
									/// get user details
									$res_user = mysqli_query($link1,"SELECT username,name FROM admin_users WHERE (app_steps_ids='".$row_apppend["process_id"]."' OR app_steps_ids LIKE '".$row_apppend["process_id"].",%' OR app_steps_ids LIKE '%,".$row_apppend["process_id"]."' OR app_steps_ids LIKE '%,".$row_apppend["process_id"].",%') AND status='active' AND username IN ('".str_replace(",","','",$row_mappedusr['mapusr'])."')");
									$row_user = mysqli_fetch_assoc($res_user);
									echo $row_user['name']." ".$row_user['username'];
									?>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4">Approval Pending Aging</label>
              					<div class="col-md-7">
          							<?php 
									
									echo $diff = timeDiff($row_apppend['last_updatedate'],$datetime);
									?>
      							</div>
            				</div>
          				</div>
                        <?php }?>
          				</div>
                        </div>
                        <div class="panel panel-info">
                            <div class="panel-heading">Claim Approval Hierarchy</div>
                            <div class="panel-body">
                                <div class="stepwizard">
                                  <div class="stepwizard-row">
                                    <?php 
                                    $i=1;
                                    $res3 = mysqli_query($link1,"SELECT process_id, current_status, approval_status FROM approval_status_matrix WHERE ref_no='".$row_master["claim_no"]."' AND process_name = 'CLAIM'");
                                    while($row3 = mysqli_fetch_assoc($res3)){
                                        $id_type = explode("~",getAnyDetails($row3['process_id'],"id_type,utype","process_id","approval_step_master",$link1));
                                        $res_mapusr = mysqli_query($link1,"SELECT a.username, a.name FROM admin_users a, access_location b WHERE a.username=b.uid AND a.utype='".$id_type[1]."' AND a.status='active' AND b.status='Y' AND b.location_id='".$row_master["party_id"]."'");
                                        $row_mapusr = mysqli_fetch_array($res_mapusr);
										$stss = "";
                                      	if($row3['approval_status']=="Approved"){ 
											$btnclass = "btn-success"; 
											$stss = "Approved"; 
											$icn = "<i class='fa fa-check-circle-o fa-lg'></i>";
										} else if($row3['approval_status']=="Rejected"){ 
											$btnclass = "btn-danger";
											$stss = "Rejected";
											$icn = "<i class='fa fa-ban fa-lg'></i>";
										} else if($row3['approval_status']=="Resend"){ 
											$btnclass = "btn-info";
											$stss = "Resend";
											$icn = "<i class='fa fa-reply fa-lg'></i>";
										} else if($row3['current_status']=="Pending"){ 
											$btnclass = "btn-warning"; 
											$stss = "Pending";
											$icn = "<span class='spinner-grow spinner-grow-sm' role='status' aria-hidden='true'></span>";
										} else{ 
											$btnclass = "btn-default"; 
											$stss = "Pending";
											$icn = "<i class='fa fa-clock-o fa-lg'></i>";
										}
                                    ?>
                                    <div class="stepwizard-step">
                                      <button type="button" class="btn <?=$btnclass?>" disabled><?=$icn?>&nbsp;<?=$i?>.&nbsp;<?=$stss?></button>
                                      <p><?=$row_mapusr[1].",".$id_type[0]?><br/><?php echo $row3['current_status'];?></p>
                                    </div>
                                    <?php $i++;}?>
                                    <br/>
                                    <br/>
                                    <br/>
                                    <br/>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-info table-responsive">
        				<div class="panel-heading">Claim Summary</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable2">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="20%">Subject</th>
                                            <th width="25%">Description</th>
                                            <th width="15%">Date</th>
                                            <th width="20%">Nos.</th>
                                            <th width="20%">Amount</th>
                                        </tr>
                    				</thead>
                                    <tbody>
                                    <?php
									$i=0;
									$sql_data = "SELECT * FROM claim_data WHERE claim_no='".$docid."'";
									$res_data = mysqli_query($link1,$sql_data);
									while($row_data = mysqli_fetch_assoc($res_data)){
									?>
                    				
                        				<tr id="addr_claim<?=$i?>">
                                            <td><input type="text" readonly class="form-control entername cp required" required name="claim_subject[<?=$i?>]" id="claim_subject[<?=$i?>]" value="<?=$row_data['claim_subject']?>"></td>
                                            <td><textarea readonly class="form-control addressfield cp required" required name="claim_desc[<?=$i?>]" id="claim_desc[<?=$i?>]" style="resize:vertical"><?=$row_data['claim_desc']?></textarea></td>
                                            <td><input readonly type="text" class="form-control required" required name="claim_date[<?=$i?>]" id="claim_date0" value="<?=$row_data['claim_date']?>"></td>
                                            <td><input readonly type="text" class="form-control required digits" required name="claim_qty[<?=$i?>]" id="claim_qty[<?=$i?>]" value="<?=$row_data['qty']?>"></td>
                                            <td><input readonly type="text" class="form-control required number" required name="claim_amt[<?=$i?>]" id="claim_amt[<?=$i?>]" value="<?=$row_data['amount']?>"></td>
                        				</tr>
                    				
                                    <?php
									$i++;
									}
									?>
                                    </tbody>
                				</table>   
                			</div>
                		</div>

                        
                        </div>
                        </div>
                        <?php 
						$res_bill = mysqli_query($link1,"SELECT * FROM billing_master WHERE ref_no='".$docid."'");
						if(mysqli_num_rows($res_bill)>0){
							$row_bill = mysqli_fetch_assoc($res_bill);
						?>
                        <div class="panel panel-info">
        				<div class="panel-heading">Invoice Summary</div>
         				<div class="panel-body">
                        <div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Plant Code <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<input type="text" name="plant_code" id="plant_code" value="<?=str_replace("~"," , ",getAnyDetails($row_bill["to_location"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>" class="form-control mastername" autocomplete="off" readonly/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Party GSTIN <span class="red_small">*</span></label>
              					<div class="col-md-7">
          							<input type="text" class="form-control alphanumeric required" required name="party_gstin" id="party_gstin" autocomplete="off" readonly value="<?=$row_bill['from_gst_no']?>"/>
      							</div>
            				</div>
          				</div>
		    			<div class="form-group">
            				<div class="col-md-6"><label class="col-md-5 control-label">Invoice No. <span class="red_small">*</span></label>
              					<div class="col-md-7">
               						<input type="text" name="invoice_no" id="invoice_no" class="form-control mastername" autocomplete="off" readonly value="<?=$row_bill['challan_no']?>"/>
              					</div>
            				</div>
            				<div class="col-md-6"><label class="col-md-4 control-label">Invoice Date <span class="red_small">*</span></label>
              					<div class="col-md-7">
                                     <input type="text" class="form-control span2" name="invoicedate" id="invoicedate" autocomplete="off" readonly value="<?=$row_bill['sale_date']?>"/>
                                </div>
            				</div>
          				</div>
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable21">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="25%">Product</th>
                                            <th width="8%">HSN</th>
                                            <th width="8%">Qty</th>
                                            <th width="10%">Price</th>
                                            <th width="10%">Discount</th>
                                            <th width="12%">Taxable Val</th>
                                            <th width="15%">GST</th>
                                            <th width="12%">Total</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                                    	<?php 
										$k=0;
										$sql_invdata = "SELECT * FROM billing_model_data WHERE challan_no='".$row_bill['challan_no']."'";
										$res_invdata = mysqli_query($link1,$sql_invdata);
										while($row_invdata = mysqli_fetch_assoc($res_invdata)){
											$proddet=explode("~",getProductDetails($row_invdata['prod_code'],"productname,productcode",$link1));
										?>
                        				<tr id="addr_inv<?=$k?>">
                                            <td><input type="text" class="form-control" name="prod_code[<?=$k?>]" id="prod_code[<?=$k?>]" autocomplete="off" value="<?=$proddet[0].", ".$proddet[1]?>" style="text-align:left" readonly></td>
                                            <td><input type="text" class="form-control" name="hsn[<?=$k?>]" id="hsn[<?=$k?>]" autocomplete="off" value="<?=$row_invdata['combo_code']?>" style="text-align:left" readonly></td>
                                            <td><input type="text" class="form-control digits" name="bill_qty[<?=$k?>]" id="bill_qty[<?=$k?>]" value="<?=$row_invdata['qty']?>" autocomplete="off" style="text-align:right" readonly></td>
                                            <td><input type="text" class="form-control number" name="price[<?=$k?>]" id="price[<?=$k?>]" value="<?=$row_invdata['price']?>" autocomplete="off" style="text-align:right" readonly></td>
                                            <td><input type="text" class="form-control number" name="rowdiscount[<?=$k?>]" id="rowdiscount[<?=$k?>]" value="<?=$row_invdata['discount']?>" autocomplete="off" style="text-align:right" readonly></td>
                                            <td><input type="text" class="form-control" name="rowsubtotal[<?=$k?>]" id="rowsubtotal[<?=$k?>]" autocomplete="off" value="<?=$row_invdata['value']-$row_invdata['discount']?>" style="text-align:right" readonly></td>
                                            <td><?php if($row_bill['from_state']==$row_bill['to_state']){ ?>
                                              	<div class="row">
                                                	<div class="col-md-4">
                                               		<input type="text" class="form-control" name="rowsgstper[<?=$k?>]" id="rowsgstper[<?=$k?>]" value="<?=$row_invdata['sgst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowsgstamount[<?=$k?>]" id="rowsgstamount[<?=$k?>]" value="<?=$row_invdata['sgst_amt']?>" readonly style="width:80px;text-align:right;padding: 4px">					
                                                </div>
                                                </div>
                                                
                                                
                                                <div class="row">
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstper[<?=$k?>]" id="rowcgstper[<?=$k?>]" value="<?=$row_invdata['cgst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                    </div>
                                                	<div class="col-md-4">
                                                    <input type="text" class="form-control" name="rowcgstamount[<?=$k?>]" id="rowcgstamount[<?=$k?>]" value="<?=$row_invdata['cgst_amt']?>" readonly style="width:80px;text-align:right;padding: 4px">
                                                    </div>
                                               </div>
                                                <?php }else{?>
                                                <div class="row">
                                                	<div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstper[<?=$k?>]" id="rowigstper[<?=$k?>]" value="<?=$row_invdata['igst_per']?>" readonly style="width:50px;text-align:right;padding: 4px">
                                                	</div>
                                                    <div class="col-md-4">
                                                	<input type="text" class="form-control" name="rowigstamount[<?=$k?>]" id="rowigstamount[<?=$k?>]" value="<?=$row_invdata['igst_amt']?>" readonly style="width:60px;text-align:right;padding: 4px">
                                                    </div>
                                                </div>
                                                <?php }?></td>
                                            <td><input type="text" class="form-control" name="total_val[<?=$k?>]" id="total_val[<?=$k?>]" value="<?=$row_invdata['totalvalue']?>" autocomplete="off" readonly  style="text-align:right"></td>
                        				</tr>
                                        <?php 
										$tot_qty += $row_invdata['qty'];
										
										}?>
                                        <tr>
                        				  <td colspan="2" align="right"><strong>Total</strong></td>
                        				  <td align="right"><strong><?=$tot_qty?></strong></td>
                        				  <td>&nbsp;</td>
                        				  <td align="right"><strong><?=$row_bill['discount_amt']?></strong></td>
                        				  <td align="right"><strong><?=$row_bill['basic_cost']-$row_bill['discount_amt']?></strong></td>
                        				  <td align="right"><strong><?=$row_bill['total_sgst_amt']+$row_bill['total_cgst_amt']+$row_bill['total_igst_amt']?></strong></td>
                        				  <td align="right"><strong><?=$row_bill['total_cost']?></strong></td>
                      				  	</tr>
                    				</tbody>
                				</table>   
               			  </div>
                	    </div>
                        </div>
                        </div>
                        <?php }?>
                        <?php
						$res_poapp = mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$docid."' ORDER BY id ASC")or die("ERR1".mysqli_error($link1)); 
						if(mysqli_num_rows($res_poapp)>0){
						?>
						<div class="panel panel-info table-responsive">
							<div class="panel-heading">Approval History</div>
							<div class="panel-body">
				
							<div class="form-group">
								<div class="col-sm-12">
									<table class="table table-bordered" width="100%" id="itemsTable3">
										<thead>
											<tr class="<?=$tableheadcolor?>" >
												<th width="20%">Action Date & Time</th>
												<th width="30%">Action Taken By</th>
												<th width="20%">Action</th>
												<th width="30%">Action Remark</th>
											</tr>
										</thead>
										<tbody>
											<?php
											while($row_poapp=mysqli_fetch_assoc($res_poapp)){
											?>
											  <tr>
												<td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
												<td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
												<td><?php echo $row_poapp['req_type']." ".$row_poapp['action_taken']?></td>
												<td><?php echo $row_poapp['action_remark']?></td>
											  </tr>
											<?php
											}
											?>  
										</tbody>
									</table>   
								</div>
							</div>
							</div> 
						</div>
						<?php 
						}
						?>
                   		<div class="panel panel-info table-responsive">
        				<div class="panel-heading">Supporting Document</div>
         				<div class="panel-body">
		    
			 			<div class="form-group">
                			<div class="col-sm-12">
                				<table class="table table-bordered" width="100%" id="itemsTable3">
                    				<thead>
                                        <tr class="<?=$tableheadcolor?>" >
                                            <th width="30%">Document Name</th>
                                            <th width="30%">Description</th>
                                            <th width="40%">Attachment</th>
                                        </tr>
                    				</thead>
                    				<tbody>
                                    	<?php
										$j=0;
										$sql_data_doc = "SELECT * FROM document_attachment WHERE ref_no='".$docid."'";
										$res_data_doc = mysqli_query($link1,$sql_data_doc);
										while($row_data_doc = mysqli_fetch_assoc($res_data_doc)){
										?>
                        				<tr id="addr_doc<?=$j?>">
                                            <td><input type="text" readonly class="form-control entername cp" name="document_name[<?=$j?>]"  id="document_name[<?=$j?>]" value="<?=$row_data_doc['document_name']?>"></td>
                                            <td><input type="text" readonly class="form-control entername cp" name="document_desc[<?=$j?>]"  id="document_desc[<?=$j?>]" value="<?=$row_data_doc['document_desc']?>"></td>
                                            <td><a href="<?=$row_data_doc['document_path']?>" target="_blank" class="btn <?=$btncolor?>" title="Attachment"><i class="fa fa-paperclip" title="Attachment"></i></a></td>
                        				</tr>
                                        <?php
										$j++;
										}
										?>
                    				</tbody>
                				</table>   
                			</div>
                		</div>
                		<div class="form-group">
            				<div class="col-md-12" align="center">
                                <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='<?=$backlink?>.php?<?=$pagenav?>'">
            				</div>
          				</div>
                        
                        </div>
                        </div>
                        </div>
    				</form>
      			</div>
    		</div>
  		</div>
	</div>
<?php
include("../includes/footer.php");
?>
</body>
</html>