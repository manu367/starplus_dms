<?php
////// Function ID ///////
$fun_id = array("u"=>array(151)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//////////////////////////
$_SESSION['msgcatalog'] ="";
////// initialize filter values
if(isset($_REQUEST['status'])){$selstatus=$_REQUEST['status'];}else{$selstatus="";}
if(isset($_REQUEST['product_cat'])){$selpc=$_REQUEST['product_cat'];}else{$selpc="";}
if(isset($_REQUEST['product_subcat'])){$selpsc=$_REQUEST['product_subcat'];}else{$selpsc="";}
//////////// get operational rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#account-grid').DataTable( {
		"responsive": true, 
		"processing": true,
		"serverSide": true,
		"order":  [[0,"asc"]],
		"ajax":{
			url :"../pagination/catalog-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "status": "<?=$selstatus?>", "product_cat": "<?=$selpc?>", "product_subcat": "<?=$selpsc?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".account-grid-error").html("");
				$("#account-grid").append('<tbody class="account-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#account-grid_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-book"></i> Catalog Master</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
	  <div class="row">
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Category</label>
        	<select  name='product_cat' id="product_cat" class='form-control selectpicker' data-live-search="true" onChange="document.form1.submit();">
                  <option value=''>All</option>
                  <?php
                    $res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                    while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$_REQUEST["product_cat"]){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
               </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Sub-Category</label>
        	<select  name='product_subcat' id="product_subcat" class='form-control selectpicker' data-live-search="true" onChange="document.form1.submit();">
                  <option value=''>All</option>
                  <?php
                  $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1'  and productid = '".$_REQUEST['product_cat']."' ");
                  while($row_pcat=mysqli_fetch_array($pcat)){
                  ?>
                  <option value="<?=$row_pcat['psubcatid']?>"<?php if($row_pcat['psubcatid']==$_REQUEST["product_subcat"]){ echo 'selected'; }?>>
                  <?=$row_pcat['prod_sub_cat']?>
                  </option>
                  <?php
                  }
                  ?>
               </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Status</label>
        	<select name="status" id="status" class="form-control">
                <option value=""<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==''){ echo "selected";}}?>>All</option>
                <option value="Active"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']=="Active"){ echo "selected";}}?>>Active</option>
                <option value="Deactive"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']=="Deactive"){ echo "selected";}}?>>Deactive</option>
            </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
        </div>
      </div>
      <br/>
      <div class="row">
      	<div class="col-sm-6 col-md-6 col-lg-6">
		   <!-- excel export code here-->
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <button title="Add New Catalog" type="button" class="btn<?=$btncolor?>" style="float:right;margin-bottom:20px" onClick="window.location.href='add_catalog.php?op=add<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add New</button>
        </div>
      </div>
	  </form>
      <form class="form-horizontal" role="form">
       <table  width="100%" id="account-grid" class="display table-striped" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Category</th>
              <th>Sub-Category</th>
              <th>Catalog Name</th>
              <th>Status</th>
              <th>View/Edit</th>
            </tr>
          </thead>
          </table>
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