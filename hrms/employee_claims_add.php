<?php
require_once("../config/config.php");

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Save"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	//////// File uploades in diffrent filds ////////
	$trav_n = "";
	$trav_p = "";
	$food_n = "";
	$food_p = "";
	$lodge_n = "";
	$lodge_p = "";
	//////// upload file for travel //////
	if($_FILES['travel_doc']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['travel_doc']['name'];
		$file_tmp =$_FILES['travel_doc']['tmp_name'];
		$trav_n =$datetime.$file_name;
		$trav_p ="../doc_attach/CLAIM/claim_travel_doc/$trav_n";
		move_uploaded_file($file_tmp,$trav_p);	
	} // end of file upload	
	
	//////// upload file for food //////
	if($_FILES['fooding_doc']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['fooding_doc']['name'];
		$file_tmp =$_FILES['fooding_doc']['tmp_name'];
		$food_n =$datetime.$file_name;
		$food_p ="../doc_attach/CLAIM/claim_food_doc/$food_n";
		move_uploaded_file($file_tmp,$food_p);	
	} // end of file upload	
	
	//////// upload file for lodg //////
	if($_FILES['lodging_doc']["name"]!=''){	
	   //// upload doc into folder ////
		$file_name =$_FILES['lodging_doc']['name'];
		$file_tmp =$_FILES['lodging_doc']['tmp_name'];
		$lodge_n =$datetime.$file_name;
		$lodge_p ="../doc_attach/CLAIM/claim_lodg_doc/$lodge_n";
		move_uploaded_file($file_tmp,$lodge_p);	
	} // end of file upload	
	
	///////////////////////////////////////////////// 
	
	//// Create Claim no ////////////
	$claim_sql = mysqli_query($link1, "select max(temp_id) from hrms_claim_request");
	$claim_res = mysqli_fetch_array($claim_sql);
	$temp_id = $claim_res[0];
	/// make 6 digit padding
	$pad=str_pad(++$temp_id,6,"0",STR_PAD_LEFT);		
	$claim_no = "CL".$pad;
	
	////// find employee information /////
	$mngr = explode("~", getAnyDetails($_SESSION['userid'],'managerid,empname,designationid','loginid','hrms_employe_master',$link1));
	
	/////////////////insert into claim request////////////////////////////////////	
	$claim_request_res = mysqli_query($link1, " INSERT INTO hrms_claim_request SET temp_id = '".$temp_id."', emp_id = '".$_SESSION['userid']."', emp_name = '".$mngr[1]."', designation = '".$mngr[2]."', manager_id  = '".$mngr[0]."',  status = 'Pending', remark = '".$remark1."', claim_type = '".$claim_type1."', claim_id = '".$claim_no."', claim_date = '".$today."', travelling_doc_name = '".$trav_n."', travelling_doc_path = '".$trav_p."', fooding_doc_name = '".$food_n."', fooding_doc_path = '".$food_p."', lodging_doc_name = '".$lodge_n."', lodging_doc_path = '".$lodge_p."' ");
	
	/// check if query is execute or not//
	if(!$claim_request_res){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}
	
	////// Find insert ID ///// 
	$rid = mysqli_insert_id($link1);
	
	//////////// insert into request master ///////
	$request_res = mysqli_query($link1, " INSERT INTO hrms_request_master SET emp_id = '".$_SESSION['userid']."', name  = 'Travelling and Lodging Claim', type = 'TLC', update_date = '".$today."', mgr_id = '".$mngr[0]."', request_no = '".$claim_no."', status = 'Pending' ");
	
	/// check if query is execute or not//
	if(!$request_res){
		$flag = false;
		$err_msg = "Error 2". mysqli_error($link1) . ".";
	}
	
	////// travel information added /////////
	foreach($travel_date as $k=>$val){
		// checking row value should not be blank
		if($travel_date[$k]!='' && $travel_from[$k]!='' && $travel_to[$k]!='' && $travel_km[$k]!='' && $travel_mode[$k]!='' && $travel_purpose[$k]!='' && ($travel_amt[$k]!='' || $travel_amt[$k]!=0.00)) {
			////// Insert into hrms_travelling_details table /////////
			mysqli_query($link1, " INSERT INTO hrms_travelling_details SET claim_id = '".$claim_no."', tv_date = '".$travel_date[$k]."', from_place = '".$travel_from[$k]."', to_place = '".$travel_to[$k]."', distance = '".$travel_km[$k]."', tv_mode = '".$travel_mode[$k]."', purpose = '".$travel_purpose[$k]."', amt = '".$travel_amt[$k]."', status = 'R' " );
		}
	}
	
	////// fooding information added /////////
	foreach($fooding_date as $l=>$val){
		// checking row value should not be blank
		if($fooding_date[$l]!='' && $fooding_remark[$l]!='' && ($fooding_amt[$l]!='' || $fooding_amt[$l]!=0.00)) {
			////// Insert into hrms_fooding_details table /////////
			mysqli_query($link1, " INSERT INTO hrms_fooding_details SET claim_id = '".$claim_no."', fd_date = '".$fooding_date[$l]."', location = '".$fooding_remark[$l]."', amt = '".$fooding_amt[$l]."', status = 'R' " );
		}
	}
	
	////// lodging information added /////////
	foreach($lodging_date as $m=>$val){
		// checking row value should not be blank
		if($lodging_date[$m]!='' && $lodging_location[$m]!='' && $lodging_hotel[$m]!='' && ( $lodging_days[$m]!='' || $lodging_days[$m]!=0) && ( $lodging_amt[$m]!='' || $lodging_amt[$m]!=0.00)) {
			////// Insert into hrms_lodging_details table /////////
			mysqli_query($link1, " INSERT INTO hrms_lodging_details SET claim_id = '".$claim_no."', log_date = '".$lodging_date[$m]."', location = '".$lodging_location[$m]."', hotel_name = '".$lodging_hotel[$m]."', days = '".$lodging_days[$m]."',  amt = '".$lodging_amt[$m]."', status = 'R' " );
		}
	}
				
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$claim_no,"CLAIM","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Claim is successfully requested, with ref - ".$claim_no.".";
		///// move to parent page
		header("location:employee_claims_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:employee_claims_list.php?msg=".$msg."&sts=fail".$pagenav);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
    $(document).ready(function(){
		$("#frm1").validate();
	}); 
 	//////// Enter Number Only/////////
	function onlyNumbers(evt){  
		var e = event || evt; // for trans-browser compatibility
		var charCode = e.which || e.keyCode;  
		if (charCode > 31 && (charCode < 48 || charCode > 57) &&  charCode!=43){
			return false;
		}
		return true;
	}
 	$(document).ready(function() {
		$('#travel_date0').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var travelDate = "travel_date"+numi.value;
        var travelFrom = "travel_from["+numi.value+"]";
		var travelTo = "travel_to["+numi.value+"]";
		var travelKm = "travel_km["+numi.value+"]";
		var travelMode = "travel_mode["+numi.value+"]";
		var travelPurpose = "travel_purpose["+numi.value+"]";
		var travelAmt = "travel_amt["+numi.value+"]";

		var num = (document.getElementById("rowno").value -1)+ 2;
		/// check required fields ////
		if((document.getElementById(travelDate).value!="" && document.getElementById(travelFrom).value!="" && document.getElementById(travelTo).value!="" && document.getElementById(travelKm).value!="" && document.getElementById(travelMode).value!="" && document.getElementById(travelPurpose).value!="" && document.getElementById(travelAmt).value!="") || ($("#addr"+numi.value+":visible").length==0)){
		numi.value = num;
     	var r='<tr id="addr'+num+'"><td><div><input type="text" class="form-control span2" name="travel_date['+num+']"  id="travel_date'+num+'" ></div></td><td><input type="text" class="form-control" name="travel_from['+num+']" id="travel_from['+num+']"  autocomplete="off" ></td><td><input type="text" class="form-control" name="travel_to['+num+']" id="travel_to['+num+']" autocomplete="off" ></td><td><input type="text" class="form-control numbers" name="travel_km['+num+']" id="travel_km['+num+']" autocomplete="off" onKeyPress="return onlyNumbers(this.value);" ></td><td><select class="form-control" name="travel_mode['+num+']" id="travel_mode['+num+']" ><option value="">Please Select</option><option value="Bike">Bike</option><option value="Bus">Bus</option><option value="Car">Car</option><<?php if($_REQUEST['claim_type']=="OUTSTATION"){ ?>option value="Flight">Flight</option><?php } ?><option value="Metro">Metro</option><option value="Taxi">Taxi</option><?php if($_REQUEST['claim_type']=="OUTSTATION"){ ?><option value="Train">Train</option><?php } ?></select></td><td><input type="text" class="form-control numbers" name="travel_purpose['+num+']" id="travel_purpose['+num+']" autocomplete="off" ></td><td><div><span><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></span><span><input type="text" class="form-control" name="travel_amt['+num+']" id="travel_amt['+num+']" onKeyPress="return onlyNumbers(this.value);" /></span></div></td></tr>';
        $('#itemsTable1').append(r);  
	    $('#travel_date'+num).datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});        
	}
  });
});

