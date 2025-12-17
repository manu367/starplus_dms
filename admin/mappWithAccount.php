<?php
////// Function ID ///////
$fun_id = array("a"=>array(73)); 
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$getid = base64_decode($_REQUEST['id']);
////// get details of selected account////
$res_accdet = mysqli_query($link1,"SELECT * FROM account_master WHERE id='".$getid."'")or die(mysqli_error($link1));
$row_accdet = mysqli_fetch_array($res_accdet);
////// final submit form ////
@extract($_POST);
   if($_POST['save']=='Update'){
      $count=count($parentloc);
      $i=0;
	  mysqli_query($link1,"UPDATE mapped_account SET status='' WHERE account_id='".$childcode."'")or die("er0".mysqli_error($link1));
      while($i < $count){
           if($parentloc[$i]==''){
              $status='';
           }else{
              $status='Y';
		   }
		   ///// check mapping is already is there
           if(mysqli_num_rows(mysqli_query($link1,"SELECT account_id FROM mapped_account WHERE location_code='".$parentloc[$i]."' AND account_id='".$childcode."'"))>0){
              mysqli_query($link1,"UPDATE mapped_account SET status='".$status."',update_date='".$datetime."' WHERE location_code='".$parentloc[$i]."' AND account_id='".$childcode."'")or die("ER1".mysqli_error($link1));
           }else{
              mysqli_query($link1,"INSERT INTO mapped_account SET account_id='".$childcode."',location_code='".$parentloc[$i]."',status='".$status."',update_date='".$datetime."'")or die("ER2".mysqli_error($link1));
		   }
           $i++;	
	  }///close while loop
      ////// insert in activity table////
	  dailyActivity($_SESSION['userid'],$row_accdet["account_no"],"MAPPING","UPDATE",$ip,$link1,"");
	  ////// return message
	  $msg="You have successfully mapped selected account ".$row_accdet["account_name"]." - ".$row_accdet["account_no"]." to locations";
	  ///// move to parent page
      header("Location:account_master.php?msg=".$msg."".$pagenav);
	  exit;
   }

############################ Filters value apply to refine loctaion ids to mapped
if($_REQUEST['state']!=''){ $locstate = "state = '".$_REQUEST['state']."'" ; } else{ $locstate = "1";} 
if($_REQUEST['city']!=''){ $loccity = "city = '".$_REQUEST['city']."'" ; } else{ $loccity = "1";}
if($_REQUEST['loctype']!=''){ $loctype = "id_type = '".$_REQUEST['loctype']."'" ; } else{ $loctype = "1";}
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
</script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
		<?php 
    		include("../includes/leftnav2.php");
    	?>
    		<div class="col-sm-9">
      			<h2 align="center"><i class="fa fa-map-signs"></i> Mapped with locations</h2>
      			<h4 align="center">Account Name: <b><?=$row_accdet["account_name"]."</b> - <span class='red_small'>".$row_accdet["account_no"]."</span> (".$row_accdet["account_type"].")"?></h4><br/>
          		<form  name="form1" id="form1" class="form-horizontal" action="" method="post">
					<div class="form-group">
            			<div class="col-md-6"><label class="col-md-5 control-label">Location State</label>
              				<div class="col-md-5">
               					<select name="state" class="form-control" id="state" onChange="document.form1.submit();">
                					<option value=''>All</option>
									<?php
                                    $circlequery="select state from state_master order by state";	
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
                 					<option value=''>All</option>
                 					<?php
				 					$circlequery="SELECT distinct city FROM district_master WHERE ".$locstate." order by city";
									$circleresult=mysqli_query($link1,$circlequery) or die("error-5".mysqli_error($link1));
									while($circlearr=mysqli_fetch_array($circleresult)){ ?>
                 					<option value="<?=$circlearr['city']?>" <?php if($circlearr['city']==$_REQUEST['city']) echo "selected";?>><?=$circlearr['city']?></option>
                 					<?php }?>
                 					<option value='Others'<?php if($row_locdet['city']=="Others"){ echo "selected";}?>>Others</option>
               					</select>
              				</div> 
             			</div>
          			</div>
		  			<div class="form-group">
            			<div class="col-md-6"><label class="col-md-5 control-label">Location Type</label>
              				<div class="col-md-5">
                 				<select name="loctype" id="loctype" class="form-control" onChange="document.form1.submit();">
                   					<option value=''>All</option>
				   					<?php
                    				$circlequery="select locationtype,locationname from location_type WHERE status='A'";	
									$circleresult=mysqli_query($link1,$circlequery) or die("error-5".mysqli_error($link1));
									while($circlearr=mysqli_fetch_array($circleresult)){ ?>
                    				<option value="<?=$circlearr['locationtype']?>"<?php if($circlearr['locationtype']==$_REQUEST['loctype']) echo "selected";?>><?=$circlearr['locationname']?></option>
                  					<?php }?>
                 				</select>
               				</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="col-md-12" align="right">
                        	<input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.form1.list)" value="Check All" />&nbsp;
                            <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.form1.list)" value="Uncheck All" />
            			</div>
          			</div>
          			<div class="panel-group">
           				<div class="panel panel-default table-responsive">
             				<div class="panel-heading">Locations Name</div>
               				<div class="panel-body">
          					<?php
          					$sql_locations = "SELECT distinct(asc_code),name,state,city FROM asc_master WHERE ".$locstate." AND ".$loccity." AND ".$loctype." AND status = 'Active' ORDER BY state, name";
          					$loccount = mysqli_query($link1,$sql_locations) or die(mysqli_error($link1));
		  					?>
          					<table id="myTable" class="table table-hover" border="0">
           						<tbody>
          						<?php
          						if(mysqli_num_rows($loccount) > 0){
             						$hide='NO';
             						$i=1;
             						while($row_locations = mysqli_fetch_array($loccount)){
               							if($i%3==1){
		  						?>
            					<tr>
          						<?php  
										} 
								$mappedloc = mysqli_query($link1,"SELECT location_code FROM mapped_account WHERE account_id = '".$row_accdet["id"]."' AND location_code='".$row_locations["asc_code"]."' AND status='Y'")or die(mysqli_error($link1));
								$num = mysqli_num_rows($mappedloc);
								//  if($num>0) {
		  						?>	
              						<td><input type="checkbox" name="parentloc[]" id="list" <?php if($num>0){ echo "checked";}?> value="<?=$row_locations['asc_code']?>"/>&nbsp;<?=$row_locations['name']." | ".$row_locations['state']." | ".$row_locations['city']."(".$row_locations['asc_code'].")";?></td>
              					<?php 
								//  }
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
            			<div class="col-md-12" align="center">
              			<?php if($hide=='NO'){ ?>
                 			<input type="submit" class="btn btn-primary" name="save" id="save" value="Update">&nbsp;
                 			<input name="childcode" id="childcode" type="hidden" value="<?=$row_accdet["id"]?>"/>
              			<?php } ?>   
                			<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='account_master.php?<?=$pagenav?>'">
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
