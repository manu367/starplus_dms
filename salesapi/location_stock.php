<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
@extract($_POST);
## selected location
if($locationcode!=""){
	$loc="a.asc_code='".$locationcode."'";
	$imeiloc="a.owner_code='".$locationcode."'";
}else{
	$locstr=getAccessLocation($_REQUEST["usercode"],$link1);
	$loc="a.asc_code in (".$locstr.")";
	$imeiloc="a.owner_code in (".$locstr.")";
}
////// filters value/////
## selected Product Category
if($product_cat!=""){
	$pc = " productid='".$product_cat."'";
	$pcat = " productcategory='".$product_cat."'";
	$pcat_s= " b.productcategory='".$product_cat."'";
}else{
	$pc = " 1";
	$pcat = " 1";
	$pcat_s = " 1";
}
## selected Product Sub Category
if($product_subcat!=""){
	$psc = " psubcatid='".$product_subcat."'";
	$pscat = " productsubcat='".$product_subcat."'";
	$pscat_s = " b.productsubcat='".$product_subcat."'";
}else{
	$psc = " 1";
	$pscat = " 1";
	$pscat_s = " 1";
}
## selected brand
if($brand!=""){
	$brd = " brand='".$brand."'";
	$brd_s = " b.brand='".$brand."'";
}else{
	$brd = " 1";
	$brd_s = " 1";
}
## selected product id
if($partcode!=""){
	$part_code = " b.productcode='".$partcode."'";
}else{
	$part_code = " 1";
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
<script language="javascript">
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h2 align="center"><i class="fa fa-cubes fa-lg"></i>Stock</h2>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
      <form class="form-horizontal" role="form" name="frm1" id="frm1" action="" method="post">
      <div class="row">
        <div class="col-sm-3" align="left"><label class="control-label">Location</label></div>
        <div class="col-sm-3"><select name="locationcode" id="locationcode" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='".$_REQUEST["usercode"]."' and status='Y' AND id_type IN ('HO','BR')";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
	                    
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					
                    ?>
                 </select>
              </div>
         <div class="col-sm-3" align="left"><label class="control-label">Inventory Type</label></div>
         <div class="col-sm-3"><select name="inventorytype" id="inventorytype" class="form-control" required>
                    <option value="qty"<?php if($_REQUEST['inventorytype']=="qty"){ echo "selected";}?>>QTY INVENTORY</option>
                 </select></div>
      </div>
      <div class="row">
        <div class="col-sm-3" align="left"><label class="control-label">Product Category</label></div>
        <div class="col-sm-3"><select name="product_cat" id="product_cat" class="form-control"  onChange="document.frm1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql1 = "select catid,cat_name from product_cat_master where status='1' order by cat_name";
					$res1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
					while($row1 = mysqli_fetch_array($res1)){
					?>
				  	<option value="<?=$row1['catid']?>"<?php if($_REQUEST['product_cat']==$row1['catid']){ echo "selected";}?>><?=$row1['cat_name']?></option>
					<?php 
					}
                	?></select></div>
        <div class="col-sm-3" align="left"><label class="control-label">Product Sub Category</label></div>
        <div class="col-sm-3"><select name="product_subcat" id="product_subcat" class="form-control"  onChange="document.frm1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql2 = "select psubcatid,prod_sub_cat from product_sub_category where ".$pc." and status='1' order by prod_sub_cat";
					$res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
					while($row2 = mysqli_fetch_array($res2)){
					?>
				  	<option value="<?=$row2['psubcatid']?>"<?php if($_REQUEST['product_subcat']==$row2['psubcatid']){ echo "selected";}?>><?=$row2['prod_sub_cat']?></option>
					<?php 
					}
                	?>
                </select></div>
      </div>
      <div class="row">
        <div class="col-sm-3" align="left"><label class="control-label">Brand</label></div>
        <div class="col-sm-3"><select name="brand" id="brand" class="form-control"  onChange="document.frm1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql3 = "select id, make from make_master where status='1' order by make";
					$res3 = mysqli_query($link1,$sql3) or die(mysqli_error($link1));
					while($row3 = mysqli_fetch_array($res3)){
					?>
				  	<option value="<?=$row3['id']?>"<?php if($_REQUEST['brand']==$row3['id']){ echo "selected";}?>><?=$row3['make']?></option>
					<?php 
					}
                	?>
                </select></div>
        <div class="col-sm-3" align="left"><label class="control-label">Product</label></div>
        <div class="col-sm-3"><select name="partcode" id="partcode" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql4 = "select productcode, productname, model_name from product_master where ".$pcat." and ".$pscat." and ".$brd." and status='active' order by productname";
					$res4 = mysqli_query($link1,$sql4) or die(mysqli_error($link1));
					while($row4 = mysqli_fetch_array($res4)){
					?>
				  	<option value="<?=$row4['productcode']?>"<?php if($_REQUEST['partcode']==$row4['productcode']){ echo "selected";}?>><?=$row4['productname']." | ".$row4['model_name']." | ".$row4['productcode']?></option>
					<?php 
					}
                	?>
                </select></div>
      </div>
      <div class="row">
        <div class="col-sm-3">&nbsp;</div>
        <div class="col-sm-3"><input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
        <div class="col-sm-3">&nbsp;</div>
      </div>
      <?php if($_REQUEST['inventorytype']!=''){ ?>
      <?php if($_REQUEST['inventorytype']=="qty"){ ?>
      <div class="row">
        <div class="col-sm-12 table-responsive">
       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th data-class="expand"><a href="#" name="name" title="asc" ></a>Location Name</th>
              <!--<th>Sub Location</th>
              <th><a href="#" name="name" title="asc" ></a>Location Type</th>-->
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Product Code</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Product Name</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Ok Qty</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Damage Qty</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Missing Qty</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Total Qty</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Current Price</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Value</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			$new_loc = "";
			$old_loc = "";
			$sql=mysqli_query($link1,"Select a.asc_code, a.sub_location, a.partcode, SUM(a.okqty) as okqty, SUM(a.broken) as broken, SUM(a.missing) as missing, b.productname, b.productcolor FROM stock_status a, product_master b WHERE a.partcode=b.productcode AND ".$loc." AND ".$pcat_s." AND ".$pscat_s." AND ".$brd_s." AND ".$part_code." GROUP BY a.asc_code,a.partcode");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  $new_loc = $row['asc_code'];
				  if($old_loc != $new_loc){
				  	$locdet=explode("~",getLocationDetails($row['asc_code'],"name,city,state,id_type",$link1));
				  }
				  /// sub location
				 /* $subloc=getLocationDetails($row['sub_location'],"name,city,state",$link1);
				  $explodevalf=explode("~",$subloc);
				  if($explodevalf[0]){ $sublocname=str_replace("~",",",$subloc); }else{ $sublocname=getAnyDetails($row['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}*/
				  
	              //$proddet=str_replace("~",",",getProductDetails($row['partcode'],"productname,productcolor",$link1));
				  $price = mysqli_fetch_assoc(mysqli_query($link1,"SELECT price FROM price_master where state='".$locdet[2]."' and location_type='".$locdet[3]."' and product_code='".$row['partcode']."' and status='active'"));
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $locdet[0].",".$locdet[1].",".$row['asc_code'];?></td>
<?php /*?>              <td><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$row['asc_code'];}?></td>
              <td><?=getLocationType($locdet[3],$link1);?></td><?php */?>
              <td><?php echo $row['partcode'];?></td>
              <td><?php echo $row['productname'].",".$row['productcolor'];?></td>
              <td align="right"><?php echo $row['okqty'];?></td>
              <td align="right"><?php echo $row['broken'];?></td>
              <td align="right"><?php echo $row['missing'];?></td>
              <td align="right"><?php echo $total = $row['okqty']+$row['broken']+$row['missing'];?></td>
              <td align="right"><?=number_format($price["price"],'2')?></td>
              <td align="right"><?=number_format($price["price"]*$total,'2')?></td>
            </tr>
            <?php 
			$old_loc = $row['asc_code'];
			}?>
          </tbody>
          </table> 
         </div>
      </div>
      <?php }
	  }?>
	  </form>   
      </div>  
    </div><!--close tab pane-->
  </div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>