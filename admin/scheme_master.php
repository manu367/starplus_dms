<?php
////// Function ID ///////
$fun_id = array("a"=>array(56));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

@extract($_POST);
////// filters value/////
$filter_str = 1;
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " and from_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " and to_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['product_cat'] !=''){
	$filter_str	.= " and productcategory = '".$_REQUEST['product_cat']."'";
}
if($_REQUEST['product_subcat'] !=''){
	$filter_str	.= " and productsubcat = '".$_REQUEST['product_subcat']."'";
}
if($_REQUEST['brand'] !=''){
	$filter_str	.= " and brand = '".$_REQUEST['brand']."'";
}
if($_REQUEST['product'] !=''){
	$filter_str	.= " and productcode = '".$_REQUEST['product']."'";
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-tags"></i>&nbsp;Scheme Master</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Scheme From</label>
                     <div class="col-sm-5 col-md-5 col-lg-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Scheme To</label>
                    <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                    </div>
                  </div>
                </div><!--close form group-->
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Product Category</label>
                     <div class="col-sm-5 col-md-5 col-lg-5">
                     	 <select  name='product_cat' id="product_cat" class='form-control selectpicker' onChange="document.form1.submit();">
                          <option value=''>All</option>
                          <?php
                            $res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                            while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                          <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$_REQUEST["product_cat"]){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                          <?php } ?>
                       </select>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Product Sub Category</label>
                    <div class="col-md-5">
                    <select  name='product_subcat' id="product_subcat" class='form-control selectpicker' onChange="document.form1.submit();">
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
                  </div>
                </div><!--close form group-->
                <div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Brand</label>
                     <div class="col-sm-5 col-md-5 col-lg-5">
                     	<select name="brand" id="brand" class="form-control selectpicker"  onChange="document.form1.submit();">
                          <option value=''>All</option>
                          	<?php
                        	$brand="select id,make from make_master";
                        	$circleresult=mysqli_query($link1,$brand) or die(mysqli_error($link1));
                        	while($circlearr=mysqli_fetch_array($circleresult)){
                        	?>
                          <option value="<?=$circlearr['id']?>"<?php if($_REQUEST['brand']==$circlearr['id']){ echo "selected";}?>><?=ucwords($circlearr['make'])?></option>
                        	<?php 
                        	}
                        	?>
                       </select>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Product</label>
                    <div class="col-md-5">
                    	<select  name='product' id="product" class='form-control selectpicker' data-live-search="true"  onChange="document.form1.submit();">
                          <option value=''>All</option>
                          <?php
                        $model_query="SELECT * FROM product_master where productsubcat='".$_REQUEST['product_cat']."' and productcategory='".$_REQUEST["product_subcat"]."' and brand='".$_REQUEST['brand']."'";
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
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Scheme Status</label>
                     <div class="col-sm-5 col-md-5 col-lg-5">
                        <select name="scheme_status" id="scheme_status" class="form-control selectpicker">
                          <option value=''>All</option>
                          <option value='Active'<?php if($_REQUEST['scheme_status']=="Active"){echo "selected";}?>>Active</option>
                          <option value='Deactive'<?php if($_REQUEST['scheme_status']=="Deactive"){echo "selected";}?>>Deactive</option>
                        </select>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label"></label>
                    <div class="col-md-5">
                       
                    </div>
                  </div>
                </div><!--close form group-->
              </form>
      <form class="form-horizontal" role="form">  
        <button title="Add New Scheme" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addScheme.php?op=add<?=$pagenav?>'"><span>Add New Scheme</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th width="7%"><a href="#" name="entity_id" title="asc" ></a>#</th>
			  <th width="25%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Scheme Name</th>
              <th width="19%"><a href="#" name="name" title="asc" class="not-sort"></a>Scheme Period</th>
              <th width="29%" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Product</th>
			  <th width="12%" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Status</th>
			  <th width="8%" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View</th>
            </tr>
          </thead>
          <tbody>
          	<?php 
			$i=1;
			$sql1 = "SELECT * FROM scheme_master WHERE ".$filter_str." ORDER BY id DESC";
       		$rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   		while($row1 = mysqli_fetch_assoc($rs1)) { 
				$proddet = explode("~",getProductDetails($row1['productcode'],"productname,productcolor",$link1));
				$prodcat = getAnyDetails($row1['productcategory'],"cat_name","catid" ,"product_cat_master"  ,$link1);
				$prodsub = getAnyDetails($row1['productsubcat'],"prod_sub_cat","psubcatid" ,"product_sub_category" ,$link1);
				$prodbrd = getAnyDetails($row1['brand'],"make","id" ,"make_master"  ,$link1);
			?>
	    	<tr>
				<td><?php echo $i ;?></td>
				<td><?php echo $row1['scheme_name']." ".$row1['scheme_code'];?></td>
				<td><?php echo $row1['from_date']." - ".$row1['to_date'];?></td>
		 		<td><?php echo "<b>".$proddet[0]."</b> / ".$prodcat." / ".$prodsub." / ".$prodbrd;?></td>
         		<td><?php echo getAdminDetails($row1['entry_by'],"name",$link1);?></td>
		  		<td align="center"><a href='view_scheme.php?op=edit&id=<?php echo base64_encode($row1['id']);?>&fdate=<?=$_REQUEST['fdate']?>&tdate=<?=$_REQUEST['tdate']?>&product_cat=<?=$_REQUEST['product_cat']?>&product_subcat=<?=$_REQUEST['product_subcat']?>&brand=<?=$_REQUEST['brand']?>&product=<?=$_REQUEST['product']?>&scheme_status=<?=$_REQUEST['scheme_status']?><?=$pagenav?>' title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
            </tr>
	   		<?php 
	  		$i++;
			}
			?>
          </tbody>
		</table>
	</div>
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