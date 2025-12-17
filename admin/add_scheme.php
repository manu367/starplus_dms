<?php
require_once("../config/config.php");
$date=date("Y-m-d");
@extract($_POST);
////// final submit form ////

$folder="query";
$allowedExts = array("gif", "jpeg", "jpg", "png","PNG","GIF","JPEG","JPG");

if($_POST[Submit]=='Save'){
	$query_code="select MAX(id) as qc from query_master";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
	
    $query="QUERY".$pad; 
	
	
   if($_FILES['attach']['name']!='' )
	{
		
	  $temp = explode(".", $_FILES["attach"]["name"]);
	 $extension = end($temp);
	 $f_size=$_FILES["attach"]["size"];
	  
	  if(!in_array($extension, $allowedExts))
	 {
		 $msgg=".".$extension." ". "not allowed";
		 header("Location:add_query.php?msg=$msgg&sts=fail");
	 }
	 
	
	 
	 
	 if ($_FILES["attach"]["size"]>2097152)
	 {
		 $msgg="File size should be less than or equal to 2 mb";
		 header("Location:add_query.php?msg=$msgg&sts=fail");
	 }
	 
	 
	else
	{ 
    $file_name = $_FILES['attach']['name'];
	$file_tmp =$_FILES['attach']['tmp_name'];
	$up=move_uploaded_file($file_tmp,"../".$folder."/".time().$file_name);
    $path1="../".$folder."/".time().$file_name;	
	$img_name1=time().$file_name;
	
if($up)
{
 
	$sql=mysqli_query($link1,"insert into query_master set query='".$query."',temp='".$code_id."',module='".$module."',problem='".$problem."',contact_no='".$phone."',request='".$request."',attachment='".$path1."',status='pending',entry_date='$date',entry_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1)); 
   
    ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$module,"QUERY","ADD",$ip,$link1,"");
    //return message
	$msg="You have successfully created a new Query with module ".$module." ";
	///// move to parent page
    header("Location:query_master.php?msg=".$msg."".$pagenav);
	exit;
 }	
	
 	
  	

		
	
}
	
}
else{
$sql=mysqli_query($link1,"insert into query_master set query='".$query."',temp='".$code_id."', module='".$module."',problem='".$problem."',contact_no='".$phone."',request='".$request."',status='active',entry_date='$date',entry_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1)); 
   
    ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$module,"QUERY","ADD",$ip,$link1,"");
    //return message
	$msg="You have successfully created a new Query with module ".$module." ";
	///// move to parent page
    header("Location:query_master.php?msg=".$msg."".$pagenav);
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
    });
 </script>
 <script>
var _validFileExtensions = [".gif", ".jpeg", ".jpg", ".png",".PNG",".GIF",".JPEG",".JPG"];    
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
</script>
 <style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function(){
    $("#frm2").validate();
});
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
</script>

 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-book"></i>&nbsp;&nbsp;Add New Scheme</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="return Validate(this);">
           <div class="form-group">
          <div class="col-md-12">
              <label class="col-md-3 control-label">From Date</label>
             
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
           
          </div>
        </div>
         
          
         <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Remark <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="remark" id="remark" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Scheme  Attach  (Allowed jpg, png, gif,GIF,JPEG,PNG  and Upload upto 2 MB) <span class="red_small">*</span></label>
			      <div class="col-md-6">
                 <input type="file" name="attach" id="attach" required />
				 </div>
               </div>
          </div>
		   <div class="form-group">
		  <div class="col-md-6"><label class="col-md-6 control-label">Enter Scheme  Number<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="phone" type="text" class="digits form-control required"  id="phone" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
			</div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="Submit" id="" value="Save" title="submit">
             
              
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='query_master.php?<?=$pagenav?>'">
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