<?php
require_once("../config/config.php");
@extract($_POST);
////// final submit form ////
if($_POST['Submit']=="Save"){
	$partid = base64_decode($keyid);
	$flag = 0;
	foreach($param_id as $k => $val){  
		if(mysqli_num_rows(mysqli_query($link1,"select parameter_id from pr_specification where parameter_id='".$param_id[$k]."' and product_id='".$partid."'"))==0){
			$res = mysqli_query($link1,"insert into pr_specification set product_id='".$partid."', parameter_id='".$param_id[$k]."',parameter_details='".ucwords($param_desc[$k])."',update_date='".date("Y-m-d")."'");
			$flag += 1;
		}
		else{
			$flag += 0;
		}
	}
	///// check if any one parameter is added
	if($flag > 0){
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$partid,"SPECIFICATION","ADD",$ip,$link1,"");	
		//return message
		$msg="You have successfully saved Specification for product id ".$partid;
	}else{
		////// return message
		$msg="Entered Specification may already exist or you have not selected any Specification.";
	}
	///// move to parent page
	header("Location:specification_master.php?msg=".$msg."".$pagenav);
	exit;
}
## selected Product Category
if($product_cat!=""){
	$pc = " productid='".$product_cat."'";
	$pcat = " productcategory='".$product_cat."'";
}else{
	$pc = " 1";
	$pcat = " 1";
}
## selected Product Sub Category
if($product_subcat!=""){
	$psc = " psubcatid='".$product_subcat."'";
	$pscat = " productsubcat='".$product_subcat."'";
}else{
	$psc = " 1";
	$pscat = " 1";
}
## selected brand
if($brand!=""){
	$brd = " brand='".$brand."'";
}else{
	$brd = " 1";
}
## selected product id
if($partcode!=""){
	$expld = explode("~",$partcode);
	$part_id = $expld[0];
	$psc_id = $expld[1];
}else{
	$part_id = " 1";
	$psc_id = " 1";
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
 
<script>
	$(document).ready(function(){
        $("#frm1").validate();
		$("#frm2").validate();
    });
	$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var itm = "param_id["+numi.value+"]";
        var itm_desc = "param_desc["+numi.value+"]";
		var preno = document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+ 2;
		if((document.getElementById(itm).value!="" && document.getElementById(itm_desc).value!="") || ($("#addr"+numi.value+":visible").length==0)){
		numi.value = num;
     	var r='<tr id="addr'+num+'"><td><span id="pdtid'+num+'"><select class="form-control selectpicker required" data-live-search="true" name="param_id['+num+']" id="param_id['+num+']" required onchange="checkDuplicate(' + num + ',this.value);"><option value="">--None--</option><?php $sql1 = "select parameter_id,parameter_name from pr_parameter_master where status='1' and sub_categaory_id = '".$psc_id."' order by parameter_name"; $res1 = mysqli_query($link1,$sql1);while($row1 = mysqli_fetch_array($res1)){?><option data-tokens="<?php echo $row1['parameter_name'];?>" value="<?php echo $row1['parameter_id'];?>"><?php echo $row1['parameter_name'];?></option><?php }?></select></span><span><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></span></td><td><input type="text" name="param_desc['+num+']" id="param_desc['+num+']" class="required addressfield form-control" required/></td></tr>';
      $('#itemsTable1').append(r);
	  serachdropdown();
		}
  });
});
///////////////////////////
////// delete product row///////////
function deleteRow(ind){  
  //$("#addr"+(indx)).html(''); 
     var id="addr"+ind; 
     var itemid="param_id"+"["+ind+"]";
	 var qtyid="param_desc"+"["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(itemid).value="";
	document.getElementById(qtyid).value="";
}
///// function for checking duplicate Product value
function checkDuplicate(fldIndx1, enteredsno) {  
 document.getElementById("save").disabled = false;
	if (enteredsno != '') {
		var check2 = "param_id[" + fldIndx1 + "]";
		var flag = 1;
		for (var i = 0; i <= fldIndx1; i++) {
			var check1 = "param_id[" + i + "]";
			if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
				if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
					alert("Duplicate Parameter Selection.");
					document.getElementById(check2).value = '';
					document.getElementById(check2).style.backgroundColor = "#F66";
					flag *= 0;
				}
				else {
					document.getElementById(check2).style.backgroundColor = "#FFFFFF";
					flag *= 1;
					///do nothing
				}
			}
		}//// close for loop
		if (flag == 0) {
			return false;
		} else {
			return true;
		}
	}
	
}
function serachdropdown(){
	$('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
	});
}
 </script>
 <style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-film"></i>&nbsp;&nbsp;Add New Specification</h2><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
          <div class="form-group">
		  	<div class="col-md-6"><label class="col-md-5 control-label">Product Category<span class="red_small">*</span></label>
            	<div class="col-md-5">
                <select name="product_cat" id="product_cat" class="form-control required" required onChange="document.frm1.submit();">
                	<option value=''>--Please Select--</option>
                  	<?php
					$sql1 = "select catid,cat_name from product_cat_master where status='1' order by cat_name";
					$res1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
					while($row1 = mysqli_fetch_array($res1)){
					?>
				  	<option value="<?=$row1['catid']?>"<?php if($_REQUEST['product_cat']==$row1['catid']){ echo "selected";}?>><?=$row1['cat_name']?></option>
					<?php 
					}
                	?>
                </select>
              	</div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Product Sub Category<span class="red_small">*</span></label>
            	<div class="col-md-5">
                <select name="product_subcat" id="product_subcat" class="form-control required" required onChange="document.frm1.submit();">
                	<option value=''>--Please Select--</option>
                  	<?php
					$sql2 = "select psubcatid,prod_sub_cat from product_sub_category where ".$pc." and status='1' order by prod_sub_cat";
					$res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
					while($row2 = mysqli_fetch_array($res2)){
					?>
				  	<option value="<?=$row2['psubcatid']?>"<?php if($_REQUEST['product_subcat']==$row2['psubcatid']){ echo "selected";}?>><?=$row2['prod_sub_cat']?></option>
					<?php 
					}
                	?>
                </select>
              	</div>
           	</div>
          </div>
          <div class="form-group">
		  	<div class="col-md-6"><label class="col-md-5 control-label">Brand<span class="red_small">*</span></label>
            	<div class="col-md-5">
                <select name="brand" id="brand" class="form-control required" required onChange="document.frm1.submit();">
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
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Product<span class="red_small">*</span></label>
            	<div class="col-md-5">
                <select name="partcode" id="partcode" class="form-control required" required onChange="document.frm1.submit();">
                	<option value=''>--Please Select--</option>
                  	<?php
					$sql4 = "select id, productname,productsubcat from product_master where ".$pcat." and ".$pscat." and ".$brd." and status='active' order by productname";
					$res4 = mysqli_query($link1,$sql4) or die(mysqli_error($link1));
					while($row4 = mysqli_fetch_array($res4)){
					?>
				  	<option value="<?=$row4['id']."~".$row4['productsubcat']?>"<?php if($_REQUEST['partcode']==$row4['id']."~".$row4['productsubcat']){ echo "selected";}?>><?=$row4['productname']?></option>
					<?php 
					}
                	?>
                </select>
              	</div>
           	</div>
          </div>
          </form>
          <?php if($_REQUEST["partcode"]!=""){ ?>       
          <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary">
                <th style="font-size:13px;" width="30%">Parameter Name</th>
                <th style="font-size:13px;" width="70%">Description</th>
                </tr>
            </thead>
            <tbody>
              <tr id='addr0'>
                <td class="col-md-3">
                    <span id="pdtid0">
                  <select name="param_id[0]" id="param_id[0]" class="form-control selectpicker required" required data-live-search="true" onChange="checkDuplicate(0, this.value);">
                    <option value="">--None--</option>
                    <?php 
					$sql1 = "select parameter_id,parameter_name from pr_parameter_master where status='1' and sub_categaory_id = '".$psc_id."' order by parameter_name";
			        $res1 = mysqli_query($link1,$sql1);
			        while($row1 = mysqli_fetch_array($res1)){?>
                    <option data-tokens="<?php echo $row1['parameter_name'];?>" value="<?php echo $row1['parameter_id'];?>"><?php echo $row1['parameter_name'];?></option>
                    <?php }?>
                  </select>
                    </span>
                </td>
                <td class="col-md-1"><input type="text" class="form-control required addressfield" name="param_desc[0]" id="param_desc[0]"  autocomplete="off" required></td>
                </tr>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
              <tr class="0">
                <td colspan="2" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              </tr>
            </tfoot>
          </table>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="Add Specification">
              <input type="hidden" name="keyid" id="keyid" value="<?=base64_encode($part_id)?>"/>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='specification_master.php?<?=$pagenav?>'">
            </div>
          </div>
    	</form>
        <?php
		  }
		?>
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