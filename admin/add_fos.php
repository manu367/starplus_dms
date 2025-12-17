<?php
require_once("../config/config.php");
$date=date("Y-m-d");
@extract($_POST);
////// final submit form ////

$folder="fos";
$allowedExts = array("gif", "jpeg", "jpg", "png","PNG","GIF","JPEG","JPG");
if($_POST[Submit]=='Save'){
	
	////implode identity proof
	$mode = implode(",", $_POST['iden']); 
	
	////generate username and insert into admin_users table 
	$query_code="select MAX(uid) as qc from admin_users";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysql_error());
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
	$admiCode=substr(strtoupper(BRANDNAME),0,2)."USR".$pad;
	$shuffled=substr(strtoupper(BRANDNAME),0,2)."USR".$pad."@123";
	if($_FILES['fos_photo']['name']!='' || ($_FILES['ifsc_img']['name']!='') || ($_FILES['iden_no']['name']!='') )
	{
   if($_FILES['fos_photo']['name']!='' )
	{
	  $temp = explode(".", $_FILES["fos_photo"]["name"]);
	 $extension = end($temp);
	  if(!in_array($extension, $allowedExts))
	 {
		 $msgg=".".$extension." ". "not allowed";
		 header("Location:add_fos.php?msg=$msgg&sts=fail");
	 }
	 
	  if ($_FILES["fos_photo"]["size"]>2097152)
	 {
		 $msgg="File size should be less than or equal to 2 mb";
		 header("Location:add_fos.php?msg=$msgg&sts=fail");
	 }
	 $file_name = $_FILES['fos_photo']['name'];
	$file_tmp =$_FILES['fos_photo']['tmp_name'];
	$up1=move_uploaded_file($file_tmp,"../".$folder."/".time().$file_name);
	$path1="../".$folder."/".time().$file_name;	
	
	}
	 if($_FILES['ifsc_img']['name']!='')
	 {
	 $ifsc_file=explode(".",$_FILES['ifsc_img']['name']);
	 $ifsc_ext=end($ifsc_file);
	 if(!in_array($ifsc_ext, $allowedExts))
	 {
		 $msgg=".".$ifsc_ext." ". "not allowed";
		 header("Location:add_fos.php?msg=$msgg&sts=fail");
	 }
	  if ($_FILES["ifsc_img"]["size"]>2097152)
	 {
		 $msgg="File size should be less than or equal to 2 mb";
		 header("Location:add_fos.php?msg=$msgg&sts=fail");
	 }
	 $ifs_name = $_FILES['ifsc_img']['name'];
	$ifs_tmp =$_FILES['ifsc_img']['tmp_name'];
	$up2=move_uploaded_file($ifs_tmp,"../".$folder."/".time().$ifs_name);
	$path="../".$folder."/".time().$ifs_name;	
	 }
	 if($_FILES['iden_no']['name']!='')
	 {
	 $iden_file=explode(".",$_FILES['iden_no']['name']);
	  $iden_ext=end($iden_file);
	  if(!in_array($iden_ext, $allowedExts))
	 {
		 $msgg=".".$iden_ext." ". "not allowed";
		 header("Location:add_fos.php?msg=$msgg&sts=fail");
	 }
	 
	 if ($_FILES["iden_no"]["size"]>2097152)
	 {
		 $msgg="File size should be less than or equal to 2 mb";
		 header("Location:add_fos.php?msg=$msgg&sts=fail");
	 }
	 $iden_name = $_FILES['iden_no']['name'];
	$iden_tmp =$_FILES['iden_no']['tmp_name'];
	$up3=move_uploaded_file($iden_tmp,"../".$folder."/".time().$iden_name);
	$path2="../".$folder."/".time().$iden_name;
	 }
	 
	
	 
	 
	 
	
	
	
	
 	
  	
if($up1 || $up2 || $up3)
{
 
	$sql=mysqli_query($link1,"insert into fos_master set name='".$fos_name."',contact_no='".$contact."',dob='".$fdate."',email='".$email."',local_add='".$local_address."',per_add='".$per_address."',state='".$state."',doj='".$tdate."',f_type='".$type."',holder_name='".$account."',acc_no='".$acc_no."',br_add='".$branch_addr."',br_name='".$branch."',ifsc_code='".$ifsc."',fos_file='".$path1."',cheque_file='".$path."',identity_file='".$path2."',identity='".$mode."',acknowledge='".$terms."',username='".$admiCode."',status='Active',entry_date='$datetime',entry_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1)); 
   
   if($sql)
     {
	   $usr_add=mysqli_query($link1,"INSERT INTO admin_users set username ='".$admiCode."',password='".$shuffled."',name= '".$fos_name."',utype='FOS',phone='".$contact."',emailid= '".$email."',status='active',createdate='".$datetime."'");
     }
	 ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$fos_name,"FOS","ADD",$ip,$link1);
    //return message
	$msg="You have successfully created a new FOS with ".$fos_name." ";
	///// move to parent page
   header("Location:fos_master.php?msg=".$msg."".$pagenav);
	exit;
 }
		
	

	
}
else
{
     
	   $sql=mysqli_query($link1,"insert into fos_master set name='".$fos_name."',contact_no='".$contact."',dob='".$fdate."',email='".$email."',local_add='".$local_address."',per_add='".$per_address."',state='".$state."',doj='".$tdate."',f_type='".$type."',holder_name='".$account."',acc_no='".$acc_no."',br_add='".$branch_addr."',br_name='".$branch."',ifsc_code='".$ifsc."',identity='".$mode."',acknowledge='".$terms."',username='".$admiCode."',status='Active',entry_date='$datetime',entry_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1)); 
     
      if($sql)
      {
	   $usr_add=mysqli_query($link1,"INSERT INTO admin_users set username ='".$admiCode."',password='".$shuffled."',name= '".$fos_name."',utype='FOS',phone='".$contact."',emailid= '".$email."',status='active',createdate='".$datetime."'");
        }
    
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$fos_name,"FOS","ADD",$ip,$link1);
	//return message
	$msg="You have successfully created a new FOS with ".$fos_name." ";
	///// move to parent page
   header("Location:fos_master.php?msg=".$msg."".$pagenav);
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
 <script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
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
      <h2 align="center"><i class="fa fa-bank"></i> Add New FOS</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="return Validate(this);">
          
		  <div class="form-group">
		   <div class="col-md-6" ><label class="col-md-6 control-label" style="padding-right:70px;">FOS Name<span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="fos_name" class="form-control required"  id="fos_name" value="" required/>
              </div>
			  </div>
			  <div class="col-md-6"><label class="col-md-6 control-label " style="padding-right:58px;">Contact No.<span class="red_small">*</span></label>
              <div class="col-md-6" >
                <input type="text" name="contact" class="form-control required" maxlength="10"  id="contact" value="" onKeyPress="return onlyNumbers(this.value);" required/>
              </div>
            </div>
			</div>
            
            
         
		 
		  <div class="form-group">
          <div class="col-md-6">
              <label class="col-md-6 control-label" style="padding-right:58px;">Date of Birth </label>
             <div class="col-md-6 input-append date">
  					<input type="text" class="form-control span2" name="fdate"  id="fdate"  value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required/>
			   </div>
                 </div>
				 <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:68px;" >E-Mail id<span class="red_small">*</span>&nbsp;&nbsp;</label>
              <div class="col-md-6">
                <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');">
              </div>
            </div>
        </div>
		
		<div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:36px;">  Local Address <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="local_address" id="local_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Permanent Address <span class="red_small">*</span></label>
              <div class="col-md-6">
               <textarea name="per_address" id="per_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
          </div>
		  
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:100px;">State<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <?PHP 
		  $query1="select distinct state from state_master order by state";
		  $result=mysqli_query($link1,$query1);
		  ?>
             <select name="state" id="state"  class="form-control " required>
              <option value="">--Please Select--</option>
              <?php
			  while($row=mysqli_fetch_array($result))
			 {
			 ?>
              <option value="<?=$row['state'];?>">
              <?=$row['state'];?>
              </option>
              <?php
			}
			?>
            </select>
            </div>
			 </div>
			 
			<div class="col-md-6">
              <label class="col-md-6 control-label" style="padding-right:30px;">Date of Joining</label>
                 <div class="col-md-6 input-append date">
  					<input type="text" class="form-control span2" name="tdate"  id="tdate"  value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" required/>
			      </div>
               </div>
         </div>
          
          
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:37px;">FOS Type<span class="red_small">*</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
              <div class="col-md-6" id="parentdiv">
                 <select name="type" id="type" required class="form-control required">
                    <option value="">--Please Select--</option>
                    <option value="NONE">NONE</option>
                 </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-6 control-label">(Upload FOS Photograph upto 2 MB, Allowed jpg, png, gif,GIF,JPEG,PNG ) </label>
            <div class="col-md-6">
                 <input type="file" name="fos_photo" id="fos_photo" />
               </div>
			   </div>
          </div>
		  
          <h2 align="center">Bank Details</h2>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label" style="padding-left:12px;">Name of Account  Holder<span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="account" type="text" class="form-control required" id="account" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:8px;">Account Number <span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="acc_no" type="text" class="form-control required" id="acc_no" required  onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
          </div>
		  
		  <div class="form-group">
		  <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:25px;">Branch Address <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="branch_addr" id="branch_addr" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
           
            <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:33px;">Branch Name<span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="branch"   required class="digits form-control"   id="branch">
              </div>
            </div>
          </div>
		  
		  
		 
          
		  
          <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label" style="padding-right:70px;">IFSC Code </label>
              <div class="col-md-6">
                <input type="text" name="ifsc" id="ifsc" class="form-control">
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-6 control-label">(Upload Blank Cheque  upto 2 MB, Allowed jpg, png, gif,GIF,JPEG,PNG ) </label>
            <div class="col-md-6">
               <input type="file" name="ifsc_img" id="ifsc_img" />(2MB)
              </div>
			  </div>
          </div>
		  
		  <div class="form-group">
			  <div class="col-md-6">
                 <label class="col-md-6 control-label" > Proof of Identity Number (Upload upto 2 MB, Allowed jpg,png, gif,GIF,JPEG,PNG )<strong><span style="color:red"></span></strong></label>
				    <div class="col-md-6">
                       <input type="file" name="iden_no" id="iden_no" />
                           </div>
                </div>
		   </div>
		   
		  <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm1.iden)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm1.iden)" value="Uncheck All" /></div> 
          <table id="myTable" class="table table-hover">
            <thead>
                  <tr>
                    <th style="border:none;" >Proof of Identity </th>
                  </tr>
                </thead>
                <tbody>
                 <?php
				  $k=1;
				   $res_loctype=mysqli_query($link1,"select * from identity"); 
				   while($row_loctype=mysqli_fetch_assoc($res_loctype)){
				   	if($k%5==1){   
				  ?>
                  <tr>
                  <?php }?>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input style="width:20px" type='checkbox'   name="iden[]" id='iden' value="<?=$row_loctype['identity_proof']?>"/> <?=$row_loctype['identity_proof']?></td>
                    <?php if($k/5==0){?>
                    </tr>
                  <?php 
				          }
						  $k++;
				   }
				  ?>  
                  
                </tbody>
              </table>
              </div>
			  
			   <div class="form-group">
			  <div class="col-md-6" style="padding-left:105px;" >
			  <input type="checkbox" name="terms" id="terms" required>
			   Above Information is true I have Knowledge.
             </div>
			</div>
			
		  <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='fos_master.php?<?=$pagenav?>'">
            </div>
          </div>
    </form>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>