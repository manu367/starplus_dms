<?php
require_once("../config/config.php");

$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM payment_receive where doc_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$p_row=mysqli_fetch_assoc($po_res);

@extract($_POST);
////// we hit save button

 if (isset($_POST['upd']) && $_POST['upd']=='Save'){
	mysqli_autocommit($link1, false);
	$flag = true;
	///// Insert Master Data
	 $query1= "update payment_receive set from_location='".$from_location."',to_location='".$to_location."',amount='".$amount."',status='PFA',payment_mode='".$pay_mode."', bank_name='".$bank_name."', bank_branch='".$bank_branch."', dd_cheque_no='".$dd_ch_no."', dd_cheque_dt='".$dd_ch_dt."', receipt_no='".$rec_no."', transaction_id='".$trans_id."', remark='".$remark."', ip='".$ip."', update_by='".$_SESSION['userid']."'  where doc_no='".$docid."'";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
		
	///// insert into party ledger
	$result=mysqli_query($link1, "delete from party_ledger where doc_no='".$docid."'");
	
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }

	
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"RP","Receipt",$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Receipt is edited successfully  with ref. no.".$docid;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
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
$(document).ready(function(){   
  get_details();
});


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
      <h2 align="center"><i class="fa fa-rupee"></i> Edit Receipt</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" onSubmit="return validate();">
          <div class="form-group">
          
          <div class="col-md-10"><label class="col-md-3 control-label">Document No<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                                   
                 <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?php echo $p_row['doc_no'];?>" readonly required/>
              </div>
            </div>
            
            
          </div>
          <div class="form-group">
          
          <div class="col-md-10"><label class="col-md-3 control-label">From Location<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                                   
                 <input type="hidden" name="from_location" id="from_location" class="form-control" value="<?php echo $p_row['from_location'];?>" readonly required/>
                 <input type="text" name="from_location1" id="from_location1" class="form-control" value="<?php echo str_replace("~",",",getLocationDetails($p_row['from_location'],"name,city,state",$link1));?>" readonly required/>
              </div>
            </div>
            
          </div>
          <div class="form-group">
          <div class="col-md-10"><label class="col-md-3 control-label">To Location<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                  <input type="hidden" name="to_location" id="to_location" class="form-control" value="<?php echo $p_row['to_location'];?>" readonly required/>
                    <input type="text" name="to_location1" id="to_location1" class="form-control" value="<?php echo str_replace("~",",",getLocationDetails($p_row['to_location'],"name,city,state",$link1));?>" readonly required/>
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
                  <option value="<?php echo $mrow['id'];?>"<?php if($mrow['id']==$p_row['payment_mode']){echo "selected='selected'";}?>><?php echo $mrow['mode'];?></option>
              <?php } ?>
                </select>
                
              </div></div>
              <div class="col-md-6" id="b_name" style="display:block">
              <label class="col-md-6 control-label">Bank Name<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?php echo $p_row['bank_name'];?>"/>
              </div>
              </div>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6" id="b_branch" style="display:block">
              <label class="col-md-6 control-label">Bank Branch/IFSC Code<span style="color:#F00">*</span></label>
              <div class="col-md-6">
               <input type="text" name="bank_branch" id="bank_branch" class="form-control" value="<?php echo $p_row['bank_branch'];?>"/>
                
              </div></div>
              <div class="col-md-6" id="ch_no" style="display:block">
              <label class="col-md-6 control-label">DD/Cheque No<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="dd_ch_no" id="dd_ch_no" class="form-control" value="<?php echo $p_row['dd_cheque_no'];?>"/>
              </div>
              </div>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6" id="ch_dt" style="display:block">
              <label class="col-md-6 control-label">DD/Cheque Date<span style="color:#F00">*</span></label>
              <div class="col-md-6">
               <input type="text" class="form-control span2" name="dd_ch_dt"  id="dd_ch_dt"  value="<?php echo $p_row['dd_cheque_dt'];?>" required></div>
                
              </div>
              <div class="col-md-6" id="ref_no" style="display:block">
              <label class="col-md-6 control-label">Bank Receipt No/Ref No.<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="rec_no" id="rec_no" class="form-control" value="<?php echo $p_row['receipt_no'];?>"/>
              </div>
              </div>
          </div>
     </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6" id="t_id" style="display:block">
              <label class="col-md-6 control-label">Transaction Id<span style="color:#F00">*</span></label>
              <div class="col-md-6">
               <input type="text" name="trans_id" id="trans_id" class="form-control" value="<?php echo $p_row['transaction_id'];?>"/>
                
              </div></div>
              <div class="col-md-6">
              <label class="col-md-6 control-label">Receiving Amount<span style="color:#F00">*</span></label>
              <div class="col-md-6">
             <input type="text" name="amount" id="amount" class="form-control" value="<?php echo $p_row['amount'];?>" onKeyPress="return onlyFloatNum(this.value);" required/>
              </div>
              </div>
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-10">
          <div class="col-md-6">
              <label class="col-md-6 control-label">Remark</label>
              <div class="col-md-6">
               <textarea  name="remark" id="remark" class="form-control"><?php echo $p_row['remark'];?></textarea>
                
              </div></div>
              
          </div>
        </div>
        
        <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save This Receipt">
                <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['to_location']?>"/>
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['from_location']?>"/>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='paymentlist.php?<?=$pagenav?>'">
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
