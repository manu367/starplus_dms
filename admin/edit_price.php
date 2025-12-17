<?php
////// Function ID ///////
$fun_id = array("a"=>array(54));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$getid=base64_decode($_REQUEST[id]);
////// get details of selected product////
$res_locdet=mysqli_query($link1,"SELECT * FROM price_master where id='".$getid."'")or die(mysqli_error($link1));
$row=mysqli_fetch_array($res_locdet);
$ref=$row[state]."~".$row[location_type]."~".$row[product_code];
////// final submit form ////
if($_POST['Submit']=="Update"){

   
if(mysqli_query($link1,"update  price_master set mrp='$_POST[mrp]',price='$_POST[price]',combo_price='$_POST[combo_price]',status='$_POST[status]',update_date='$datetime',update_by='$_SESSION[userid]' where id='$getid' ")or die("ER4".mysqli_error($link1)))
  
		{
$sql=(mysqli_query($link1,"insert into price_history  set type_id='$getid' ,ref_no='$ref', mrp='$_POST[mrp]',price='$_POST[price]',combo_price='$_POST[combo_price]',action='UPDATE',modify_date='$datetime'  ")or die("ER4".mysqli_error($link1)));
		   
	 ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_POST[product_code],"PRICE","UPDATE",$ip,$link1,"");
	
	//return message
	$msg="You have successfully updated Price";
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
	///// move to parent page
    header("Location:price_master.php?msg=".$msg."".$pagenav);
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
 <script type="text/javascript" src="../js/ajax.js"></script>
 
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
      <h2 align="center"><i class="fa fa-inr"></i>&nbsp;&nbsp;Edit Price</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-5">
              <input type="text" name="state" id="state" class="form-control"  disabled value="<?php echo $row['state']; ?>"  required />
           
          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Location Type<span class="red_small">*</span></label>
              <div class="col-md-5">
                <input type="text" name="loc" id="loc" class="form-control" disabled value="<?php echo $row['location_type']; ?>"  required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Mrp<span class="red_small">*</span></label>
              <div class="col-md-5">
               
	          <input type="text" name="mrp" id="mrp" class="form-control" placeholder="0.00" onKeyPress="return onlyFloatNum(event);" value="<?php echo $row['mrp']; ?>"  required />
            
                 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Price<span class="red_small">*</span></label>
              <div class="col-md-5">
                  <input type="text" name="price" id="price" class="form-control" placeholder="0.00" onKeyPress="return onlyFloatNum(event);"  value="<?php echo $row['price']; ?>"   required/>
              </div>
            </div>
            
          </div>
          
          
		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="status" class="form-control required" required >
				 <option value="">--Plaese Select--</option>
	           <option value="active"<?php if($row['status']=="active"){ echo "selected";}?>>Active</option>
	           <option value="deactive"<?php if($row['status']=="deactive"){ echo "selected";}?>>Deactive</option>
	           
            </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Price in combo <span class="red_small">*</span></label>
              <div class="col-md-5">
                   <input type="text" name="combo_price" id="combo_price" class="form-control" placeholder="0.00" onKeyPress="return onlyFloatNum(event);" value="<?php echo $row['combo_price']; ?>"   required/>
              </div>
            </div>
            
            
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <textarea name="productname" id="productname" class="form-control" disabled style="resize:vertical"><?php echo getProduct($row['product_code'],$link1);?></textarea>              
              </div>
            </div>
          </div>
		 
		 
		 
		   
          <div class="form-group">
            <div class="col-md-12" align="center">
              
            <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="" value="Update" >
        <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='price_master.php?<?=$pagenav?>'">
      
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
