<?php
require_once("../config/config.php");
$refid = base64_decode($_REQUEST['id']);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='edit'){
	 $sel_usr="select * from product_delivery_matrix where id='".$refid."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['addButton']=='ADD'){}
   
   else if ($_POST['updButton']=='Update'){
    $usr_upd="update product_delivery_matrix set from_location ='".$from_location."',to_location='".$to_location."',productcategory='".$prod_cat."',delivery_days='".$delivery_days."',update_date='".$datetime."',update_by='".$_SESSION['userid']."' where id = '".$refid."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$refid,"Delivery Matrix","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated product delivery matrix details";
	$cflag = "success";
	$cmsg = "Success";
   }
   ///// move to parent page
    header("location:product_delivery.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="google" content="notranslate">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
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
      <h2 align="center"><i class="fa fa-car"></i> Edit Product Delivery Matrix</h2><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Product Category </label>
              <div class="col-md-6">
               <select name="prod_cat" id="prod_cat" class="form-control required custom-select">
                  <option value="">Please Select</option>
                  <?php
                $res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']."~".$row_pro['cat_name']?>"<?php if(isset($sel_result['productcategory'])){ if($row_pro['catid']==$sel_result['productcategory']){ echo 'selected'; }}?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
                </select>          
              </div>
            </div>
          </div>
          
		<div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">From Location</label>
              <div class="col-md-6">
                 <select name="from_location" id="from_location" required class="form-control selectpicker" data-live-search="true">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_ch1="select city,state from district_master where status='A' group by city,state order by city,state";
					$res_ch1=mysqli_query($link1,$sql_ch1);
					while($result_ch1=mysqli_fetch_array($res_ch1)){
                          ?>
                    <option data-tokens="<?=$result_ch1['city']." | ".$result_ch1['state']?>" value="<?=$result_ch1['city']?>" <?php if($result_ch1['city']==$sel_result['from_location'])echo "selected";?> >
                       <?=$result_ch1['city']." | ".$result_ch1['state']?>
                    </option>
                    <?php
					}
                    ?>
                 </select> 
              </div>
            </div>
          </div>          
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">To Location</label>
              <div class="col-md-6">
                 <select name="to_location" id="to_location" required class="form-control selectpicker" data-live-search="true">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_ch1="select city,state from district_master where status='A' group by city,state order by city,state";
					$res_ch1=mysqli_query($link1,$sql_ch1);
					while($result_ch1=mysqli_fetch_array($res_ch1)){
                          ?>
                    <option data-tokens="<?=$result_ch1['city']." | ".$result_ch1['state']?>" value="<?=$result_ch1['city']?>" <?php if($result_ch1['city']==$sel_result['to_location'])echo "selected";?> >
                       <?=$result_ch1['city']." | ".$result_ch1['state']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Delivery Days</label>
              <div class="col-md-6">
                 <input name="delivery_days" id="delivery_days" class="form-control number" type="text" value="<?=$sel_result['delivery_days']?>"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <!--<button class="btn btn-primary" id="add" type="submit" name="addButton" value="ADD"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Add</button>-->
              <?php }else{
              	?>
              <button class="btn <?=$btncolor?>" id="upd" type="submit" name="updButton" value="Update"><i class="fa fa-retweet fa-lg"></i>&nbsp;&nbsp;Update</button>
              <?php  }?>
              <button title="Back" type="button" class="btn <?=$btncolor?>" onClick="window.location.href='product_delivery.php?from_location=<?php if(!empty($_REQUEST['from_location'])){ echo $_REQUEST['from_location'];}?>&to_location=<?php if(!empty($_REQUEST['to_location'])){ echo $_REQUEST['to_location'];}?>&prod_cat=<?php if(!empty($_REQUEST['prod_cat'])){ echo $_REQUEST['prod_cat'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
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