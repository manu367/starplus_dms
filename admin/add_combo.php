<?php
////// Function ID ///////
$fun_id = array("a"=>array(53));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_POST);
$code = explode("~", $_POST['bom_model']);
////// if we want to Add Combo
if($_POST){
	if($_REQUEST['save']=='Save'){
		if ($bom_model){
			if($apply_app=="Y"){ $current_status = 3;}else{ $current_status = 1;}
   			/////check duplicate Combo of Model
			if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM combo_master WHERE bom_modelcode='".$bom_model."' AND status='1'"))==0){
				///// pick max bom id
				$max_bomid = mysqli_fetch_array(mysqli_query($link1,"SELECT MAX(bomid) FROM combo_master"));
				$next_bomid = $max_bomid[0]+1;
				///// Insert in item data by picking each data row one by one
				foreach($bom_part as $k=>$val){
					$expld_bompart = explode("~",$val);
					//// converson factor ////
					if($conversion_factor[$k]==""||$conversion_factor[$k]==0||$conversion_factor[$k]==0.000000){ $cf = 1; }else{ $cf = $conversion_factor[$k]; }
					$sql_add="INSERT INTO combo_master set bomid='".$next_bomid."', bom_modelcode ='".$bom_model."',bom_modelname='".$bom_model_name."',bom_hsn='".$bom_model_hsn."',bom_partcode='".$expld_bompart[0]."',bom_qty='".$bom_qty[$k]."',bom_unit='NOS',purchase_unit='NOS',conversion_factor='".$cf."',packing_level='0', status='".$current_status."',createdate='".$today."',createby='".$_SESSION['userid']."'";
					$res_add=mysqli_query($link1,$sql_add)or die("error1".mysqli_error($link1));
				}
				////// insert in activity table////
				dailyActivity($_SESSION['userid'],$bom_model."-".$next_bomid,"Combo","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
				////// return message
				$msg="You have successfully created Combo for ".$bom_model_name;
				$cflag="success";
				$cmsg = "Success";
			}else{
				$msg = "Combo is already active for Model ".$bom_model_name;
				$cflag="warning";
				$cmsg = "Warning";
			}   
		}
   		///// move to parent page
    	header("location:combo_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    	exit;
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
 <script>
$(document).ready(function(){
	var spinner = $('#loader');
        $("#frm1").validate({
		  submitHandler: function (form) {
			if(!this.wasSent){
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
								  .attr('disabled', 'disabled')
								  .addClass('disabled');
				spinner.show();				  
				form.submit();
			} else {
				return false;
			}
          }
		});
	$('.selectpicker').selectpicker({
	  //actionsBox: true,
	  //container: true,
      liveSearch: true
	});
});
function makedropdown(){
	$('.selectpicker').selectpicker({
      liveSearch: true
	});
}
$(document).ready(function() {
	$('.number').on('input', function() {
    match = (/(\d{0,6})[^.]*((?:\.\d{0,6})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
  });
});
/////// add new row 
$(document).ready(function() {
	$("#add_row").click(function() {		
		var numi = document.getElementById('rowno');
		var itm = "bom_part[" + numi.value+"]";
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr1_" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr1_'+num+'"><td width="60%"><div style="display:inline-block;float:left;"><select name="bom_part['+num+']" id="bom_part['+num+']" class="form-control required selectpicker show-tick" required data-width="570px" onChange="getbomunit(this.value,'+num+')"><option value="">--Select Part--</option><?php $res_pro = mysqli_query($link1,"SELECT productcode,productname FROM product_master WHERE status='Active' ORDER BY productname")or die(mysqli_error($link1));while($row_pro = mysqli_fetch_assoc($res_pro)){?><option value="<?=$row_pro['productcode']?>"><?=$row_pro['productname']." | ".$row_pro['productcode']?></option><?php } ?></select></div><div style="display:inline-block;float:right;"><i class="fa fa-close fa-lg" onClick="fun_remove('+num+');"></i></div></td><td width="15%"><input type="text" name="bom_qty['+num+']" class="form-control number required" id="bom_qty['+num+']" required /></td><?php /*?><td width="15%"><input type="text" name="conversion_factor['+num+']" class="form-control required number" id="conversion_factor['+num+']" required /></td><?php */?><td width="10%"><span id="bomunit'+num+'"></span></td></tr>';
			$('#itemsTable1').append(r);
			makedropdown();
		}
	});
});
function fun_remove(con){
	var c = document.getElementById('addr1_' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno').value = con;
}
function getbomunit(val,indx){
	//var split_val = val.split("~");
	document.getElementById("bomunit"+indx).innerHTML = "PCS";
}
///// check Combo Model is already active or not
$(document).ready(function() {
	$("#bom_model").keyup(function(){
		var bm = $('#bom_model').val();
          $.ajax({
		  type:"post",
		  url:"../includes/getAzaxFields.php",
		  data:{bommodel:bm},
		  success:function(data){
			  if(parseInt(data) > 0){ 
				  $("#error_msg").html("Combo is already in active status for this model.");
				  $("#save").attr("disabled","disabled");                                  
               
			  }else{
				  $("#error_msg").html("");
				  $("#save").removeAttr("disabled");
			  }          
		  }
		});           
    });
});
</script>
<script src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa <?=$fa_icon?>"></i> Add Combo</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-8"><label class="col-md-3 control-label">Combo Model Name<span class="red_small">*</span></label>
              <div class="col-md-8">
                 <input name="bom_model_name" id="bom_model_name" class="form-control required"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-4 control-label">Combo Model Code<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input name="bom_model" id="bom_model" class="form-control required alphanumeric"/><span id="error_msg" class="red_small"><?=$msg?></span>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Combo Model HSN<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input name="bom_model_hsn" id="bom_model_hsn" class="form-control required digits" maxlength="10"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
            	<table class="table table-bordered" width="100%" id="itemsTable1">
            	<thead>
                	<tr class="<?=$tableheadcolor?>">
                    	<th width="60%">Combo Part</th>
                        <th width="15%">Combo Qty</th>
                        <!--<th width="15%">Conversion Factor</th>-->
                        <th width="10%">Combo Unit</th>
                    </tr>
                </thead>
            	<tbody>
                	<tr id="addr1_0">
                        <td width="60%">
                          <select name="bom_part[0]" id="bom_part[0]" class="form-control required selectpicker" data-live-search="true" required onChange="getbomunit(this.value,0)">
                           <option value="">--Select Part--</option>
                            <?php
                            $res_pro = mysqli_query($link1,"SELECT productcode,productname FROM product_master WHERE status='Active' ORDER BY productname")or die(mysqli_error($link1)); 
                            while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                                <option value="<?=$row_pro['productcode']?>"><?=$row_pro['productname']." | ".$row_pro['productcode']?></option>
                            <?php } ?>
                         </select></td>
                        <td width="15%">
                 			<input type="text" name="bom_qty[0]" class="form-control number required" id="bom_qty[0]" required />
                        </td>
                        <?php /*?><td width="15%">
                 			<input type="text" name="conversion_factor[0]" class="form-control required number" id="conversion_factor[0]" required />
                        </td><?php */?>
                        <td width="10%">
                        	<span id="bomunit0"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
          </div>
            <div class="form-group">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <button class='btn<?=$btncolor?>' id="add_row" type="button" name="add" value="Add"><i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add More Combo Part</button>
                <input type="hidden" name="rowno" id="rowno" value="0"/>
                    </div>
                    <div class="col-md-6"></div>
                </div>
            </div>
          

           
           
          <div class="form-group">
          	<div class="col-md-4" style="display:inline-block; float:left">
                
           </div>
           <div class="col-md-8" style="display:inline-block; float:right" align="left">
               <button class='btn<?=$btncolor?>' id="save" type="submit" name="save" value="Save"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Save</button>
               <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='combo_master.php?status=<?php if(!empty($_REQUEST['status'])){ echo $_REQUEST['status'];}?>&bom_model=<?php if(!empty($code[0])){ echo $code[0];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
            </div>
          </div>
    </form>
      </div>

    </div>
    
  </div>
</div>
<div id="loader"></div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>