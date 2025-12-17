<?php
////// Function ID ///////
$fun_id = array("a"=>array(102));
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//////////////////////////
if(isset($_REQUEST['status'])){$selstatus=$_REQUEST['status'];}else{$selstatus="";}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#prod-subcat-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],
		"ajax":{
			url :"../pagination/appsteps-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "status": "<?=$selstatus?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".prod-subcat-grid-error").html("");
				$("#prod-subcat-grid").append('<tbody class="brand-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
				$("#prod-subcat-grid_processing").css("display","none");
				
			}
		}
	} );
} );
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-gavel"></i> Approval Steps Master</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Status</label>	  
			<div class="col-md-5" align="left">
			   <select name="status" id="status" class="form-control custom-select">
                    <option value=""<?php if($selstatus==''){ echo "selected";}?>>All</option>
                    <option value="1"<?php if($selstatus==1){ echo "selected";}?>>Active</option>
                    <option value="2"<?php if($selstatus==2){ echo "selected";}?>>Deactive</option>
                </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">&nbsp;</label>
			<div class="col-md-5" align="left">
			 	&nbsp;
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="icn" id="icn" type="hidden" value="<?=$_REQUEST['icn']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <?php
			   if($get_opr_rgts['excel']=="Y"){
			   ?>
               <?php /*?><a href="../excelReports/prodsubcatmaster.php?status=<?=base64_encode($selstatus)?>&prod_cat=<?=base64_encode($selpc)?>" title="Export product sub category details in excel"><i class="fa fa-file-excel-o fa-2x faicon excelicon" title="Export product sub category details in excel"></i></a><?php */?>
               <?php
			   }
				?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
       <?php
     // if($get_opr_rgts['add']=="Y"){
      ?>
        <button title="Add New Approval Step" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='add_approval_step.php?op=Add<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add New Approval Step</button>&nbsp;&nbsp;
         <?php //} ?> 
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="prod-subcat-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Approval Step Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>View/Edit</th>
            </tr>
          </thead>
          </table>
          </div>
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