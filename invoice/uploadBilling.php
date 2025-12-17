<?php
require_once("../config/config.php");
function unixstamp( $excelDateTime ) {
    $d = floor( $excelDateTime ); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
///// get parent location details
$parentlocdet=explode("~",getLocationDetails($_REQUEST['biillingFrom'],"name,state",$link1));
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
   if ($_FILES["attchfile"]["name"]) {
	require_once "../includes/simplexlsx.class.php";
	$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
	move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload/bill_upload/".$now.$_FILES["attchfile"]["name"]);
	$f_name=$now.$_FILES["attchfile"]["name"];
	//////insert into upload file data////////////
	mysqli_query($link1,"delete from temp_bill_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	mysqli_query($link1,"insert into upload_file_data set file_name='".$f_name."',entry_date='".$today."',entry_time='".$currtime."'");
	$file_id=mysqli_insert_id($link1);
	list($cols) = $xlsx->dimension();	
	foreach( $xlsx->rows() as $k => $r) {
	 if ($k == 0 || $k == 1) continue; // skip first row 
	  for( $i = 0; $i < count($k); $i++)
	  {
		  /// check excel row data
	      if($r[0]=='' && $r[1]=='' && $r[2]==''){
			  
		  }
		  else if($r[0]=="EOF"){
		       $eof="1";
		  }else{
	      ////Make Variable for each element of excel//////
		  $partcode=$r[0];
		  $imei1="".$r[1];
		  $imei2="".$r[2];
	  
	      $sql="INSERT INTO temp_bill_upload set bill_from='".$biillingFrom."',bill_to='".$biillingTo."',prod_code='".$partcode."',imei1='".$imei1."',imei2='".$imei2."',bill_date='".$billdate."',update_by='".$_SESSION['userid']."',browserid='".$browserid."',file_id='".$file_id."'";
          mysqli_query($link1,$sql);
		  }
	  }
	}//Close For loop
	//// check excel file is completely uploaded///
	if($eof=='1'){
	    mysqli_query($link1,"update upload_file_data set status='1' where id='".$file_id."'");
		header("Location:showtempbilldata.php?msg=sucess&f_name=$f_name&file_id=$file_id&fromloc=$biillingFrom&toloc=$biillingTo&bdate=$billdate&discount=$discount&tax=$tax".$pagenav);
		exit;
    }
	else{
	    ////////////delete all un-valid data from temp table////////////////
	    mysqli_query($link1,"delete from temp_bill_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
		$msg="File is not uploaded Properly.Please Upload it again.";	
		header("Location:uploadBilling.php?msg=".$msg."".$pagenav);
        exit;
	}
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
 <script>
  $(document).ready(function(){
	  $('#myTable').dataTable();
  });
  $(document).ready(function(){
	  $("#frm1").validate();
  });
  	// When the document is ready
$(document).ready(function () {
	$('#billdate').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$row['sale_date']?>",
        //endDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
  </script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> Upload Billing</h2><div style="display:inline-block;float:right"><a href="../templates/UPLOAD_BILL.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Billing From <span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="biillingFrom" id="biillingFrom" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                <option value="">--Select--</option>
                <?php 
			    $sql_parent="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
				$res_parent=mysqli_query($link1,$sql_parent);
				while($result_parent=mysqli_fetch_array($res_parent)){
	                  $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[location_id]'"));
                ?>
                <option data-tokens="<?=$party_det['name']." | ".$result_parent['uid']?>" value="<?=$result_parent['location_id']?>" <?php if($result_parent['location_id']==$_REQUEST['biillingFrom'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['location_id']?></option>
                <?php
				}
                ?>
              </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Billing To <span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="biillingTo" id="biillingTo" required class="form-control selectpicker required" data-live-search="true">
                <option value="">--Select--</option>
                <?php 
					$sql_chl="select * from mapped_master where uid='$_REQUEST[biillingFrom]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[mapped_code]'"));
	                      if($party_det[id_type]!='HO'){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl[mapped_code]?>" <?php if($result_chl[mapped_code]==$_REQUEST[biillingTo])echo "selected";?> >
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
            <div class="col-md-12"><label class="col-md-4 control-label">Bill Date<span class="red_small">*</span></label>
              <div class="col-md-4 input-append date">
                  <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="billdate"  id="billdate" style="width:280px;" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
            </div>
          </div>         
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div class="input-group">
                    <label class="input-group-btn">
                        <span class="btn btn-primary">
                            Browse&hellip; <input type="file" name="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        </span>
                    </label>
                    <input type="text" class="form-control" name="billfile"  id="billfile" readonly>
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='corporateInvoice.php?<?=$pagenav?>'">
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