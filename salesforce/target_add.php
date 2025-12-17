<?php
require_once("../config/config.php");

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Add"){
	if($target_task=="Dealer Visit" || $target_task=="Collection" || $target_task=="Feedback" || $target_task=="Sale Order" || $target_task=="BTL Activity" || $target_task=="Meeting" || $target_task=="Dealer Activeness"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$targetval = "";
	$uid_expl = explode("~",$user_id);
	/////// generate target no //////////
	$tar_qr = mysqli_fetch_array(mysqli_query($link1, "SELECT MAX(temp_no) AS tn FROM sf_target_master WHERE user_id='".$uid_expl[0]."'"));
	$temp_id = $tar_qr[0];
	/// make 3 digit padding
	$pad = str_pad(++$temp_id,3,"0",STR_PAD_LEFT);	
	$targetid = "TR/".$target_year.$target_month."/".strtoupper($uid_expl[0])."/".$pad;
	//// check target value is blank or not ////
	if($_REQUEST['target_val']==""){
		$targetval = "";
	}else{
		$targetval = $target_val;
	}
	// insert all details of target into target master table //
	$sql_master = "INSERT INTO sf_target_master SET target_no ='".$targetid."', temp_no = '".$temp_id."', month = '".$target_month."', year = '".$target_year."', target_type = '".$target_type."', period_type = 'Monthly', user_id = '".$uid_expl[0]."', emp_id = '".$uid_expl[1]."', entry_screen = 'FRONT', status = '".$status."', remark = 'Target Add', target_val = '".$targetval."', create_date  = '".$today."', create_by  = '".$_SESSION['userid']."'";
	$res_master =  mysqli_query($link1,$sql_master)or die("ER 1".mysqli_error($link1));
	/// check if query is execute or not//
	if(!$res_master){
		$flag = false;
		$err_msg = "Error 1". mysqli_error($link1) . ".";
	}
	
	//if(($_REQUEST['target_val']=="")||($_REQUEST['target_val']==0)||($_REQUEST['target_val']==0.00)){
		//if((count($prod_code)>0)&&(count($target_qty)>0)){
			//for($i=0; $i < count($prod_code); $i++){
				//if(($prod_code[$i]!="") && ($target_qty[$i]!="")){
					
					// insert all details of target_data into target data table //
					$sql_data = "INSERT INTO sf_target_data SET target_no = '".$targetid."', prod_code = '".$psubcat."', target_val = '".$targetval."', month = '".$target_month."', year = '".$target_year."',user_id ='".$uid_expl[0]."', emp_id = '".$uid_expl[1]."',task_name='".$target_task."',remark='".$remark."', status = '".$status."' ";
				
					$res_data =  mysqli_query($link1,$sql_data)or die("ER 2".mysqli_error($link1));
					/// check if query is execute or not//
					if(!$res_data){
						$flag = false;
						$err_msg = "Error 2". mysqli_error($link1) . ".";
					}
					
				//}
			//}
		//}
	//}				
					
	///// check all query are successfully executed
	if ($flag) {
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$targetid,"TARGET","ADD",$ip,$link1,"");
		
        mysqli_commit($link1);
        $msg = "Target is successfully added with ref. id. - ".$targetid;
		///// move to parent page
		header("location:target_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		///// move to parent page
		header("location:target_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	} 
    mysqli_close($link1);
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
 	$(document).ready(function(){
        $("#frm1").validate();
    });
	function makeSelect(){
	  $('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
	  });
	}
	function showTargetField(val){
		if(val == "Value"){
			document.getElementById("targ_val").style.display = "block";
			document.getElementById("targ_qty").style.display = "none";
			document.getElementById("target_val").value = "";
		}else{
			document.getElementById("targ_val").style.display = "none";
			document.getElementById("targ_qty").style.display = "block";
			document.getElementById("target_val").value = "";
		}
	}
	///// function for checking duplicate Product value
	function checkDuplicate(fldIndx1, enteredsno) {  
		document.getElementById("save").disabled = false;
		if (enteredsno != '') {
			var check2 = "prod_code[" + fldIndx1 + "]";
			var flag = 1;
			for (var i = 0; i <= fldIndx1; i++) {
				var check1 = "prod_code[" + i + "]";
				if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
					if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
						alert("Duplicate Product Selection.");
						document.getElementById("save").disabled = true;
						document.getElementById(check2).value = '';
						document.getElementById(check2).style.backgroundColor = "#F66";
						flag *= 0;
					}
					else {
						document.getElementById(check2).style.backgroundColor = "#FFFFFF";
						document.getElementById("save").disabled = false;
						flag *= 1;
						///do nothing
					}
				}
			}//// close for loop
			if (flag == 0) {
				return false;
			} else {
				return true;
			}
		}
	}
	$(document).ready(function(){
		$('#add_row').click(function(){
			var numi = document.getElementById('rowno');
			var prd = "prod_code["+numi.value+"]";
			var qty = "target_qty["+numi.value+"]";
			var num = (document.getElementById('rowno').value -1)+2;
			if((document.getElementById(prd).value!="" && document.getElementById(qty).value!="" && document.getElementById(qty).value!="0") || ($("#addr"+numi.value+":visible").length==0))
			{
				numi.value = num;
				
				var r = '<tr id="addr'+num+'"><td><select name="prod_code['+num+']" id="prod_code['+num+']" class="form-control selectpicker" data-live-search="true"  onChange="checkDuplicate('+num+',this.value);"><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active' order by productname ";	$check1=mysqli_query($link1,$model_query); while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option><?php }?></select></td><td><input style="width:70%;" type="text" class="form-control" name="target_qty['+num+']" id="target_qty['+num+']" /><span style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');" ></i></span></td></tr>';
				
		 		$('#itemsTable').append(r);
			    makeSelect();
		    }
		});
	});
	
	////// delete product row///////////
	function deleteRow(ind){  
		 var id="addr"+ind; 
		 var itemId="prod_code"+"["+ind+"]";
		 var reqQty="target_qty"+"["+ind+"]";
		 		
		 // hide fieldset \\
		document.getElementById(id).style.display="none";
		// Reset Value\\
		// Blank the Values \\
		document.getElementById(itemId).value="";
		document.getElementById(reqQty).value="";
	}
	
 </script>

