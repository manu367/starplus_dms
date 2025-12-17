<?php
////// Function ID ///////
$fun_id = array("a"=>array(28));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
////// final submit form ////
if($_POST['Submit']=="Save"){
if(mysqli_num_rows(mysqli_query($link1,"select id from district_master where city='".$_POST['city_name']."' and state='".$_POST['state']."'"))==0){	
if(mysqli_query($link1,"insert into district_master set state='".$_POST['state']."',city='".ucwords($_POST['city_name'])."',status='A',country='India'")or die("".mysqli_error($link1)))
{
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_POST['city_name'],"CITY","ADD",$ip,$link1,"");
	
	//return message
	$msg="You have successfully created a new city like ".$_POST['city_name'];
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
      <h2 align="center"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;Add New City</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State Name <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <?PHP 
		  $query1="select distinct state from state_master order by state";
		  $result=mysqli_query($link1,$query1);
		  ?>
             <select name="state" id="state"  class="form-control required" required>
              <option value="">--Please Select--</option>
              <?php
			  while($row=mysqli_fetch_array($result))
			 {
			 ?>
              <option value="<?=$row['state'];?>">
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
                 <input type="text" name="city_name" class="form-control required"  id="city_name" required/>
                 
              </div>
            </div>
          </div>
          <br><br>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="Add City">
             
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