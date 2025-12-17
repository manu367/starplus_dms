<?php
require_once("../config/config.php");
@extract($_POST);
## selected  Filter
////// product category
if($product_cat == ""){
	$prd_cat = "1";
}else{
	$prd_cat = "productcategory = '".$product_cat."'";
}
////// product sub category
if($product_subcat == ""){
	$prd_subcat = "1";
}else{
	$prd_subcat = "productsubcat = '".$product_subcat."'";
}
////// brand
if($brand == ""){
	$prd_brand = "1";
}else{
	$prd_brand = "brand = '".$brand."'";
}
////// product code
if($prod_code =='' ){
	$product_code = " partcode in (select productcode from product_master where ".$prd_cat." and ".$prd_subcat." and ".$prd_brand.")";
}
else{
	$product_code="(partcode='".$prod_code."') ";
}
////// go-down
if($go_down == ""){
	$godown = "1";
}else{
	$godown = "sub_location = '".$go_down."'";
}
if($_REQUEST['locationcode']){
	$arr_prdstr = "";
	$arr_okstr = "";
	$arr_dmgstr = "";
	$arr_misstr = "";
	$arr_godown=array();
	$arr_prod=array();
	$arr_okcnt=array();
	$arr_dmgcnt=array();
	$arr_miscnt=array();
	$stock_query="SELECT * FROM stock_status WHERE asc_code='".$_REQUEST['locationcode']."' AND ".$product_code." AND ".$godown;
	$stock_res=mysqli_query($link1,$stock_query);
	$i=1;
	while($stock_row = mysqli_fetch_array($stock_res)){
		$proddet=explode("~",getProductDetails($stock_row['partcode'],"productname,productcolor",$link1));
		///// make product string
		if ($arr_prdstr == "") {
			$arr_prdstr.="'".$proddet[0]."'";
		} else {
			$arr_prdstr.=",'".$proddet[0]."'";
		}
		///// make ok qty string
		if ($arr_okstr == "") {
			$arr_okstr.="".$stock_row['okqty']."";
		} else {
			$arr_okstr.=",".$stock_row['okqty']."";
		}
		///// make damage qty string
		if ($arr_dmgstr == "") {
			$arr_dmgstr.="".$stock_row['broken']."";
		} else {
			$arr_dmgstr.=",".$stock_row['broken']."";
		}
		///// make missing qty string
		if ($arr_misstr == "") {
			$arr_misstr.="".$stock_row['missing']."";
		} else {
			$arr_misstr.=",".$stock_row['missing']."";
		}
		$arr_godown[]=$stock_row['sub_location'];
		$arr_prod[]=$stock_row['partcode'];
		$arr_okcnt[]=$stock_row['okqty'];
		$arr_dmgcnt[]=$stock_row['broken'];
		$arr_miscnt[]=$stock_row['missing'];
	}
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
<script type="text/javascript">
$(function () {
        $('#container').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Inventory Status'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [
                    <?=$arr_prdstr?>
                ]
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Quantity (Pcs)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} Pcs</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Ok',
                data: [<?=$arr_okstr?>]

            }, {
                name: 'Damage',
                data: [<?=$arr_dmgstr?>]

            }, {
                name: 'Missing',
                data: [<?=$arr_misstr?>]

            }]
        });
    });