</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-bullseye"></i> Add Target </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
      
           
          
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Year <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <select id="target_year" name="target_year" class="form-control required" required>
                                <option value="" <?php if($_REQUEST['emp_year']==""){ echo "selected";}?> > -- Please Select -- </option>
                                <?php
                                $currrent_year=date('Y');
                                $next_year=$currrent_year+1;
                                ?>
                                <option value="<?=$currrent_year?>" <?php if($_REQUEST['emp_year']==$currrent_year)echo "selected";?>><?=$currrent_year?></option>
                                <option value="<?=$next_year?>" <?php if($_REQUEST['emp_year']==$next_year)echo "selected";?>><?=$next_year?></option>
                            </select> 
                        </div>
                  </div>    
              </div>  
          </div>  
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Month <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <select id="target_month" name="target_month" class="form-control required" required>
                                <option value="" <?php if($_REQUEST['emp_month'] == "") { echo "selected" ;} ?> > -- Please Select -- </option>
                                <option value="01" <?php if($_REQUEST['emp_month']=='01')echo "selected";?>>JAN</option>
                                <option value="02" <?php if($_REQUEST['emp_month']=='02')echo "selected";?>>FEB</option>
                                <option value="03" <?php if($_REQUEST['emp_month']=='03')echo "selected";?>>MAR</option>
                                <option value="04" <?php if($_REQUEST['emp_month']=='04')echo "selected";?>>APR</option>
                                <option value="05" <?php if($_REQUEST['emp_month']=='05')echo "selected";?>>MAY</option>
                                <option value="06" <?php if($_REQUEST['emp_month']=='06')echo "selected";?>>JUN</option>
                                <option value="07" <?php if($_REQUEST['emp_month']=='07')echo "selected";?>>JUL</option>
                                <option value="08" <?php if($_REQUEST['emp_month']=='08')echo "selected";?>>AUG</option>
                                <option value="09" <?php if($_REQUEST['emp_month']=='09')echo "selected";?>>SEP</option>
                                <option value="10" <?php if($_REQUEST['emp_month']=='10')echo "selected";?>>OCT</option>
                                <option value="11" <?php if($_REQUEST['emp_month']=='11')echo "selected";?>>NOV</option>
                                <option value="12" <?php if($_REQUEST['emp_month']=='12')echo "selected";?>>DEC</option>	 
                            </select> 
                        </div>
                  </div>    
              </div>  
          </div> 
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Task Name<span class="red_small">*</span></label> 
                  <div class="col-md-6">
					  <select name="target_task" id="target_task" class="form-control required" required>
						  <option value="" <?php if($_REQUEST['target_task']==""){ echo "selected"; } ?> > -- Please Select -- </option>
						  <option value="Dealer Visit" <?php if($_REQUEST['target_task']=="Dealer Visit"){ echo "selected"; } ?> >Dealer Visit</option>
						  <option value="Collection" <?php if($_REQUEST['target_task']=="Collection"){ echo "selected"; } ?> >Collection</option>
                          <option value="Feedback" <?php if($_REQUEST['target_task']=="Feedback"){ echo "selected"; } ?> >Feedback</option>
                          <option value="Sale Order" <?php if($_REQUEST['target_task']=="Sale Order"){ echo "selected"; } ?> >Sale Order</option>
                          <option value="BTL Activity" <?php if($_REQUEST['target_task']=="BTL Activity"){ echo "selected"; } ?> >BTL Activity</option>
                          <option value="Meeting" <?php if($_REQUEST['target_task']=="Meeting"){ echo "selected"; } ?> >Meeting</option>
                          <option value="Dealer Activeness" <?php if($_REQUEST['target_task']=="Dealer Activeness"){ echo "selected"; } ?> >Dealer Activeness</option>
					  </select>
                  </div>    
              </div>  
          </div>
		  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Product Sub-category</label> 
                  <div class="col-md-6">
				  <select name="psubcat"  id= "psubcat" class="form-control">
				   <option value="">--Please Select--</option>
				  <?php
                      $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1' ORDER BY prod_sub_cat");
                      while($row_pcat=mysqli_fetch_array($pcat)){
                      ?>
                      <option value="<?=$row_pcat['prod_sub_cat']?>" <?php if($_REQUEST['psubcat'] == $row_pcat['psubcatid']) { echo "selected" ;} ?>>
                      <?=$row_pcat['prod_sub_cat']?>
                      </option>
                      <?php
                      }
                      ?>
                </select>
                  </div>    
              </div>  
          </div>
		  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Employee <span class="red_small">*</span></label> 
                  <div class="col-md-6">
				  <?php
					if($_SESSION["userid"]=="admin"){
						$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND oth_empid!='' order by name");
					}else{
						$child = getHierarchyStr($_SESSION["userid"], $link1, "");
						$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND username IN ('".str_replace(",","','",$child)."') order by name");
						//$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND reporting_manager='".$_SESSION["userid"]."' order by name");
					}
				  ?>
                  <select name="user_id" id="user_id"  data-live-search="true" class="form-control selectpicker required" required >
                      <option value="" <?php if($_REQUEST['user_id']==""){ echo "selected"; } ?> > -- Please Select -- </option>
					  <?php while($row = mysqli_fetch_assoc($sql)){ ?>
					  <option value="<?=$row['username']."~".$row['oth_empid'];?>" <?php if($_REQUEST['user_id']==$row['username']."~".$row['oth_empid']){ echo "selected"; } ?> ><?php echo $row['username']." | ".$row['name']." | ".$row['oth_empid']; ?></option>
					  <?php } ?>
                  </select>
                  </div>    
              </div>  
          </div>
		  
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Target Type <span class="red_small">*</span></label> 
                  <div class="col-md-6">
					  <select name="target_type" id="target_type" class="form-control required" required onChange="showTargetField(this.value)" >
						  <option value="" <?php if($_REQUEST['target_type']==""){ echo "selected"; } ?> > -- Please Select -- </option>
						  <?php /*?><option value="Qty" <?php if($_REQUEST['target_type']=="Qty"){ echo "selected"; } ?> >Qty</option><?php */?>
						  <option value="Value" <?php if($_REQUEST['target_type']=="Value"){ echo "selected"; } ?> >Value</option>
					  </select>
                  </div>    
              </div>  
          </div>
		  
		  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Status <span class="red_small">*</span></label> 
                  <div class="col-md-6">
					  <select name="status" id="status" class="form-control required" required >
						  <option value="Active" <?php if($_REQUEST['status']=="Active"){ echo "selected"; } ?> >Active</option>
						  <option value="Deactive" <?php if($_REQUEST['status']=="Deactive"){ echo "selected"; } ?> >Deactive</option>
					  </select>
                  </div>    
              </div>  
          </div>
		  
		  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Remark </label> 
                  <div class="col-md-6">
					  <textarea class="form-control addressfield" id="remark" name="remark" ></textarea>
                  </div>    
              </div>  
          </div>
		  
		  <!-------------------------- It shows if target type selected as value ------------------------------------->
		  <div class="form-group" id="targ_val" style="display:none;">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Target Value <span class="red_small">*</span></label> 
                  <div class="col-md-6 input-append date">
                  		<div style="display:inline-block;float:left; width:100%;">
                            <input type="text" class="form-control number" name="target_val"  id="target_val" >
                        </div>
                  </div>    
              </div>  
          </div> 
		  <!-------------------------- It shows if target type selected as qty ------------------------------------->
		  <div id="targ_qty" style="display:none;"> 
		  	<br>
		  	 <table width="100%" id="itemsTable" class="table table-bordered table-hover">
				<thead>
				  <tr class="<?=$tableheadcolor?>" >
					<th data-hide="phone" style="font-size:13px">Product</th>
					<th data-hide="phone" style="font-size:13px">Qty</th>
				  </tr>
				</thead>
				<tbody>
				  <tr id='addr0'>
					<td>
						<select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker" data-live-search="true"  onChange="checkDuplicate(0, this.value);"   >
						<option value="">--None--</option>
						<?php 
					$model_query="select productcode,productname,productcolor from product_master where status='active' order by productname ";
						$check1=mysqli_query($link1,$model_query);
						while($br = mysqli_fetch_array($check1)){?>
						<option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>
						<?php }?>
					  </select>
					</td>
					<td><input type="text" class="form-control number" name="target_qty[0]" required id="target_qty[0]" /></td>
				  </tr>
				</tbody>
				<tfoot id='productfooter' style="z-index:-9999;">
				  <tr class="0">
					<td colspan="3" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
					<input type="hidden" name="rowno" id="rowno" value="0"/></td>
				  </tr>
				</tfoot>
			</table>
		  </div>
         
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" id="save" value="Add"> Add </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='target_list.php?<?=$pagenav?>'">
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