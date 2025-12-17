<?php
require_once("../config/config.php");
$date=date("Y-m-d");
@extract($_POST);
////// final submit form ////

$folder="expense";


if($_POST[Submit]=='Save'){
	$query_code="select MAX(temp_id) as exp from locationwise_expense where location_code = '".$_POST['location']."' ";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysql_error());
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
	$tempid = $arr_result2[0]+1;
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
	
    $doc="EXP".$_REQUEST['location'].$pad; 
	
	
   if($_FILES['attach']['name']!='' )
	{
		
    $file_name = $_FILES['attach']['name'];
	$file_tmp =$_FILES['attach']['tmp_name'];
	$up=move_uploaded_file($file_tmp,$folder."/".time().$file_name);
    $path1=$folder."/".time().$file_name;	
	$img_name1=time().$file_name;
	}
	
	//// insert into location wise expense table ////////////////////////////////////////////////////////
	$sql=mysqli_query($link1,"insert into locationwise_expense set temp_id='".$tempid."',doc_no='".$doc."',location_code='".$location."',expense_date='".$expense_date."',narration='".$narration."',amount='".$amount."', payment_mode = '".$pay_mode."' ,attachment='".$path1."',status='".$status."',entry_date='$date',entry_by='".$_SESSION['userid']."' ,bankname = '".$bank_name."' , account_no = '".$acc_no."' , dd_chequeno = '".$dd_ch_no."' , dd_date = '".$dd_ch_dt."' , transcation_id = '".$transcation_id."',  bank_branch = '".$bank_branch."' , ifsc_code = '".$ifsc_code."' , bank_transfermode = '".$bank_mode."'   ")or die("ER4".mysqli_error($link1)); 
   
    ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$doc,"Expense","ADD",$ip,$link1);
    //return message
	$msg="You have successfully created Location Expense with doc no.".$doc." ";
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
      <h2 align="center"><i class="fa fa-inr"></i>&nbsp;&nbsp;Add New Expense</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post" enctype="multipart/form-data" onSubmit="return Validate(this);">
          
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Location<span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="location" id="location" required class="form-control selectpicker required" data-live-search="true" >
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['location'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						
					}
                    ?>
                 </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-6 control-label">Expense Date<span class="red_small">*</span></label>
             
                <div class="col-md-6">
               <div><input type="text" class="form-control" name="expense_date"  id="expense_date" style="width:160px;" value="<?php if($_REQUEST['expense_date']!='') {echo $_REQUEST['expense_date'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
			   </div>
        </div>
              
            
			 
          </div>
          
         <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Narration <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="narration" id="narration" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$_REQUEST['narration']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"> Attachment (Upload upto 2 MB) </label>
			      <div class="col-md-6">
                 <input type="file" name="attach" id="attach" value="<?=$_REQUEST['attach']?>" />
				 </div>
               </div>
          </div>
		   
		  <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Amount<span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="amount" id="amount" class="number form-control required" value="<?=$_REQUEST['amount']?>" required >
              </div> 
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"> Status<span class="red_small">*</span></label>
			      <div class="col-md-6">
				   <select name="status" id="status" required class="form-control selectpicker required" data-live-search="true"  onChange="document.frm1.submit();">
                     <option value="" selected="selected">Please Select </option>
					<option value="Pending" <?php if($_REQUEST['status'] == 'Pending') { echo "selected";}?>>Pending</option>
					<option value="Paid" <?php if($_REQUEST['status'] == 'Paid') { echo "selected";}?>>Paid</option>
					</select>
				 </div>
               </div>
          </div> 
		  
		  <?php if($_REQUEST['status'] != 'Pending') {?>
		  <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Payment Mode <?php if($_REQUEST['status'] == 'Paid'){?><span class="red_small">*</span> <?php }?></label>
              <div class="col-md-6">
                 <select name="pay_mode" id="pay_mode" <?php if($_REQUEST['status'] == 'Paid'){?>required <?php }?> class="form-control selectpicker <?php if($_REQUEST['status'] == 'Paid'){?> required <?php }?>" data-live-search="true"  onChange="getValidate(this.value);">
                     <option value="" selected="selected">Please Select </option>
					<option value="Cash" <?php if($_REQUEST['pay_mode'] == 'Cash') { echo "selected";}?>>Cash</option>
					<option value="Cheque" <?php if($_REQUEST['pay_mode'] == 'Cheque') { echo "selected";}?>>Cheque</option>
					<option value="Bank" <?php if($_REQUEST['pay_mode'] == 'Bank') { echo "selected";}?>>Bank Transfer</option>
					</select>
				
				
              </div> 
            </div>
			
            <div class="col-md-6" id="transcation" style="display:none"><label class="col-md-6 control-label">Transcation Id / Ref No. <span class="red_small">*</span></label>
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
		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="Submit" id="" value="Save" title="submit">
             
              
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