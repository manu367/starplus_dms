<?php
require_once("../config/config.php");
$date=date("Y-m-d");
@extract($_GET);
##seleceted date
if($fdate=='' || $tdate=='')
{
	$sql_date='1';
}
else
{
    $sql_date="(entry_date>='".$fdate."' and entry_date<='".$tdate."')";
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
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
</script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<script>
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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-book"></i>&nbsp;Query / Feedback Master</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <form id="frm1" name="frm1" class="form-horizontal" action="" method="get">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
             
              <div class="col-md-3 input-append date">
  					<div ><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-2 control-label">To Date</label>
              
             <div class="col-md-2 input-append date">
  					<div ><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
             <label class="col-md-1 control-label"></label>
            <div class="col-md-1" style="display:inline-block;float:right;">
               <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
            </div>
         
          </div>
        </div>
		<div class="col-md-6">
              
	    </div><!--close form group-->
		</form>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Add New Query" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='add_query.php?op=add<?=$pagenav?>'"><span>Add New Query / Feedback</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
			  <th ><a href="#" name="entity_id" title="asc" ></a>Doc No.</th>
			  <th ><a href="#" name="entity_id" title="asc" ></a>Module</th>
              <th><a href="#" name="name" title="asc" class="not-sort"></a>Problem Type</th>
              
              <th><a href="#" name="name" title="asc" ></a>Problem / Feedback</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Due Date </th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Entry By</th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View</th>
			   <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Reply</th>
			  
             
			  
              
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
			 
			$sql1 = "SELECT * FROM query_master  where $sql_date order by id desc ";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
		<td><?php echo $row1['query']?></td>
		 <td><?php echo $row1['module']?></td>
		<td > <?php echo $row1['problem']?></td>
         
          <td ><?php echo $row1['request']?></td>
		  <td ><?php echo $row1['entry_date']?></td>
		  <td><?=getAdminDetails($row1['entry_by'],"name",$link1);?></td>
          
          <td align="center"><a href='view_query.php?op=edit&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
		  <td align="center"><a href='reply_query.php?op=reply&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='reply'><i class="fa fa-edit fa-lg" title="reply"></i></a></td>
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