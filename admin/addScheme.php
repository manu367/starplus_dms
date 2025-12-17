<?php
////// Function ID ///////
$fun_id = array("a"=>array(56));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$date=date("Y-m-d");
@extract($_POST);
////// final submit form ////
/////// save scheme
if($_POST['Submit'] == 'Save'){
	$folder="scheme";
	$allowedExts = array("gif","jpeg","jpg","png","PNG","GIF","JPEG","JPG","xlsx","xls","doc","docx","ppt","pptx","txt","pdf");
	////// check attach file
   	if($_FILES['attach']['name'] != ''){	
		$temp = explode(".", $_FILES["attach"]["name"]);
	 	$extension = end($temp);
	 	$f_size=$_FILES["attach"]["size"];
	  	///// check extension
		if(!in_array($extension, $allowedExts)){
			$msgg = ".".$extension." ". "not allowed";
		 	header("Location:addScheme.php?msg=$msgg&sts=fail");
			exit;
		}
		////// check file size upto 2 MB
	 	if ($_FILES["attach"]["size"]>2097152){
			$msgg = "File size should be less than or equal to 2 mb";
		 	header("Location:addScheme.php?msg=$msgg&sts=fail");
			exit;
		}
		else{ 
    		$file_name = $_FILES['attach']['name'];
			$file_tmp = $_FILES['attach']['tmp_name'];
			$up = move_uploaded_file($file_tmp,"../".$folder."/".time().$file_name);
    		$path1 = "../".$folder."/".time().$file_name;	
			$img_name1 = time().$file_name;
		}
	}
	///// make Scheme ref no
	$res_ref = mysqli_query($link1,"SELECT MAX(ref_id) as mno FROM scheme_master");
	$row_ref = mysqli_fetch_assoc($res_ref);
	$next_mno = $row_ref["mno"] + 1;
	$ref_no = "SCH/".date("Y")."/".$next_mno;
	/////// insert scheme 
	$sql = mysqli_query($link1,"INSERT INTO scheme_master SET from_date = '".$fdate."', to_date = '".$tdate."', productcode = '".$product."', productcategory = '".$category."', productsubcat = '".$psubcategory."', brand = '".$brand."', scheme_code = '".$ref_no."', scheme_name = '".$scheme_name."', ref_id = '".$next_mno."', scheme_based_type = '".$scheme_based_type."', scheme_based_on = '".$scheme_based_on."', scheme_given_type = '".$scheme_given_type."', scheme_given = '".$scheme_given."', status = 'Active', scheme_attachment = '".$path1."', remark = '".$remark."', entry_by = '".$_SESSION['userid']."', entry_date = '".$datetime."', entry_ip = '".$ip."'")or die("ER4".mysqli_error($link1)); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$ref_no,"Scheme","ADD",$ip,$link1,"");
	//return message
	$msg = "You have successfully created a new scheme with ref. no. ".$ref_no."";
	///// move to parent page
	header("Location:scheme_master.php?msg=".$msg."".$pagenav);
	exit;
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
<script type="text/javascript">
$(document).ready(function(){
    $("#frm1").validate();
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
	///// select scheme based type
	$("#scheme_based_type").change(function() {
		$("#scheme_based_on").val("");
		var aa = $("#scheme_based_type").val();
		$.ajax({
			type:'post',
			url:'../includes/getAzaxFields.php',
			data:{sbt:aa},
			success:function(data){
				$('#hhh').html(data);
			}
		});
		if(this.value){			
			$("#sbo1").css("display","");
			$("#sbo2").html(this.value);
			$("#scm").css("display","");	
		}else{
			$("#sbo2").html("");
			$("#sbo1").css("display","none");
			$("#scm").css("display","none");
		}
    });
	///// check scheam amount ///////
	$("#scheme_given").blur(function(){
		if($("#scheme_given_type").val() == "Discount Percentage"){
			if(parseInt($("#scheme_given").val())>100){
				alert("Please insert less then 100%.");
				$("#scheme_given").val("");
			}
		}else{
			if(parseInt($("#scheme_based_on").val()) > parseInt($("#scheme_given").val())){
			
			}else{
				alert("Scheme Qty is allways less then total Qty.");
				$("#scheme_given").val("");
			}	
		}		
	});
	
});
///// select scheme given type
function show_INput(val){
	$("#scheme_given").val("");
	if(val){
		$("#scm_gvn1").css("display","");
		$("#scm_gvn2").html(val);
	}else{
		$("#scm_gvn2").html("");
		$("#scm_gvn1").css("display","none");
	}
}
</script>
<script src="../js/jquery.validate.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-tags"></i>&nbsp;Add New Scheme</h2><br/>
      		<?php if($_REQUEST[msg]){?><br>
      			<h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      		<?php }?>
	  		<form id="frm1" name="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
            	<div class="form-group">
           			<div class="col-md-10"><label class="col-md-3 control-label">Scheme Name <span class="red_small">*</span></label>
              			<div class="col-md-9">
                			<input name="scheme_name" id="scheme_name" type="text" class="form-control required mastername" value="<?=$_REQUEST['scheme_name']?>"/>
              			</div>
            		</div>
          		</div>
    			<div class="form-group">
                  <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label">Valid From <span class="red_small">*</span></label>
                     <div class="col-sm-5 col-md-5 col-lg-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                     </div>
                  </div> 
                  <div class="col-md-6"><label class="col-md-5 control-label">Valid To <span class="red_small">*</span></label>
                    <div class="col-md-5 input-append date">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                    </div>
                  </div>
                </div><!--close form group-->
                <div class="form-group">
           			<div class="col-md-6"><label class="col-md-5 control-label">Product Category <span class="red_small">*</span></label>
              			<div class="col-md-6">
                			<select name="category"  id= "category" class="form-control selectpicker required" onChange="document.frm1.submit();" required>
                            	<option value="">--Please Select--</option>
								<?php
                              	$pcat=mysqli_query($link1,"Select catid , cat_name  from product_cat_master where status = '1' ");
                              	while($row_pcat=mysqli_fetch_array($pcat)){
                              	?>
                              	<option value="<?=$row_pcat['catid']?>" <?php if($_REQUEST['category'] == $row_pcat['catid']) { echo "selected" ;}?>><?=$row_pcat['cat_name']?></option>
                              	<?php
								}
                              	?>
                        	</select>
              			</div>
            		</div>
            		<div class="col-md-6"><label class="col-md-5 control-label">Product Sub Category<span class="red_small">*</span></label>
			      		<div class="col-md-6">
                 			<select name="psubcategory"  id= "psubcategory" class="form-control selectpicker required" required onChange="document.frm1.submit();">
				   				<option value="">--Please Select--</option>
	          					<?php
								$pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1'  and productid = '".$_REQUEST['category']."' ");
								while($row_pcat=mysqli_fetch_array($pcat)){
								?>
                  				<option value="<?=$row_pcat['psubcatid']?>"<?php if($_REQUEST['psubcategory'] == $row_pcat['psubcatid']) { echo "selected" ;}?>><?=$row_pcat['prod_sub_cat']?></option>
								<?php
                                }
                                ?>
                        	</select>
				 		</div>
               		</div>
          		</div>
                <div class="form-group">
           			<div class="col-md-6"><label class="col-md-5 control-label">Brand <span class="red_small">*</span></label>
              			<div class="col-md-6">
                			<select name="brand" id="brand" class="form-control selectpicker required" required onChange="document.frm1.submit();">
				 				<option value="">--Please Select--</option>
	           					<?php 
			   					$brand=mysqli_query($link1,"select * from make_master where status = '1'");
			    				while($row=mysqli_fetch_array($brand)){
			   					?>
				 				<option value="<?=$row['id']?>"<?php if($_REQUEST['brand'] == $row['id']) { echo "selected" ;}?>><?=$row['make']?></option>
								<?php
                                }
                                ?>
							</select>
              			</div>
            		</div>
            		<div class="col-md-6"><label class="col-md-5 control-label">Product <span class="red_small">*</span></label>
			      		<div class="col-md-6">
                 			<select  name='product' id="product" class='form-control selectpicker required' data-live-search="true" onChange="document.frm1.submit();">
                          		<option value=''>--Please Select--</option>
                          		<?php
                        		$model_query="SELECT * FROM product_master where productsubcat='".$_REQUEST['category']."' and productcategory='".$_REQUEST["psubcategory"]."' and brand='".$_REQUEST['brand']."'";
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
          		</div>
                <div class="form-group">
           			<div class="col-md-6"><label class="col-md-5 control-label">Applicable On <span class="red_small">*</span></label>
              			<div class="col-md-6">
                        	<select name="scheme_based_type" id="scheme_based_type" required class="form-control selectpicker required" >
                                <option value="">--Please select--</option>
                            	<option value="Total Amount">Total Amount</option>
                                <option value="Total Qty">Total Qty</option>
                            </select>
              			</div>
            		</div>
            		<div class="col-md-6" id="sbo1" style="display:none"><label class="col-md-5 control-label" id="sbo2"> <span class="red_small">*</span></label>
			      		<div class="col-md-6">
							<input name="scheme_based_on" id="scheme_based_on" type="text" class="form-control required number" required/>
				 		</div>
               		</div>
          		</div>
                <div class="form-group" id="scm" style="display:none" > 
           			<div class="col-md-6"><label class="col-md-5 control-label">Scheme Given <span class="red_small">*</span></label>
              			<div class="col-md-6" id="hhh" > 
                        	
              			</div>
            		</div>
            		<div class="col-md-6" id="scm_gvn1" style="display:none"><label class="col-md-5 control-label" id="scm_gvn2"> <span class="red_small">*</span></label>
			      		<div class="col-md-6">
                 			<input name="scheme_given" id="scheme_given" type="text" class="form-control required digits" required/>
				 		</div>
               		</div>
          		</div>
		 		<div class="form-group">
           			<div class="col-md-6"><label class="col-md-5 control-label">Remark</label>
              			<div class="col-md-6">
                			<textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea>
              			</div>
            		</div>
            		<div class="col-md-6"><label class="col-md-5 control-label">Scheme Attach<br/><span class="small">(Allowed upto 2 MB)</span></label>
			      		<div class="col-md-6">
                 			<input type="file" name="attach" id="attach" class="form-control" accept=".xlsx,.xls,image/*,.doc,.docx,.ppt,.pptx,.txt,.pdf"/>
				 		</div>
               		</div>
          		</div>
				<br><br>
          		<div class="form-group">
            		<div class="col-md-12" align="center">
              			<input type="submit" class="btn <?=$btncolor?>" name="Submit" id="" value="Save" title="submit">
              			<input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='scheme_master.php?<?=$pagenav?>'">
            		</div>
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