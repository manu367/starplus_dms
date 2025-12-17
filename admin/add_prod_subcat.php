<?php
////// Function ID ///////
$fun_id = array("a"=>array(50));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);

/////get status//
//$arrstatus = getFullStatus("master",$link1);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	 $sel_usr="select * from product_sub_category where psubcatid='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['addButton']=='ADD'){
   	
	if(mysqli_num_rows(mysqli_query($link1,"select psubcatid from product_sub_category where prod_sub_cat='".$prodsub_name."'"))==0){  
	 	
   	$product_expld = explode("~",$prod_cat);
    $usr_add="INSERT INTO product_sub_category set prod_sub_cat ='".ucwords($prodsub_name)."',productid='".$product_expld[0]."',product_category='".$product_expld[1]."',status='".$status."',createdate='".$datetime."',createby='".$_SESSION['userid']."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"PRODUCT SUB CAT","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created a Product Sub Category like ".ucwords($prodsub_name);
	$cflag = "success";
	$cmsg = "Success";

	}else{
		$msg = ucwords($prodsub_name)." is already exists";
		$cflag = "warning";
		$cmsg = "Warning";
	} 
	  
}else if ($_POST['updButton']=='Update'){

	if((mysqli_num_rows(mysqli_query($link1,"select psubcatid from product_sub_category where prod_sub_cat='".$prodsub_name."'"))==0) || $prodsub_name==$sel_result['prod_sub_cat']){  
		$product_expld = explode("~",$prod_cat);
		$usr_upd="update product_sub_category set prod_sub_cat ='".ucwords($prodsub_name)."',status='".$status."',productid='".$product_expld[0]."',product_category='".$product_expld[1]."',updatedate='".$datetime."',updateby='".$_SESSION['userid']."' where psubcatid = '".$refid."'";
		$res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$refid,"PRODUCT SUB CAT","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated Product Sub Category details for ".ucwords($prodsub_name);
		$cflag = "success";
		$cmsg = "Success";
	}else{
		$msg = ucwords($prodsub_name)." is already exists";
		$cflag = "warning";
		$cmsg = "Warning";
	} 
	
   }
   ///// move to parent page
    header("location:prod_subcat_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	 var spinner = $('#loader');
        $("#frm1").validate({
		  submitHandler: function (form) {
			if(!this.wasSent){
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
								  .attr('disabled', 'disabled')
								  .addClass('disabled');
				spinner.show();				  
				form.submit();
			} else {
				return false;
			}
          }
		});
});
</script>
<script src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
     include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-cog"></i> <?=$_REQUEST['op']?> Product Sub Category </h2><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label"> Product Category </label>
              <div class="col-md-6">
               <select name="prod_cat" id="prod_cat" class="form-control required custom-select">
                  <option value="">Please Select</option>
                  <?php
                $res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']."~".$row_pro['cat_name']?>"<?php if(isset($sel_result['productid'])){ if($row_pro['catid']==$sel_result['productid']){ echo 'selected'; }}?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
                </select>          
              </div>
            </div>
          </div>
          
		<div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Product Sub Category Name</label>
              <div class="col-md-6">
                 <input type="text" name="prodsub_name" class="required form-control mastername cp" id="prodsub_name" value="<?php if(!empty($sel_result['prod_sub_cat'])){ echo $sel_result['prod_sub_cat'];}?>" required/> 
              </div>
            </div>
          </div>          
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status</label>
              <div class="col-md-6">
                 <select name="status" id="status" class="form-control custom-select">
                  <option value="1"<?php if(isset($sel_result['status'])){ if(($sel_result['status']=="1")){echo "selected";}}?>>Active</option>
              		<option value="2"<?php if(isset($sel_result['status'])){ if(($sel_result['status']=="2")){echo "selected";}}?>>Deactive</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <button class="btn <?=$btncolor?>" id="add" type="submit" name="addButton" value="ADD"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Add</button>
              <?php }else{
					  	
              	?>
              <button class="btn <?=$btncolor?>" id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Update</button>
              <?php  }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['psubcatid']?>" />
              <button title="Back" type="button" class="btn <?=$btncolor?>" onClick="window.location.href='prod_subcat_master.php?status=<?php if(isset($_REQUEST['status'])){ echo $_REQUEST['status'];}?>&prod_cat=<?php if(isset($_REQUEST['prod_cat'])){ echo $_REQUEST['prod_cat'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
            </div>
          </div>
    </form>
      </div>

    </div>
    
  </div>
</div>
<div id="loader"></div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>