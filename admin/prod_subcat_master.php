<?php
////// Function ID ///////
$fun_id = array("a"=>array(50));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

if(isset($_REQUEST['status'])){$selstatus=$_REQUEST['status'];}else{$selstatus="";}
if(isset($_REQUEST['prod_cat'])){$selpc=$_REQUEST['prod_cat'];}else{$selpc="";}
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
	var dataTable = $('#prod-subcat-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],
		"ajax":{
			url :"../pagination/prodsubcat-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "status": "<?=$selstatus?>", "prod_cat": "<?=$selpc?>"},
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
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-cog fa-lg"></i> Product Sub Category</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
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
                        <option value="1"<?php if($selstatus==1){ echo "selected";}?>>Active</option>
                        <option value="2"<?php if($selstatus==2){ echo "selected";}?>>Deactive</option>
                    </select>
                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product Category</label>
            	<select name="prod_cat" id="prod_cat" class="form-control custom-select" onChange="document.form1.submit();">
                  <option value=""<?php if($selpc==''){ echo "selected";}?>>All</option>
                  <?php
                	$res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                	while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$selpc){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
                </select></div>
            <div class="col-sm-3 col-md-3 col-lg-3"></div>
            <div class="col-sm-3 col-md-3 col-lg-3"></div>
          </div>
          <br/>
          <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                <?php
			   //if($get_opr_rgts['excel']=="Y"){
			   ?>
               <a href="../excelReports/prodsubcatmaster.php?status=<?=base64_encode($selstatus)?>&prod_cat=<?=base64_encode($selpc)?>" title="Export product sub category details in excel"><i class="fa fa-file-excel-o fa-2x faicon excelicon" title="Export product sub category details in excel"></i></a>
               <?php
			   //}
				?>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6">
                <button title="Add New Product Sub Category" type="button" class="btn <?=$btncolor?>"  style="float:right;margin-bottom:20px" onClick="window.location.href='add_prod_subcat.php?op=Add<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add New Product Sub Category</button>
            </div>
          </div>
	  </form>
      <form class="form-horizontal" role="form">
		<!--<div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="prod-subcat-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th style="text-align:center;" >S.No</th>
              <th style="text-align:center;" >Product Sub Category</th>
              <th style="text-align:center;" >Product Category</th>
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
