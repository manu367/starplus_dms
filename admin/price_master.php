<?php
////// Function ID ///////
$fun_id = array("a"=>array(54));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
////// filters value/////
## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected product
if($product!=""){
	$product="product_code='".$product."'";
}else{
	$product="1";
}
## selected location type
if($locationtype!=""){
	$loc_type="location_type='".$locationtype."'";
}else{
	$loc_type="1";
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
 <script type="text/javascript" src="../js/ajax.js"></script>
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
      <h2 align="center"><i class="fa fa-inr"></i>&nbsp;Price Master</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
      <?php
		if(isset($_SESSION["logres"]) && $_SESSION["logres"]){
		echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:#e51111;margin:15px;font-size:12px;">';
		echo '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"];
		echo '<br/><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.implode(" , ",$_SESSION["logres"]["invalid"]);
		echo '</div>';
		}
		unset($_SESSION["logres"]);
		?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Location State:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="locationstate" id="locationstate" class="form-control"  onChange="document.form1.submit();">
                  <option value=''>--Please Select-</option>
                  <?php
				$circlequery="select distinct(state) from asc_master order by state";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
				  <option value="<?=$circlearr['state']?>"<?php if($_REQUEST['locationstate']==$circlearr['state']){ echo "selected";}?>><?=ucwords($circlearr['state'])?></option>
				<?php 
				}
                ?>
                </select>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-md-5 control-label">Product:</label>
            <div class="col-md-5">
                <select  name='product' id="product" class='form-control'  onChange="document.form1.submit();">
                  <option value=''>--Please Select-</option>
				  <?php
				$model_query="SELECT * FROM product_master ";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['productcode']?>"<?php if($_REQUEST['product']==$br['productcode']){echo 'selected';}?>><?=getProduct($br['productcode'],$link1)?></option>
				<?php
                }
				?>
               </select>
            </div>
          </div>
	    </div><!--close form group-->
	    <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location Type:</label>
            <div class="col-md-5">
               <select  name="locationtype" id="locationtype" class='form-control' >
                 <option value=''>--Please Select-</option>
                 <?php
				$type_query="SELECT locationname,locationtype FROM location_type where status='A' order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']?>"<?php if($_REQUEST['locationtype']==$br_type['locationtype']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
               </select>
            </div>
          </div>
		  <div class="col-md-3"><label class="col-md-5 control-label"></label>
            <div class="col-md-3">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-3"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-3" align="left">
               <?php
			    //// get excel process id ////
				$processid=getExlCnclProcessid("Location",$link1);
			    ////// check this user have right to export the excel report
			    if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("pricemaster")?>&rheader=<?=base64_encode("Price Master")?>&locstate=<?=base64_encode($_GET['locationstate'])?>&product=<?=base64_encode($_GET['product'])?>&loctype=<?=base64_encode($_GET['locationtype'])?>" title="Export price details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export price details in excel"></i></a>
               <?php
				}
				?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">  
			
        <button title="Add New Price" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='addPrice.php?op=add<?=$pagenav?>'"><span>Add New Price</span></button>&nbsp;&nbsp;
        <button title="Upload Price" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='price_uploader.php?op=upload<?=$pagenav?>'"><span>Upload Price</span></button> 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th style="text-align:center;" ><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>State</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Location Type</th>
              <th style="text-align:center;" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Product</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Product Mrp</th>
			  <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Product Price</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Status</th>
			  <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View/Edit</th>
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
			 
			$sql1 = "SELECT * FROM price_master where $loc_state and $product and $loc_type order by id ";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td style="text-align:center;" ><?php echo $i ;?><div align="left"></div></td>
		<td><div align="left">&nbsp;<?php echo $row1['state']?></div></td>
          <td><div align="left">&nbsp;<?php echo getLocationType($row1['location_type'],$link1);?></div></td>
          <td><?php echo getProduct($row1['product_code'],$link1)." - ".$row1['product_code'];?></td>
          <td style="text-align:center;" ><div align="right">&nbsp;<?php echo $row1['mrp']?></div></td>
          <td align="right"><?php echo $row1['price']?></td>
               <td style="text-align:center;" ><?php echo $row1['status']?></td>
          <td align="center"><a href='edit_price.php?op=edit&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
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
<?php
include("../includes/footer.php");
?>
</body>
</html>