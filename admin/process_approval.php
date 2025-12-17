<?php
////// Function ID ///////
$fun_id = array("a"=>array(103)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);

////// filters value/////
## selected status
if($status!=""){
	$statusdata="where status='".$status."'";
}else{
	$statusdata="";
}

//////End filters value/////

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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-sitemap"></i>&nbsp;Approval Sequence</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="status" class="form-control required" required >
				 <option value="">--Please Select--</option>
                   <option value="1" <?php if($_REQUEST['status'] == '1'){ echo 'selected';} ?>>Active</option>
	           <option value="2" <?php if($_REQUEST['status'] == '2'){ echo 'selected';} ?>>Deactive</option>
	           
            </select>
              </div>
            </div>
          <div class="col-md-6">
<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
          </div>
	    </div><!--close form group-->
	    

	  </form>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <?php /*?><button title="Add New Brand" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addBrand.php?op=add<?=$pagenav?>'"><span>Add New Brand</span></button>&nbsp;&nbsp; <?php */?>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>      
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Process Name</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Map</th>
            </tr>
          </thead>
          <tbody>
             <?php $i=1;            
		   $sql1 ="SELECT * FROM process_approval_step $statusdata order by id";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error());
	   while($row1=mysqli_fetch_assoc($rs1)) { 
             if($row1['status']=='1'){
                 $statusval = 'Active';
             }else{
                 $statusval = 'Deactive';
             }  ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
		<td><?php echo $row1['process_name']?></td>
                <td><?php echo $statusval?></td>
          <td align="center"><a href='map_process_stepapp.php?id=<?php echo base64_encode($row1['id']);?>&refname=<?=base64_encode($row1['process_name'])?><?=$pagenav?>'  title='view'><i class="fa fa-microchip fa-lg" title="Process Approval Step"></i></a></td>
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