////// delete product row///////////
function deleteRow(ind){  
     var id="addr"+ind; 
     var travelDate = "travel_date"+ind;
	 var travelFrom = "travel_from["+ind+"]";
	 var travelTo = "travel_to["+ind+"]";
	 var travelKm = "travel_km["+ind+"]";
	 var travelMode = "travel_mode["+ind+"]";
	 var travelPurpose = "travel_purpose["+ind+"]";
	 var travelAmt = "travel_amt["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(travelDate).value = "";
	document.getElementById(travelFrom).value = "";
	document.getElementById(travelTo).value = "";
	document.getElementById(travelKm).value = "";
	document.getElementById(travelMode).value = "";
	document.getElementById(travelPurpose).value = "";
	document.getElementById(travelAmt).value = "";
}

/////// fooding script start //////////
$(document).ready(function() {
		$('#fooding_date0').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	$(document).ready(function(){
     $("#fooding_add_row").click(function(){
		var numi = document.getElementById('foodingrowno');
		var foodingDate = "fooding_date"+numi.value;
        var foodingRemark = "fooding_remark["+numi.value+"]";
		var foodingAmt = "fooding_amt["+numi.value+"]";

		var num = (document.getElementById("foodingrowno").value -1)+ 2;
		/// check required fields ////
		if((document.getElementById(foodingDate).value!="" && document.getElementById(foodingRemark).value!="" && document.getElementById(foodingAmt).value!="") || ($("#foodingaddr"+numi.value+":visible").length==0)){
		numi.value = num;
     	var r='<tr id="foodingaddr'+num+'"><td><div><input type="text" class="form-control span2" name="fooding_date['+num+']"  id="fooding_date'+num+'" ></div></td><td><input type="text" class="form-control" name="fooding_remark['+num+']" id="fooding_remark['+num+']"  autocomplete="off" ></td><td><div><span><i class="fa fa-close fa-lg" onClick="foodingDeleteRow('+num+');"></i></span><span><input type="text" class="form-control" name="fooding_amt['+num+']" id="fooding_amt['+num+']" autocomplete="off" onKeyPress="return onlyNumbers(this.value);"  ></span></div></td></tr>';
        $('#foodingitemsTable').append(r);  
	    $('#fooding_date'+num).datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});        
	}
  });
});

