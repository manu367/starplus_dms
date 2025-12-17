<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);

///// find users details /////////////
$usr_sql = mysqli_query($link1, "SELECT uid FROM mapped_user WHERE mapped_code = '".$docid."' and status = 'Y' ");
if(mysqli_num_rows($usr_sql)>0){
while($r =  mysqli_fetch_assoc($usr_sql)){
	$b .= $r['uid'].",";
}
	$user_str1 = explode(",",$b.$docid);
}else{
	$user_str2 = $docid;
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
 <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
		$('#myTable1').dataTable();
	});
 </script>
 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-wrench"></i> Work Related </h2>
      <h4 align="center">
          <?=getAnyDetails($docid,'name','username','admin_users',$link1)."  (".$docid.")";?>
        </h4>
		<br>
<!---------------- Use for target list ---------------------->
<div class="panel-group">
<div class="panel panel-info table-responsive">
<div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Target List</div>
<div class="panel-body">

<div class="row">
	<div class="col-sm-12 table-responsive">
    
    	<table  width="99%" id="myTable1" class="table-striped table-bordered table-hover" align="center">
        	<thead>
            	<tr class="<?=$tableheadcolor?>">
              		<th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
                    <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Employee</th>
                    <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Targets</th>
                    <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
            	</tr>
          	</thead>
          	<tbody>
            <?php
			if($user_str1 != ""){
			$sno=0;
			for($i=0; $i<count($user_str1); $i++){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno;?></td>
              <td><?php echo getAnyDetails($user_str1[$i],'name','username','admin_users',$link1)." | ".$user_str1[$i]; ?></td>
              <td>
				  <p>
					<?php 
						$tar_sql = mysqli_query($link1, "SELECT target_no FROM sf_target_master WHERE emp_id = '".$user_str1[$i]."' ");
						$tar_count1 = mysqli_num_rows($tar_sql);
						while($w = mysqli_fetch_assoc($tar_sql)){
					?>		
							<a href="admin_target_view.php?id=<?=base64_encode($w['target_no']);?>&user=<?=base64_encode($user_str1[$i]);?><?=$pagenav?>" title="View Details"><?php echo $w['target_no']; ?></a>
					<?php 
						echo " , ";
						}
					?>
				  </p>
              </td>
              <td style="text-align:center;">
			  <?php if($tar_count1>0){ ?>
			  	<a href="admin_target_monthly_view.php?id=<?=base64_encode($row['id']);?>&user=<?=base64_encode($docid);?><?=$pagenav?>" title="View"><i class="fa fa-eye fa-lg" title="View"></i></a>
			  <?php } ?>	
			  </td>
            </tr>
            <?php }}else{ $sno = 1; ?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno;?></td>
              <td><?php echo getAnyDetails($user_str2,'name','username','admin_users',$link1)." | ".$user_str2; ?></td>
              <td>
				  <p>
					<?php 
						$tar_sql2 = mysqli_query($link1, "SELECT target_no FROM sf_target_master WHERE emp_id = '".$user_str2."' ");
						$tar_count2 = mysqli_num_rows($tar_sql2);
						while($u = mysqli_fetch_assoc($tar_sql2)){
					?>		
							<a href="admin_target_view.php?id=<?=base64_encode($u['target_no']);?>&user=<?=base64_encode($user_str2);?><?=$pagenav?>" title="View"><?php echo $u['target_no']; ?></a>
					<?php   
						echo " , ";     
						}
					?>
				  </p>
              </td>
              <td style="text-align:center;">
			  <?php if($tar_count2>0){ ?>
			  	<a href="admin_target_monthly_view.php?id=<?=base64_encode($row['id']);?>&user=<?=base64_encode($docid);?><?=$pagenav?>" title="View"><i class="fa fa-eye fa-lg" title="View"></i></a>
			  <?php } ?>		
			  </td>
            </tr>
            <?php }?>
        	</tbody>
    	</table> 
	</div>
</div>

</div>                                                      
</div><!--close panel body-->
</div><!--close panel-->

<br><br>
<div class="form-group">
    <div class="col-md-12" style="text-align:center;" > 
       <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='emp_history.php?empcode=<?=base64_encode($docid);?><?=$pagenav?>'">
    </div>  
</div>  

<br><br>
</div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>

</body>
</html>