<?php
require_once("../config/config.php");
$today = date('Y-m-d');
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='edit'){
	$sel_usr="select * from coupon_master where id='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysql_error());
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['add']=='ADD'){

	///// check whether coupon code already exist in table or not ////////////////////////////////////////////
	$check = mysqli_query($link1 , "select coupon_code from coupon_master where coupon_code = '".$coupon."' ");
	if(mysqli_num_rows($check) == 0) {

	 $coupon_add="INSERT INTO coupon_master set coupon_code ='".$coupon."',valid_from ='".$fdate."',valid_to= '".$tdate."',amount='".$amount."',status='".$status."',create_date= '".$today."',create_by='".$_SESSION['userid']."' , remark = '".$remark."'  ";

     $res_add=mysqli_query($link1,$coupon_add)or die("error3".mysqli_error($link1)); 
	////// insert in activity table////
	 dailyActivity($_SESSION['userid'],$coupon,"Coupon Master","ADD",$_SERVER['REMOTE_ADDR'],$link1);
	////// return message
	 $msg="You have successfully created coupon  no. ".$coupon;
	 }
	 else {
	 $msg="Coupon code is already existed" ;
	 }
	 
   }
   else if ($_POST['upd']=='Update'){
  
   
    $usr_upd="update coupon_master set valid_from ='".$fdate."',valid_to= '".$tdate."',amount='".$amount."',status='".$status."',remark = '".$remark."' ,  updatedate='".date("Y-m-d H:i:s")."' where id = '".$usrid2."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$coupon,"Coupon Master","UPDATE",$ip,$link1);
	////// return message
	$msg="You have successfully updated coupon details of ".$coupon;
   }
   ///// move to parent page
    header("location:coupon_master.php?msg=".$msg."".$pagenav);
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
</script>
<script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script language="javascript" type="text/javascript">
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		startDate:'<?=$today?>'
		//autoclose: true
	});
});
</script>

<script src="../js/bootstrap-datepicker.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-codiepie"></i> Add Coupon</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Coupon Name <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input type="text" name="coupon" class="required alphanumeric form-control" id="coupon" value="<?=$sel_result['coupon_code']?>"  maxlength="20" required  <?php if($_REQUEST['op']!='add'){?> readonly <?php }?> />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Amount <span class="red_small">*</span></label>
              <div class="col-md-5">
             <input type="text" name="amount" id="amount" class="form-control" value="<?php echo $sel_result['amount'];?> " required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Valid From</label>
              <div class="col-md-5">
                 <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $sel_result['valid_from'];}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Valid To</label>
              <div class="col-md-5">
                <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $sel_result['valid_to'];}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Remark</label>
              <div class="col-md-5">
                <textarea name="remark" id="remark" class="form-control"><?=$sel_result['remark']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
              <div class="col-md-5">
                 <select name='status' id='status' class="form-control selectpicker" data-live-search="true">
                    <option value="active" <?php if($sel_result['status'] =='active') {echo 'selected'; }?>>Activate</option>
                    <option value="deactive" <?php if($sel_result['status'] =='deactive') {echo 'selected'; }?>>Deactivate</option>
                 </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='add'){ ?>
              <input type="submit" class="btn <?=$btncolor?>" name="add" id="add" value="ADD" title="Add Coupon">
              <?php }else{?>
              <input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Coupon Details">
              <?php }?>
              <input type="hidden" name="usrid2"  id="usrid2" value="<?=$sel_result['id']?>" />
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='coupon_master.php?<?=$pagenav?>'">
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