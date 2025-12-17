<?php
////// Function ID ///////
$fun_id = array("a"=>array(27));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$getid=base64_decode($_REQUEST['id']);
////// get details of selected product////
$res_locdet=mysqli_query($link1,"SELECT * FROM product_master where id='".$getid."'")or die(mysqli_error($link1));
$row=mysqli_fetch_array($res_locdet);
////// final submit form ////
if($_POST['Submit']=="Update"){
	$color=strtoupper($_POST['color']);
	$str = "";
	if($_POST['pcategory']!=$row['productcategory']){ $str .= "PC change(".$row['productcategory'].") ";}
	if($_POST['brand']!=$row['brand']){ $str .= "Brand change(".$row['brand'].") ";}
	if($_POST['psubcat']!=$row['productsubcat']){ $str .= "PSC change(".$row['productsubcat'].") ";}
	if($_POST['hs_code']!=$row['hsn_code']){ $str .= "HSN change(".$row['hsn_code'].") ";}
	if($_POST['is_serialized']!=$row['is_serialize']){ $str .= "Is Serialize change(".$row['is_serialize'].") ";}
	if($_POST['serial_length']!=$row['serial_length']){ $str .= "Serial Length change(".$row['serial_length'].") ";}
	if($_POST['product_code']!=$row['product_code']){ $str .= "ProductCode change(".$row['product_code'].") ";}
	if($_POST['model_code']!=$row['model_code']){ $str .= "ModelCode change(".$row['model_code'].") ";}
	if(mysqli_query($link1,"update product_master set productname='".$_POST['pro_name']."',productcategory='".$_POST['pcategory']."',brand='".$_POST['brand']."',productsubcat='".$_POST['psubcat']."',productcolor='".$color."',productdesc='".$_POST['pro_desc']."',model_name='".$_POST['model_name']."',status='".$_POST['status']."',warranty_days='".$_POST['warranty_days']."',warranty_terms='".$_POST['warranty_terms']."',grace_period='".$_POST['grace_days']."',eol='".$_POST['eol']."',weight='".$_POST['weight']."',net_weight='".$_POST["net_weight"]."',scrap_weight='".$_POST["scrap_weight"]."',pro_rata='".$_POST["pro_rata"]."',battery_rating='".$_POST["battery_rating"]."',division='".$_POST['division']."',updatedate='".$datetime."',type_id='".$_POST['product_type']."',hsn_code='".$_POST['hs_code']."' , is_serialize = '".$_POST['is_serialized']."',serial_length='".$_POST['serial_length']."' , product_code = '".$_POST['product_code']."', product_code2='".trim($_POST['product_code2']," ").",' , pc_serial_place = '".$_POST['product_code_place']."', model_code='".$_POST['model_code']."', model_code2='".trim($_POST['model_code2']," ").",', mc_serial_place='".$_POST['model_code_place']."',other_specification1='".$_POST['oth_specification1']."',other_specification2='".$_POST['oth_specification2']."'  where id='".$getid."' ")or die("ER1".mysqli_error($link1)))
{
	/////// update master history
	updateMasterHistory($_SESSION['userid'],$row['productcode'],$str,"",$ip,$link1,"");
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$row['productcode'],"PRODUCT","UPDATE",$ip,$link1,"");
	//return message
	$msg="You have successfully updated details of reference ".$row['productcode'] ;
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
	///// move to parent page
    header("Location:model_master.php?msg=".$msg."".$pagenav);
	exit;
}
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
		$('#is_serialized').change(function(){
		  var isser = $('#is_serialized').val();
		  if(isser=="Y"){
		  	$('#serchk').css("display","");
			$('#serial_length').addClass("digits form-control required");
		  }else{
		  	$('#serchk').css("display","none");
			$('#serial_length').addClass("digits form-control");
		  }
		});
    });
	/*$(document).ready(function() {
		$('#eol').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});*/
	/*$(document).ready(function() {
		$('#reward_start_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	$(document).ready(function() {
		$('#reward_end_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});*/
 </script>
 
