<?php
require_once("../config/config.php");
@extract($_POST);
///// extract lat long
$latlong = explode(",",base64_decode($_REQUEST["latlong"]));
////// we hit save button

 if (isset($_POST['upd']) && $_POST['upd']=='Save'){
     //// Make System generated PO no.//////
	
	$res_po=mysqli_query($link1,"select max(rcvpay_counter) as no, rcvpay_str from document_counter where location_code='".$parentcode."'");
	if(mysqli_num_rows($res_po)){
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po['no']+1;
	$pad1=str_pad($c_nos,4,"0",STR_PAD_LEFT);  
	$doc_no=$row_po['rcvpay_str'].$pad1; 
	mysqli_autocommit($link1, false);
	$flag = true;
	///// Insert Master Data
	 $query1= "INSERT INTO payment_receive set doc_no='".$doc_no."',against_ref_no='".$inv_no."',from_location='".$partycode."',to_location='".$parentcode."',amount='".$amount."',status='PFA',payment_mode='".$pay_mode."', bank_name='".$bank_name."', bank_branch='".$bank_branch."', dd_cheque_no='".$dd_ch_no."', dd_cheque_dt='".$dd_ch_dt."', receipt_no='".$rec_no."', transaction_id='".$trans_id."', remark='".$remark."',payment_date='".$today."',entry_dt='".$today."',entry_time='".$currtime."',entry_by='".$_SESSION['userid']."',ip='".$ip."',address='',latitude='".$latlong[0]."',longitude='".$latlong[1]."',pjp_id='".$_REQUEST['task_id']."'";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
	///// update document counter
	$result=mysqli_query($link1, "update document_counter set rcvpay_counter='".$c_nos."' where location_code='".$parentcode."'");
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	if($_REQUEST['task_id']){
   		mysqli_query($link1,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$_REQUEST['task_id']."'");
		$resultut = mysqli_query($link1,"INSERT INTO user_track SET userid='".$_SESSION['userid']."', task_name='Collection', task_action='Add', ref_no='".$doc_no."', latitude='".$latlong[0]."', longitude='".$latlong[1]."', address='',travel_km='', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."'");
		//// check if query is not executed
		if (!$resultut) {
			 $flag = false;
			 $err_msg = "Error details4: " . mysqli_error($link1) . ".";
		}
		$_REQUEST['task_id'] = "";
		unset($_REQUEST);
   }
		
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$doc_no,"RP","Receive payment",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Reciept is successfully saved with ref. no.".$doc_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	}else{
		$msg = "Request could not be processed receipt series not found. Please try again.";
	}
	///// move to parent page
  header("location:paymentlist.php?msg=".$msg."".$pagenav);
   exit;
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
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});
$(document).ready(function(){
    $("#frm2").validate();
});

$(document).ready(function () {
	$('#dd_ch_dt').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});

</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript" src="../js/ajax.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>

<script>
function get_details()
{var val=document.getElementById('pay_mode').value;
	if(val==1)
	{
		document.getElementById('b_name').style.display='none';
		document.getElementById('b_branch').style.display='none';
		document.getElementById('ch_no').style.display='none';
		document.getElementById('ch_dt').style.display='none';
		document.getElementById('t_id').style.display='none';
		document.getElementById('ref_no').style.display='none';
	}
	
	else if(val==2 || val==3)
	{
		document.getElementById('b_name').style.display='block';
		document.getElementById('b_branch').style.display='block';
		document.getElementById('ch_no').style.display='block';
		document.getElementById('ch_dt').style.display='block';
		document.getElementById('t_id').style.display='none';
		document.getElementById('ref_no').style.display='none';
		
		document.getElementById("b_name").required = 'yes';
		document.getElementById("b_branch").required = 'yes';
		document.getElementById("ch_no").required = 'yes';
		document.getElementById("ch_dt").required = 'yes';
	}
	
	else if(val==4)
	{
		document.getElementById('b_name').style.display='block';
		document.getElementById('b_branch').style.display='block';
		document.getElementById('ch_no').style.display='none';
		document.getElementById('ch_dt').style.display='none';
		document.getElementById('t_id').style.display='block';
		document.getElementById('ref_no').style.display='none';
	}
	
	else if(val==5)
	{
		document.getElementById('b_name').style.display='block';
		document.getElementById('b_branch').style.display='block';
		document.getElementById('ch_no').style.display='none';
		document.getElementById('ch_dt').style.display='none';
		document.getElementById('t_id').style.display='none';
		document.getElementById('ref_no').style.display='block';
	}
	
	else
	{
		document.getElementById('b_name').style.display='block';
		document.getElementById('b_branch').style.display='block';
		document.getElementById('ch_no').style.display='block';
		document.getElementById('ch_dt').style.display='block';
		document.getElementById('t_id').style.display='block';
		document.getElementById('ref_no').style.display='block';
	}
}

