<?php
require_once("../config/config.php");
////// final submit form ////
if($_POST[Submit]=="Save"){
	///seq no add/////
if($_POST[loc]=="HO"){ $seq_no=1;}
	elseif($_POST[loc]=="BR"){ $seq_no=2;}
	elseif($_POST[loc]=="SS"){ $seq_no=3;}
	elseif($_POST[loc]=="DS"){ $seq_no=4;}
	elseif($_POST[loc]=="DL"){ $seq_no=5;}
	else{ $seq_no=6;}
////ref add in price_history table////
$ref=$_POST[state]."~".$_POST[loc]."~".$_POST[product_code];
if(mysqli_query($link1,"insert into price_master set state='$_POST[state]' ,location_type='$_POST[loc]',location_seq='$seq_no',product_code='$_POST[product_code]',mrp='$_POST[mrp]',price='$_POST[price]',status='active',create_date='$datetime',create_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1)))
$id=mysqli_insert_id($link1);
    if(($id)>0)
		{
$sql=(mysqli_query($link1,"insert into price_history set type_id='$id',ref_no='$ref',mrp='$_POST[mrp]',price='$_POST[price]',action='ADD',modify_date='$datetime' ")or die("ER4".mysqli_error($link1)));
		 ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_POST[product_code],"PRICE","ADD",$ip,$link1);
	
	//return message
	$msg="You have successfully created a new Price";
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
      <h2 align="center"><i class="fa fa-inr"></i>&nbsp;&nbsp;Add New Price</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-5">
              <select name="state"  class="form-control" id="state" required>
                                       <option value="">--Please Select-- </option>
      <?php $state =mysqli_query($link1,"select * from state_master order by state asc"); while($srow=mysqli_fetch_assoc($state)){?>
                            				<option value="<?php echo $srow['state'];?>"><?php echo $srow['state'];?></option>
                                            <?php }?>
                   			 					</select>
           
          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Location Type<span class="red_small">*</span></label>
              <div class="col-md-5">
                  <select name="loc"  class="form-control" id="loc" required>
                    <option value="">--Please Select--</option>
          <?php $loc =mysqli_query($link1,"select * from location_type"); while($srow=mysqli_fetch_assoc($loc)){?>
                            				<option value="<?php echo $srow['locationtype'];?>"><?php echo $srow['locationname'];?></option>
                                            <?php }?>
                   			 					</select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Mrp<span class="red_small">*</span></label>
              <div class="col-md-5">
               
	          <input type="text" name="mrp" id="mrp" class="form-control" value="" onkeypress="return onlyFloatNum(event);" placeholder="0.00"  required />
            
                 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Product Price<span class="red_small">*</span></label>
              <div class="col-md-5">
                  <input type="text" name="price" id="price" class="form-control" onkeypress="return onlyFloatNum(event);" value=""  placeholder="0.00" required/>
              </div>
            </div>
            
          </div>
          
          
		 
		  <div class="form-group">
            
			<div class="col-md-6"><label class="col-md-5 control-label">Product <span class="red_small">*</span></label>
              <div class="col-md-5">
                  <select name="product_code"  class="form-control" id="product_code" required>
                    <option value="">--Please Select--</option>
          <?php $loc =mysqli_query($link1,"select * from product_master"); while($srow=mysqli_fetch_assoc($loc)){?>
                            				<option value="<?php echo $srow['productcode'];?>"><?php echo $srow['productname'];?></option>
                                            <?php }?>
                   			 					</select>
              </div>
            </div>
            
            
          </div>
		 
		 
		 
		   
          <div class="form-group">
            <div class="col-md-12" align="center">
              
            <input type="submit" class="btn btn-primary" name="Submit" id="" value="Save" >
        <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='price_master.php?<?=$pagenav?>'">
      
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
