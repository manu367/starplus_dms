<?php
////// Function ID ///////
$fun_id = array("a"=>array(27));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_POST);
////// final submit form ////
if($_POST['Submit']=="Save"){
	$color=strtoupper($_POST['color']);
	$category=strtoupper($_POST['category']);
	 ////// product code create
	//$ref = mysqli_query($link1,"SELECT COUNT(id) AS cnt FROM product_master");
	//$row = mysqli_fetch_assoc($ref);
    //$result = $row['cnt']+1;
	//$pad = str_pad($result,5,"0",STR_PAD_LEFT);  
	//$reference = "P".$pad;
	$reference = $productcode;
	 // insert all details of product //
	 ///////// explode hsn post values /////////////////////
	 $hsnvalue = explode("~",$_POST['hs_code']);
	 ////////////  explode hsncode ////////////////////////////
	 $hsncode = explode("_",$hsnvalue[0]);
	 
	 if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM product_master WHERE productcode='".$reference."'"))==0){
		if(mysqli_query($link1,"INSERT IGNORE INTO product_master SET productname='".$_POST['pro_name']."',productcode='".$reference."',sap_code='".$reference."',productcolor='".$color."',productdesc='".$_POST['pro_desc']."',model_name='".$_POST['model_name']."',productcategory='".$category."',brand='".$_POST['brand']."',status='Active',warranty_days='".$_POST['warranty_days']."',warranty_terms='".$_POST['warranty_terms']."',grace_period='".$_POST['grace_days']."',eol='".$_POST['eol']."',weight='".$_POST['weight']."',net_weight='".$_POST["net_weight"]."',scrap_weight='".$_POST["scrap_weight"]."',pro_rata='".$_POST["pro_rata"]."',battery_rating='".$_POST["battery_rating"]."',division='".$_POST['division']."',createdate='".$datetime."',type_id='".$_POST['product_type']."',hsn_code='".$_POST['hs_code']."' ,productsubcat = '".$_POST['psubcategory']."', is_serialize = '".$_POST['is_serialized']."',serial_length='".$_POST['serial_length']."' , product_code = '".$_POST['product_code']."', product_code2='".trim($_POST['product_code2']," ").",' , pc_serial_place = '".$_POST['product_code_place']."', model_code='".$_POST['model_code']."', model_code2='".trim($_POST['model_code2']," ").",', mc_serial_place='".$_POST['model_code_place']."',other_specification1='".$_POST['oth_specification1']."',other_specification2='".$_POST['oth_specification2']."'")or die("ER4".mysqli_error($link1)))
		{
		 ////// insert in activity table////
		dailyActivity($_SESSION['userid'],$reference,"PRODUCT","ADD",$ip,$link1,"");
		
		//return message
		$msg="You have successfully created a new Product with Product Code ".$reference;
	   }else{
		////// return message
		$msg="Something went wrong. Please try again.";
	   }
   }else{
   		$msg="Something went wrong. Product is already created";
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
	$(document).ready(function() {
		$('#eol').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	
	$(document).ready(function() {
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
	});
	
	
$(document).ready(function () {
    var charReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
    $('.keyup-char').keyup(function () {
        $('span.error-keyup-1').hide();
        var inputVal = $(this).val();

        if (!charReg.test(inputVal)) {
            $(this).parent().find(".warning").show();
			$("#save").prop("disabled",true);
        } else {
            $(this).parent().find(".warning").hide();
			$("#save").prop("disabled",false);
        }
    });
});
 /////////// function to get city on the basis of state
 function checkDupliCcode(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{pcode:val},
		success:function(data){
	      //// if string found then alert
		  if(data>0){
			  //alert("Duplicate product code");
			  $("#dupli").show();
			  $("#save").prop("disabled",true);
			  //$('#docstr').val('');
		  }else{
			  $("#dupli").hide();
			  $("#save").prop("disabled",false);
		  }
	    }
	  });
   
 }
 </script>
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
<style>
.red_small{
	color:red;
}
.warning,.warning2 {
    color:#d2232a;
    -webkit-border-radius: 12px; 
    border-radius: 12px;
    background-color:#ffdd97;
    padding:5px;
    width:100%;
    display:none;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-tablet"></i>&nbsp;&nbsp;Add New Product</h2>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post">
		  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Category <span class="red_small">*</span></label>
              <div class="col-md-5">
                  <select name="category"  id= "category" class="form-control required"  onChange="document.frm1.submit();" required >
				   <option value="">--Please Select--</option>
	          <?php
                  $pcat=mysqli_query($link1,"Select catid , cat_name  from product_cat_master where status = '1' ORDER BY cat_name");
				  while($row_pcat=mysqli_fetch_array($pcat)){
				  ?>
                  <option value="<?=$row_pcat['catid']?>" <?php if($_REQUEST['category'] == $row_pcat['catid']) { echo "selected" ;} ?>>
                  <?=$row_pcat['cat_name']?>
                  </option>
                  <?php
				  }
                  ?>
            </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Product Sub Category <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="psubcategory"  id= "psubcategory" class="form-control required" required>
				   <option value="">--Please Select--</option>
	          <?php
                  $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1'  and productid = '".$_REQUEST['category']."' ORDER BY prod_sub_cat");
				  while($row_pcat=mysqli_fetch_array($pcat)){
				  ?>
                  <option value="<?=$row_pcat['psubcatid']?>">
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
            	<input type="text" name="productcode" class="form-control required required keyup-char" id="productcode" required value="" onBlur="checkDupliCcode(this.value);" onKeyUp="checkDupliCcode(this.value);"/> 
                <span class="warning col-md-5">Alphanumeric only.</span>
                <span class="warning2 col-md-5" id="dupli">Duplicate Part Code</span>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">HSN Code <span class="red_small">*</span></label>
              <div class="col-md-5">
              <select name="hs_code" id= "hs_code" class="form-control required"  required>
				 <option value="">--Please Select--</option>
	           <?php
                  $hsn=mysqli_query($link1,"SELECT DISTINCT(hsn_code),sgst,igst,cgst FROM tax_hsn_master WHERE status ='Active'");
				  while($row_gp=mysqli_fetch_array($hsn)){
				  ?>
                  <option value="<?=$row_gp['hsn_code']?>">
                  <?=$row_gp['hsn_code']." (SGST - ".$row_gp['sgst']."%  CGST - ".$row_gp['cgst']."%  IGST - ".$row_gp['igst']."%)"?>
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
            <input type="text" name="pro_name" class="form-control required mastername" id="pro_name" required value=""/> 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Brand <span class="red_small">*</span></label>
              <div class="col-md-5">
              <select name="brand" class="form-control required" required>
				 <option value="">--Please Select--</option>
	           <?php 
			   $brand=mysqli_query($link1,"SELECT * FROM make_master ORDER BY make");
			    while($row=mysqli_fetch_array($brand)){
			   ?>
			  
				 <option value="<?=$row['id']?>">
                  <?=$row['make']?>
                  </option>
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
                <input name="model_name" type="text" class="form-control mastername required" id="model_name" value="" required> 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Lid Color <span class="red_small">*</span></label>
              <div class="col-md-5">
                <select name="color" class="form-control required"  required>
				 <option value="">--Please Select--</option>
	           <?php
                  $color=mysqli_query($link1,"Select *  from colour_master");
				  while($row_gp=mysqli_fetch_array($color)){
				  ?>
                  <option value="<?=$row_gp['color']?>">
                  <?=$row_gp['color']?>
                  </option>
                  <?php
				  }
                  ?>
                  <option value="NONE">NONE</option>
                </select>
              </div>
            </div>           
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Description <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <textarea name="pro_desc" type="text" class="form-control required" id="pro_desc" style="resize:vertical" required></textarea>  
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Product Type <span class="red_small">*</span></label>
              <div class="col-md-5">
                   <select name="product_type" id="product_type" class="form-control required" required>
                      <option value="ACCESSORIES">ACCESSORIES</option>
                      <option value="SPARE">SPARE</option>
                      <option value="UNIT" selected>UNIT</option>
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
                  	<option value="<?=$row_divseg['segment']?>"><?=$row_divseg['segment']?></option>
                  	<?php
				 	}
                  	?>
                </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Gross Weight</label>
              <div class="col-md-5">
                   <input name="weight" type="text" class="form-control" id="weight" value=""> 
                </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Net Weight</label>
              <div class="col-md-5">
                 <input name="net_weight" type="text" class="form-control" id="net_weight" value=""> 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Scrap Weight</label>
              <div class="col-md-5">
                   <input name="scrap_weight" type="text" class="form-control" id="scrap_weight" value=""> 
                </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Pro Rata</label>
              <div class="col-md-5">
                 <input name="pro_rata" type="text" class="form-control digits" id="pro_rata" value=""> 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Battery Rating</label>
              <div class="col-md-5">
                   <input name="battery_rating" type="text" class="form-control alphanumeric" id="battery_rating" value=""> 
                </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Warranty ( Days )</label>
              <div class="col-md-5">
                <input name="warranty_days" id="warranty_days" class="form-control number" type="text"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Permissible Storage Period ( Days )</label>
              <div class="col-md-5">
                <input name="grace_days" id="grace_days" class="form-control number" type="text"/>
              </div>
            </div>
			<?php /*?><div class="col-md-6"><label class="col-md-5 control-label">EOL</label>
              <div class="col-md-5 input-append date">
                    <div style="display:inline-block;float:left;">
                        <input type="text" class="form-control span2" name="eol"  id="eol" style="width:160px;">
                    </div>
                    <div style="display:inline-block;float:right;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                </div>
            </div><?php */?>
          </div> 
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Warranty Terms & Conditions</label>
              <div class="col-md-5">
                <textarea name="warranty_terms" type="text" class="form-control addressfield" id="warranty_terms" size="30" value="" style="resize:vertical"></textarea>  
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Is Serialized? <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="is_serialized" id="is_serialized" class="form-control required" required>
				 	<option value="">--Please Select--</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                 </select>   
              </div>
            </div>           
          </div>
          <div class="form-group" id="serchk" style="display:none">
            <div class="col-md-6"><label class="col-md-5 control-label">&nbsp;</label>
              <div class="col-md-5">
                &nbsp;
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Serial Length <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input name="serial_length" type="text" class="digits form-control" id="serial_length"  minlength="1" maxlength="2"/>  
              </div>
            </div>           
          </div>  
		   <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Product Code</label>
              <div class="col-md-5">
                <input name="product_code" type="text" class="form-control alphanumeric" id="product_code" maxlength="3"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Product Code 2</label>
              <div class="col-md-5">
                <textarea name="product_code2" class="form-control addressfield" id="product_code2" placeholder= "For multiple product code use comma (,) separation" size="30" style="resize:vertical"></textarea>  
				  <input name="product_code_place" type="hidden" class="form-control digits" id="product_code_place" maxlength="2"/>
              </div>
            </div>
          </div>  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Model Code</label>
              <div class="col-md-5">
                <input name="model_code" type="text" class="form-control alphanumeric" id="model_code" maxlength="4"/>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Model Code 2</label>
              <div class="col-md-5">
                <textarea name="model_code2" class="form-control addressfield" id="model_code2" placeholder= "For multiple model code use comma (,) separation" size="30" style="resize:vertical"></textarea>  
				  <input name="model_code_place" type="hidden" class="form-control digits" id="model_code_place" maxlength="2"/>
              </div>
            </div>
          </div>  
			
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Other Specification 1<br/><span class="small">(Capacity)</span></label>
              <div class="col-md-5">
                <input name="oth_specification1" type="text" class="form-control addressfield" id="oth_specification1"/>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Other Specification 2<br/><span class="small">(Voltage)</span></label>
              <div class="col-md-5">
                 <input name="oth_specification2" type="text" class="form-control addressfield" id="oth_specification2"/>
              </div>
            </div>           
          </div>
              
		  <div class="form-group">
            <div class="col-md-12" align="center">
              
            <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" >
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