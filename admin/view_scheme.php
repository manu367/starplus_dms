<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST['id']);
////// get details of selected scheme////
$res_schm = mysqli_query($link1,"SELECT * FROM scheme_master WHERE id='".$getid."'")or die(mysqli_error($link1));
$row_schm = mysqli_fetch_array($res_schm);
$proddet = explode("~",getProductDetails($row_schm['productcode'],"productname,productcolor",$link1));
$prodcat = getAnyDetails($row_schm['productcategory'],"cat_name","catid" ,"product_cat_master"  ,$link1);
$prodsub = getAnyDetails($row_schm['productsubcat'],"prod_sub_cat","psubcatid" ,"product_sub_category" ,$link1);
$prodbrd = getAnyDetails($row_schm['brand'],"make","id" ,"make_master"  ,$link1);
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
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-tags"></i>&nbsp;View Scheme</h2>
      		<h4 align="center"><?php echo "( ".$row_schm['scheme_code']." )"; ?></h4>
			<br>
	  		<div class="panel-group">
    			<div class="panel panel-info table-responsive">
                    <div class="panel-heading heading1">Product Information</div>
                     <div class="panel-body">
                      <table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Product Category</label></td>
                            <td width="30%"><?php echo $prodcat;?></td>
                            <td width="20%"><label class="control-label">Product Sub Category</label></td>
                            <td width="30%"><?php echo $prodsub;?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Brand</label></td>
                            <td width="30%"><?php echo $prodbrd;?></td>
                            <td width="20%"><label class="control-label">Product</label></td>
                            <td width="30%"><?php echo $proddet[0]." - ".$row_schm['productcode'];?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div><!--close panel body-->
                </div><!--close panel-->
                <div class="panel panel-info table-responsive">
                    <div class="panel-heading heading1">Scheme Information</div>
                     <div class="panel-body">
                      <table class="table table-bordered" width="100%">
                        <tbody>
                          <tr class="alert-warning">
                            <td width="20%"><label class="control-label">Scheme Name</label></td>
                            <td colspan="3"><?php echo $row_schm['scheme_name'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Scheme Ref. No.</label></td>
                            <td width="30%"><?php echo $row_schm['scheme_code'];?></td>
                            <td width="20%"><label class="control-label">Status</label></td>
                            <td width="30%"><?php echo $row_schm['status'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Valid From</label></td>
                            <td width="30%"><?php echo $row_schm['from_date'];?></td>
                            <td width="20%"><label class="control-label">Valid To</label></td>
                            <td width="30%"><?php echo $row_schm['to_date'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Applicable On</label></td>
                            <td width="30%"><?php echo $row_schm['scheme_based_type'];?></td>
                            <td width="20%"><label class="control-label"><?=$row_schm['scheme_based_type']?></label></td>
                            <td width="30%"><?php echo $row_schm['scheme_based_on'];?></td>
                          </tr>
                          <tr>
                            <td width="20%"><label class="control-label">Scheme Given</label></td>
                            <td width="30%"><?php echo $row_schm['scheme_given_type'];?></td>
                            <td width="20%"><label class="control-label"><?=$row_schm['scheme_given_type']?></label></td>
                            <td width="30%"><?php echo $row_schm['scheme_given'];?></td>
                          </tr>
                          <?php if($row_schm['scheme_attachment']){?>
                          <tr>
                            <td><label class="control-label">Scheme Attachment</label></td>
                            <td colspan="3"><a href="../scheme/<?=$row_schm['scheme_attachment']?>" target="_blank"><i class="fa fa-download"></i></a></td>
                          </tr>
                          <?php }?>
                        </tbody>
                      </table>
                    </div><!--close panel body-->
                </div><!--close panel-->
                <div class="panel panel-info table-responsive">
                    <div class="panel-heading heading1">Entry Information</div>
                     <div class="panel-body">
                      <table class="table table-bordered" width="100%">
                        <tbody>
                          <tr>
                            <td width="20%"><label class="control-label">Entry By</label></td>
                            <td width="30%"><?php echo getAdminDetails($row_schm['entry_by'],"name",$link1);?></td>
                            <td width="20%"><label class="control-label">Entry Date</label></td>
                            <td width="30%"><?php echo $row_schm['entry_date'];?></td>
                          </tr>
                          <tr>
                            <td><label class="control-label">Entry Remark</label></td>
                            <td colspan="3"><?php echo $row_schm['remark'];?></td>
                          </tr>
                          <tr>
                            <td colspan="4" align="center"><input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='scheme_master.php?fdate=<?=$_REQUEST['fdate']?>&tdate=<?=$_REQUEST['tdate']?>&product_cat=<?=$_REQUEST['product_cat']?>&product_subcat=<?=$_REQUEST['product_subcat']?>&brand=<?=$_REQUEST['brand']?>&product=<?=$_REQUEST['product']?>&scheme_status=<?=$_REQUEST['scheme_status']?><?=$pagenav?>'"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div><!--close panel body-->
                </div><!--close panel-->
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