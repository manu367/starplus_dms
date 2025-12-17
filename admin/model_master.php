<?php
////// Function ID ///////
$fun_id = array("a"=>array(27));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
////// filters value/////
## selected brand
if($brand!=""){
	$pro_brand="brand='".$brand."'";
}else{
	$pro_brand="1";
}
## selected product cat
if($product_cat!=""){
	$pc = "productid='".$product_cat."'";
}else{
	$pc = "1";
}
## selected product sub cat
if($product_sub_cat!=""){
	$psc = "productsubcat='".$product_sub_cat."'";
}else{
	$psc = "1";
}
## selected product
if($product!=""){
	$product="productcode='".$product."'";
}else{
	$product="1";
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
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
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-tablet"></i>&nbsp;Product Master</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
      <div class="row">
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Brand</label>
        	<select name="brand" id="brand" class="form-control"  onChange="document.form1.submit();">
                  <option value=''>--Select Brand--</option>
                  <?php
				$brand="SELECT id,make FROM make_master ORDER BY make";
				$circleresult=mysqli_query($link1,$brand) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
				  <option value="<?=$circlearr['id']?>"<?php if($_REQUEST['brand']==$circlearr['id']){ echo "selected";}?>><?=ucwords($circlearr['make'])?></option>
				<?php 
				}
                ?>
        	</select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product Category</label>
        	<select  name='product_cat' id="product_cat" class='form-control'  onChange="document.form1.submit();">
                  <option value=''>--Select Product Cat-</option>
				  	<?php
					$pc_qry ="SELECT * FROM product_cat_master ORDER BY cat_name";
					$pc_res = mysqli_query($link1,$pc_qry);
					while($pc_row = mysqli_fetch_array($pc_res)){
			    	?>
				  	<option value="<?php echo $pc_row['catid'];?>"<?php if($_REQUEST['product_cat']==$pc_row['catid']){ echo "selected";}?>><?=$pc_row['cat_name']?></option>
				<?php
                }
				?>
        	</select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product Sub Category</label>
        	<select  name='product_sub_cat' id="product_sub_cat" class='form-control'  onChange="document.form1.submit();">
                  <option value=''>--Select Product Sub Cat-</option>
				  	<?php
					$psc_qry ="SELECT * FROM product_sub_category WHERE ".$pc." ORDER BY prod_sub_cat";
					$psc_res = mysqli_query($link1,$psc_qry);
					while($psc_row = mysqli_fetch_array($psc_res)){
			    	?>
				  <option value="<?php echo $psc_row['psubcatid'];?>"<?php if($_REQUEST['product_sub_cat']==$psc_row['psubcatid']){ echo "selected";}?>><?=$psc_row['prod_sub_cat']?></option>
				<?php
                }
				?>
        	</select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Product</label>
        	<select  name='product' id="product" class='form-control selectpicker' data-live-search="true" onChange="document.form1.submit();">
                  <option value=''>--Select Product-</option>
				  <?php
				$model_query="SELECT * FROM product_master WHERE ".$psc." AND ".$pro_brand." ORDER BY productname";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?php echo $br['productcode'];?>"<?php if($_REQUEST['product']==$br['productcode']){ echo "selected";}?>><?=$br['productname']." | ".$br['productcolor']." | ".$br['productcode']?></option>
				<?php
                }
				?>
        	</select>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
      </div>
      <br/>
      <div class="row">
      	<div class="col-sm-6 col-md-6 col-lg-6">
        	<?php
			//// get excel process id ////
			//$processid=getExlCnclProcessid("Product",$link1);
			////// check this user have right to export the excel report
			//if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
		   ?>
		   <a href="excelexport.php?rname=<?=base64_encode("productmaster")?>&rheader=<?=base64_encode("Product Master")?>&brand=<?=base64_encode($_GET['brand'])?>&product_cat=<?=base64_encode($_GET['product_cat'])?>&product_sub_cat=<?=base64_encode($_GET['product_sub_cat'])?>&product=<?=base64_encode($_GET['product'])?>" title="Export Product details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Product details in excel"></i></a>
		   <?php
			//}
			?>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
        	<button title="Add New Product" type="button" class="btn<?=$btncolor?>" style="float:right;margin-bottom:20px" onClick="window.location.href='addModel.php?op=add<?=$pagenav?>'"><span>Add New Product</span></button>&nbsp;&nbsp;
            <button title="Upload Product" type="button" class="btn<?=$btncolor?>" style="float:right;margin-bottom:20px" onClick="window.location.href='partcodeUpload.php?op=upload<?=$pagenav?>'"><span>Upload Product</span></button>
        </div>
      </div>
	  </form>
      <form class="form-horizontal" role="form">
      <!--<div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
	   <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th style="text-align:center;" ><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Product Code</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Product Name</th>
              <th style="text-align:center;" data-hide="phone"><a href="#" name="name" title="asc" ></a>Model Name</th>
              <th style="text-align:center;" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Product Category</th>
              
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Product Sub Category</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Brand</th>
              <th style="text-align:center;" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>HSN Code</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
			  <th style="text-align:center;" data-hide="phone,tablet">Created Date</th>
			  <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View/Edit</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1;
	  	$sql1 = "SELECT * FROM product_master WHERE ".$pro_brand." AND ".$psc." AND ".$product." ORDER BY productname";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) {
	  
	    ?>
	    <tr>
	      <td><?php echo $i;?></td> 
          <td><?php echo $row1['productcode']?></td>
          <td align="left"><?php echo $row1['productname']?></td>
          <td align="left"><?php echo $row1['model_name']?></td>
          <td align="left"><?php echo getAnyDetails($row1['productcategory'],"cat_name","catid" ,"product_cat_master"  ,$link1);?></td>
          <td align="left"><?php echo getAnyDetails($row1['productsubcat'],"prod_sub_cat","psubcatid" ,"product_sub_category"  ,$link1);?></td>
          <td align="left"><?php echo   getAnyDetails($row1['brand'],"make","id" ,"make_master"  ,$link1);?></td>
          <td align="center"><?php echo $row1['hsn_code']?></td>
          <td align="center"><?php echo $row1['status']?></td>
          <td align="center"><?php echo $row1['createdate']?></td>
         <td align="center"><a href='edit_model.php?op=edit&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
      </tr>
	   <?php 
	  $i++;
	   }  
	   ?>
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