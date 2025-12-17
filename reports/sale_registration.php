<?php
////// Function ID ///////
$fun_id = array("u"=>array(150)); // User:, Admin:24:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

///// if action taken
if($_POST['upd2']=="Update"){
	//// system ref no.
	$reference = base64_decode($_POST["ref_no"]);
	$res_sr = mysqli_query($link1,"SELECT serial_no,location_code,prod_code FROM sale_registration WHERE id='".$reference."'");
	$row_sr = mysqli_fetch_assoc($res_sr);
	///// update sale register
	$res2 =	mysqli_query($link1,"UPDATE sale_registration SET status='".$_POST['status']."', cancel_by ='".$_SESSION['userid']."', cancel_date='".$datetime."', cancel_ip='".$_SERVER['REMOTE_ADDR']."',cancel_remark='".$_POST['remark']."' WHERE id ='".$reference."'");
	//// check if query is not executed
	if ($res2) {
		///check entry in reward ledger
		$res_rwd_ldg = mysqli_query($link1,"SELECT cr_reward FROM reward_points_ledger WHERE transaction_no='".$row_sr['serial_no']."' AND partcode='".$row_sr['prod_code']."' AND location_code='".$row_sr['location_code']."' AND reward_type ='EARN'");
		$row_rwd_ldg = mysqli_fetch_assoc($res_rwd_ldg);
		if($row_rwd_ldg['cr_reward']>0){
			////// insert in reward ledger
			$result_ldg = mysqli_query($link1,"INSERT INTO reward_points_ledger SET partcode='".$row_sr['prod_code']."', location_code='".$row_sr['location_code']."', transaction_no='".$row_sr['serial_no']."', transaction_date='".$today."', reward_type ='EARN-CANCEL', cr_reward='0', dr_reward='".$row_rwd_ldg['cr_reward']."', update_by='".$_SESSION['userid']."', update_on='".$datetime."', update_ip='".$_SERVER['REMOTE_ADDR']."'");
			$result_rwd = mysqli_query($link1,"UPDATE reward_points_balance SET total_reward = total_reward - '".$row_rwd_ldg['cr_reward']."', lastupdate_by='".$_SESSION['userid']."', lastupdate_on='".$datetime."' WHERE location_code='".$row_sr['location_code']."'");
		}
		$flag = dailyActivity($_SESSION['userid'],$reference,"REGISTER SALE","CANCEL",$ip,$link1,"");
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Registered sale with reference id ".$reference." is cancelled successfully.";	
	}else{
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. ".$err_msg;		
	}
	mysqli_close($link1);
	header("location:sale_registration.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}

$accessState=getAccessState($_SESSION['userid'],$link1);

@extract($_POST);
if($brand!=""){
	$pro_brand="brand='".$brand."'";
}else{
	$pro_brand="1";
}
## selected product cat
if($product_cat!=""){
	$pc = "productid='".$product_cat."'";
}else{
	$pc = "1";
}
## selected product sub cat
if($product_sub_cat!=""){
	$psc = "productsubcat='".$product_sub_cat."'";
}else{
	$psc = "1";
}
## selected product
if($product!=""){
	$product="productcode='".$product."'";
}else{
	$product="1";
}
if($location_code!=""){
	$location = "location_code='".$location_code."'";
}else{
	//$acc_loc = getAccessLocation($_SESSION['userid'],$link1);
	//$location = "location_code IN (".$acc_loc.")";
	$location = "";
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <style>
/* The Modal (background) */
.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		padding-top: 50px; /* Location of the box */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: rgb(0,0,0); /* Fallback color */
		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content */
	.modal-content {
		background-color: #fefefe;
		margin: auto;
		padding: 20px;
		border: 1px solid #888;
		width: 50%;
		height: 50%;
		margin-top: 20px;
	}

	/* The Close Button */
	.close {
		color: #aaaaaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}

	.close:hover,
	.close:focus {
		color: #000;
		text-decoration: none;
		cursor: pointer;
	}
</style>
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
 <script>
/////// datatable
$(document).ready(function() {
	var dataTable = $('#myTable').DataTable( {
		"processing": true,
		"serverSide": true,
		//"bStateSave": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/appSaleRegister-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "fdate": "<?=$_REQUEST['fdate']?>", "tdate": "<?=$_REQUEST['tdate']?>", "state": "<?=$_REQUEST['locationstate']?>", "city": "<?=$_REQUEST['locationcity']?>", "prod_cat": "<?=$_REQUEST['product_cat']?>", "prod_subcat": "<?=$_REQUEST['product_sub_cat']?>", "prod_brand": "<?=$_REQUEST['brand']?>", "prod_code": "<?=$_REQUEST['product']?>", "location_code": "<?=$_REQUEST['location_code']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".employee-grid-error").html("");
				$("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#employee-grid_processing").css("display","none");
				
			}
		}
	});
}); 
$(document).ready(function(){
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
	$('[data-toggle="popover"]').popover({
		//trigger : 'hover',
		placement:'top',
		html : true
	});
	$('#myTable').on( 'draw.dt', function () {
   		 rePop();
	});
});
function rePop(){
	$('[data-toggle="popover"]').popover({
		//trigger : 'hover',
		placement : 'top',
		html : true
	}); 
}
/*$(document).ready(function() {
    var eventFired = function ( ) {
        rePop();     
    }
 
    $('#myTable')
        .on( 'order.dt',  function () { eventFired( 'Order' ); } )
        .on( 'search.dt', function () { eventFired( 'Search' ); } )
        .on( 'page.dt',   function () { eventFired( 'Page' ); } )
        .dataTable();
});*/
$(document).ready(function(){
	$("#location_code").select2({
  		ajax: {
   			url: "../includes/getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchCust: params.term, // search term
					requestFor: "allloc",
					userid: '<?=$_SESSION['userid']?>',
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
	$("#frm3").validate({
	  submitHandler: function (form) {
		if(!this.wasSent){
			this.wasSent = true;
			$(':submit', form).val('Please wait...')
							  .attr('disabled', 'disabled')
							  .addClass('disabled');
			//spinner.show();				  
			form.submit();
		} else {
			return false;
		}
	  }
	});
});
function openModelUpd(docid){
	$.get('cancel_sale_registration.php?id=' + docid, function(html){
		 $('#viewModel2 .modal-body').html(html);
		 $('#viewModel2').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#viewModel2 #close_btn").html('<input type="submit" class="btn<?=$btncolor?>" name="upd2" id="upd2" title="Save this" value="Update" <?php if($_POST['upd2']=='Update'){?>disabled<?php }?>/>&nbsp;<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
}
</script>
<style type="text/css">
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
}
.modal-lg{
	 width:auto;
 }
</style>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-book"></i>&nbsp;Registered Sale</h2>
      		<?php if(isset($_REQUEST['msg'])){?>
            <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            </div>
          <?php }?>
            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
            	<div class="row">
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Date</label>
                        <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                        <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">State</label>
                        <select name="locationstate" id="locationstate" class="form-control"  onChange="document.form1.submit();">
                            <option value=''>--Please Select-</option>
                            <?php
                            $circlequery="select distinct(state) from asc_master where state in ($accessState)  order by state";
                            $circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
                            while($circlearr=mysqli_fetch_array($circleresult)){
                            ?>
                            <option value="<?=$circlearr['state']?>"<?php if($_REQUEST['locationstate']==$circlearr['state']){ echo "selected";}?>>
                              <?=ucwords($circlearr['state'])?>
                              </option>
                            <?php 
                            }
                            ?>
                          </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">City</label>
                    	<span id="subdptdiv">
                        <select  name='locationcity' id="locationcity" class='form-control'  onChange="document.form1.submit();">
                            <option value=''>--Please Select-</option>
                            <?php
                            $model_query="SELECT distinct city FROM asc_master where state='".$_REQUEST['locationstate']."' order by city";
                            $check1=mysqli_query($link1,$model_query);
                            while($br = mysqli_fetch_array($check1)){
                            ?>
                            <option value="<?=$br['city']?>"<?php if($_REQUEST['locationcity']==$br['city']){echo 'selected';}?>>
                              <?=$br['city']?>
                              </option>
                            <?php
                            }
                            ?>
                          </select>
                        </span>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Brand</label>
                        <select name="brand" id="brand" class="form-control"  onChange="document.form1.submit();">
                          <option value=''>--Select Brand--</option>
                          <?php
                        $brand="SELECT id,make FROM make_master ORDER BY make";
                        $circleresult=mysqli_query($link1,$brand) or die(mysqli_error($link1));
                        while($circlearr=mysqli_fetch_array($circleresult)){
                        ?>
                          <option value="<?=$circlearr['id']?>"<?php if($_REQUEST['brand']==$circlearr['id']){ echo "selected";}?>><?=ucwords($circlearr['make'])?></option>
                        <?php 
                        }
                        ?>
                    </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product Cat</label>
                        <select  name='product_cat' id="product_cat" class='form-control'  onChange="document.form1.submit();">
                          <option value=''>--Select Product Cat-</option>
                            <?php
                            $pc_qry ="SELECT * FROM product_cat_master ORDER BY cat_name";
                            $pc_res = mysqli_query($link1,$pc_qry);
                            while($pc_row = mysqli_fetch_array($pc_res)){
                            ?>
                            <option value="<?php echo $pc_row['catid'];?>"<?php if($_REQUEST['product_cat']==$pc_row['catid']){ echo "selected";}?>><?=$pc_row['cat_name']?></option>
                        <?php
                        }
                        ?>
                    </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product Sub-Cat</label>
                        <select  name='product_sub_cat' id="product_sub_cat" class='form-control'  onChange="document.form1.submit();">
                          <option value=''>--Select Product Sub Cat-</option>
                            <?php
                            $psc_qry ="SELECT * FROM product_sub_category WHERE ".$pc." ORDER BY prod_sub_cat";
                            $psc_res = mysqli_query($link1,$psc_qry);
                            while($psc_row = mysqli_fetch_array($psc_res)){
                            ?>
                          <option value="<?php echo $psc_row['psubcatid'];?>"<?php if($_REQUEST['product_sub_cat']==$psc_row['psubcatid']){ echo "selected";}?>><?=$psc_row['prod_sub_cat']?></option>
                        <?php
                        }
                        ?>
                    </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Product</label>
                        <select  name='product' id="product" class='form-control selectpicker' data-live-search="true" onChange="document.form1.submit();">
                          <option value=''>--Select Product-</option>
                          <?php
                        $model_query="SELECT * FROM product_master WHERE ".$psc." AND ".$pro_brand." ORDER BY productname";
                        $check1=mysqli_query($link1,$model_query);
                        while($br = mysqli_fetch_array($check1)){
                        ?>
                          <option value="<?php echo $br['productcode'];?>"<?php if($_REQUEST['product']==$br['productcode']){ echo "selected";}?>><?=$br['productname']." | ".$br['productcolor']." | ".$br['productcode']?></option>
                        <?php
                        }
                        ?>
                    </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Location</label>
                        <select name="location_code" id="location_code" class="form-control required" onChange="document.form1.submit();">
                              <option value=''>--Please Select--</option>
                             <?php
                            if(isset($_POST["location_code"])){
                              $loc_name = explode("~",getAnyDetails($_POST["location_code"],"name, city, state","asc_code","asc_master",$link1));
                              echo '<option value="'.$_POST["location_code"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["location_code"].'</option>';
                            }
                            ?>
                            </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><br/>
                        <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                        <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                        <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><br/>
                    	<?php if(daysDifference($_REQUEST['tdate'],$_REQUEST['fdate'])<=30 && $_REQUEST["Submit"]!=""){?>
                    	<a href="excelexport.php?rname=<?=base64_encode("saleRegisterReport")?>&rheader=<?=base64_encode("SaleRegister")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&state=<?=base64_encode($_REQUEST['locationstate'])?>&city=<?=base64_encode($_REQUEST['locationcity'])?>&prod_cat=<?=base64_encode($_REQUEST['product_cat'])?>&prod_subcat=<?=base64_encode($_REQUEST['product_sub_cat'])?>&prod_brand=<?=base64_encode($_REQUEST['brand'])?>&prod_code=<?=base64_encode($_REQUEST['product'])?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>" title="Export sale register details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export sale register details in excel"></i></a>
                        <?php }else{ echo "You can download excel upto 30 days";}?>
                    </div>
                  </div>
              </form>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       		<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          		<thead>
            		<tr class="<?=$tableheadcolor?>">
              			<th width="2%">S.No.</th>
                        <th width="10%">Serial No.</th>
                        <th width="10%">Location Code</th> 
                        <th width="10%">Product Code</th>
                        <th width="10%">Product Name</th>
                        <th width="10%">Invoice No.</th>
                        <th width="8%">Invoice Date</th>
                        <th width="10%">Customer Name</th>
                        <th width="10%">Contact No.</th>
                        <th width="10%">Status</th>
                        <th width="10%">Image</th>
            		</tr>
          		</thead>
          	</table>
   		  </div>
		</div>
	</div>
</div>
<!-- Start Modal view -->
<div class="modal modalTH fade" id="viewModel2" role="dialog">
	<form class="form-horizontal" role="form" id="frm3" name="frm3" method="post">	
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
                	<h2 class="modal-title" align="center"><i class='fa fa-trash faicon'></i>&nbsp; &nbsp;Cancel Sale Registration</h2>
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div align="center" id="err_msg"></div>
      				
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn">
      
    			</div> 
  			</div>
		</div>
	</form>        
</div><!--close Modal view -->
<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
		<span class="close">&times;</span>
        <p id="img" style="text-align: center;"></p>
    </div>
</div>

<?php
include("../includes/footer.php");
?>
<script>
// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[1];

// When the user clicks the button, open the modal 
function getThisValue(i) {
	var img = $("#image"+i).attr('src');
    $("#img").html('<img src="'+img+'" width="550px"/>');
    modal.style.display = "block";
}
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
	if (event.target == modal) {
		modal.style.display = "none";
	}
}
        
//        $(document).on('click',"#myBtn1",function(){
//           
//        });
</script>
</body>
</html>