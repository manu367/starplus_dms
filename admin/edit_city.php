<?php
////// Function ID ///////
$fun_id = array("a"=>array(28));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$getid=base64_decode($_REQUEST['id']);
////// get details of selected city////
$rs=mysqli_query($link1,"select * from district_master where id='$getid'")or die(mysqli_error($link1));
  $row2=mysqli_fetch_array($rs);
  ////// final submit form ////
if($_POST['Submit']=="Update"){
///// Update tax details if needed
if(mysqli_num_rows(mysqli_query($link1,"select id from district_master where city='".$_POST['city_name']."' and state='".$_POST['state']."'"))<1){	
	if(mysqli_query($link1,"update district_master set status='".$_POST['status']."' where  id='".$getid."' ")or die("ER1".mysqli_error($link1))){
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$row2['city'],"CITY","UPDATE",$ip,$link1,"");
	//return message
	$msg="You have successfully update city status ".$row2['city'];
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
   
   }
else{
	////// return message
	$msg="Entered city is already exist. Please add new city only.";
}
   
	///// move to parent page
    header("Location:city_master.php?msg=".$msg."".$pagenav);
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
 <style>
.red_small{
	color:red;
}
</style>
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
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
      <h2 align="center"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;City Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State Name <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <?PHP 
		  $query1="select distinct state from state_master order by state";
		  $result=mysqli_query($link1,$query1);
		  ?>
             <select name="state" id="state" class="form-control required" required disabled>
              <option value="">--Please Select--</option>
              <?php
			  while($row=mysqli_fetch_array($result))
			 {
			 ?>
              <option value="<?=$row['state'];?>"<?php if($row2['state']==$row['state']){ echo "selected";}?>>
              <?=$row['state'];?>
              </option>
              <?php
			}
			?>
            </select>
            
              </div>
			  
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">City Name <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input type="text" name="city_name" class="form-control required" disabled id="city_name" value="<?php echo $row2['city']; ?>" required/>
                 
              </div>
            </div>
           
          </div>
         
          <div class="form-group">
		   <div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-5">
                <select name='status' id='status' class="form-control required"  required/>
				 <option value="">--Please Select--</option>
                    <option value="A"<?php if($row2['status']=="A"){ echo "selected";}?>>Active</option>
                    <option value="D"<?php if($row2['status']=="D"){ echo "selected";}?>>Deactive</option>
                 </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label"></label>
              <div class="col-md-5">
                 
              </div>
            </div>
            
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Update" title="Update Status">
             
              
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='city_master.php?<?=$pagenav?>'">
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