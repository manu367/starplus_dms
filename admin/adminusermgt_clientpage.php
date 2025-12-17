<?php

require_once("../config/config.php");
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

///// get access location ///

$accessLocation=getAccessLocation($_SESSION['userid'],$link1);

@extract($_GET);



## selected state

if($accessLocation!="" && $status!='' ){

	$loc_code="owner_code = '".$accessLocation."'";

}else{

	$loc_code="owner_code in ($accessLocation)";

}

## selected  Status

if($_SESSION['utype']==1){

if($status!=""){

	$status="status='".$status."'";

}else{

	$status="1";

}

## selected user type

if($u_type!=""){

	$utype=" utype='".$u_type."'";

}else{

	$utype="1";

}}

else { if($status!=""){

	$status="and status='".$status."'";

}else{

	$status="";

}

## selected user type

if($u_type!=""){

	$utype="and utype='".$u_type."'";

}else{

	$utype="";

}}



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
  $(function() {
    $('.togg').bootstrapToggle();
  })
</script>

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
        <form class="form-horizontal" role="form" name="form1" action="" method="get">
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
                <option value="<?php echo $srow['refid'];?>" <?php if($_REQUEST[u_type]==$srow['refid']){ echo "selected";}?>><?php echo $srow['typename'];?></option>
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
              <a href="excelexport.php?rname=<?=base64_encode("adminuser")?>&rheader=<?=base64_encode("Admin User Master")?>&u_type=<?=base64_encode($_GET['u_type'])?>&status=<?=base64_encode($_GET['status'])?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export user details in excel"></i></a>
              <?php

				//}

			?>
            </div>
          </div>
        </div>
	    <!--close form group-->
        <?php if($_SESSION['utype']==1){?>
        <button title="Add New User" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addAdminUser.php?op=add<?=$pagenav?>'"><span>Add User</span></button>
	    <br/>
	    <br/>
        <?php } else { ?>
        <button title="Add New User" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addAdminUser.php?op=add<?=$pagenav?>'"><span>Add User</span></button>
	    <br/>
	    <br/>
        <?php } ?>
        <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
        <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Login Id</th>
              <th><a href="#" name="name" title="asc" ></a>Emp Id</th>
              <th><a href="#" name="name" title="asc" ></a>User Name</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>User Type</th>
              <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Phone No.</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Email-id</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">View/Edit</th>
              <th data-hide="phone,tablet">History</th>
              <th data-hide="phone,tablet">App Logout</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			if($_SESSION['utype']==1){

			

			 $sql=mysqli_query($link1,"Select * from admin_users where $status  and  $utype  order by uid ASC");}

			else {

			

			 $sql=mysqli_query($link1,"Select * from admin_users where $loc_code $status   $utype  order by uid ASC");

			}

			while($row=mysqli_fetch_assoc($sql)){

				  $sno=$sno+1;

			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $row['username'];?></td>
              <td><?php echo $row['oth_empid'];?></td>
              <td><?php echo $row['name'];?></td>
              <td><?php echo gettypeName($row['utype'],$link1);?></td>
              <td><?php echo $row['phone'];?></td>
              <td><?php echo $row['emailid'];?></td>
              <td><?php echo $row['status'];?></td>
              <?php  if($_SESSION['utype']==1){ ?>
              <td align="center"><a href='addAdminUser.php?op=edit&id=<?php echo $row['username'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
              <?php }  else { ?>
              <td align="center"><a href='addAdminUser.php?op=edit&id=<?php echo $row['username'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
              
              <?php } ?>
              <td align="center"><a href='emp_history.php?empcode=<?php echo base64_encode($row['username']);?><?=$pagenav?>'  title='user history'><i class="fa fa-history fa-lg" title="view history"></i></a></td>
              <td align="center">
              	<input type="checkbox" class="togg" id="app_logout<?=$row['uid']?>" name="app_logout<?=$row['uid']?>" <?php if($row['app_logout']=="1"){?> checked <?php }?> onChange="appLogout('<?=$row['uid']?>')" ><span id="<?=$row['uid']?>"></span>
               </td>
            </tr>
            <?php }?>
          </tbody>
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