////// delete product row///////////
function foodingDeleteRow(ind){  
     var id = "foodingaddr"+ind; 
     var foodingDate = "fooding_date"+ind;
	 var foodingRemark = "fooding_remark["+ind+"]";
	 var foodingAmt = "fooding_amt["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(foodingDate).value = "";
	document.getElementById(foodingRemark).value = "";
	document.getElementById(foodingAmt).value = "";
}
////// fooding script end /////////////

/////// lodging script start //////////
$(document).ready(function() {
		$('#lodging_date0').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	$(document).ready(function(){
     $("#lodging_add_row").click(function(){
		var numi = document.getElementById('lodgingrowno');
		var lodgingDate = "lodging_date"+numi.value;
        var lodgingLocation = "lodging_location["+numi.value+"]";
		var lodgingHotel = "lodging_hotel["+numi.value+"]";
		var lodgingDays = "lodging_days["+numi.value+"]";
		var lodgingAmt = "lodging_amt["+numi.value+"]";

		var num = (document.getElementById("lodgingrowno").value -1)+ 2;
		/// check required fields ////
		if((document.getElementById(lodgingDate).value!="" && document.getElementById(lodgingLocation).value!="" && document.getElementById(lodgingHotel).value!="" && document.getElementById(lodgingDays).value!="" && document.getElementById(lodgingAmt).value!="") || ($("#lodgingaddr"+numi.value+":visible").length==0)){
		numi.value = num;
     	var r='<tr id="lodgingaddr'+num+'"><td><div><input type="text" class="form-control span2" name="lodging_date['+num+']"  id="lodging_date'+num+'" ></div></td><td><input type="text" class="form-control" name="lodging_location['+num+']" id="lodging_location['+num+']"  autocomplete="off" ></td><td><input type="text" class="form-control" name="lodging_hotel['+num+']" id="lodging_hotel['+num+']"  autocomplete="off" ></td><td><input type="text" class="form-control" name="lodging_days['+num+']" required id="lodging_days['+num+']" onKeyPress="return onlyNumbers(this.value);" /></td><td><div><span><i class="fa fa-close fa-lg" onClick="lodgingDeleteRow('+num+');"></i></span><span><input type="text" class="form-control" name="lodging_amt['+num+']" id="lodging_amt['+num+']" autocomplete="off" onKeyPress="return onlyNumbers(this.value);"  ></span></div></td></tr>';
        $('#lodgingitemsTable').append(r);  
	    $('#lodging_date'+num).datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});        
	}
  });
});

