<?php
////// Function ID ///////
$fun_id = array("a"=>array(20));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_POST);
$today=date("Y-m-d");
//$locstr=getAccessLocation($_SESSION['userid'],$link1);
if($po_from=='' )
{
	//$from_party="from_party in (".$locstr.")";
	//$from_party="(from_party in (".$locstr.") or to_party in (".$locstr."))";
	$from_party = "owner_code='".$po_from."'";
}
else
{
	//$from_party="(from_party='".$po_from."') ";
	//$from_party="(from_party='".$po_from."' or to_party='".$po_from."') ";
	$from_party = "owner_code='".$po_from."'";
}
if($fdate=='' || $tdate=='')
{
	$fromdate = date("Y-m-01");
	$todate = $today;
	$sql_date="(reference_date>='".$fromdate."' and sale_date<='".$todate."')";
}
else
{
	$sql_date="(reference_date>='".$fdate."' and reference_date<='".$tdate."')";
}
////// product category
if($product_cat == ""){
	$prd_cat = "1";
}else{
	$prd_cat = "productcategory = '".$product_cat."'";
}
if($product_subcat == ""){
	$prd_subcat = "1";
}else{
	$prd_subcat = "productsubcat = '".$product_subcat."'";
}
if($brand == ""){
	$prd_brand = "1";
}else{
	$prd_brand = "brand = '".$brand."'";
}
if($product =='' ){
	$product_code = " partcode in (select productcode from product_master where ".$prd_cat." and ".$prd_subcat." and ".$prd_brand.")";
}
else{
	$product_code="(partcode='".$product."') ";
}
if($stock_type != ''){
	$stktype = " stock_type='".$stock_type."'";
}else{
	$stktype = " 1";
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
 <script src='../js/select2.min.js'></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		endDate: "<?=$today?>",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		endDate: "<?=$today?>",
		autoclose: true
	});
});
$(document).ready(function(){
	$('#myTable').dataTable();
});
$(document).ready(function(){
	$("#po_from2").select2({
  		ajax: {
   			url: "../includes/getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchCust: params.term, // search term
					requestFor: "allloc",
					userid: '<?=$_SESSION['userid']?>'
    			};
   			},
   			processResults: function (response) {
     			return {
        			results: response
     			};
   			},
   			cache: true
  		}
	});	
});	
</script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-cubes"></i> Stock Ledger </h2><br/>
   <div class="form-group" id="page-wrap" style="margin-left:10px;">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required autocomplete="off"></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" required autocomplete="off"></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
          </div>
        </div>
         <div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label">Location<span style="color:#F00">*</span></label>
            <div class="col-md-3">
            	<select name="po_from2" id="po_from2" class="form-control required" required onChange="document.frm1.submit();">        
                    <option value=''>--Please Select--</option>
                    <?php
                    if(isset($_POST["po_from2"])){
                      $loc_name = explode("~",getAnyDetails($_POST["po_from2"],"name, city, state","asc_code","asc_master",$link1));
                    echo '<option value="'.$_POST["po_from2"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["po_from2"].'</option>';
                    }
                    ?>
                </select>
				<?php /*?><select name="po_from" id="po_from"  class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                     
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					
                    ?>
                 </select><?php */?>
                 <select name="po_from" id="po_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                     <?php                                 
                    $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['po_from2']."'";
                    $smfm_res = mysqli_query($link1,$smfm_sql);
                    while($smfm_row = mysqli_fetch_array($smfm_res)){
                    ?>
                    <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['po_from'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                    <?php
                    }
                    ?>
                    <?php                                 
                    $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['po_from2']."' AND status='Active'";
                    $smf_res = mysqli_query($link1,$smf_sql);
                    while($smf_row = mysqli_fetch_array($smf_res)){
                    ?>
                    <option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['po_from'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
         	<label class="col-md-3 control-label">Stock Type</label>
            <div class="col-md-3">
			<select name="stock_type" id="stock_type" class="form-control">
                <option value="">All</option>
                <option value="OK"<?php if($_REQUEST['stock_type']=="OK"){ echo "selected";}?>>OK</option>
                <option value="DAMAGE"<?php if($_REQUEST['stock_type']=="DAMAGE"){ echo "selected";}?>>DAMAGE</option>
                <option value="MISSING"<?php if($_REQUEST['stock_type']=="MISSING"){ echo "selected";}?>>MISSING</option>
              </select>
            </div>
          </div>
	    </div><!--close form group-->    
         <div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label">Product Category</label>
            <div class="col-md-3">
                <select  name='product_cat' id="product_cat" class='form-control selectpicker required' data-live-search="true" onChange="document.frm1.submit();">
                  <option value=''>--Please Select--</option>
				  <?php
                	$res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                	while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$_REQUEST["product_cat"]){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
               </select>
            </div>
         	<label class="col-md-3 control-label">Product Sub Category:</label>
            <div class="col-md-3">
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
          </div>
	    </div><!--close form group-->  
		 <div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label">Brand:</label>
            <div class="col-md-3">
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
			<label class="col-md-3 control-label">Product:</label>
            <div class="col-md-3">
            	<select  name='product' id="product" class='form-control selectpicker required' data-live-search="true"  onChange="document.frm1.submit();">
                  <option value=''>--Please Select--</option>
				  <?php
				$model_query="SELECT * FROM product_master where productsubcat='".$_REQUEST['product_subcat']."' and productcategory='".$_REQUEST["product_cat"]."' and brand='".$_REQUEST["brand"]."'";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['productcode']?>"<?php if($_REQUEST['product']==$br['productcode']){echo 'selected';}?>><?=getProduct($br['productcode'],$link1)." | ".$br['productcode']?></option>
				<?php
                }
				?>
               </select>
            </div>
          </div>
	    </div><!--close form group--> 
		<div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label"></label>
            <div class="col-md-3">
                <strong>Excel Export</strong>&nbsp;&nbsp;&nbsp;&nbsp; <a href="excelexport.php?rname=<?=base64_encode("stockLedgerList")?>&rheader=<?=base64_encode("stockLedgerList")?>&fdate=<?=$_REQUEST['fdate'];?>&tdate=<?=$_REQUEST['tdate'];?>&po_from=<?=base64_encode($_REQUEST["po_from"]);?>&brand=<?=base64_encode($_REQUEST['brand']);?>&product_cat=<?=base64_encode($_REQUEST['product_cat']);?>&product_subcat=<?=base64_encode($_REQUEST['product_subcat'])?>&product=<?=base64_encode($_REQUEST['product']);?>&stock_type=<?=base64_encode($_REQUEST['stock_type']);?>" title="Export Stock Ledger details in excel" style="float:right"><i class="fa fa-file-excel-o fa-2x" title="Export Stock Ledger details in excel"></i></a>
            </div>
			<label class="col-md-3 control-label">&nbsp;</label>
            <div class="col-md-3">
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
          </div>
	    </div><!--close form group--> 
         </form>
         <table  width="100%" id="myTable" class="table-bordered table-hover" align="center">
          <thead>
            <tr class="btn-primary">
              <th>S.No</th>
              <th>Movement Date & Time</th>
              <th>From Location</th>
              <th>To Location</th>
              <th>Item</th>
              <th>Opening Stock</th>
              <th>In</th>
              <th>Out</th>
              <th>Closing Stock</th>
              <th>System Ref. No.</th>
              <th>Stock Type</th>
              <th>Transfer Type</th>
              <th>Stock Transfer</th>
            </tr>
            </tr>
          </thead>
			<tbody>        
         <?php
		 if($_REQUEST['Submit']=="GO"){
			 $j=1;
			$sql_sl = "SELECT * FROM stock_ledger WHERE ".$from_party." AND ".$sql_date." AND ".$product_code." AND ".$stktype;
			$res_sl = mysqli_query($link1, $sql_sl);
			while($row = mysqli_fetch_assoc($res_sl)){
				/////// get full part desc //////////////
				$part_info =  explode("~",getAnyDetails($row["partcode"],"productname,brand,productcategory,productsubcat","productcode","product_master",$link1));
				$part_desc = "<b>".$part_info[0]."</b> <span style='color:blue;'> - </span> ".getAnyDetails($part_info[1],"make","id","make_master",$link1)." <span style='color:blue;'> - </span> ".getAnyDetails($part_info[2],"cat_name","catid","product_cat_master",$link1)." <span style='color:blue;'> - </span> ".getAnyDetails($part_info[3],"prod_sub_cat","psubcatid","product_sub_category",$link1)." <span style='color:blue;'> - </span> ".$row["partcode"];
				
				/////// get in out qty  ///////////	 
				$in_qty = 0;
				$out_qty = 0;
			
				if($row['stock_transfer']=='IN'){ $in_qty = $row['qty'];}
				if($row['stock_transfer']=='OUT'){ $out_qty = $row['qty'];}
				
				//////////////////
				if($row['stock_transfer']=='IN'){ $in = $row['qty']; $out=0; } else{ $in = 0;  $out = $row['qty']; }
				if($j==1){ $open = 0; }else{}
				$closing = $open+$in-$out;
		 ?>
         	<tr>
            	<td><?=$j?></td>
				<td><?=dt_format($row["create_date"])." | ".$row["create_time"];?></td>
				<td><?php echo getAnyParty($row['from_party'],$link1);?></td>
				<td><?php echo getAnyParty($row['to_party'],$link1);?></td>
				<td><?=$part_desc;?></td>
				<td><?=$open;?></td>
				<td><?=$in_qty;?></td>
				<td><?=$out_qty;?></td>
				<td><?=$closing;?></td>
				<td><?=$row["reference_no"];?></td>
				<td><?=$row["stock_type"];?></td>
				<td><?=$row["type_of_transfer"];?></td>
				<td><?=$row["stock_transfer"];?></td>
            </tr>
         <?php
		 	$j++;
			$open=$closing;
			}
		 }
		 ?>
         </tbody>
      </table>
  </div><!--close panel group-->
  
   
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>