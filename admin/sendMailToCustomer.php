<?php
////// Function ID ///////
$fun_id = array("a"=>array(76));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$accessState=getAccessState($_SESSION['userid'],$link1);
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
include("customer_mail.php");
////// final submit form ////
@extract($_POST);
if($_POST['save']=='Send Mail'){
     
	if(count($parentloc)>0){  
		$msgsent="Mail Sent";
		$count=count($parentloc);
		$i=0;
		while($i < $count){
			$cust_details=explode("~",$parentloc[$i]);
            //if($cust_details[1]==''){
           // }else{
				$content1="Hi $cust_details[2],<br><br>$mail_text<br><br>Regards,<br>CANSALE";
 				send_mail_function($content1,$cust_details[1],"support@candoursoft.com",$mail_subject);
				dailyActivity($_SESSION['userid'],$cust_details[0],"Mail",$msgsent,$ip,$link1,$flag);
		   	//}
			$i++;
		}
	  $msg="You have successfully sent Mail to selected customers";
	  ///// move to parent page   
	}
        else {
	    $msg="Please checked atleast one customer to send Mail";
	  }

         header("Location:customer_details.php?msg=".$msg."".$pagenav);
	  exit;
}

############################ Filters value apply to refine loctaion ids to mapped
if($_REQUEST[state]!=''){ $locstate="state='$_REQUEST[state]'" ; } else{ $locstate="state in ($accessState)";} 
if($_REQUEST[city]!=''){ $loccity="city='$_REQUEST[city]'" ; } else{ $loccity="1";}
//if($_REQUEST[loctype]!=''){ $loctype="id_type='$_REQUEST[loctype]'" ; } else{ $loctype="1";}
if($_REQUEST[location]!='' && $_REQUEST[location]!='all'){ $loccod="mapplocation='$_REQUEST[location]'" ; } else{ $loccod="mapplocation in(select asc_code from asc_master where ".$loccity." and ".$locstate." and asc_code in (".$accessLocation."))";}
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
    $('#myTable').dataTable();
});
$(document).ready(function(){
	$("#form1").validate();
});
</script>
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
      <h2 align="center"><i class="fa fa-envelope-o"></i> e-Mail To Customer</h2><br><br>
         
      <form  name="form1" id="form1" class="form-horizontal" action="" method="post">
			<div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Location State</label>
              <div class="col-md-5">
               <select name="state" class="form-control" id="state" onChange="document.form1.submit();">
                <option value=''>--Please Select--</option>
                <?php
				$circlequery="select state from state_master where state in($accessState) order by state";	
				$circleresult=mysqli_query($link1,$circlequery) or die("error-4".mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){ ?>
                <option value="<?=$circlearr['state']?>" <?php if($circlearr['state']==$_REQUEST['state']) echo "selected";?>><?=$circlearr['state']?></option>
                <?php }?>
               </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Location City</label>
              <div class="col-md-5">
               <select name="city"  class="form-control" id="city"  onChange="document.form1.submit();">
                 <option value=''>--Please Select--</option>
                 <?php
				 $circlequery="SELECT distinct city FROM district_master where ".$locstate." order by city";
				 $circleresult=mysqli_query($link1,$circlequery) or die("error-5".mysqli_error($link1));
				 while($circlearr=mysqli_fetch_array($circleresult)){ ?>
                 <option value="<?=$circlearr['city']?>" <?php if($circlearr['city']==$_REQUEST[city]) echo "selected";?>><?=$circlearr['city']?></option>
                 <?php	}?>
                 <option value='Others'<?php if($row_locdet[city]=="Others"){ echo "selected";}?>>Others</option>
               </select>
              </div> 
             </div>
          </div>
		  <div class="form-group">
           
            <div class="col-md-6">
              <label class="col-md-5 control-label">Location </label>
              <div class="col-md-5">
                 <select name="location" id="location" required class="form-control"  onChange="document.form1.submit();" >
                    <option value="all" selected="selected"> All </option>
                    <?php $sql_parent = "select name , city, state,id_type,asc_code from asc_master where ".$loccity." and ".$locstate."  and asc_code in  (".$accessLocation.")";
                    $res_parent = mysqli_query($link1, $sql_parent);
                    while ($party_det = mysqli_fetch_array($res_parent)) {
                   // $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "' and id_type='".$_REQUEST['locationtype']."'"));?>
                    <option value="<?=$party_det['asc_code'] ?>" <?php if ($party_det['asc_code'] == $_REQUEST['location']) echo "selected"; ?> ><?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $party_det['asc_code'] ?></option>
                    <?php }?>
                </select>
              </div> 
            </div>
          </div>
        
          <div class="form-group">
            <div class="col-md-12" align="right"><input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.form1.list)" value="Check All" />&nbsp;
                                  <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.form1.list)" value="Uncheck All" />
            </div>
          </div>
          <div class="panel-group">
           <div class="panel panel-info table-responsive">
             <div class="panel-heading">Locations Name</div>
               <div class="panel-body">
          <?php
         $sql_locations="Select distinct(customerid),customername,state,city,emailid from customer_master where ".$loccod." and emailid!='' and status='Active'  order by state, customername";
          $loccount=mysqli_query($link1,$sql_locations) or die(mysqli_error($link1));
		  ?>
          <table id="myTable" class="table table-hover" border="0">
           <tbody>
          <?php
          if(mysqli_num_rows($loccount) > 0){
             $hide='NO';
             $i=1;
             while($row_locations=mysqli_fetch_array($loccount)){
               if($i%3==1){
		  ?>
            <tr>
          <?php  } 
		  
		  ?>	
              <td><input type="checkbox" name="parentloc[]" id="list" value="<?=$row_locations['customerid']."~".$row_locations['emailid']."~".$row_locations['customername']?>"/>&nbsp;<?=$row_locations['customername']." | ".$row_locations['state']." | ".$row_locations['city']."(".$row_locations['customerid'].")";?></td>
              <?php 
			
			  if($i/3==0){
			  ?>
            </tr>
              <?php
              }
		     $i++;
			 }//close while loop
			 }//close row check if
          else{
            echo "<br/>";
			echo "<div align='center' class='red_small'>No Record Found !!</div>";
			echo "<br/>";
			$hide='YES';
		  }
		  ?>
          </tbody>
          </table>
          </div><!--close panel body-->
         </div><!--close panel-->
         </div><!--close panel group-->
         <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Mail Subject</label>
              <div class="col-md-6">
              	<input type="text" name="mail_subject" id="mail_subject" class="form-control required mastername" required>             
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Mail Content</label>
              <div class="col-md-6">
				<textarea name="mail_text" id="mail_text" class="form-control required" required style="height:150px;resize:vertical"></textarea>  
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($hide=='NO'){ ?>
                 <input type="submit" class="btn btn-primary" name="save" id="save" value="Send Mail">&nbsp;
              <?php } ?>   
                 <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='customer_details.php?<?=$pagenav?>'">
            </div>
          </div>
    </form>
      </div>
    </div>    
  </div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
