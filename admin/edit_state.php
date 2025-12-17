<?php
////// Function ID ///////
$fun_id = array("a"=>array(66));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$getid=base64_decode($_REQUEST['id']);
////// get details of selected city////
$rs=mysqli_query($link1,"select * from state_master where sno='$getid'")or die(mysqli_error($link1));
  $row2=mysqli_fetch_array($rs);
  ////// final submit form ////
if($_POST['Submit']=="Update"){
///// Update tax details if needed
	if(mysqli_query($link1,"update state_master set state='".$_POST['state']."',zone='".$_POST['circle']."',code='".$_POST['code']."', statecode = '".$_POST['statecode']."' where  sno='".$getid."' ")or die("ER1".mysqli_error($link1)))

{
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$row2['state'],"STATE","UPDATE",$ip,$link1,"");
	//return message
	$msg="You have successfully update state  ".$row2['state'];
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
	///// move to parent page
    header("Location:state_master.php?msg=".$msg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;State Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">State</label>
              <div class="col-md-6">
                 <input type="text" name="state" class="required form-control" id="state" value="<?=$row2['state']?>" /> 
             
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Circle / Zone</label>
              <div class="col-md-6">              
               <select name="circle" id="circle" class="form-control ">
                  <option value="">Please Select</option>
                 <option value="NORTH" <?php if($row2['zone'] == "NORTH") { echo "selected" ;}?>>NORTH</option>
				 <option value="SOUTH" <?php if($row2['zone'] == "SOUTH") { echo "selected" ;}?>>SOUTH</option>
				 <option value="EAST" <?php if($row2['zone'] == "EAST") { echo "selected" ;}?>>EAST</option>
				<option value="WEST" <?php if($row2['zone'] == "WEST") { echo "selected" ;}?>>WEST</option>
                  </option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Code</label>
              <div class="col-md-6">
        <input type="text" name="code" class="required form-control" id="code" value="<?=$row2['code']?>" />  
              </div>
            </div>
          </div>
		   <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Statecode</label>
              <div class="col-md-6">
                   <input type="text" name="statecode" class="required form-control" id="statecode" value="<?=$row2['statecode']?>" />  
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Update" title="Update Status">
             
              
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='state_master.php?<?=$pagenav?>'">
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