function validate()
{
	var val=document.getElementById('pay_mode').value;
	
	///// if payment mode is DD or Cheque
		if((val==2 || val==3) && ((document.getElementById('bank_name').value=='') || (document.getElementById('bank_branch').value=='')|| (document.getElementById('dd_ch_no').value=='')|| (document.getElementById('dd_ch_dt').value=='')))
		{
			alert("Please enter all * fields");
			return false;
		}
		
		///// if payment mode is neft/rtgs/imps
		else if((val==4) && ((document.getElementById('bank_name').value=='') || (document.getElementById('bank_branch').value=='')|| (document.getElementById('trans_id').value=='')))
		{
			alert("Please enter all * fields");
			return false;
		}
		///// if payment mode is bank receipt
		else if((val==5) && ((document.getElementById('bank_name').value=='') || (document.getElementById('bank_branch').value=='')|| (document.getElementById('rec_no').value=='')))
		{
			alert("Please enter all * fields");
			return false;
		}
		else
		{
			return true;
		}
	
}
</script>

</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-rupee"></i> Add New Receipt</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" onSubmit="return validate();">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">From Location<span style="color:#F00">*</span></label>
              <div class="col-md-9">
               
                  <select name="from_location" id="from_location" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                      if($party_det[id_type]!='HO'){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if(isset($_REQUEST['from_location']) && $result_chl['location_id']==$_REQUEST['from_location'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					}
                    ?>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">To Location<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="to_location" id="to_location" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select uid from mapped_master where mapped_code='$_REQUEST[from_location]'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[uid]'"));
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_parent['uid']?>" value="<?=$result_parent['uid']?>" <?php if(isset($_REQUEST['to_location']) && $result_parent['uid']==$_REQUEST['to_location'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['uid']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6">
              <label class="col-md-6 control-label">Payment Mode<span style="color:#F00">*</span></label>
              <div class="col-md-6">
              <select name="pay_mode" id="pay_mode" required class="form-control required" onChange="get_details();">
                  <option value="">Please Select</option>
                  <?php $mode=mysqli_query($link1, "select * from payment_mode"); while($mrow=mysqli_fetch_assoc($mode)){?>
                  <option value="<?php echo $mrow['id'];?>"><?php echo $mrow['mode'];?></option>
              <?php } ?>
                </select>
                
              </div></div>
              <div class="col-md-6" id="b_name" style="display:block">
              <label class="col-md-6 control-label">Bank Name<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="bank_name" id="bank_name" class="form-control" value=""/>
              </div>
              </div>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6" id="b_branch" style="display:block">
              <label class="col-md-6 control-label">Bank Branch/IFSC Code<span style="color:#F00">*</span></label>
              <div class="col-md-6">
               <input type="text" name="bank_branch" id="bank_branch" class="form-control" value=""/>
                
              </div></div>
              <div class="col-md-6" id="ch_no" style="display:block">
              <label class="col-md-6 control-label">DD/Cheque No<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="dd_ch_no" id="dd_ch_no" class="form-control" value=""/>
              </div>
              </div>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6" id="ch_dt" style="display:block">
              <label class="col-md-6 control-label">DD/Cheque Date<span style="color:#F00">*</span></label>
              <div class="col-md-6">
               <input type="text" class="form-control span2" name="dd_ch_dt"  id="dd_ch_dt"  value="<?php if(isset($_REQUEST['dd_ch_dt'])){echo $_REQUEST['dd_ch_dt']; } else{echo $today;}?>" required></div>
                
              </div>
              <div class="col-md-6" id="ref_no" style="display:block">
              <label class="col-md-6 control-label">Bank Receipt No/Ref No.<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="rec_no" id="rec_no" class="form-control" value=""/>
              </div>
              </div>
          </div>
     </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6" id="t_id" style="display:block">
              <label class="col-md-6 control-label">Transaction Id<span style="color:#F00">*</span></label>
              <div class="col-md-6">
               <input type="text" name="trans_id" id="trans_id" class="form-control" value=""/>
                
              </div></div>
              <div class="col-md-6">
              <label class="col-md-6 control-label">Receiving Amount<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="amount" id="amount" class="form-control" value="" onKeyPress="return onlyFloatNum(this.value);" required/>
              </div>
              </div>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6">
              <label class="col-md-6 control-label">Remark</label>
              <div class="col-md-6">
               <textarea  name="remark" id="remark" class="form-control"></textarea>
                
              </div>
		  </div>
		  <div class="col-md-6">
			  <label class="col-md-6 control-label">Invoice No.</label>
			  <div class="col-md-6">
				  <select name="inv_no" id="inv_no" class="form-control selectpicker" data-live-search="true">
					  <option value="" selected="selected">Please Select </option>
					  <?php 
					$sql_parent="select challan_no  from billing_master where from_location='".$_REQUEST['to_location']."' and to_location='".$_REQUEST['from_location']."' and document_type='INVOICE' and status IN ('Dispatched','Received') and crdr_flag = '' ";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      ?>
                    <option data-tokens="<?=$result_parent['challan_no']?>" value="<?=$result_parent['challan_no']?>"<?php if($result_parent['challan_no']==$_REQUEST['inv_no'])echo "selected";?>>
                       <?=$result_parent['challan_no']?>
                    </option>
                     <?php
					}
                    ?>
				  </select>    
			  </div>
			  </div>	  
              
          </div>
        </div>
        
        <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Save" title="Save This Receipt">
                <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['to_location']?>"/>
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['from_location']?>"/>
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='paymentlist.php?<?=$pagenav?>'">
            </div>
          </div>
         </form>
         
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
