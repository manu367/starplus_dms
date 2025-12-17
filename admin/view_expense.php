<?php
require_once("../config/config.php");
$date=date("Y-m-d");
$getid=base64_decode($_REQUEST[id]);
//// fetch details //////////////
$res_locdet=mysqli_fetch_array(mysqli_query($link1 , "select * from locationwise_expense where id = '".$getid."' "));

@extract($_POST);
////// final submit form ////


if($_POST['Submit']=='Save'){

	if($pay_mode !=''){
	//// insert into location wise expense table ////////////////////////////////////////////////////////
	$sql=mysqli_query($link1,"update locationwise_expense set  payment_mode = '".$pay_mode."' ,status='Paid',update_date='$date',update_by='".$_SESSION['userid']."' ,bankname = '".$bank_name."' , account_no = '".$acc_no."' , dd_chequeno = '".$dd_ch_no."' , dd_date = '".$dd_ch_dt."' , transcation_id = '".$transcation_id."' , bank_branch = '".$bank_branch."' , ifsc_code = '".$ifsc_code."' , bank_transfermode = '".$bank_mode."' where id = '".$sno."'  ")or die("ER4".mysqli_error($link1)); 
   
    ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$sno,"Expense","Update",$ip,$link1);
    //return message
	$msg="You have successfully updated Location Expense";
	}
	else {
	$msg = "Please Select Payment Mode";
	 }
	///// move to parent page
    header("Location:locationwise_expense.php?msg=".$msg."".$pagenav);
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
 
