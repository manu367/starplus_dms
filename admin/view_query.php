<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST[id]);
////// get details of selected product////
$res_locdet=mysqli_query($link1,"SELECT * FROM query_master where id='".$getid."'")or die(mysqli_error($link1));
$row=mysqli_fetch_array($res_locdet);


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
 
 <style>
.red_small{
	color:red;
}
</style>
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
      <h2 align="center"><i class="fa fa-book"></i>&nbsp;&nbsp;View Query</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="return Validate(this);">
          
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Module Problem <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="module" id="module" class="form-control required" required disabled>
                  <option value="">--Please Select--</option>
                  <option value="IMEI" <?php if($row['module']=="IMEI"){ echo "selected";}?>>IMEI</option>
                  <option value="Sale" <?php if($row['module']=="Sale"){ echo "selected";}?>>Sale</option>
                  <option value="Dealer/MD" <?php if($row['module']=="Dealer/MD"){ echo "selected";}?>>Dealer/MD</option>
                  <option value="Report" <?php if($row['module']=="Report"){ echo "selected";}?>>Report</option>
				   <option value="Sales/Return" <?php if($row['module']=="Sales/Return"){ echo "selected";}?>>Sales/Return</option>
                </select>
              </div>
            </div>
			 <div class="col-md-6"><label class="col-md-6 control-label">Problem</label>
            <div class="col-md-6">
                <select  name='problem' id="problem" class='form-control selectpicker required' disabled data-live-search="true"  onChange="document.form1.submit();">
                  <option value=''>--Please Select-</option>
				  <?php
				$model_query="SELECT * FROM problem_master ";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['problem']?>"<?php if($row['problem']==$br['problem']){echo 'selected';}?>><?=$br['problem']?></option>
				<?php
                }
				?>
               </select>
            </div>
          </div>
          </div>
          
         <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Request <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="request" id="request" class="form-control required" disabled required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?php echo $row['request']; ?></textarea>
              </div>
            </div>
			
            <div class="col-md-6"><label class="col-md-6 control-label">Attach Images (Allowed jpg, png, gif,GIF,JPEG,PNG  and Upload upto 2 MB) </label>
			      <div class="col-md-6">
                 <input type="file" name="attach" id="attach" disabled />
				 <?php if($row['attachment']!=''){?> <div id="inline1" ><img src="<?php echo $row['attachment'];?>" height="50px" width="100px"  /></div>   <?php }?> 
                   <input type="hidden" name="attach" id="attach" value="<?php $row['attachment'];?>"> 
				 </div>
               </div>
			
          </div>
		  <div class="form-group">
		  <div class="col-md-6"><label class="col-md-6 control-label">Contact Number<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="phone" type="text" class="digits form-control required" disabled  id="phone" value="<?php echo $row['contact_no']; ?>" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
			<?php if ($row['solution']!='') {?>
			 <div class="col-md-6"><label class="col-md-6 control-label">Solution<span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="solution" id="solution" class="form-control required" disabled  required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?php echo $row['solution']; ?></textarea>
              </div>
			<?php }?>
            </div>
			</div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              
             
              
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