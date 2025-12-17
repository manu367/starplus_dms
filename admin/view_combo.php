<?php
////// Function ID ///////
$fun_id = array("a"=>array(53));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//$arrstatus = getFullStatus("master",$link1);
$arrstatus = array("1" => "Active","2" => "Deactive","3" => "Pending For Approval");
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
$docid=base64_decode($_REQUEST['id']);
//// po to vendor details
$sql_locdet="SELECT * FROM combo_master where bomid='".$docid."'";
$res_locdet=mysqli_query($link1,$sql_locdet);
$row_locdet=mysqli_fetch_assoc($res_locdet);
/////////check approval is applicable or not
//$apply_app = getAnyDetails(base64_decode($_REQUEST['pid']),"apply_approval","tabid","tab_master",$link1);
@extract($_POST);
////// if we want to change status of Combo
if($_POST){
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$err_msg = "";	
	
	////// loop start for qty update ///////	
	for($j=0; $j<count($rowId); $j++){
		////// check updated row info ////////////
		$p_check = mysqli_fetch_assoc(mysqli_query($link1, "SELECT bom_qty FROM combo_master WHERE id = '".$rowId[$j]."' "));
		if($p_check['bom_qty'] !="" && $p_check['bom_qty']!=$bom_qt[$j]){
			$qr1 ="INSERT INTO combo_history ( bomid, bom_modelcode, bom_modelname, bom_partcode, bom_qty_old, conversion_factor, createby, createdate, createtime ) SELECT  bomid,  bom_modelcode, bom_modelname, bom_partcode, bom_qty, conversion_factor,  '".$_SESSION['userid']."', '".$today."', '".$currtime."' FROM combo_master WHERE id='".$rowId[$j]."' ORDER BY id ASC ";
			
			$rslt1 = mysqli_query($link1, $qr1);
			//// check if query is not executed
			if (!$rslt1) {
				$flag = false;
				$err_msg = "Error Code 1 : " . mysqli_error($link1);
			}
			if($bom_qt[$j]==0 || $bom_qt[$j]=="0" || $bom_qt[$j]=="0.000000"){ $sts = ",status='2'";}else{$sts = "";}
			$rslt2 = mysqli_query($link1, "UPDATE combo_master SET bom_qty = '".$bom_qt[$j]."' ".$sts." WHERE  id='".$rowId[$j]."' ");
			//// check if query is not executed
			if (!$rslt2) {
				$flag = false;
				$err_msg = "Error Code 2 : " . mysqli_error($link1);
			}
			
		}
	}
	////// loop stop for qty update ///////
	if($apply_app=="Y"){ $current_status = 3;}else{ $current_status = 1;}
	////// loop start for new part add ///////
	for($h=1; $h<=count($bom_part); $h++){
		//// converson factor ////
		if($conversion_factor[$h]=="" || $conversion_factor[$h]==0 || $conversion_factor[$h]==0.000000){ $cf = 1; }else{ $cf = $conversion_factor[$h]; }
		if($bom_part[$h]!="" && $bom_qty[$h]!="" && $cf!=""){
			$info_part = explode("~",$bom_part[$h]);
					
			$qr2 = "INSERT INTO combo_master SET bomid = '".$docid."', bom_modelcode = '".$row_locdet['bom_modelcode']."', bom_modelname = '".$row_locdet['bom_modelname']."', bom_partcode = '".$info_part[0]."', bom_qty = '".$bom_qty[$h]."', bom_unit = 'PCS', purchase_unit = 'PCS', conversion_factor = '".$cf."', packing_level = '".$row_locdet['packing_level']."', status = '".$row_locdet['status']."', createdate = '".$datetime."', createby = '".$_SESSION['userid']."'  ";
			
			$rslt3 = mysqli_query($link1, $qr2);
			//// check if query is not executed
			if (!$rslt3) {
				$flag = false;
				$err_msg = "Error Code 3 : " . mysqli_error($link1);
			}
		}
	}
	////// loop stop for new part add ///////
		
	if ($status){
		//// if Combo status is going to change as Active then we have to check there should not any other Combo is in active stat for the same model
		if($status == 1){
			if(mysqli_num_rows(mysqli_query($link1,"select id from combo_master where bom_modelcode='".$row_locdet['bom_modelcode']."' and status='1'"))==0){
				///// again bom should be approve if we are going to activate again
				if($apply_app=="Y"){ $current_status = 3; $rtn_msg = "pending for approval";}else{ $current_status = 1; $rtn_msg = "active";}
				$rslt5 = mysqli_query($link1,"UPDATE combo_master set status='".$current_status."', updateby='".$_SESSION['userid']."',updatedate='".$datetime."' where bomid='".$docid."'");
				//// check if query is not executed
				if (!$rslt5) {
					$flag = false;
					$err_msg = "Error Code 5 : " . mysqli_error($link1);
				}
				////// insert in activity table////
				dailyActivity($_SESSION['userid'],$row_locdet['bom_modelcode']."-".$docid,"Combo","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,"");
				////// return message
				$msg = "You have just change the Combo status for ".$row_locdet['bom_modelcode']." and now it is ".$rtn_msg;
				$cflag = "success";
				$cmsg = "Success";
			}else{
				$cflag = "warning";
				$cmsg = "Warning";
				$msg = "Combo is already active for Model ".$row_locdet['bom_modelcode'];
			}
		}else{
			$rslt6 = mysqli_query($link1,"UPDATE combo_master set status='".$status."', updateby='".$_SESSION['userid']."',updatedate='".$datetime."' where bomid='".$docid."'");
			//// check if query is not executed
			if (!$rslt6) {
				$flag = false;
				$err_msg = "Error Code 6 : " . mysqli_error($link1);
			}
			////// insert in activity table////
			dailyActivity($_SESSION['userid'],$row_locdet['bom_modelcode']."-".$docid,"Combo","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,"");	
			////// return message
			$msg = "You have just change the Combo status for ".$row_locdet['bom_modelcode']."";
			$cflag = "success";
			$cmsg = "Success";
		}
		
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg = "Combo list updated with ref. no. " . $row_locdet['bom_modelcode'];
			$cflag = "success";
			$cmsg = "Success";
		} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed " . $err_msg . ". Please try again.";
			$cflag = "danger";
			$cmsg = "Failed";
		}
		mysqli_close($link1);
		
		///// move to parent page
		header("Location:combo_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
  <script src="../js/jquery.validate.js"></script>
  <script src="../js//bootbox.js"></script>
  <script>
	$(document).ready(function(){
		var spinner = $('#loader');
		$("#frm1").validate({
			submitHandler: function (form) {
				////// pop up a box for confirmation
				bootbox.confirm("Are you sure want to update Combo List or Status ?", function(result) {
					///// if user hit yes
					if(result==true){
						if(!this.wasSent){
							this.wasSent = true;
							$(':submit', form).val('Please wait...')
										  .attr('disabled', 'disabled')
										  .addClass('disabled');
							spinner.show();		  
							form.submit();
						} else {
							return false;
						}
					}//// close yes condition
				});//// close confirmation box
			}
		});
	});
	function makedropdown(){
		$('.selectpicker').selectpicker({
		  liveSearch: true
		});
	}
	/////// add new row 
	$(document).ready(function() {
		$("#add_row").click(function() {		
			var numi = document.getElementById('rowno');
			//var itm = "bom_part[" + numi.value+"]";
			var preno=document.getElementById('rowno').value;
			var num = (document.getElementById("rowno").value -1)+2;
			numi.value = num;
			//if ((document.getElementById(itm).value != "") || ($("#addr1_" + numi.value + ":visible").length == 0)) {
			if (($("#addr1_" + numi.value + ":visible").length == 0)) {
				var r = '<tr id="addr1_'+num+'"><td width="60%"><div style="display:inline-block;float:left;"><select name="bom_part['+num+']" id="bom_part['+num+']" class="form-control required selectpicker show-tick" required data-width="570px" onChange="getbomunit(this.value,'+num+')"><option value="">--Select Part--</option><?php $res_pro = mysqli_query($link1,"SELECT productcode,productname FROM product_master WHERE status='Active' ORDER BY productname")or die(mysqli_error($link1));while($row_pro = mysqli_fetch_assoc($res_pro)){?><option value="<?=$row_pro['productcode']?>"><?=$row_pro['productname']." | ".$row_pro['productcode']?></option><?php } ?></select></div><div style="display:inline-block;float:right;"><i class="fa fa-close fa-lg" onClick="fun_remove('+num+');"></i></div></td><td width="15%"><input type="text" name="bom_qty['+num+']" class="form-control number required" id="bom_qty['+num+']" required /></td><?php /*?><td width="15%"><input type="text" name="conversion_factor['+num+']" class="form-control required number" id="conversion_factor['+num+']" value="1.000000" required /></td><?php */?><td width="10%"><span id="bomunit'+num+'"></span></td></tr>';
				$('#itemsTable1').append(r);
				makedropdown();
			}
		});
	});
	function fun_remove(con){
		var c = document.getElementById('addr1_' + con);
		c.parentNode.removeChild(c);
		con--;
		document.getElementById('rowno').value = con;
	}
	function getbomunit(val,indx){
		var split_val = val.split("~");
		document.getElementById("bomunit"+indx).innerHTML = "PCS";
	}
  </script>
  <link href="../css/loader.css" rel="stylesheet"/>
  </head>
  <body>
  <div class="container-fluid">
    <div class="row content">
    <?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="<?=$screenwidth?>">
        <h2 align="center"><i class="fa fa-cubes"></i> Combo Details</h2>
      	<h4 align="center"><?=$row_locdet['bom_modelname']."  (".$row_locdet['bom_modelcode'].")";?></h4>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
	  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-success table-responsive">
        <div class="panel-heading"><i class="fa fa-cube fa-lg"></i>&nbsp;&nbsp;Combo Model Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Combo Model</label></td>
                <td width="30%"><?php echo $row_locdet['bom_modelname']." (".$row_locdet['bom_modelcode'].")"; ?></td>
                <td width="20%"><label class="control-label">Combo Model HSN</label></td>
                <td width="30%"><?php echo $row_locdet['bom_hsn'];?></td>
              </tr>
              <tr>
                <td width="20%"><label class="control-label">Create By</label></td>
                <td width="30%"><?=getAnyDetails($row_locdet['createby'],"name","username","admin_users",$link1)?></td>
                <td width="20%"><label class="control-label">Create Date</label></td>
                <td width="30%"><?=dt_format($row_locdet['createdate'])?></td>
              </tr>
              <?php if($row_locdet['updateby']){?>
              <tr>
                <td width="20%"><label class="control-label">Update By</label></td>
                <td width="30%"><?=getAnyDetails($row_locdet['updateby'],"name","username","admin_users",$link1)?></td>
                <td width="20%"><label class="control-label">Update Date</label></td>
                <td width="30%"><?=dt_format($row_locdet['updatedate'])?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-success">
        <div class="panel-heading"><i class="fa fa-sitemap fa-lg"></i>&nbsp;&nbsp;Combo Part Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%" id="itemsTable1">
            <thead>
                <tr class="<?=$tableheadcolor?>">
                    <th width="55%">Combo Part</th>
                    <th width="20%">Combo Qty</th>
                    <!--<th width="15%">Conversion Factor</th>-->
                    <th width="10%">Combo Unit</th>
                </tr>
            </thead>
            <tbody>
			<?php
			$r=0;
			$res_bom = mysqli_query($link1,"SELECT bom_partcode,bom_qty,bom_unit,conversion_factor ,id FROM combo_master where bomid='".$docid."' and bom_qty != '0.000000' ");
			while($row_bom = mysqli_fetch_assoc($res_bom)){
			?>
				  <tr>
					<td><?php echo getAnyDetails($row_bom['bom_partcode'],"productname","productcode","product_master",$link1)." (".$row_bom['bom_partcode'].")";?></td>
					<td style="position: relative;"><input type="text" name="bom_qt[<?=$r;?>]" id="bom_qt<?=$r;?>" value="<?=$row_bom['bom_qty']?>" class="form-control required" required /><input type="hidden" name="rowId[<?=$r;?>]" id="rowId<?=$r;?>" value="<?=$row_bom['id']?>" /></td>
					<?php /*?><td><?=$row_bom['conversion_factor']?></td><?php */?>
					<td><?=$row_bom['bom_unit']?></td>
				  </tr>
				<?php $r++; }?>
				 <tr>
				 <span style="color:#FF0000;font-size: 12px;font-weight: bolder;" > * Update Qty Zero for remove the part from Combo list. </span>
			 </tbody>
          </table>
		  
		<table class="table table-bordered" width="100%" id="itemsTable1">
            <tbody>
			 	<td width="50%">
					<button class='btn<?=$btncolor?>' id="add_row" type="button" name="add" value="Add">
						<i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add New
					</button>
                	<input type="hidden" name="rowno" id="rowno" value="0"/>
				</td>
				<td width="20%" style="text-align:right;" ><label class="control-label">Change Status : </label></td>
                <td width="30%">
					<select name="status" id="status" class="form-control custom-select" style="width:200px;text-align:left;">
						<?php 
						foreach($arrstatus as $key => $value){
							if($key!=3 || ($key!=1 && $row_locdet['status']==3)){?>
							<option value="<?=$key?>" <?php if($row_locdet['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
						<?php } }?>
					</select>
				 </td>
             </tr>  
            <tr>
                <td align="center" colspan="4">
                  <?php //if($get_opr_rgts['edit']=="Y"){ ?>
                  <button class='btn<?=$btncolor?>' id="upd" type="submit" name="upd" value="Save"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Save</button>
                  <!--<input type="submit" class="btn<?//=$btncolor?>" name="upd" id="upd" value="Change" title="Change Combo Status">-->
                  <?php //}?>
                  <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='combo_master.php?status=<?php if(isset($_REQUEST['status'])){ echo $_REQUEST['status'];}?>&bom_model=<?php if(isset($_REQUEST['bom_model'])){ echo $_REQUEST['bom_model'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
                 </td>
             </tr>  
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->    
    </div><!--close panel group-->
	</form>
        </div>
        <!--End form group--> 
      </div>
      <!--End col-sm-9--> 
    </div>
    <!--End row content--> 
  </div>
  <!--End container fluid-->
  <div id="loader"></div>
  <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