<script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
	
	
	$(document).ready(function () {
	$('#expense_date').datepicker({
		format: "yyyy-mm-dd",
		endDate : '<?=$date?>'
	});
});
	
	$(document).ready(function () {
	$('#dd_ch_dt').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
	
	function getValidate(val){
  
  if(val == 'Cash'){
   document.getElementById("show").style.display = "none";
   document.getElementById("transcation").style.display = "";
    document.getElementById("shownew").style.display = "none";
 document.getElementById("shownew1").style.display = "none"; 
   document.getElementById("cheque").style.display = "none";
     
  }
  else if(val == 'Bank'){
 document.getElementById("show").style.display = "";
 document.getElementById("shownew").style.display = "";
 document.getElementById("shownew1").style.display = ""; 
 document.getElementById("cheque").style.display = "none";
  document.getElementById("transcation").style.display = "none";
  
  }
  else if(val == 'Cheque') {
   document.getElementById("show").style.display = "";
   document.getElementById("cheque").style.display = "";
   document.getElementById("shownew").style.display = "none";
 document.getElementById("shownew1").style.display = "none"; 
 document.getElementById("transcation").style.display = "none";
  
  }

   else {
   document.getElementById("show").style.display = "none";
   document.getElementById("cheque").style.display = "none";
   document.getElementById("transcation").style.display = "none";
   document.getElementById("shownew").style.display = "none";
 document.getElementById("shownew1").style.display = "none"; 
    }
  
  
   }
	
	
 </script>
 <script>
/*var _validFileExtensions = [".gif", ".jpeg", ".jpg", ".png",".PNG",".GIF",".JPEG",".JPG",".xlsx",".xls"];    
function Validate(oForm) {
    var arrInputs = oForm.getElementsByTagName("input");
    for (var i = 0; i < arrInputs.length; i++) {
        var oInput = arrInputs[i];
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    }
                }
                
                if (!blnValid) {
                    alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                    return false;
                }
            }
        }
    }
  
    return true;
}
*/
</script>
 <style>
.red_small{
	color:red;
}
</style>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/ajax.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">

<script src="../js/bootstrap-datepicker.js"></script>

</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-inr"></i>&nbsp;&nbsp;View Expense</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post" enctype="multipart/form-data" onSubmit="return Validate(this);">
          
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Location</label>
              <div class="col-md-6">
                <input type="text" name="location" id="location"  class="form-control" value="<?=getLocationDetails($res_locdet['location_code'],"name",$link1)?>"  readonly>
                    
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-6 control-label">Expense Date</label>
             
                <div class="col-md-6">
               <div><input type="text" class="form-control" name="expense_date"  id="expense_date" style="width:160px;" value="<?php echo $res_locdet['expense_date']?>" readonly=""></div>
			   </div>
        </div>
              
            
			 
          </div>
          
         <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Narration</label>
              <div class="col-md-6">
                <input type="text" name="narration" id="narration"  class="form-control"  value="<?php echo $res_locdet['narration']?>" readonly="">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"> Attachment </label>
			      <div class="col-md-6">
                 <input type="text" name="attach" id="attach" value="<?=$res_locdet['attachment']?>"  class="form-control"  readonly=""/>
				 </div>
               </div>
          </div>
		   
		  <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Amount</label>
              <div class="col-md-6">
                <input type="text" name="amount" id="amount" class="number form-control" value="<?=$res_locdet['amount']?>" readonly >
              </div> 
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"> Status</label>
			      <div class="col-md-6">
				  <input type="text" name="status" id="status" value="<?=$res_locdet['status']?>"  class="form-control" readonly="">
				 </div>
               </div>
          </div> 
		  
		  <?php if($res_locdet['status'] == 'Pending') {?>
		  <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Payment Mode <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="pay_mode" id="pay_mode" required  class="form-control selectpicker required" data-live-search="true"  onChange="getValidate(this.value);">
                     <option value="" selected="selected">Please Select </option>
					<option value="Cash" <?php if($_REQUEST['pay_mode'] == 'Cash') { echo "selected";}?>>Cash</option>
					<option value="Cheque" <?php if($_REQUEST['pay_mode'] == 'Cheque') { echo "selected";}?>>Cheque</option>
					<option value="Bank" <?php if($_REQUEST['pay_mode'] == 'Bank') { echo "selected";}?>>Bank Transfer</option>
					</select>
				
				
              </div> 
            </div>
			
            <div class="col-md-6" id="transcation" style="display:none"><label class="col-md-6 control-label">Transcation Id/Ref No. <span class="red_small">*</span></label>
			      <div class="col-md-6">
				 <input type="text" name="transcation_id" id="transcation_id" class="form-control" value="<?=$_REQUEST['transcation_id']?>" required > 
				 </div>
               </div>
          </div> 
		  
		 <div class="form-group" id="show" style="display:none">
           <div class="col-md-6"><label class="col-md-6 control-label">Bank Name <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?=$_REQUEST['bank_name']?>" required>
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label">Bank Account No. <span class="red_small">*</span></label>
              <div class="col-md-6">               
                 <input name="acc_no" id="acc_no" type="text" value="<?php echo $_REQUEST['acc_no']?>" class="form-control" required/>
               </div>
             </div>
           </div>
		   
		   <div class="form-group" id="shownew" style="display:none">
           <div class="col-md-6"><label class="col-md-6 control-label">Bank Branch </label>
              <div class="col-md-6">
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control" value="<?=$_REQUEST['bank_branch']?>" required>
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label">IFSC Code <span class="red_small">*</span></label>
              <div class="col-md-6">               
                 <input name="ifsc_code" id="ifsc_code" type="text" value="<?php echo $_REQUEST['ifsc_code']?>" class="form-control" required/>
               </div>
             </div>
           </div>
		   
		   <div class="form-group" id="shownew1" style="display:none">
           <div class="col-md-6"><label class="col-md-6 control-label">Mode of Transfer <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="bank_mode" id="bank_mode" class="form-control" value="<?=$_REQUEST['bank_mode']?>" required>
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">               
                 
               </div>
             </div>
           </div>
		   
		  
		   <div class="form-group" id="cheque" style="display:none">
           <div class="col-md-6"><label class="col-md-6 control-label">Cheque No.<span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="dd_ch_no" id="dd_ch_no" class="form-control" value="<?=$_REQUEST['dd_ch_no']?>"/>
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label">Cheque Date <span class="red_small">*</span></label>
              <div class="col-md-6">               
                 <input type="text" class="form-control span2" name="dd_ch_dt"  id="dd_ch_dt"  value="<?php if(isset($_REQUEST['dd_ch_dt'])){echo $_REQUEST['dd_ch_dt']; } else{}?>" required></div>
               </div>
             </div>
           </div>
		   <?php }?>
		   
		   <?php  if($res_locdet['status'] == 'Paid') {?>
		   
		   <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Payment Mode</label>
              <div class="col-md-6">
          <input type="text" name="payment_mode" id="payment_mode"	 value="<?=$res_locdet['payment_mode']?>" class="form-control" readonly="">	
				 
              </div> 
            </div>
			<?php if($res_locdet['payment_mode'] == 'Cash'){?>
            <div class="col-md-6"><label class="col-md-6 control-label">Transcation Id</label>
			      <div class="col-md-6">
				 <input type="text" name="transcation_id1" id="transcation_id1" class="form-control" value="<?=$res_locdet['transcation_id']?>" readonly > 
				 </div>
               </div>
			   <?php }?>
          </div> 
		  
		  <?php  if($res_locdet['payment_mode'] == 'Bank') {?>
		 <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Bank Name</label>
		    <div class="col-md-6">
                 <input type="text" name="bank_name1" id="bank_name1" class="form-control" value="<?=$res_locdet['bankname']?>" readonly="">
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label">Bank Account No.</label>
              <div class="col-md-6">               
                 <input name="acc_no1" id="acc_no1" type="text" value="<?php echo $res_locdet['account_no']?>" class="form-control" readonly/>
               </div>
             </div>
           </div>
		   
		    <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Bank Branch</label>
		    <div class="col-md-6">
                 <input type="text" name="bank_branch1" id="bank_branch1" class="form-control" value="<?=$res_locdet['bank_branch']?>" readonly="">
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label">IFSC Code</label>
              <div class="col-md-6">               
                 <input name="ifsc_code1" id="ifsc_code1" type="text" value="<?php echo $res_locdet['ifsc_code']?>" class="form-control" readonly/>
               </div>
             </div>
           </div>
		   
		    <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Mode of Transfer</label>
		    <div class="col-md-6">
                 <input type="text" name="transfermode" id="transfermode" class="form-control" value="<?=$res_locdet['bank_transfermode']?>" readonly="">
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">               
                 
               </div>
             </div>
           </div>
		   <?php }?>
		   
		   <?php if($res_locdet['payment_mode'] == 'Cheque') {?>
		   <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Cheque Date</label>
              <div class="col-md-6">
                <input type="text" class="form-control span2" name="dd_ch_dt"  id="dd_ch_dt"  value="<?php echo $res_locdet['dd_date']?>" readonly>
               </div>
             </div>
             <div class="col-md-6"><label class="col-md-6 control-label">Cheque No.</label>
              <div class="col-md-6">  
			  <input type="text" name="dd_ch_no" id="dd_ch_no" class="form-control" value="<?=$res_locdet['dd_chequeno']?>" readonly/>             
                 </div>
               </div>
             </div>
           </div>
		   <?php }?>
		   
		   <?php }?>
		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              
			  <?php if($res_locdet['status'] == 'Pending') {?>
              <input type="submit" class="btn btn-primary" name="Submit" id="" value="Save" title="submit">
			  <input type="hidden" name="sno" id="sno" value="<?=$getid?>" >
			  <?php }?>
             
              
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='locationwise_expense.php?<?=$pagenav?>'">
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