////// delete product row///////////
function lodgingDeleteRow(ind){  
     var id = "lodgingaddr"+ind; 
     var lodgingDate = "lodging_date"+ind;
	 var lodgingLocation = "lodging_location["+ind+"]";
	 var lodgingHotel = "lodging_hotel["+ind+"]";
	 var lodgingDays = "lodging_days["+ind+"]";
	 var foodingAmt = "fooding_amt["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(lodgingDate).value = "";
	document.getElementById(lodgingLocation).value = "";
	document.getElementById(lodgingHotel).value = "";
	document.getElementById(lodgingDays).value = "";
	document.getElementById(foodingAmt).value = "";
}
////// lodging script end /////////////


 </script>
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-legal"></i> Add Claim </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
                     
          <div class="form-group">
              <div class="col-md-12">
                  <label class="col-md-2 control-label">Employee Code <span style="color:#F00">*</span></label>
                  <div class="col-md-4">
                    <input type="text" name="emp_code" id="emp_code" class="form-control required" value="<?=$_SESSION['userid']?>" required readonly/>
                  </div>
                  <label class="col-md-2 control-label">Claim Type <span style="color:#F00">*</span></label>
                  <div class="col-md-4">
                  	<select name="claim_type" id="claim_type" required class="form-control required"  onChange="document.frm1.submit();">
					  <option value=""<?php if($_REQUEST['claim_type']=="")echo "selected";?>> -- Please Select -- </option>
                      <option value="LOCAL"<?php if($_REQUEST['claim_type']=="LOCAL")echo "selected";?>> LOCAL </option>
                      <option value="OUTSTATION"<?php if($_REQUEST['claim_type']=="OUTSTATION")echo "selected";?>> OUTSTATION </option>
                    </select>
                  </div>
              </div>
          </div>   
               
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label"> Remark </label> 
                  <div class="col-md-10">
                    <textarea name="remark" id="remark" class="form-control addressfield" onBlur="document.frm1.submit();" ><?=$_REQUEST['remark']?></textarea>
                  </div>    
              </div>  
          </div>
		  </form>
		  <form name="frm2" id="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
          
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
                        <th data-hide="phone" style="font-size:13px;">Date</th>
                        <th data-hide="phone" style="font-size:13px">From</th>
                        <th data-hide="phone" style="font-size:13px">To</th>
                        <th data-hide="phone" style="font-size:13px">K.M.</th>
                        <th data-hide="phone,tablet" style="font-size:13px;width: 130px;">Mode</th>
                        <th data-hide="phone,tablet" style="font-size:13px">Purpose</th>
                        <th data-hide="phone" style="font-size:13px">Amt</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr id='addr0'>
                        <td>
                        	<div>
                                <input type="text" class="form-control span2 required" name="travel_date[0]"  id="travel_date0" required >
                            </div>
                        </td>
                        <td><input type="text" class="form-control required" name="travel_from[0]" id="travel_from[0]"  autocomplete="off" required ></td>
                        <td><input type="text" class="form-control required" name="travel_to[0]" id="travel_to[0]" autocomplete="off" required ></td>
                        <td><input type="text" class="form-control required" name="travel_km[0]" id="travel_km[0]" autocomplete="off" onKeyPress="return onlyNumbers(this.value);" required ></td>
                        <td>
                        	<select class="form-control required" name="travel_mode[0]" id="travel_mode[0]" required >
                            	<option value="">Please Select</option>
                                <option value="Bike">Bike</option>
                                <option value="Bus">Bus</option>
                                <option value="Car">Car</option>
                                <?php if($_REQUEST['claim_type']=="OUTSTATION"){ ?><option value="Flight">Flight</option> <?php } ?>
                                <option value="Metro">Metro</option>
                                <option value="Taxi">Taxi</option>
                                 <?php if($_REQUEST['claim_type']=="OUTSTATION"){ ?><option value="Train">Train</option> <?php } ?>
                      		</select>
                        </td>
                        <td><input type="text" class="form-control required" name="travel_purpose[0]" id="travel_purpose[0]" autocomplete="off" required ></td>
                        <td><input type="text" class="form-control required" name="travel_amt[0]" required id="travel_amt[0]" onKeyPress="return onlyNumbers(this.value);" value="0.00" required /></td>
                      </tr>
                    </tbody>
                    <tfoot id='productfooter' style="z-index:-9999;">
                      <tr class="0">
                        <td colspan="8" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
                        <input type="hidden" name="rowno" id="rowno" value="0"/></td>
                      </tr>
					  <tr>
                        <td colspan="3">
							<i class="fa fa-question-circle-o fa-2x"></i>&nbsp;
							<div style="margin-top: -30px;margin-left: 32px;">
								<label class="control-label"> Have you any attachment file? </label>
							</div>
                        </td>
						<td colspan="5" ><input type="file" name="travel_doc" id="travel_doc" class="form-control"  /></td>
                      </tr>
                    </tfoot>
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
                            <th data-hide="phone" style="font-size:13px;">Date</th>
                            <th data-hide="phone" style="font-size:13px">Remark/details</th>
                            <th data-hide="phone" style="font-size:13px">Amt</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr id='foodingaddr0'>
                            <td>
                                <div>
                                    <input type="text" class="form-control span2" name="fooding_date[0]"  id="fooding_date0" >
                                </div>
                            </td>
                            <td><input type="text" class="form-control" name="fooding_remark[0]" id="fooding_remark[0]"  autocomplete="off" ></td>
                            <td><input type="text" class="form-control" name="fooding_amt[0]" required id="fooding_amt[0]" onKeyPress="return onlyNumbers(this.value);" value="0.00" /></td>
                          </tr>
                        </tbody>
                        <tfoot id='productfooter' style="z-index:-9999;">
                          <tr class="0">
                            <td colspan="3" style="font-size:13px;"><a id="fooding_add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
                            <input type="hidden" name="foodingrowno" id="foodingrowno" value="0"/></td>
                          </tr>
						  <tr>
							<td colspan="1">
								<i class="fa fa-question-circle-o fa-2x"></i>&nbsp;
								<div style="margin-top: -30px;margin-left: 32px;">
									<label class="control-label"> Have you any attachment file? </label>
								</div>
							</td>
							<td colspan="2" ><input type="file" name="fooding_doc" id="fooding_doc" class="form-control"  /></td>
						  </tr>
                        </tfoot>
                      </table>
                  </div>
              </div>
              </div><!--close panel body-->
              </div><!--close panel-->
          </div>
          <!-------------- Food details end ---------------->
          
		  <?php if($_REQUEST['claim_type']=="OUTSTATION"){ ?>
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
                            <th data-hide="phone" style="font-size:13px;">Date</th>
                            <th data-hide="phone" style="font-size:13px">Location</th>
                            <th data-hide="phone" style="font-size:13px">Hotel Name</th>
                            <th data-hide="phone" style="font-size:13px">Days</th>
                            <th data-hide="phone" style="font-size:13px">Amt</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr id='lodgingaddr0'>
                            <td>
                                <div>
                                    <input type="text" class="form-control span2" name="lodging_date[0]"  id="lodging_date0" >
                                </div>
                            </td>
                            <td><input type="text" class="form-control" name="lodging_location[0]" id="lodging_location[0]"  autocomplete="off" ></td>
                            <td><input type="text" class="form-control" name="lodging_hotel[0]" id="lodging_hotel[0]"  autocomplete="off" ></td>
                            <td><input type="text" class="form-control" name="lodging_days[0]" required id="lodging_days[0]" onKeyPress="return onlyNumbers(this.value);" value="0" /></td>
                            <td><input type="text" class="form-control" name="lodging_amt[0]" required id="lodging_amt[0]" onKeyPress="return onlyNumbers(this.value);" value="0.00" /></td>
                          </tr>
                        </tbody>
                        <tfoot id='productfooter' style="z-index:-9999;">
                          <tr class="0">
                            <td colspan="5" style="font-size:13px;"><a id="lodging_add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
                            <input type="hidden" name="lodgingrowno" id="lodgingrowno" value="0"/></td>
                          </tr>
						  <tr>
							<td colspan="2">
								<i class="fa fa-question-circle-o fa-2x"></i>&nbsp;
								<div style="margin-top: -30px;margin-left: 32px;">
									<label class="control-label"> Have you any attachment file? </label>
								</div>
							</td>
							<td colspan="3" ><input type="file" name="lodging_doc" id="lodging_doc" class="form-control"  /></td>
						  </tr>
                        </tfoot>
                      </table>
                  </div>
              </div>
              </div><!--close panel body-->
              </div><!--close panel-->
          </div>
          <!-------------- Lodging details end ---------------->
          <?php } ?>
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Save"> Save </button>  
				  <input type="hidden" name="claim_type1" id="claim_type1" value="<?=$_REQUEST['claim_type']?>" />
				  <input type="hidden" name="remark1" id="remark1" value="<?=$_REQUEST['remark']?>" />
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onclick="window.location.href='employee_claims_list.php?<?=$pagenav?>'">
              </div>  
          </div>
         
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