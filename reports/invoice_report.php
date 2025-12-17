<?php
////// Function ID ///////
$fun_id = array("a"=>array(1));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_POST);
$date=date("Y-m-d");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
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
      <h2 align="center"><i class="fa fa-desktop"></i> Invoice/STN Report </h2><br/>
   <div class="form-group" id="page-wrap" style="margin-left:10px;">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
          </div>
        </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">From Location<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_from" id="po_from"  class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                     
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl[location_id]?>" <?php if($result_chl['location_id']==$_REQUEST['po_from'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					
                    ?>
                 </select>
              </div>
            </div>
			</div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">To Location<span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_to" id="po_to"  class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select mapped_code from mapped_master where uid='$_REQUEST[po_from]'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[mapped_code]'"));
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_parent['mapped_code']?>" value="<?=$result_parent[mapped_code]?>" <?php if($result_parent[mapped_code]==$_REQUEST[po_to])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['mapped_code']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
              </div>
            </div>
           </div>
         <div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label">Product Category</label>
            <div class="col-md-3">
                <select  name='product_cat' id="product_cat" class='form-control selectpicker required' data-live-search="true" onChange="document.frm1.submit();">
                  <option value=''>All</option>
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
		  <div class="col-md-10"><label class="col-md-3 control-label">Brand:</label>
            <div class="col-md-3">
                <select name="brand" id="brand" class="form-control"  onChange="document.frm1.submit();">
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
                </select>
            </div>
			<label class="col-md-3 control-label">Product:</label>
            <div class="col-md-3">
            	<select  name='product' id="product" class='form-control selectpicker required' data-live-search="true"  onChange="document.frm1.submit();">
                  <option value=''>All</option>
				  <?php
				$model_query="SELECT * FROM product_master where productcategory='".$_REQUEST['product_cat']."' and productsubcat='".$_REQUEST["product_subcat"]."' and brand='".$_REQUEST["brand"]."'";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['productcode']?>"<?php if($_REQUEST['product']==$br['productcode']){echo 'selected';}?>><?=$br['productname']." | ".$br['model_name']." | ".$br['productcode']?></option>
				<?php
                }
				?>
               </select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label">Document Type:</label>
            <div class="col-md-3">
                <select name="docType" id="docType" class="form-control"  onChange="document.frm1.submit();">
                	<option value=''>All</option>
                    <option value="INVOICE"<?php if($_REQUEST['docType']=="INVOICE"){ echo "selected";}?>>INVOICE</option>
				  	<option value="Delivery Challan"<?php if($_REQUEST['docType']=="Delivery Challan"){ echo "selected";}?>>Delivery Challan</option>
                </select>
            </div>
			<label class="col-md-3 control-label">Scheme Name</label>
            <div class="col-md-3">
                <select name="scheme" id="scheme" class="form-control"  onChange="document.frm1.submit();">
                	<option value="">Please Select</option>
                    <?php
					 $data_scheme = mysqli_query($link1 , "select distinct(scheme_name) from billing_model_data where scheme_name != '' ") ;
					 while($row = mysqli_fetch_array($data_scheme)){?>
					 <option value="<?=$row['scheme_name']?>"<?php if($_REQUEST['scheme']==$row['scheme_name']){ echo "selected";}?>><?=$row['scheme_name']?></option>
					 
					<?php
					 }
					?>
                </select>
            </div>
          </div>
	    </div><!--close form group-->  
		
		
		<div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label">Status</label>
            <div class="col-md-3">
                <select name="status" id="status" class="form-control"  onChange="document.frm1.submit();">
                    <option value=''>All</option>
                    <option value="Pending"<?php if($_REQUEST['status']=="Pending"){ echo "selected";}?>>Pending</option>
                    <option value="Pending For Serial"<?php if($_REQUEST['status']=="Pending For Serial"){ echo "selected";}?>>Pending For Serial</option>
                    <option value="PFA"<?php if($_REQUEST['status']=="PFA"){ echo "selected";}?>>Pending For Approval</option>
                    <option value="Dispatched"<?php if($_REQUEST['status']=="Dispatched"){ echo "selected";}?>>Dispatched</option>
                    <option value="Received"<?php if($_REQUEST['status']=="Received"){ echo "selected";}?>>Received</option>
                    <option value="Cancelled"<?php if($_REQUEST['status']=="Cancelled"){ echo "selected";}?>>Cancelled</option>
                </select>
            </div>
			<label class="col-md-3 control-label">&nbsp;</label>
            <div class="col-md-3">
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
          </div>
	    </div><!--close form group--> 
		     
        <div class="form-group">
         
		  <div class="col-md-12"><label class="col-md-3 control-label"></label>	  
			<div class="col-md-9" align="left">
               <?php
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("Invoice",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
					if($_REQUEST['Submit']=="GO"){
						
			   ?>
			  
              <div class="col-md-3" style="color:#FF0033"> <a href="excelexport.php?rname=<?=base64_encode("detailinvoice")?>&rheader=<?=base64_encode("Detail Invoice")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&floc=<?=base64_encode($_REQUEST['po_from'])?>&tloc=<?=base64_encode($_REQUEST['po_to'])?>&docType=<?=base64_encode($_REQUEST['docType'])?>&pro=<?=base64_encode($_REQUEST['product'])?>&product_cat=<?=base64_encode($_REQUEST['product_cat'])?>&product_subcat=<?=base64_encode($_REQUEST['product_subcat'])?>&brand=<?=base64_encode($_REQUEST['brand'])?>&status=<?=base64_encode($_REQUEST['status'])?>&scheme=<?=base64_encode($_REQUEST['scheme'])?>" title="Export detail in excel"><i class="fa fa-file-excel-o fa-2x" title="Export detail details in excel"></i> Detail Invoice</a></div>
               
			 <div class="col-md-3" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("summerizeinvoice")?>&rheader=<?=base64_encode("Summerize Invoice")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&floc=<?=base64_encode($_REQUEST['po_from'])?>&tloc=<?=base64_encode($_REQUEST['po_to'])?>&docType=<?=base64_encode($_REQUEST['docType'])?>&status=<?=base64_encode($_REQUEST['status'])?>&scheme=<?=base64_encode($_REQUEST['scheme'])?>" title="Export summerize in excel"><i class="fa fa-file-excel-o fa-2x" title="Export summerize details in excel"></i> Summerize Invoice </a></div>
             
             <div class="col-md-3" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("imeilinvoice")?>&rheader=<?=base64_encode("Serial No. Invoice")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&floc=<?=base64_encode($_REQUEST['po_from'])?>&tloc=<?=base64_encode($_REQUEST['po_to'])?>&docType=<?=base64_encode($_REQUEST['docType'])?>&pro=<?=base64_encode($_REQUEST['product'])?>&product_cat=<?=base64_encode($_REQUEST['product_cat'])?>&product_subcat=<?=base64_encode($_REQUEST['product_subcat'])?>&brand=<?=base64_encode($_REQUEST['brand'])?>&status=<?=base64_encode($_REQUEST['status'])?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i> Serial No. Invoice </a></div>
               <?php
					}
				//}
				
				?>
            </div>
          </div>
	    </div><!--close form group-->
         </form>
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