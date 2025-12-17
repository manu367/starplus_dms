<?php
////// Function ID ///////
$fun_id = array("a"=>array(94));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$locstr=getAccessLocation($_SESSION['userid'],$link1);
@extract($_POST);
////// start filters
$filter_val = "1";
////if date
if($fdate!='' && $tdate!='')
{
	$filter_val .=" AND DATE(entry_date) >= '".$fdate."' AND DATE(entry_date) <= '".$tdate."'";
}
else
{
	$filter_val .=" AND DATE(entry_date) >= '".$today."' AND DATE(entry_date) <= '".$today."'";
}
//// if state
if($locationstate!=""){
	$state="state='".$locationstate."'";
	$filter_val .= "";
}else{
	$state="1";
}
/// if city
if($cityname!=""){
	$filter_val .= "";
}
//
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="google" content="notranslate" />
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function(){
		$('#myTable').dataTable();
	});
</script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   		<div class="col-sm-9">
      		<h2 align="center"><i class="fa fa-address-card"></i> Credit Limit Approval </h2>
            	<?php 
				if(isset($_REQUEST['msg'])){
				?>
            	<div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            	</div>
            	<?php }?> 
   			<div class="form-group" id="page-wrap" style="margin-left:10px;">
			  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    				<div class="form-group">
          				<div class="col-md-10">
              				<label class="col-md-3 control-label">From Date</label>
              				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required autocomplete="off" onChange="document.frm1.submit();"></div>
			   				</div>
              				<label class="col-md-3 control-label">To Date</label>
             				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" required autocomplete="off" onChange="document.frm1.submit();"></div>
			   				</div>
          				</div>
					</div>
                    <div class="form-group">
          				<div class="col-md-10">
              				<label class="col-md-3 control-label">State</label>
              				<div class="col-md-3">
  								<select name="locationstate" id="locationstate" class="form-control"  onChange="document.frm1.submit();">
                          			<option value=''>--Select All--</option>
                                  	<?php
                                	$circlequery="select distinct(state) from state_master order by state";
                                	$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
                                	while($circlearr=mysqli_fetch_array($circleresult)){
                                	?>
                                  	<option value="<?=$circlearr['state']?>"<?php if($_REQUEST['locationstate']==$circlearr['state']){ echo "selected";}?>><?=ucwords($circlearr['state'])?></option>
                                	<?php 
                                	}
                                	?>
                                </select>
			   				</div>
              				<label class="col-md-3 control-label">City</label>
             				<div class="col-md-3">
  								<select name="cityname" class="form-control" id="cityname" onChange="document.frm1.submit();">
                                    <option value="">--Select All--</option>
                                    <?php
                                    $circlequery="select distinct(city) from district_master where ".$state." order by city";
                                    $circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
                                    while($circlearr=mysqli_fetch_array($circleresult)){
                                    ?>
                              		<option value="<?=$circlearr['city']?>"<?php if($_REQUEST['cityname']==$circlearr['city']){echo "selected";}?> ><?=$circlearr['city']?></option>
                              		<?php 
                                    }
                                    ?>
                            	</select>
			   				</div>
          				</div>
					</div>
       		  </form>
         		<table  width="100%" id="myTable" class="table-bordered table-hover" align="center">
          			<thead>
                        <tr class="<?=$tableheadcolor?>">
                            <th>S.No</th>
                            <th>Location Name</th>
                            <th>In Favour Of</th>
                            <th>Requested Credit Limit</th>
                            <th>Entry By</th>
                            <th>Entry Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
          			</thead>
          			<tbody>
	   				<?php 
     				$sno=0;
     				$sql = mysqli_query($link1,"SELECT id , asc_code, parent_code, credit_limit, entry_by, entry_date, status FROM credit_limit_history WHERE ".$filter_val." ORDER BY asc_code");
     				if(mysqli_num_rows($sql)>0){
					while($row = mysqli_fetch_array($sql))
					{
  						$sno=$sno+1;
     					?>
      					<tr class="even pointer">
                            <td><?php echo $sno;?></td>
                            <td><?php echo str_replace("~",", ",getAnyDetails($row['asc_code'],"name,city,state,asc_code","asc_code","asc_master",$link1));?></td>
                            <td><?php echo str_replace("~",", ",getAnyDetails($row['parent_code'],"name,city,state,asc_code","asc_code","asc_master",$link1));?></td>
                            <td><?php echo $row['credit_limit'];?></td>
                            <td><?php echo str_replace("~",", ",getAnyDetails($row['entry_by'],"name,oth_empid,username","username","admin_users",$link1));?></td>
                            <td><?php echo $row['entry_date'];?></td>
                            <td><?php echo $row['status'];?></td>                            
                            <td align="center"><a href='credit_limit.php?id=<?php echo base64_encode($row['id']);?><?=$pagenav?>' title='view/approval action'><i class="fa fa-eye fa-lg" title="view/approval action"></i></a></td>
        				</tr>
            			<?php }
						}else{?>
                        <tr class="even pointer">
                            <td>&nbsp;</td>
                            <td>No record available</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
        				</tr>
                        <?php }?>
          			</tbody>
      			</table>
		  </div><!--close panel group-->  
 		</div><!--close col-sm-9-->
	</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>