</script>
<script src="../high/highcharts.js" type="text/javascript"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-area-chart"></i> Inventory Dashboard</h2>
	  <form class="form-horizontal" role="form" name="frm1" action="" method="post">
      <div class="row">
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Location <span class="red_small">*</span></label>
            <select name="locationcode" id="locationcode" class="form-control selectpicker required" required data-live-search="true" onChange="document.frm1.submit();">
                   <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
					}
                    ?>
              </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Go-down</label>
            <select name="go_down" id="go_down" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                     <?php                                 
                    $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['locationcode']."'";
                    $smfm_res = mysqli_query($link1,$smfm_sql);
                    while($smfm_row = mysqli_fetch_array($smfm_res)){
                    ?>
                    <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['go_down'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                    <?php
                    }
                    ?>
                    <?php                                 
                    $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['locationcode']."' AND status='Active'";
                    $smf_res = mysqli_query($link1,$smf_sql);
                    while($smf_row = mysqli_fetch_array($smf_res)){
                    ?>
                    <option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['go_down'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
                    <?php
                    }
                    ?>
                </select>
        </div>
      	<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product Category</label>
        	<select  name='product_cat' id="product_cat" class='form-control selectpicker required' data-live-search="true" onChange="document.frm1.submit();">
                  <option value=''>--Please Select--</option>
				  <?php
                	$res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                	while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$_REQUEST["product_cat"]){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
               </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product Sub Category</label>
        	<select  name='product_subcat' id="product_subcat" class='form-control selectpicker required' data-live-search="true" onChange="document.frm1.submit();">
                  <option value=''>--Please Select--</option>
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
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Brand</label>
        	<select name="brand" id="brand" class="form-control"  onChange="document.frm1.submit();">
                	<option value=''>--Please Select--</option>
                  	<?php
					$sql3 = "select id, make from make_master where status='1' order by make";
					$res3 = mysqli_query($link1,$sql3) or die(mysqli_error($link1));
					while($row3 = mysqli_fetch_array($res3)){
					?>
				  	<option value="<?=$row3['id']?>"<?php if($_REQUEST['brand']==$row3['id']){ echo "selected";}?>><?=$row3['make']?></option>
					<?php 
					}
                	?>
                </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Product</label>
        	<select  name='prod_code' id="prod_code" class='form-control selectpicker required' data-live-search="true"  onChange="document.frm1.submit();">
                  <option value=''>--Please Select--</option>
				  <?php
				$model_query="SELECT * FROM product_master where productsubcat='".$_REQUEST['product_subcat']."' and productcategory='".$_REQUEST["product_cat"]."' and brand='".$_REQUEST["brand"]."'";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['productcode']?>"<?php if($_REQUEST['prod_code']==$br['productcode']){echo 'selected';}?>><?=getProduct($br['productcode'],$link1)." | ".$br['productcode']?></option>
				<?php
                }
				?>
               </select>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label>
        </div>
      </div>
	  </form>
      <br/>
      <div class="form-group table-responsive" id="page-wrap" style="margin-left:10px;">
          <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
           <thead>
            <tr class="<?=$tableheadcolor?>">
              <th width="4%">S.No.</th>
              <th width="15%">Go-Down</th>
              <th width="7%">Product Category</th>
              <th width="7%">Sub Category</th>
              <th width="7%">Brand</th>
              <th width="30%">Product</th>
              <th width="10%">Ok Qty</th>
              <th width="10%">Damage Qty</th>
              <th width="10%">Missing Qty</th>
            </tr>
            </thead>
            <tbody>
            <?php
			if(!empty($arr_godown)){
				for($i=0; $i<count($arr_godown); $i++){
					/// sub location
					$subloc = getLocationDetails($arr_godown[$i],"name,city,state",$link1);
					$explodevalf = explode("~",$subloc);
					if($explodevalf[0]){ $sublocname=str_replace("~",",",$subloc); }else{ $sublocname=getAnyDetails($arr_godown[$i],"sub_location_name","sub_location","sub_location_master",$link1);}
					
					$proddet = explode("~",getProductDetails($arr_prod[$i],"productname,model_name,productcategory,productsubcat,brand",$link1));
				?>
				<tr>
				  <td><?=($i+1);?></td>
				  <td align="left"><?php if($sublocname){ echo $sublocname;}else{ echo $locdet[0].",".$locdet[1].",".$row['asc_code'];}?></td>
				  <td align="left"><?=getAnyDetails($proddet[2],"cat_name","catid","product_cat_master",$link1)?></td>
				  <td align="left"><?=getAnyDetails($proddet[3],"prod_sub_cat","psubcatid","product_sub_category",$link1)?></td>
				  <td align="left"><?=getAnyDetails($proddet[4],"make","id","make_master",$link1)?></td>
				  <td class="lable"><?=$proddet[0]." - ".$proddet[1]."  (".$arr_prod[$i].")"?></td>
				  <td align="right"><?=$arr_okcnt[$i]?></td>
				  <td align="right"><?=$arr_dmgcnt[$i]?></td>
				  <td align="right"><?=$arr_miscnt[$i]?></td>
				</tr>
				<?php
				}
			}
            ?>
            </tbody>
          </table>
         </div> 
      <div id="container" class="form-group table-responsive" style="min-width: 800px; height: 400px; margin: 0 auto"></div>
    </div>
  </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>