<?php
////// Function ID ///////
$fun_id = array("a"=>array(3));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
$date=date("Y-m-d");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
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
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-reply"></i> Return Report </h2><br/>
   <div class="form-group" id="page-wrap" style="margin-left:10px;">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="get">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
          </div>
        </div>
		
		
		<div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Return Type <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="return_type" id="return_type" required class="form-control required" onChange="document.frm1.submit();" >
					 <option value="PR" <?php if($_REQUEST['return_type'] == "PR"){ echo "selected"; } ?> > Purchase Return </option>
					 <option value="VR" <?php if($_REQUEST['return_type'] == "VR"){ echo "selected"; } ?> > Vendor Return </option>
                 </select>
              </div>
            </div>
           </div><!--close form group--> 
		
		<?php if($_REQUEST['return_type'] == "PR"){ ?>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">From Location <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                      if($party_det['id_type']!='HO'){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?> >
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
			<?php }else{ ?>
			
			<div class="form-group">
				<div class="col-md-10"><label class="col-md-3 control-label">From Vendor <span style="color:#F00">*</span></label>
				  <div class="col-md-9">
					 <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
						<option value="" selected="selected">Please Select </option>
						<?php 
						$sql_ch7="select * from vendor_master where status='Active' and id!='' ";
						$res_ch7=mysqli_query($link1,$sql_ch7);
						while($result_ch7=mysqli_fetch_array($res_ch7)){
							  ?>
						<option data-tokens="<?=$result_ch7['name']." | ".$result_ch7['location_id']?>" value="<?=$result_ch7['id']?>" <?php if($result_ch7['id']==$_REQUEST['po_from'])echo "selected";?> >
						   <?=$result_ch7['name']." | ".$result_ch7['city']." | ".$result_ch7['state']." | ".$result_ch7['id']?>
						</option>
						<?php
						}
						?>
					 </select>
				  </div>
				</div>
			</div>
			
			<?php } ?>
			
			<?php if($_REQUEST['return_type'] == "PR"){ ?>
			
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">To Location <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select uid from mapped_master where mapped_code='$_REQUEST[po_from]'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[uid]'"));
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_parent['city']?>" value="<?=$result_parent['uid']?>" <?php if($result_parent['uid']==$_REQUEST['po_to'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['uid']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
              </div>
            </div>
           </div>
		   
		   <?php }else{ ?>
		   
		   <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label"> To Vendor <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_ch8="select * from access_location where uid='$_SESSION[userid]' and status='Y' and location_id like '%HO%'";
					$res_ch8=mysqli_query($link1,$sql_ch8);
					while($result_ch8=mysqli_fetch_array($res_ch8)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_ch8[location_id]'"));
	                      if($party_det['id_type']=='HO'){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_ch8['location_id']?>" value="<?=$result_ch8['location_id']?>" <?php if($result_ch8['location_id']==$_REQUEST['po_to'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_ch8['location_id']?>
                    </option>
                    <?php
						  }
					}
                    ?>
                 </select>
              </div>
            </div>
          </div>
		   
		   <?php } ?>
		   
			 <br><br>
			 <div class="form-group">
				 <div class="col-md-12" style="text-align:center;">
					 <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
					 <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
					 <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
				 </div>
			 </div><!--close form group--> 
		 
		     <br><br>
        <div class="form-group">
		  <div class="col-md-12" style="text-align:center;">
               <?php
					if($_GET['Submit']=="GO"){
						if($_REQUEST['return_type'] == "PR"){
			   ?>
              <div style="color:#FF0033"> <a href="excelexport.php?rname=<?=base64_encode("purchase_return_excel")?>&rheader=<?=base64_encode("Purchase Return")?>&fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&floc=<?=base64_encode($_REQUEST['po_from'])?>&tloc=<?=base64_encode($_REQUEST['po_to'])?>" title="Export detail in excel"><i class="fa fa-file-excel-o fa-2x" title="Export detail details in excel"></i> Purchase Return </a></div>
               <?php
					}else{
				?>
				<div style="color:#FF0033"> <a href="excelexport.php?rname=<?=base64_encode("vendor_return_excel")?>&rheader=<?=base64_encode("Vendor Return")?>&fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&floc=<?=base64_encode($_REQUEST['po_from'])?>&tloc=<?=base64_encode($_REQUEST['po_to'])?>&type=<?=base64_encode($_REQUEST['return_type']);?>" title="Export detail in excel"><i class="fa fa-file-excel-o fa-2x" title="Export detail details in excel"></i> Vendor Return </a></div>
				<?php
						}
					}
				?>
          </div>
	    </div><!--close form group-->
         </form>
  </div><!--close panel group-->
  
   
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>