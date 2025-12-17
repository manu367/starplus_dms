<?php
require_once("../config/config.php");
if(isset($_REQUEST['status'])){$selstatus=$_REQUEST['status'];}else{$selstatus="";}
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
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
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#product-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],
		"ajax":{
			url :"../pagination/chargingsite-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>" ,"status": "<?php echo $selstatus;?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".product-grid-error").html("");
				$("#product-grid").append('<tbody class="product-grid-error"><tr><th colspan="4">No data found in the server</th></tr></tbody>');
				$("#product-grid_processing").css("display","none");
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
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class=" fa fa-bolt fa-lg"></i> Charging Site Master </h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?> 
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
      <div class="row">
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Status</label>
        	<select name="status" id="status" class="form-control custom-select" onChange="document.form1.submit();">
                    <option value=""<?php if($selstatus==''){ echo "selected";}?>>All</option>
                    <option value="A"<?php if($selstatus=="A"){ echo "selected";}?>>Active</option>
                    <option value="D"<?php if($selstatus=="D"){ echo "selected";}?>>Deactive</option>
                </select>
                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"></div>
        <div class="col-sm-3 col-md-3 col-lg-3"></div>
        <div class="col-sm-3 col-md-3 col-lg-3"></div>
      </div>
      <br/>
      <div class="row">
      	<div class="col-sm-6 col-md-6 col-lg-6">
        	<?php
			    ////// check this user have right to export the excel report
			    	//if($get_opr_rgts['excel']=="Y"){
			   ?>
               <?php /*?><a href="../excelReports/productmaster.php?status=<?=base64_encode($selstatus)?>" title="Export product category details in excel"><i class="fa fa-file-excel-o fa-2x faicon excelicon" title="Export product category details in excel"></i></a><?php */?>
               <?php
				//}
			//}
				?>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
        	<button title="Add New Charging Site" type="button" class="btn <?=$btncolor?>"  style="float:right;margin-bottom:20px" onClick="window.location.href='add_charging_site.php?op=Add<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add New Charging Site</button>
        </div>
      </div>
	  </form>
      <form class="form-horizontal" role="form">
        <!--<div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="product-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th style="text-align:center;" >S.No</th>
              <th style="text-align:center;" >Charging Site Name</th>
              <th style="text-align:center;" >Charging Site Code</th>
              <th style="text-align:center;" >Manufacturing Unit</th>
			  <th style="text-align:center;" >Status</th>
              <th style="text-align:center;" >View/Edit</th>
            </tr>
          </thead>
          </table>
          <!--</div>-->
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
