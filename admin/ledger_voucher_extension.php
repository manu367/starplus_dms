<?php
require_once("../config/config.php");
$main_location = base64_decode($_REQUEST['locid']);
if($_POST){
	if(isset($_POST['Submit'])){
   		if ($_POST['Submit']=='Save'){
			@extract($_POST);
			///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
			$messageIdent = md5($main_location . "extension");
			//and check it against the stored value:
    		$sessionMessageIdent = isset($_SESSION['messageIdent'])?$_SESSION['messageIdent']:'';
			if($messageIdent!=$sessionMessageIdent){//if its different:
				//save the session var:
              	$_SESSION['messageIdent'] = $messageIdent;
				////////////insert in voucher extension
				foreach($voucher_ext as $key => $vchextval){
					//// check if already exist
					$row_ve= mysqli_fetch_assoc(mysqli_query($link1,"SELECT id,extension_name FROM ledger_voucher_extension WHERE location_code='".$main_location."' AND ledger_voucher='Voucher' AND extension_for='".base64_decode($vch_type_key[$key])."' AND status='Active'"));
					if($row_ve["extension_name"]==$vchextval){
					
					}else{
						$res_upd = mysqli_query($link1,"UPDATE ledger_voucher_extension SET status='Deactive',update_by='".$_SESSION['userid']."', update_date='".$datetime."' WHERE id ='".$row_ve["id"]."'");
						$usr_add="INSERT INTO ledger_voucher_extension SET location_code ='".$main_location."', ledger_voucher = 'Voucher', extension_for='".base64_decode($vch_type_key[$key])."', extension_name='".$vchextval."',status='Active', entry_by='".$_SESSION['userid']."', entry_date='".$datetime."'";
						$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
					}
				}
				////////////insert in ledger extension
				foreach($ledger_ext as $key => $ldgextval){		
					//// check if already exist
					$row_le= mysqli_fetch_assoc(mysqli_query($link1,"SELECT id,extension_name FROM ledger_voucher_extension WHERE location_code='".$main_location."' AND ledger_voucher='Ledger' AND extension_for='".base64_decode($ldg_type_key[$key])."' AND status='Active'"));
					if($row_le["extension_name"]==$ldgextval){
					
					}else{
						$res_upd = mysqli_query($link1,"UPDATE ledger_voucher_extension SET status='Deactive',update_by='".$_SESSION['userid']."', update_date='".$datetime."' WHERE id ='".$row_le["id"]."'");
						$usr_add="INSERT INTO ledger_voucher_extension SET location_code ='".$main_location."', ledger_voucher = 'Ledger', extension_for='".base64_decode($ldg_type_key[$key])."', extension_name='".$ldgextval."',status='Active', entry_by='".$_SESSION['userid']."', entry_date='".$datetime."'";
						$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
					}
				}
				//////
				dailyActivity($_SESSION['userid'],$main_location,"Ledger Extension","ADD",$ip,$link1,"");
				////// return message
				$msg="You have successfully added voucher/ledger extension for location ".$main_location;
				$cflag = "success";
				$cmsg = "Success";
			}else {
        		//you've sent this already!
				$msg="You have saved this already ";
				$cflag = "warning";
				$cmsg = "Warning";
    		}
		}
		///// move to parent page
    	header("location:asp_details.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&locid=".base64_encode($main_location)."".$pagenav);
    	exit;
	}
}
$vchtyp = getVoucherType($link1);
////// get voucher ledger extension name
$arr_vle = array();
$res_vle = mysqli_query($link1,"SELECT ledger_voucher, extension_for, extension_name FROM ledger_voucher_extension WHERE location_code='".$main_location."' AND status='Active'");
while($row_vle = mysqli_fetch_assoc($res_vle)){
	$vouchertype = getAnyDetails($row_vle["extension_for"],"type_name","id","voucher_type",$link1);
	$arr_vle[$row_vle["ledger_voucher"]][$vouchertype] = $row_vle["extension_name"];
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
    $('#subloc-grid').dataTable();
});
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
		<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
        	<h2 align="center"><i class="fa fa-bank"></i> Voucher / Ledger Extension Entry For Location <br/><?=str_replace("~"," , ",getAnyDetails($main_location,"name,city,state","asc_code","asc_master",$link1));?>(<?=$main_location?>)</h2>
	    	<?php if(isset($_REQUEST['msg'])){
			$_SESSION['messageIdent'] = "";
			?>
            <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
            </div>
            <?php }?>
            <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><i class="fa fa-building" aria-hidden="true"></i> Voucher Extension</div>
                        <div class="panel-body">
                        	<?php
							for($i=0; $i<count($vchtyp); $i++){
								////explode val
								$exp_vch = explode("~",$vchtyp[$i]);
								
							?>
                            <div class="form-group">
                				<div class="col-sm-6"><strong><?=$exp_vch[1]?> Voucher <span class="red_small">*</span></strong><input name="vch_type_key[]" value="<?=base64_encode($exp_vch[0])?>" type="hidden"/></div>
                                <div class="col-sm-6"><input type="text" name="voucher_ext[]" class="required mastername form-control cp" id="voucher_ext<?=$i?>" required value="<?=$arr_vle["Voucher"][$exp_vch[1]]?>"/></div>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-success">
                        <div class="panel-heading"><i class="fa fa-balance-scale" aria-hidden="true"></i> Ledger Extension</div>
                        <div class="panel-body">
                			<?php
							for($i=0; $i<count($vchtyp); $i++){
								////explode val
								$exp_ldg = explode("~",$vchtyp[$i]);
							?>
                            <div class="form-group">
                				<div class="col-sm-6"><strong><?=$exp_ldg[1]?> Ledger <span class="red_small">*</span></strong><input name="ldg_type_key[]" value="<?=base64_encode($exp_ldg[0])?>" type="hidden"/></div>
                                <div class="col-sm-6"><input type="text" name="ledger_ext[]" class="required mastername form-control cp" id="ledger_ext<?=$i?>" required value="<?=$arr_vle["Ledger"][$exp_ldg[1]]?>"/></div>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<div class="col-md-12" align="center">
                    	<input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
                      	<input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
                    </div>
            	</div>
            </div>
			</form>
    	</div><!--close tab pane-->
	</div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>