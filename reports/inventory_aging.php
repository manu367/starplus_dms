<?php
////// Function ID ///////
$fun_id = array("a"=>array(4));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
## selected location
if($locationcode!=""){
	$loc="asc_code='".$locationcode."'";
	$imeiloc="owner_code='".$locationcode."'";
}else{
	$locstr=getAccessLocation($_SESSION['userid'],$link1);
	$loc="asc_code in (".$locstr.")";
	$imeiloc="owner_code in (".$locstr.")";
}
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
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-linode fa-lg"></i>Stock Aging</h2><br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
      <form class="form-horizontal" role="form" name="form1" action="" method="get">
      <div class="row">
        <div class="col-sm-3" align="right"><label class="control-label">Product Category</label></div>
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
        <div class="col-sm-3" align="right"><label class="control-label">Product Sub Category</label></div>
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
        <div class="col-sm-3"><br/></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-3" align="right"><label class="control-label">Brand</label></div>
        <div class="col-sm-3"><select name="brand" id="brand" class="form-control"  onChange="document.form1.submit();">
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
        <div class="col-sm-3" align="right"><label class="control-label">Product</label></div>
        <div class="col-sm-3"><select name="partcode" id="partcode" class="form-control"  onChange="document.form1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql4 = "select productcode, productname from product_master where ".$pcat." and ".$pscat." and ".$brd." and status='active' order by productname";
					$res4 = mysqli_query($link1,$sql4) or die(mysqli_error($link1));
					while($row4 = mysqli_fetch_array($res4)){
					?>
				  	<option value="<?=$row4['productcode']?>"<?php if($_REQUEST['partcode']==$row4['productcode']){ echo "selected";}?>><?=$row4['productname']?></option>
					<?php 
					}
                	?>
                </select></div>
      </div>
      <div class="row">
        <div class="col-sm-3"><br/></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-3" align="right"><label class="control-label">Location Type</label></div>
        <div class="col-sm-3"><select name="loc_type" id="loc_type" class="form-control"  onChange="document.form1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql3 = "select locationtype, locationname from location_type where status='A' order by seq_id";
					$res3 = mysqli_query($link1,$sql3) or die(mysqli_error($link1));
					while($row3 = mysqli_fetch_array($res3)){
					?>
				  	<option value="<?=$row3['locationtype']?>"<?php if($_REQUEST['loc_type']==$row3['locationtype']){ echo "selected";}?>><?=$row3['locationname']?></option>
					<?php 
					}
                	?>
                </select></div>
        <div class="col-sm-3" align="right"><label class="control-label"></label></div>
        <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-3"><br/></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-3" align="right"><label class="control-label">Location:</label></div>
        <div class="col-sm-6"><select name="locationcode" id="locationcode" class="form-control selectpicker" data-live-search="true">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					if($_REQUEST['loc_type']){ $loctype = " AND id_type LIKE '".$_REQUEST['loc_type']."'";}else{$loctype = "";}
					$sql_chl="select * from access_location where uid='".$_SESSION['userid']."' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code LIKE '".$result_chl['location_id']."'".$loctype));
	                    if($party_det['name']){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
						  }
					
                    ?>
                 </select>
              </div>
              <div class="col-sm-3"><input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/></div>
      </div>
      <?php //if($_REQUEST['locationcode']!=''){ ?>
      <div class="row">
        <div class="col-sm-12 table-responsive">
        <div style="float:right">
            <?php
			    //// get excel process id ////
				//echo $processid=getExlCnclProcessid("Inventory",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
        <strong>Excel Export</strong>&nbsp;&nbsp;&nbsp;&nbsp; <a href="excelexport.php?rname=<?=base64_encode("inventory_aging")?>&rheader=<?=base64_encode("InventoryAging")?>&loc=<?=base64_encode($_GET['locationcode'])?>&product_cat=<?=base64_encode($_GET['product_cat'])?>&product_subcat=<?=base64_encode($_GET['product_subcat'])?>&brand=<?=base64_encode($_GET['brand'])?>&partcode=<?=base64_encode($_GET['partcode'])?>" title="Export inventory aging details in excel" style="float:right"><i class="fa fa-file-excel-o fa-2x" title="Export inventory aging details in excel"></i></a><br/><br/>
        <?php
				//}
				?>
                </div>
       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th rowspan="2"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th rowspan="2" data-class="expand"><a href="#" name="name" title="asc" ></a>Location Name</th>
              <th rowspan="2">Sub Location</th>
              <th rowspan="2"><a href="#" name="name" title="asc" ></a>Location Type</th>
              <th rowspan="2" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Product Code</th>
              <th rowspan="2" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Product Name</th>
              <th rowspan="2" data-hide="phone,tablet">Product Nature</th>
              <th colspan="3" data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>0 - 30 days</th>
              <th colspan="3" data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>31 - 90 days</th>
              <th colspan="3" data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Above 90 days</th>
            </tr>
            <tr class="<?=$tableheadcolor?>">
              <th data-hide="phone,tablet">OK</th>
              <th data-hide="phone,tablet">DM</th>
              <th data-hide="phone,tablet">MS</th>
              <th data-hide="phone,tablet">OK</th>
              <th data-hide="phone,tablet">DM</th>
              <th data-hide="phone,tablet">MS</th>
              <th data-hide="phone,tablet">OK</th>
              <th data-hide="phone,tablet">DM</th>
              <th data-hide="phone,tablet">MS</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			$new_loc = "";
			$old_loc = "";
			$sql=mysqli_query($link1,"Select a.asc_code, a.sub_location, a.partcode, a.okqty, a.broken, a.missing FROM stock_status a, product_master b WHERE a.partcode=b.productcode AND ".$loc." AND ".$pcat_s." AND ".$pscat_s." AND ".$brd_s." AND ".$part_code);
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  $new_loc = $row['asc_code'];
				  if($old_loc != $new_loc){
				  	$locdet=explode("~",getLocationDetails($row['asc_code'],"name,city,state,id_type",$link1));
				  }
				  /// sub location
				  $subloc=getLocationDetails($row['sub_location'],"name,city,state",$link1);
				  $explodevalf=explode("~",$subloc);
				  if($explodevalf[0]){ $sublocname=str_replace("~",",",$subloc); }else{ $sublocname=getAnyDetails($row['sub_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
				  
				  $prd_det = getProductDetails($row['partcode'],"productname,productcolor,eol",$link1);
				  $prd = explode("~",$prd_det);
				  $fifo_qty = explode("~",getFIFOStock($row['asc_code'],$row['partcode'],$row['okqty'],$row['broken'],$row['missing'],$link1));
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $locdet[0].",".$locdet[1].",".$row['asc_code'];?></td>
              <td><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$row['asc_code'];}?></td>
              <td><?=getLocationType($locdet[3],$link1);?></td>
              <td><?php echo $row['partcode'];?></td>
              <td><?php echo $prd[0].",".$prd[1];?></td>
              <td><?php 
			  ///// check product is EOL or not
			  if($prd[2]!='0000-00-00'){
				  if($prd[2] < $today){
					  $prd_natute = "EOL";
				  }
			  }else{
				  /// check product is slow/average/fast moving
				  if($fifo_qty[0] > $fifo_qty[1] && $fifo_qty[0] > $fifo_qty[2]){
					  $prd_natute = "Fast Moving";
				  }else if($fifo_qty[2] < $fifo_qty[0] && $fifo_qty[2] < $fifo_qty[1]){
					  $prd_natute = "Slow Moving";
				  }else{
					  $prd_natute = "Average Moving";
				  }
			  }
			  echo $prd_natute;?></td>
              <td align="right"><?php echo $fifo_qty[0];?></td>
              <td align="right"><?php echo $fifo_qty[3];?></td>
              <td align="right"><?php echo $fifo_qty[6];?></td>
              <td align="right"><?php echo $fifo_qty[1];?></td>
              <td align="right"><?php echo $fifo_qty[4];?></td>
              <td align="right"><?php echo $fifo_qty[7];?></td>
              <td align="right"><?php echo $fifo_qty[2];?></td>
              <td align="right"><?php echo $fifo_qty[5];?></td>
              <td align="right"><?php echo $fifo_qty[8];?></td>
            </tr>
            <?php  $old_loc = $row['asc_code'];}?>
          </tbody>
          </table> 
         </div>
      </div>
      <?php //}?>
	  </form>   
      </div>  
    </div><!--close tab pane-->
  </div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>