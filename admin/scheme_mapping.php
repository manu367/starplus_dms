<?php
////// Function ID ///////
$fun_id = array("u"=>array(152)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$refid = base64_decode($_REQUEST['id']);
$res_sch = mysqli_query($link1,"SELECT * FROM reward_scheme_master WHERE id = '".$refid."'");
$row_sch = mysqli_fetch_array($res_sch);
if($_POST){
	if(isset($_POST['submitTab'])){
   		if ($_POST['submitTab']=='Save'){
			@extract($_POST);
			///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
			$messageIdent = md5($refid.$_POST['submitTab']);
			//and check it against the stored value:
    		$sessionMessageIdent = isset($_SESSION['messageIdentSMA'])?$_SESSION['messageIdentSMA']:'';
			if($messageIdent!=$sessionMessageIdent){//if its different:
				//save the session var:
            	$_SESSION['messageIdentSMA'] = $messageIdent;
				
				$mapidtype = $_REQUEST['mapidtype'];
				$count_mapidtype =count($mapidtype);
				//print_r($mapidtype);
				$mapstate = $_REQUEST['mapstate'];
				$count_mapstate =count($mapstate);
				//print_r($mapstate);
				mysqli_query($link1,"UPDATE scheme_mapping SET status='' WHERE scheme_id='".$refid."' ");
				$j=0;
				if($count_mapidtype>0 && $count_mapstate>0){
					while($j < $count_mapidtype){
						$k=0;
						while($k < $count_mapstate){
							if($mapidtype[$j]!='' && $mapstate[$k]!=''){
								$status='Y';
							}else{
								$status='';
							}
							// alrady exist
							if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM scheme_mapping WHERE scheme_id='".$refid."' AND state='".$mapstate[$k]."' AND id_type='".$mapidtype[$j]."'"))>0){
								mysqli_query($link1,"UPDATE scheme_mapping SET status='".$status."', update_by='".$_SESSION['userid']."', update_on='".$datetime."' WHERE scheme_id='".$refid."' AND state='".$mapstate[$k]."' AND id_type='".$mapidtype[$j]."'")or die(mysqli_error($link1));
							}else{
								mysqli_query($link1,"INSERT INTO scheme_mapping SET scheme_id='".$refid."', state='".$mapstate[$k]."', id_type='".$mapidtype[$j]."', status='".$status."', update_by='".$_SESSION['userid']."', update_on='".$datetime."'")or die(mysqli_error($link1));
							}
							$k++;
						}
						$j++;
					}
					
				}else{
					
				}
				////// return message
				$msg = "You have successfully mapped scheme ".$row_sch['scheme_name']." with states and party type.";
				$cflag = "success";
				$cmsg = "Success";
			}else {
        		//you've sent this already!
				$msg = "You have re-submited the data.";
				$cflag = "warning";
				$cmsg = "Warning";
    		}
		}
		///// move to parent page
    	header("location:scheme_mapping.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&id=".base64_encode($refid)."".$pagenav);
    	exit;
	}
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script>
 $(document).ready(function(){
	$("#frm1").validate();
 });
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 } 
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
		<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
        	<h2 align="center"><i class="fa fa-sitemap"></i> Scheme Mapping</h2>
            <h4 align="center" style="color:#FF0000">You are mapping scheme <?=$row_sch['scheme_name']?></h4>
	    	<?php if(isset($_REQUEST['msg'])){
			$_SESSION['messageIdentSMA'] = "";
			?>
            <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            </div>
            <?php }?>
            <form name="frm1" id="frm1" class="form-horizontal" action="" method="post">
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-info">
                        <div class="panel-heading"><i class="fa fa-map-marker" aria-hidden="true"></i> Map with states</div>
                        <div class="panel-body">
                        	
							<div class="table-responsive"> 
              					<div class="form-buttons" style="float:right">
                					<input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm1.mapstate)" value="Check All" />
                					<input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm1.mapstate)" value="Uncheck All" />
                				</div>
                 				<table id="myTable" class="table table-hover">
                                <tbody>
								<?php
                                $res = mysqli_query($link1,"SELECT * FROM state_master WHERE 1 ORDER BY state");
                                $num = mysqli_num_rows($res);
                                if($num > 0) {
									$i=1;
									while($row=mysqli_fetch_array($res)){
										if($i%2==1){
								?>
                  					<tr>
                  					<?php
										}
									$state_map = mysqli_query($link1,"SELECT id FROM scheme_mapping WHERE status='Y' AND state='".$row['state']."' AND scheme_id='".$row_sch['id']."'")or die(mysqli_error($link1));
									$state_num = mysqli_num_rows($state_map);
									?>
                    					<td><input style="width:20px" type="checkbox" id="mapstate" name="mapstate[]" value="<?=$row['state']?>" <?php if($state_num > 0) echo "checked";?>/>&nbsp;<?=$row['state']?></td>
                                    <?php if($i/2==0){?>    
                  					</tr>
                  				<?php 	
										}
									$i++;
									}			
				  				}else{
								?>
                                	<tr>
                                    	<td>No record found.</td>
                                    </tr>
                                <?php }?>
                				</tbody>
              					</table>
              				</div>
                           
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-success">
                        <div class="panel-heading"><i class="fa fa-users" aria-hidden="true"></i> Party Type</div>
                        <div class="panel-body">

                			<div class="table-responsive"> 
              					<div class="form-buttons" style="float:right">
                					<input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm1.mapidtype)" value="Check All" />
                					<input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm1.mapidtype)" value="Uncheck All" />
                				</div>
                 				<table id="myTable" class="table table-hover">
                                <tbody>
								<?php
                                $res2 = mysqli_query($link1,"SELECT * FROM location_type WHERE status='A' ORDER BY seq_id");
                                $num2 = mysqli_num_rows($res);
                                if($num2 > 0) {
									$i=1;
									while($row2=mysqli_fetch_array($res2)){
										
								?>
                  					<tr>
                  					<?php
										
									$idtype_map = mysqli_query($link1,"SELECT id FROM scheme_mapping WHERE status='Y' AND id_type='".$row2['locationtype']."' AND scheme_id='".$row_sch['id']."'")or die(mysqli_error($link1));
									$idtype_num = mysqli_num_rows($idtype_map);
									?>
                    					<td><input style="width:20px" type="checkbox" id="mapidtype" name="mapidtype[]" value="<?=$row2['locationtype']?>" <?php if($idtype_num > 0) echo "checked";?>/>&nbsp;<?=getLocationType($row2['locationtype'],$link1)?></td>
                                    
                  					</tr>
                  				<?php 	
									
									$i++;
									}			
				  				}else{
								?>
                                	<tr>
                                    	<td>No record found.</td>
                                    </tr>
                                <?php }?>
                				</tbody>
              					</table>
              				</div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-buttons" align="center">
            	<input type="submit" class="btn<?=$btncolor?>" name="submitTab" id="submitTab" value="Save"> 
              	<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='reward_sch_master.php?id=<?php echo base64_encode($row_sch['id']);?><?=$pagenav?>'">
            </div>
			</form>
    	</div><!--close tab pane-->
	</div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>