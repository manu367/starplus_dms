<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);
////// all total var /////////
$tot_travel = 0.00;
$tot_food = 0.00;
$tot_lodge = 0.00;
/////// all info //////
$claim_info = mysqli_fetch_assoc(mysqli_query($link1, " SELECT * FROM hrms_claim_request WHERE claim_id = '".$id."' " ));
$claim_reqst_info = mysqli_fetch_assoc(mysqli_query($link1, " SELECT * FROM hrms_request_master WHERE request_no = '".$id."' " ));

@extract($_POST);
if($_POST['submit'] == "Apply"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	$full_file_name = "";
	$file_path = "";
	if($_FILES['approve_attach_doc']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['approve_attach_doc']['name'];
		$file_tmp =$_FILES['approve_attach_doc']['tmp_name'];
		$file_path="../doc_attach/approve_attach_doc/$today.$file_name";
		$full_file_name="$today.$file_name";
		move_uploaded_file($file_tmp,$file_path);	
	}	
	
	///////////////// update into request master ////////////////////////////////////
	$sql1 = " UPDATE hrms_request_master SET status = '".$action."', approve_date = '".$today."', approve_by = '".$_SESSION['userid']."', approve_remark = '".$approv_rmk."', approve_file_name = '".$full_file_name."', approve_file_path = '".$file_path."' WHERE request_no = '".$id."' ";
	
	$res1 = mysqli_query($link1, $sql1);
	/// check if query is execute or not//
	if(!$res1){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}
	
	///////////////// update into claim request ////////////////////////////////////
	$tot_aprv_amt = ($amt_travel + $amt_food + $amt_lodge);
	
	$sql2 = " UPDATE hrms_claim_request SET status = '".$action."', travel_approve = '".$amt_travel."', food_approve = '".$amt_food."', lodg_approve = '".$amt_lodge."', approved_amt = '".$tot_aprv_amt."' , total_amount = '".$amt_comp_req."', approved_date = '".$today."', approved_by = '".$_SESSION['userid']."' WHERE claim_id = '".$id."' ";
	
	$res2 = mysqli_query($link1, $sql2);
	/// check if query is execute or not//
	if(!$res2){
		$flag = false;
		$err_msg = "Error 2". mysqli_error($link1) . ".";
	}
	
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$id,"CLAIM",$action,$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Action performed successfully.";
		///// move to parent page
		header("location:approvals_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:approvals_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	} 
    mysqli_close($link1);
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
 <script src="../js/bootstrap-datepicker.js"></script>
 
 <script>
 $(document).ready(function(){
	$("#frm1").validate();
 });
 function check_travel_amt(val){
	 var t_amt = document.getElementById('amt_travel_req').value;
	 if((parseFloat(t_amt) >= parseFloat(val)) && (parseFloat(val) != '')){
		 
	 }else{
		 alert("Approved amount is less or equal to request amount.");
		 document.getElementById('amt_travel').value = '';
	 }
 }
 function check_food_amt(val){
	var t_amt = document.getElementById('amt_food_req').value;
	 if((parseFloat(t_amt) >= parseFloat(val)) && (parseFloat(val) != '')){
		 
	 }else{
		 alert("Approved amount is less or equal to request amount.");
		 document.getElementById('amt_food').value = '';
	 } 
 }
 function check_lodge_amt(val){
	 var t_amt = document.getElementById('amt_lodge_req').value;
	 if((parseFloat(t_amt) >= parseFloat(val)) && (parseFloat(val) != '')){
		 
	 }else{
		 alert("Approved amount is less or equal to request amount.");
		 document.getElementById('amt_lodge').value = '';
	 }
 }

 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-thumbs-up"></i> View Claim </h2>
      <h5 align="center"> ( Claim No. -  <?=$claim_info['claim_id']?> ) </h5><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                     
          <div class="form-group">
              <div class="col-md-12">
                  <label class="col-md-2 control-label">Employee Code <span style="color:#F00">*</span></label>
                  <div class="col-md-4">
                    <input type="text" name="emp_code" id="emp_code" class="form-control required" value="<?=$_SESSION['userid']?>" required readonly/>
                  </div>
                  <label class="col-md-2 control-label">Claim Type <span style="color:#F00">*</span></label>
                  <div class="col-md-4">
                  	<input type="text" name="claim_type" id="claim_type" class="form-control required" value="<?=$claim_info['claim_type']?>" required readonly/>
                  </div>
              </div>
          </div>   
               
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label"> Remark </label> 
                  <div class="col-md-10">
                    <textarea name="remark" id="remark" class="form-control addressfield"  readonly ><?=$claim_info['remark']?></textarea>
                  </div>    
              </div>  
          </div>
          
          <br>
          <div class="form-group">
          	  <div class="col-md-12" > 
              <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-bus fa-lg"></i>&nbsp;&nbsp;Travelling Details</div>
                 <div class="panel-body">
              
                  <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
                    <thead>
                      <tr class="<?=$tableheadcolor?>" >
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >Date</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >From</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >To</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >K.M.</th>
                        <th style="text-align:center;font-size:13px;width: 130px;" data-hide="phone,tablet" >Mode</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone,tablet" >Purpose</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >Amt</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
					  $travelling_details = mysqli_query($link1, " SELECT * FROM hrms_travelling_details WHERE claim_id = '".$claim_info['claim_id']."' ");
					  if($travelling_details != ""){
					  	while($row1 = mysqli_fetch_assoc($travelling_details)){
					  ?>
                      <tr>
                        <td style="text-align:center;"><?=dt_format($row1['tv_date']);?></td>
                        <td><?=$row1['from_place'];?></td>
                        <td><?=$row1['to_place'];?></td>
                        <td style="text-align:center;"><?=$row1['distance'];?></td>
                        <td><?=$row1['tv_mode'];?></td>
                        <td><?=$row1['purpose'];?></td>
                        <td style="text-align:right;"><?=currencyFormat($row1['amt']);?></td>
                      </tr>
                      <?php
					  	$tot_travel += $row1['amt'];
					  	}
					  ?>
                      <tr>
					  	<td colspan="3">
							 <span style="font-weight: 800; font-size:13px; padding-right: 10px;">Download Document : </span><?php if($claim_info['travelling_doc_path']) {?><a href='<?=$claim_info['travelling_doc_path']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?>
						</td>
                      	<td colspan="3" style="text-align:right; font-weight: 800; font-size:13px;">
                        	Total Travelling Amount : 
                        </td>    
                        <td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">    
                           <?=currencyFormat($tot_travel);?>
                        </td>
                      </tr>
                      <?php	
					  }
					  ?>
                    </tbody>
                  </table>
              </div>
          </div>
          </div><!--close panel body-->
          </div><!--close panel-->
          </div>
          
          <!--------------- Food details ------------------->
          <div class="form-group">
          	  <div class="col-md-12" > 
                  <div class="panel-group">
                <div class="panel panel-info table-responsive">
                    <div class="panel-heading heading1"><i class="fa fa-cutlery fa-lg"></i>&nbsp;&nbsp;Fooding/Others Details</div>
                     <div class="panel-body">
                  
                      <table width="100%" id="foodingitemsTable" class="table table-bordered table-hover">
                        <thead>
                          <tr class="<?=$tableheadcolor?>" >
                            <th style="text-align:center;font-size:13px;" data-hide="phone" > Date </th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" > Remark/details </th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" > Amt </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
						  $fooding_details = mysqli_query($link1, " SELECT * FROM hrms_fooding_details WHERE claim_id = '".$claim_info['claim_id']."' ");
						  if($fooding_details != ""){
							while($row2 = mysqli_fetch_assoc($fooding_details)){
						  ?>
                          <tr>
                            <td style="text-align:center;"><?=dt_format($row2['fd_date']);?></td>
                            <td><?=$row2['location']?></td>
                            <td style="text-align:right;"><?=currencyFormat($row2['amt']);?></td>
                          </tr>
                          <?php
						  	$tot_food += $row2['amt'];
							}
						  ?>
						  <tr>
						  	<td colspan="1">
								 <span style="font-weight: 800; font-size:13px; padding-right: 10px;" >Download Document : </span><?php if($claim_info['fooding_doc_path']) {?><a href='<?=$claim_info['fooding_doc_path']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?>
							</td>
							<td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">
								Total Fooding Amount : 
							</td>    
							<td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">    
							   <?=currencyFormat($tot_food);?>
							</td>
						  </tr>
						  <?php	
						  }
						  ?>
                        </tbody>
                      </table>
                  </div>
              </div>
              </div><!--close panel body-->
              </div><!--close panel-->
          </div>
          <!-------------- Food details end ---------------->
          
          <!--------------- Lodging details ------------------->
          <div class="form-group">
          	  <div class="col-md-12" > 
                  <div class="panel-group">
                <div class="panel panel-info table-responsive">
                    <div class="panel-heading heading1"><i class="fa fa-hotel fa-lg"></i>&nbsp;&nbsp;Lodging Details</div>
                     <div class="panel-body">
                  
                      <table width="100%" id="lodgingitemsTable" class="table table-bordered table-hover">
                        <thead>
                          <tr class="<?=$tableheadcolor?>" >
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Date</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Location</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Hotel Name</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Days</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Amt</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
						  $lodging_details = mysqli_query($link1, " SELECT * FROM hrms_lodging_details WHERE claim_id = '".$claim_info['claim_id']."' ");
						  if($lodging_details != ""){
							while($row3 = mysqli_fetch_assoc($lodging_details)){
						  ?>
                          <tr>
                            <td style="text-align:center;"><?=dt_format($row3['log_date']);?></td>
                            <td><?=$row3['location']?></td>
                            <td><?=$row3['hotel_name']?></td>
                            <td style="text-align:center;"><?=$row3['days']?></td>
                            <td style="text-align:right;"><?=currencyFormat($row3['amt']);?></td>
                          </tr>
                          <?php
						  	$tot_lodge += $row3['amt'];
							}
						  ?>
						  <tr>
						  	<td colspan="2">
								 <span style="font-weight: 800; font-size:13px; padding-right: 10px;" >Download Document : </span><?php if($claim_info['lodging_doc_path']) {?><a href='<?=$claim_info['lodging_doc_path']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?>
							</td>
							<td colspan="2" style="text-align:right; font-weight: 800; font-size:13px;">
								Total Lodging Amount : 
							</td>    
							<td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">    
							   <?=currencyFormat($tot_lodge);?>
							</td>
						  </tr>
						  <?php	
						  }
						  ?>
                        </tbody>
                      </table>
                  </div>
              </div>
              </div><!--close panel body-->
              </div><!--close panel-->
          </div>
          <!-------------- Lodging details end ---------------->
          
          <!--------------- Action details ------------------->
          <div class="form-group">
          	  <div class="col-md-12" > 
                  <div class="panel-group">
                    <div class="panel panel-info table-responsive">
                        <div class="panel-heading heading1"><i class="fa fa-gear fa-lg"></i>&nbsp;&nbsp;Action</div>
                        <div class="panel-body">
                      
                          <div class="form-group">
                              <div class="col-md-12">
                                  <label class="col-md-3 control-label">Requested Travelling Amount : </label>
                                  <div class="col-md-3">
                                    <label class="control-label"><?=currencyFormat($tot_travel);?></label>
                                    <input type="hidden" name="amt_travel_req" id="amt_travel_req" value="<?=currencyFormat($tot_travel);?>" >
                                  </div>
                                  <label class="col-md-3 control-label">Approved Travelling Amount <span style="color:#F00">*</span></label>
                                  <div class="col-md-3">
                                    <input type="text" name="amt_travel" id="amt_travel" class="form-control required"  required onBlur="check_travel_amt(this.value)" value="<?php if($claim_info['travel_approve'] != ""){ echo currencyFormat($claim_info['travel_approve']); }else {} ?>" />
                                  </div>
                              </div>
                          </div> 
                          
                          <div class="form-group">
                              <div class="col-md-12">
                                  <label class="col-md-3 control-label">Requested Fooding Amount : </label>
                                  <div class="col-md-3">
                                    <label class="control-label"><?=currencyFormat($tot_food);?></label>
                                    <input type="hidden" name="amt_food_req" id="amt_food_req" value="<?=currencyFormat($tot_food);?>" >
                                  </div>
                                  <label class="col-md-3 control-label">Approved Fooding Amount <span style="color:#F00">*</span></label>
                                  <div class="col-md-3">
                                    <input type="text" name="amt_food" id="amt_food" class="form-control required"  required onBlur="check_food_amt(this.value)" value="<?php if($claim_info['food_approve'] != ""){ echo currencyFormat($claim_info['food_approve']); }else {} ?>" />
                                  </div>
                              </div>
                          </div> 
                          
                          <div class="form-group">
                              <div class="col-md-12">
                                  <label class="col-md-3 control-label">Requested Lodging Amount : </label>
                                  <div class="col-md-3">
                                    <label class="control-label"><?=currencyFormat($tot_lodge);?></label>
                                    <input type="hidden" name="amt_lodge_req" id="amt_lodge_req" value="<?=currencyFormat($tot_lodge);?>" >
                                  </div>
                                  <label class="col-md-3 control-label">Approved Lodging Amount <span style="color:#F00">*</span></label>
                                  <div class="col-md-3">
                                    <input type="text" name="amt_lodge" id="amt_lodge" class="form-control required"  required onBlur="check_lodge_amt(this.value)" value="<?php if($claim_info['lodg_approve'] != ""){ echo currencyFormat($claim_info['lodg_approve']); }else {} ?>" />
                                  </div>
                              </div>
                          </div> 
                          
                          <div class="form-group">
                              <div class="col-md-12">
                                  <label class="col-md-3 control-label">Requested Total Amount : </label>
                                  <div class="col-md-3">
                                    <label class="control-label"><?=currencyFormat($tot_lodge + $tot_food + $tot_travel);?></label>
                                    <input type="hidden" name="amt_comp_req" id="amt_comp_req" value="<?=currencyFormat($tot_lodge + $tot_food + $tot_travel);?>" >
                                  </div>
                                  <label class="col-md-3 control-label">Approved Total Amount : </label>
                                  <div class="col-md-3">
                                  	<label class="control-label"><?php if($claim_info['approved_amt'] != ""){ echo currencyFormat($claim_info['approved_amt']); }else { echo "0.00"; } currencyFormat($tot_travel);?></label>
                                  </div>
                              </div>
                          </div> 
						  
						  <div class="form-group">
                              <div class="col-md-12" > 
                                  <label class="col-md-2 control-label"> Approve Remark </label> 
                                  <div class="col-md-10">
                                    <textarea class="form-control addressfield" id="approv_rmk" name="approv_rmk"><?=$claim_reqst_info['approve_remark']?></textarea>
                                  </div>    
                              </div>  
                          </div>
						  
						  <div class="form-group">
                              <div class="col-md-12" > 
                                  <label class="col-md-2 control-label"> Attachment </label> 
                                  <div class="col-md-10">
								  	<?php if($claim_info['status'] == "Pending"){ ?>
                                    <input type="file" name="approve_attach_doc" id="approve_attach_doc" class="form-control" />
									<?php }else{ ?>
									<?php if($claim_reqst_info['approve_file_path']) {?><a href='<?=$claim_reqst_info['approve_file_path']?>' target='_blank' title='download'><i class='fa fa-download ' title='Download Document'></i></a><?php }else{ echo "No added attachment."; } ?>
									<?php } ?>
                                  </div>    
                              </div>  
                          </div>
                          
                          <div class="form-group">
                              <div class="col-md-12" > 
                                  <label class="col-md-2 control-label"> Action <span style="color:#F00">*</span></label> 
                                  <div class="col-md-10">
                                    <select class="form-control required" name="action" id="action" required >
                                    	<option value="" <?php if($claim_info['status'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                                        <option value="Approved" <?php if($claim_info['status'] == "Approved"){ echo "selected"; } ?> > Approved </option>
                                        <option value="Reject" <?php if($claim_info['status'] == "Reject"){ echo "selected"; } ?> > Reject </option>
                                    </select>
                                  </div>    
                              </div>  
                          </div>
                                                    
                    	</div>
                    </div>
              	 </div><!--close panel body-->
              </div><!--close panel-->
          </div>
          <!-------------- Action details end ---------------->
          
          <br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
              	  <?php if($claim_info['approved_by'] == ""){ ?>
              	  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Apply"> Apply </button>  
				  <?php } ?>
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='approvals_list.php?<?=$pagenav?>'">
              </div>  
          </div>
          <br><br>
      </form>                      
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>