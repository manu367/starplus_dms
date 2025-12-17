<?php
////// Function ID ///////
$fun_id = array("a"=>array(49));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
/////get status//
//$arrstatus = getFullStatus("master",$link1);
@extract($_POST);
////// case 1. if we want to update details
if(isset($_REQUEST['op'])){
	if ($_REQUEST['op']=='Edit'){
		$sel_usr="select * from product_cat_master where catid='".$_REQUEST['id']."' ";
		$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
		$sel_result=mysqli_fetch_assoc($sel_res12);
	}
}
////// case 2. if we want to Add new product category
if(isset($_POST['addButton']) || isset($_POST['updButton'])){
   if ($_POST['addButton']=='ADD'){
   
	if(mysqli_num_rows(mysqli_query($link1,"select catid from product_cat_master where short_code='".$short_code."'"))==0){
		$usr_add="INSERT INTO product_cat_master set cat_name ='".ucwords($product_name)."', product_code = '".$_POST['product_code']."',short_code='".strtoupper($short_code)."',status='".$status."',createdate='".$datetime."',createby='".$_SESSION['userid']."'";
		$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
		$dptid = mysqli_insert_id($link1); 
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$dptid,"PRODUCT","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
		////// return message
		$msg="You have successfully created a product like ".ucwords($product_name);
		$cflag="success";
		$cmsg = "Success";
	}else{
		$msg= "Product Category Short Name is already exist";
		$cflag="warning";
		$cmsg = "Warning";
	}
	
   }else if ($_POST['updButton']=='Update'){
   
   	if((mysqli_num_rows(mysqli_query($link1,"select catid from product_cat_master where short_code='".$short_code."'"))==0) || $short_code==$sel_result['short_code']){
		$usr_upd="update product_cat_master set cat_name ='".ucwords($product_name)."', product_code = '".$_POST['product_code']."',status='".$status."',updatedate='".$datetime."',updateby='".$_SESSION['userid']."' , short_code='".$short_code."' where catid = '".$refid."'";
		$res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$com_name,"PRODUCT","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated product details for ".ucwords($product_name);
		$cflag="success";
		$cmsg = "Success";
	}else{
		$msg= "Product Category Short Name is already exist";
		$cflag="warning";
		$cmsg = "Warning";
	}
	
   }else{
   
   }
   ///// move to parent page
    header("location:productcat_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
		  rules: {
			short_code: {
			  required: true,
			  minlength: 2,
			  maxlength: 3
			}
		  },
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
      <h2 align="center"><i class="fa fa-suitcase fa-lg"></i> <?=$_REQUEST['op']?> Product Category </h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Category Name <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="product_name" class="required mastername form-control cp" id="product_name" value="<?php if(!empty($sel_result['cat_name'])){ echo $sel_result['cat_name'];}?>" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Product Code </label>
              <div class="col-md-6">
                 <input name="product_code" type="text" class="form-control alphanumeric" id="product_code" value="<?php if(!empty($sel_result['product_code'])){ echo $sel_result['product_code'];}?>" maxlength="3"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Short Code <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="short_code" class="required character form-control uc" id="short_code" value="<?php if(!empty($sel_result['short_code'])){ echo $sel_result['short_code'];}?>" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status </label>
              <div class="col-md-6">
                 <select name="status" id="status" class="form-control custom-select">
                    <option value="1" <?php if(!empty($sel_result['status'])){ if($sel_result['status']==1){echo "selected";}}?>>Active</option>
					<option value="2"<?php if(!empty($sel_result['status'])){ if($sel_result['status']==2){echo "selected";}}?>>Deactive</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
               <button class="btn <?=$btncolor?>" id="add" type="submit" name="addButton" value="ADD"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Add</button>
              <?php }else{
           
              	?>
              <button class="btn<?=$btncolor?>" id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Update</button>
              <?php  }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['catid']?>" />
              <button title="Back" type="button" class="btn <?=$btncolor?>" onClick="window.location.href='productcat_master.php?status=<?php if(isset($_REQUEST['status'])){ echo $_REQUEST['status'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
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
