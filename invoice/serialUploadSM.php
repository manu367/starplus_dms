<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
$po_sql="SELECT * FROM stock_movement_master where doc_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

/// move from party
$billfrom=getLocationDetails($po_row['from_location'],"name,city,state",$link1);
$explodevalf=explode("~",$billfrom);
if($explodevalf[0]){ $fromparty=$billfrom; }else{ $fromparty=getAnyDetails($po_row['from_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
/// move to party
$billto=getLocationDetails($po_row['to_location'],"name,city,state",$link1);
$explodeval=explode("~",$billto);
if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getAnyDetails($po_row['to_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
  
  
  
function unixstamp( $excelDateTime ) {
    $d = floor( $excelDateTime ); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
   if ($_FILES["attchfile"]["name"]) {
	require_once "../includes/simplexlsx.class.php";
	$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
	move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload/stock_move/".$now.$_FILES["attchfile"]["name"]);
	$f_name=$now.$_FILES["attchfile"]["name"];
	//////insert into upload file data////////////
	mysqli_query($link1,"DELETE FROM temp_imei_upload WHERE flag='' AND update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	mysqli_query($link1,"INSERT INTO upload_file_data SET file_name='".$f_name."',entry_date='".$today."',entry_time='".$currtime."'");
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
		  $stocktype="".$r[2];
	      $sql="INSERT INTO temp_imei_upload set prod_code='".$partcode."',imei1='".$imei1."',imei2='".$stocktype."',inv_no='".$invno."',inv_date='".$invdate."',update_by='".$_SESSION['userid']."',browserid='".$browserid."',file_id='".$file_id."'";
          mysqli_query($link1,$sql);
		  }
	  }
	}//Close For loop
	//// check excel file is completely uploaded///
	if($eof=='1'){
	    mysqli_query($link1,"UPDATE upload_file_data SET status='1' WHERE id='".$file_id."'");
		header("Location:sm_showtempserial.php?msg=sucess&f_name=$f_name&file_id=$file_id&bdate=$invdate&inv_no=$invno".$pagenav);
		exit;
    }
	else{
	    ////////////delete all un-valid data from temp table////////////////
	    mysqli_query($link1,"DELETE FROM temp_imei_upload WHERE flag='' AND update_by='".$_SESSION['userid']."' AND browserid='".$browserid."'");
		$msg="File is not uploaded Properly.Please Upload it again.";	
		header("Location:stock_move_list.php?msg=".$msg."".$pagenav);
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
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function(){
	$("#frm1").validate();
});
// When the document is ready
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-upload"></i> Upload <?=$imeitag?> For Stock Movement</h2>
            <div style="display:inline-block;float:right">
            	<a href="../templates/UPLOAD_SERIAL_FOR_SM.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a>
            </div><br/>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
      			<?php if($_REQUEST['msg']){?><br>
      			<h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      			<?php }?>
        		<form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          			<div class="form-group">
            			<div class="col-md-12"><label class="col-md-4 control-label">Main Location <span class="red_small">*</span></label>
              				<div class="col-md-4">
              					<textarea name="mainlocation" id="mainlocation" class="form-control" readonly style="resize:none"><?php echo str_replace("~",",",$billfrom);?></textarea>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
                        <div class="col-md-12"><label class="col-md-4 control-label">Move From<span class="red_small">*</span></label>
                            <div class="col-md-4">
                            <textarea name="movefrom" id="movefrom" class="form-control" readonly style="resize:none"><?php echo str_replace("~",",",$fromparty); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12"><label class="col-md-4 control-label">Move To<span class="red_small">*</span></label>
                            <div class="col-md-4">
                            <textarea name="moveto" id="moveto" class="form-control" readonly style="resize:none"><?php echo str_replace("~",",",$toparty); ?></textarea>
                            </div>
                        </div>
                    </div>
                      <div class="form-group">
                        <div class="col-md-12"><label class="col-md-4 control-label">Document No.<span class="red_small">*</span></label>
                          <div class="col-md-4">
                               <input type="text" name="invno" id="invno" class="form-control" value="<?php echo $po_row['doc_no'];?>" readonly/>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-12"><label class="col-md-4 control-label">Document Date</label>
                          <div class="col-md-4">
                          <input type="text" name="invdate" id="invdate" class="form-control" value="<?php echo $po_row['entry_date'];?>" readonly/>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
                          <div class="col-md-4">
                              <div class="input-group">
                                <label class="input-group-btn">
                                    <span class="btn btn-primary">
                                        Browse&hellip; <input type="file" name="attchfile" id="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
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
                          <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='stock_move_list.php?<?=$pagenav?>'">
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