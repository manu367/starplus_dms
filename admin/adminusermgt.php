<?php
////// Function ID ///////
$fun_id = array("a"=>array(22)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

////// get user limit configuration
$res_usrlimit = mysqli_query($link1,"SELECT user_limit FROM app_config WHERE status='1' AND expiry_date >= '".date("Y-m-d")."'")or die(mysqli_error($link1));
$row_usrlimit = mysqli_fetch_assoc($res_usrlimit);
$num_usrlimit = $row_usrlimit['user_limit'];
/// check total no. of active users
$res_totusr = mysqli_query($link1,"SELECT uid FROM admin_users WHERE status='active'")or die(mysqli_error($link1));
$num_totusr = mysqli_num_rows($res_totusr);
/// get avail qty by selecting loc
if(isset($_POST['o']) && $_POST['o'] == 'upd_logout_flag'){
	$uid = $_POST["user_id"];
	$s = ($_POST["st"] == "true")?1:0;
	$msg = "Failed";
	$sql = "UPDATE admin_users SET app_logout='".$s."' WHERE uid = '".$uid."'";
	$res = mysqli_query($link1, $sql) or die(mysqli_error($link1));
	if($res){
		if(mysqli_affected_rows($link1)>0){
			$msg = "Success";
		}
	}
	$resp[] = ["respmsg" => $msg];
	exit(json_encode($resp));
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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
/////
function appLogout(ids){
	var mkid = "app_logout"+ids;
	var chk = document.getElementById(mkid).checked;
	/////// if yes/true
	var payload = { o:"upd_logout_flag", user_id:ids, st:chk }
	$.ajax({
		type: "POST",
		url: "",
		cache: false,
		data: payload,
		success: function(response){
			const res = JSON.parse(response);
			console.log(res[0]["respmsg"]);
			if(res[0]["respmsg"] == "Success"){
				$("#"+ids).html("<p class='text-success'>"+res[0]["respmsg"]+"</p>");
			}else{
				$("#"+ids).html("<p class='text-danger'>"+res[0]["respmsg"]+"</p>");
			}
		},
		error: function(){
			alert("Something went wrong, Try Again!");
		}
	});
}
$(document).ready(function() {
	var dataTable = $('#admin-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order":  [[0,"asc"]],
		"ajax":{
			url :"../pagination/adminusr-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "status": "<?=$_REQUEST["status"]?>", "u_type": "<?=$_REQUEST["u_type"]?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".admin-grid-error").html("");
				$("#admin-grid").append('<tbody class="admin-grid-error"><tr><th colspan="13">No data found in the server</th></tr></tbody>');
				$("#admin-grid_processing").css("display","none");
			}
		},
		"drawCallback": function( settings ) {
			$('.togg').bootstrapToggle();
		}
	} );
	
} );
</script>
<link href="../css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="../js/bootstrap-toggle.min.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
	<div class="col-sm-9 tab-pane fade in active" id="home">
        <h2 align="center"><i class="fa fa-users"></i> Admin/Users Master</h2>
	    <?php if($_REQUEST['msg']){?>
	    <br>
        <h4 align="center" style="color:#FF0000">
          <?=$_REQUEST['msg']?>
        </h4>
	    <?php }?>
        <form class="form-horizontal" role="form" name="form1" action="" method="POST">
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label"> Status</label>
            <div class="col-md-5" align="left">
              <select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
                <option value=""<?php if($_REQUEST['status']==''){ echo "selected";}?>>--Please Select--</option>
                <option value="active"<?php if($_REQUEST['status']=='active'){ echo "selected";}?>>Active</option>
                <option value="deactive"<?php if($_REQUEST['status']=='deactive'){ echo "selected";}?>>Deactive</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label">User Type</label>
            <div class="col-md-5" align="left">
              <select name="u_type" id="u_type" class="form-control"  onChange="document.form1.submit();">
                <option value="">--Please Select--</option>
                <?php $u_type =mysqli_query($link1,"select * from usertype_master where status='A' order by refid"); while($srow=mysqli_fetch_assoc($u_type)){?>
                <option value="<?php echo $srow['refid'];?>" <?php if($_REQUEST['u_type']==$srow['refid']){ echo "selected";}?>><?php echo $srow['typename'];?></option>
                <?php }?>
              </select>
            </div>
          </div>
        </div>
	    <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
              <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-5" align="left">
              <?php

			    //// get excel process id ////
				//$processid=getExlCnclProcessid("Admin Users",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
              <a href="excelexport.php?rname=<?=base64_encode("adminuser")?>&rheader=<?=base64_encode("Admin User Master")?>&u_type=<?=base64_encode($_REQUEST['u_type'])?>&status=<?=base64_encode($_REQUEST['status'])?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export user details in excel"></i></a>
              <?php
				//}
			?>
            </div>
          </div>
        </div>
	    <!--close form group-->
        <?php if($_SESSION['utype']==1){?>
        <button type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addAdminUser.php?op=add<?=$pagenav?>'" <?php if($num_totusr>=$num_usrlimit){ echo "disabled"; echo " title='You have reached your user limit'";}else{ echo 'title="Add New User"';}?>><span>Add User</span></button>
	    <br/>
	    <br/>
        <?php } else { ?>
        <button type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addAdminUser.php?op=add<?=$pagenav?>'" <?php if($num_totusr>=$num_usrlimit){ echo "disabled"; echo " title='You have reached your user limit'";}else{ echo 'title="Add New User"';}?>><span>Add User</span></button>
	    <br/>
	    <br/>
        <?php } ?>
        <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
        <table  width="100%" id="admin-grid" class="display table-striped" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
          	<tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Login Id</th>
              <th>Emp Id</th>
              <th>User Name</th>
              <th>User Type</th>
              <th>Phone No.</th>
              <th>Email-id</th>
              <th>Status</th>
				<th>Create By</th>
				<th>Create On</th>
              <th>View/Edit</th>
              <th>History</th>
              <th>App Logout</th>
            </tr>
          </thead>
        </table>
	    <!--</div>-->
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