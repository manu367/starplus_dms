<?php
require_once("../config/config.php");
@extract($_REQUEST);
////// filters value/////
## selected username
$date=date("Y-m-d");

if($_REQUEST['fdate'] !='')
{
	$fdate = $_REQUEST['fdate'];
}
//////End filters value/////
if($_REQUEST['tdate'] !='')
{
	$tdate = $_REQUEST['tdate'];
}
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($isp_name){
		$team2 = getTeamMembers($isp_name,$link1);
		if($team2){
			$team2 = $team2.",'".$isp_name."'"; 
		}else{
			$team2 = "'".$isp_name."'"; 
		}
		$user_id = " AND userid IN (".$team2.")";
	}else{
		$user_id = " ";
	}
}else{
	if($isp_name){
		$team3 = getTeamMembers($isp_name,$link1);
		if($team3){
			$team3 = $team2.",'".$isp_name."'"; 
		}else{
			$team3 = "'".$isp_name."'"; 
		}
		$user_id = " AND userid IN (".$team3.")";
	}else{
		$user_id = " AND userid IN (".$team.")";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
<script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
</script>
 <link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-map-marker"></i>&nbsp;Track Location</h2>
      <?php if($_REQUEST[msg]){?><br>
	  <div class="form-group" id="page-wrap" style="margin-left:10px;">
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
	  <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
             
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
           
          </div>
        </div>
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Users:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="isp_name" id="isp_name" class="form-control selectpicker" data-live-search="true" onChange="document.form1.submit();">
                                        <option value="">All</option>
                                        <?php
										if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){ 
                                        	$sql = "Select userid from user_track  group by userid order by id desc";
										}else{
											$sql = "Select userid from user_track WHERE userid IN (".$team.") group by userid order by id desc";
										}
                                        $sqlres1 = mysqli_query($link1, $sql);                                        
                                        while($ispname1 = mysqli_fetch_assoc($sqlres1)){ 
                                        $ispname = mysqli_fetch_assoc(mysqli_query($link1, "select name,username,oth_empid from admin_users where username ='$ispname1[userid]' group by username order by uid desc"));
                                         if($ispname['username'] !=''){   ?>
                                        <option value="<?=$ispname['username']?>" <?php if($ispname['username']==$_REQUEST['isp_name']){ echo 'selected'; } ?>><?=$ispname['name']." | ".$ispname['username']." ".$ispname['oth_empid']?></option>
                                        <?php } } ?>
                                    </select>
             </div>
          </div> 
	    </div><!--close form group-->
	    
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6">  
			<div class="col-md-8" align="left">	
			<?php 
			//if($_REQUEST['isp_name'] == '' ){
			?>
               <a href="excelexport.php?rname=<?=base64_encode("trackuser")?>&rheader=<?=base64_encode("Track User")?>&user=<?=base64_encode($_REQUEST['isp_name'])?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>" title="Export Track details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Track details in excel"></i> Summerize Report</a>
               &nbsp;&nbsp;
               <a href="excelexport.php?rname=<?=base64_encode("trackuserdet")?>&rheader=<?=base64_encode("Track User")?>&user=<?=base64_encode($_REQUEST['isp_name'])?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>" title="Export Track details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Track details in excel"></i> Detailed Report</a>
			<?php //}?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
		  <button title="Check distance covered by user" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='track_total_distance.php?op=add&isp_name=<?=$_REQUEST['isp_name']?>&fdate=<?=$_REQUEST['fdate']?>&tdate=<?=$_REQUEST['tdate']?><?=$pagenav?>'"><span>Check distance covered by user</span></button>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
			   <th><a href="#" name="name" title="asc" class="not-sort"></a>User Id</th>
              <th><a href="#" name="name" title="asc" class="not-sort"></a>Address</th>
			  <th><a href="#" name="name" title="asc" class="not-sort"></a>Travel Date</th>
			  <th><a href="#" name="name" title="asc" class="not-sort"></a>Travel Time</th>
             
             
			  
              
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
			  $fromd = date_format(date_create($_REQUEST['fdate']), "Y-m-d");
              $tod = date_format(date_create($_REQUEST['tdate']), "Y-m-d");
			 if($isp_name!="" ){
			//$sql1 = "SELECT distinct(eng_id), address,update_date,travel_date FROM lead_user_track where eng_id='$isp_name'  and travel_date BETWEEN '" . $fromd . "' and '" . $tod . "' order by travel_date,travel_time desc";
				 $sql1 = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude FROM user_track where userid='".$isp_name."'  and entry_date BETWEEN '" . $fromd . "' and '" . $tod . "' order by update_date desc";
			}else{
			 //$sql1 = "SELECT distinct(eng_id), address,update_date,travel_date FROM lead_user_track where travel_date BETWEEN '" . $fromd . "' and '" . $tod . "' order by travel_date,travel_time desc ";
				 $sql1 = "SELECT distinct(userid), address,update_date,entry_date,latitude,longitude FROM user_track where entry_date BETWEEN '" . $fromd . "' and '" . $tod . "' ".$user_id." order by update_date desc ";
				}
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { 
	       $datetime = $row1['update_date'];
		  $t = explode(" ",$datetime);
	   ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
		<td><?php echo $row1['userid']?></td>
          <td><?php echo "latitude->".$row1['latitude']." , longitude->".$row1['longitude']."<br/>".$row1['address'];?></td>
		  <td><?php  echo $t[0];?></td>
		  <td><?php echo $t[1];?></td>
            </tr>
	   <?php 
	  $i++;
	   }?>
	   
	  
          </tbody>
          </table>
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