<style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
		if($_REQUEST["pcategory"]){ $pc = $_REQUEST["pcategory"];}else{$pc = $row['productcategory'];}
		if($_REQUEST["psubcat"]){ $psc = $_REQUEST["psubcat"];}else{$psc = $row['productsubcat'];}
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-tablet"></i>&nbsp;&nbsp;View/Edit Product</h2>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
		  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Category</label>
              <div class="col-md-5">
                <select name="pcategory"  id= "pcategory" class="form-control required" required onChange="document.frm1.submit();">
				   <option value="">--Please Select--</option>
	          	   <?php
                  $pcat=mysqli_query($link1,"Select catid , cat_name  from product_cat_master where status = '1' ORDER BY cat_name");
				  while($row_pcat=mysqli_fetch_array($pcat)){
				  ?>
                  <option value="<?=$row_pcat['catid']?>" <?php if($pc == $row_pcat['catid']) { echo "selected" ;} ?>>
                  <?=$row_pcat['cat_name']?>
                  </option>
                  <?php
				  }
                  ?>
            </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Product Sub Category</label>
              <div class="col-md-5">
                 <select name="psubcat"  id= "psubcat" class="form-control required" required>
				   <option value="">--Please Select--</option>
	          <?php
                  $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1'  and productid = '".$pc."' ORDER BY prod_sub_cat");
				  while($row_pcat=mysqli_fetch_array($pcat)){
				  ?>
                  <option value="<?=$row_pcat['psubcatid']?>" <?php if($psc == $row_pcat['psubcatid']) { echo "selected" ;} ?>>
                  <?=$row_pcat['prod_sub_cat']?>
                  </option>
                  <?php
				  }
                  ?>
            </select>
              </div>
            </div>
          </div>
		  
		  
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Part Code <span class="red_small">*</span></label>
              <div class="col-md-5">
            <input type="text"  name="productcode"  value="<?php echo $row['productcode']; ?>" class="form-control required" id="productcode" required  readonly/>
			<input type="hidden"  name="productcode1" id="productcode1"  value="<?php echo $row['productcode']; ?>" >
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">HSN Code <span class="red_small">*</span></label>
              <div class="col-md-5">
              <select name="hs_code" id= "hs_code" class="form-control required"  required>
				 <option value="">--Please Select--</option>
	           <?php
                  $hsn=mysqli_query($link1,"Select distinct(hsn_code) from tax_hsn_master where status ='Active'");
				  while($row_gp=mysqli_fetch_array($hsn)){
				  ?>
                  <option value="<?=$row_gp['hsn_code']?>" <?php if($row['hsn_code']==$row_gp['hsn_code']){echo "selected";}?>>
                  <?=$row_gp['hsn_code']?>
                  </option>
                  <?php
				  }
                  ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Name <span class="red_small">*</span></label>
              <div class="col-md-5">
               <input type="text" name="pro_name" class="form-control mastername required" id="pro_name"  value="<?php echo $row['productname']; ?>"/> 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Brand <span class="red_small">*</span></label>
              <div class="col-md-5">
              <select name="brand" id="brand" class="form-control required" required>
				 <option value="">--Please Select--</option>
	           <?php 
			   $brand=mysqli_query($link1,"SELECT * FROM make_master WHERE status='1' ORDER BY make");
			    while($row_b=mysqli_fetch_array($brand)){
			   ?>
				 <option value="<?=$row_b['id']?>"<?php if($row['brand']==$row_b['id']){ echo "selected";}?>><?=$row_b['make']?></option>
                  <?php
				  }
                  ?>
	          
            </select>
              </div>
            </div>
          </div>
          <div class="form-group">
          	<div class="col-md-6"><label class="col-md-5 control-label">Model Name <span class="red_small">*</span></label>
              <div class="col-md-5">
                <input name="model_name" type="text" class="form-control mastername required" id="model_name" value="<?=$row['model_name']?>" required> 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Product Color <span class="red_small">*</span></label>
              <div class="col-md-5">
                <select name="color" id="color" class="form-control" required>
				 <option value="">Please Select</option>
	            <?php
                  $color=mysqli_query($link1,"Select *  from colour_master");
				  while($row_gp=mysqli_fetch_array($color)){
				  ?>
                  <option value="<?=($row_gp['color'])?>"<?php if($row['productcolor']==strtoupper($row_gp['color'])){echo "selected";}?>><?=$row_gp['color']?></option>
                  <?php
				  }
                  ?>
                  <option value="NONE"<?php if($row['productcolor']=="NONE"){echo "selected";}?>>NONE</option>
                </select>
              </div>
            </div>           
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Description <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <textarea name="pro_desc" type="text" class="form-control required" id="pro_desc" style="resize:vertical" required><?=$row['productdesc']?></textarea>              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Product Type <span class="red_small">*</span></label>
              <div class="col-md-5">
                   <select name="product_type" id="product_type" class="form-control required" required>
                      <option value="ACCESSORIES" <?php if($row['type_id']=="ACCESSORIES"){ echo "selected";}?>>ACCESSORIES</option>
                      <option value="SPARE" <?php if($row['type_id']=="SPARE"){ echo "selected";}?>>SPARE</option>
                      <option value="UNIT" <?php if($row['type_id']=="UNIT"){ echo "selected";}?>>UNIT</option>
                    </select>
                </div>
            </div>
          </div> 
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Division <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="division" class="form-control required" required>
				 	<option value="">--Please Select--</option>
	           		<?php
                  	$res_divseg = mysqli_query($link1,"Select * from segment_master WHERE status='A'");
				  	while($row_divseg=mysqli_fetch_array($res_divseg)){
				  	?>
                  	<option value="<?=$row_divseg['segment']?>"<?php if($row['division']==$row_divseg['segment']){ echo "selected";}?>><?=$row_divseg['segment']?></option>
                  	<?php
				 	}
                  	?>
                </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Gross Weight</label>
              <div class="col-md-5">
                   <input name="weight" type="text" class="form-control" id="weight" value="<?=$row['weight']?>"> 
                </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Net Weight</label>
              <div class="col-md-5">
                 <input name="net_weight" type="text" class="form-control" id="net_weight" value="<?=$row['net_weight']?>"> 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Scrap Weight</label>
              <div class="col-md-5">
                   <input name="scrap_weight" type="text" class="form-control" id="scrap_weight" value="<?=$row['scrap_weight']?>"> 
                </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Pro Rata</label>
              <div class="col-md-5">
                 <input name="pro_rata" type="text" class="form-control digits" id="pro_rata" value="<?=$row['pro_rata']?>"> 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Battery Rating</label>
              <div class="col-md-5">
                   <input name="battery_rating" type="text" class="form-control alphanumeric" id="battery_rating" value="<?=$row['battery_rating']?>"> 
                </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Warranty ( Days )</label>
              <div class="col-md-5">
                <input name="warranty_days" id="warranty_days" class="form-control number" type="text" value="<?=$row["warranty_days"];?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Permissible Storage Period ( Days )</label>
              <div class="col-md-5">
                <input name="grace_days" id="grace_days" class="form-control number" type="text" value="<?=$row["grace_period"];?>"/>
              </div>
            </div>
			<?php /*?><div class="col-md-6"><label class="col-md-5 control-label">EOL</label>
              <div class="col-md-5 input-append date">
                    <div style="display:inline-block;float:left;">
                        <input type="text" class="form-control span2" name="eol"  id="eol" style="width:160px;" value="<?=$row["eol"]?>">
                    </div>
                    <div style="display:inline-block;float:right;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                </div>
            </div><?php */?>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Warranty Terms & Conditions</label>
              <div class="col-md-5">
                <textarea name="warranty_terms" type="text" class="form-control addressfield" id="warranty_terms" size="30" value="" style="resize:vertical"><?php echo $row["warranty_terms"]; ?></textarea>  
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Is Serialized? <span class="red_small">*</span></label>
              <div class="col-md-5">
               <select name="is_serialized" id="is_serialized" class="form-control required" required>
				 	<option value="">--Please Select--</option>
                    <option value="Y"<?php if($row['is_serialize']=="Y"){ echo "selected";}?>>Y</option>
                    <option value="N"<?php if($row['is_serialize']=="N"){ echo "selected";}?>>N</option>
                 </select> 
              </div>
            </div>
			
            
          </div> 
		  
		  <div class="form-group" id="serchk" <?php if($row['is_serialize']!="Y"){?>style="display:none"<?php }?>>
            <div class="col-md-6"><label class="col-md-5 control-label">&nbsp;</label>
              <div class="col-md-5">
                &nbsp;
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Serial Length <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input name="serial_length" type="text" class="digits form-control" id="serial_length" value="<?=$row['serial_length']?>" minlength="1" maxlength="2"/>  
              </div>
            </div>           
          </div> 
		  
			<div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Code</label>
              <div class="col-md-5">
                <input name="product_code" type="text" class="form-control alphanumeric" id="product_code" maxlength="3" value="<?=$row['product_code']?>"/>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Product Code 2</label>
              <div class="col-md-5">
              	<textarea name="product_code2" class="form-control addressfield" id="product_code2" placeholder= "For multiple product code use comma (,) separation" size="30" style="resize:vertical"><?=$row['product_code2']?></textarea>  
                 <input name="product_code_place" type="hidden" class="form-control digits" id="product_code_place" maxlength="2" value="<?=$row['pc_serial_place']?>"/>
              </div>
            </div>           
          </div>  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Model Code</label>
              <div class="col-md-5">
                <input name="model_code" type="text" class="form-control alphanumeric" id="model_code" maxlength="4" value="<?=$row['model_code']?>"/>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Model Code 2</label>
              <div class="col-md-5">
                 <textarea name="model_code2" class="form-control addressfield" id="model_code2" size="30" placeholder= "For multiple model code use comma (,) separation" style="resize:vertical"><?php echo $row['model_code2'];?></textarea> 
				  <input name="model_code_place" type="hidden" class="form-control digits" id="model_code_place" maxlength="2" value="<?=$row['mc_serial_place']?>"/>
              </div>
            </div>  
			  </div>  
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Other Specification 1<br/><span class="small">(Capacity)</span></label>
              <div class="col-md-5">
                <input name="oth_specification1" type="text" class="form-control addressfield" id="oth_specification1" value="<?=$row['other_specification1']?>"/>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Other Specification 2<br/><span class="small">(Voltage)</span></label>
              <div class="col-md-5">
                 <input name="oth_specification2" type="text" class="form-control addressfield" id="oth_specification2" value="<?=$row['other_specification2']?>"/>
              </div>
            </div>           
          </div>  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-5">
              <select name="status"  class="form-control required" required >
				 <option value="">Plaese Select</option>
	           <option value="Active"<?php if($row['status']=="Active"){ echo "selected";}?>>Active</option>
	           <option value="Deactive"<?php if($row['status']=="Deactive"){ echo "selected";}?>>Deactive</option>
	           
            </select>
              </div>
            </div>
         </div>
		

          <div class="form-group">
            <div class="col-md-12" align="center">
              
            <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="" value="Update">
			
        <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='model_master.php?<?=$pagenav?>'">
      
            </div>
			
          </div>
    </form>
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