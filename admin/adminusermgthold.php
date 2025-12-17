<?php
require_once("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/jquery.min.js"></script>
 <script src="../js/bootstrap.min.js"></script>
  
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--[if !IE]><!-->
	<style>
	
	/* 
	Max width before this PARTICULAR table gets nasty
	This query will take effect for any screen smaller than 760px
	and also iPads specifically.
	*/
	@media 
	only screen and (max-width: 760px),
	(min-device-width: 768px) and (max-device-width: 1024px)  {
	
		/* Force table to not be like tables anymore */
		table, thead, tbody, th, td, tr { 
			display: block; 
		}
		
		/* Hide table headers (but not display: none;, for accessibility) */
		thead tr { 
			position: absolute;
			top: -9999px;
			left: -9999px;
		}
		
		tr { border: 1px solid #ccc; }
		
		td { 
			/* Behave  like a "row" */
			border: none;
			border-bottom: 1px solid #eee; 
			position: relative;
			padding-left: 50%; 
		}
		
		td:before { 
			/* Now like a table header */
			position: absolute;
			/* Top/left values mimic padding */
			top: 6px;
			left: 6px;
			width: 45%; 
			padding-right: 10px; 
			white-space: nowrap;
		}
		
		/*
		Label the data
		*/
		td:nth-of-type(1):before { content: "S.No."; }
		td:nth-of-type(2):before { content: "Login Id"; }
		td:nth-of-type(3):before { content: "User Name"; }
		td:nth-of-type(4):before { content: "User Type"; }
		td:nth-of-type(5):before { content: "Phone No."; }
		td:nth-of-type(6):before { content: "Email-id"; }
		td:nth-of-type(7):before { content: "Status"; }
		td:nth-of-type(8):before { content: "View/Edit"; }
	}
	
	/* Smartphones (portrait and landscape) ----------- */
	@media only screen
	and (min-device-width : 320px)
	and (max-device-width : 480px) {
		body { 
			padding: 0; 
			margin: 0; 
			width: 320px; }
		}
	
	/* iPads (portrait and landscape) ----------- */
	@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
		body { 
			width: 495px; 
		}
	}
	
	</style>
	<!--<![endif]-->

<script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
</head>
<body>
<?php 
include("../includes/leftnav.php");
?>
<div class="container">
 <div class="col-sm-2" align="left">&nbsp;</div>
 <div class="col-sm-10">
  <h2 align="center">Admin/Users</h2>
  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
      <form class="form-horizontal" role="form">
        <button title="tender_add" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addAdminUser.php'"><span>Add User</span></button>
        <div class="form-group" id="page-wrap"><br/><br/>
          <table  width="100%" id="myTable" class="table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Login Id</th>
              <th><a href="#" name="name" title="asc" ></a>User Name</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>User Type</th>
              <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Phone No.</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Email-id</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">View/Edit</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			$sql=mysql_query("Select * from admin_users order by uid");
			while($row=mysql_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $row['username'];?></td>
              <td><?php echo $row['name'];?></td>
              <td><?php echo $row['utype'];?></td>
              <td><?php echo $row['phone'];?></td>
              <td><?php echo $row['emailid'];?></td>
              <td><?php echo $row['status'];?></td>
              <td><a class='btn red' href='addAdminUser.php?op=edit&id=<?php echo $row['username'];?>&date_by=<?=$_REQUEST[date_by]?>&ten=<?=$_REQUEST[ten]?>&fdate=<?=$_REQUEST[fdate]?>&tdate=<?=$_REQUEST[tdate]?>&state=<?=$_REQUEST[state]?>&tno=<?=$_REQUEST[tno]?>'  title='view'><img src="../img/view4.png" border="0" title="view detail"/></a></td>
            </tr>
            <?php }?>
          </tbody>
          </table>
        </div>
      </form>
    </div>
  </div>
<?php
include("../includes/footer.php");
?>
</div>
</body>
</html>
