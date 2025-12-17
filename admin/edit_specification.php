<?php
require_once("../config/config.php");
$part_id = base64_decode($_REQUEST["prdid"]);
$psc_id = base64_decode($_REQUEST["pscid"]);
@extract($_POST);
////// final submit form ////
if($_POST['Submit']=="Save"){
	$partid = base64_decode($keyid);
	$flag = 0;
	///// delete all specification of selected product
	mysqli_query($link1,"DELETE FROM pr_specification where product_id='".$partid."'");
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
		dailyActivity($_SESSION['userid'],$partid,"SPECIFICATION","UPDATE",$ip,$link1,"");	
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
		$("#frm2").validate();
    });
///// function for checking duplicate Product value
function checkDuplicate(fldIndx1, enteredsno) { 
 document.getElementById("save").disabled = false;
	if (enteredsno != '') {
		var check2 = "param_id[" + fldIndx1 + "]";
		var flag = 1;
		var maxcnt = document.getElementById("rowcnt").value;
		for (var i = 0; i < maxcnt; i++) {
			var check1 = "param_id[" + i + "]";
			if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
				if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
					alert("Duplicate Product Selection.");
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
      <h2 align="center"><i class="fa fa-film"></i>&nbsp;&nbsp;Edit Specification</h2>
      <h4 align="center"><strong>Product Name</strong>&nbsp;&nbsp;<?php echo getProductName($part_id,$link1); ?></h4>
      <br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          <div class="red_small"><strong>NOTE :</strong> If you want to remove any one parameter from this product specification, you can blank any one field either <strong>Parameter Name</strong> or <strong>Description</strong>.</div>
          <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary">
                <th style="font-size:13px;" width="30%">Parameter Name</th>
                <th style="font-size:13px;" width="70%">Description</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=0;
			$res2 = mysqli_query($link1,"SELECT * FROM  pr_specification where product_id='".$part_id."'");
			while($row2 = mysqli_fetch_assoc($res2)){
			?>
              <tr>
                <td class="col-md-3">
                    <span id="pdtid0">
                  <select name="param_id[<?=$i?>]" id="param_id[<?=$i?>]" class="form-control selectpicker" data-live-search="true" onChange="checkDuplicate('<?=$i?>',this.value);">
                    <option value="">--None--</option>
                    <?php 
					$sql1 = "select parameter_id,parameter_name from pr_parameter_master where status='1' and sub_categaory_id = '".$psc_id."' order by parameter_name";
			        $res1 = mysqli_query($link1,$sql1);
			        while($row1 = mysqli_fetch_array($res1)){?>
                    <option data-tokens="<?php echo $row1['parameter_name'];?>" value="<?php echo $row1['parameter_id'];?>" <?php if($row2["parameter_id"]==$row1['parameter_id']){ echo "selected";}?>><?php echo $row1['parameter_name'];?></option>
                    <?php }?>
                  </select>
                    </span>
                </td>
                <td class="col-md-1"><input type="text" class="form-control" name="param_desc[<?=$i?>]" id="param_desc[<?=$i?>]" value="<?=$row2["parameter_details"]?>"  autocomplete="off"></td>
                </tr>
                <?php $i++; }?>
            </tbody>
          </table>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="Edit Specification">
              <input type="hidden" name="rowcnt" id="rowcnt" value="<?=$i?>"/>
              <input type="hidden" name="keyid" id="keyid" value="<?=base64_encode($part_id)?>"/>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='specification_master.php?<?=$pagenav?>'">
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