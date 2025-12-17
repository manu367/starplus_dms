<?php
////// Function ID ///////
$fun_id = array("a"=>array(7));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
## selected location
if($locationcode!=""){
	$loc="asc_code='".$locationcode."'";
	$imeiloc="owner_code='".$locationcode."'";
}else{
	$locstr=getAccessLocation($_SESSION['userid'],$link1);
	$loc="asc_code in (".$locstr.")";
	$imeiloc="owner_code in (".$locstr.")";
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
 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-money fa-lg"></i>&nbsp;Payment</h2><br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
      <form class="form-horizontal" role="form" name="form1" action="" method="POST">
      <div class="row">
        <div class="col-sm-4" align="right"><label class="control-label">Location:</label></div>
        <div class="col-sm-5"><select name="locationcode" id="locationcode" class="form-control selectpicker" data-live-search="true" onChange="document.form1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                    
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					
                    ?>
                 </select>
              </div>
              <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-4"><br/></div>
        <div class="col-sm-5"></div>
        <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-4" align="right"><label class="control-label">Payment Type:</label></div>
        <div class="col-sm-5"><select name="paymenttype" id="paymenttype" class="form-control" required onChange="document.form1.submit();"> 
                    <option value="" selected="selected">Please Select </option>
                    <option value="Payable"<?php if($_REQUEST['paymenttype']=="Payable"){ echo "selected";}?>>Payable</option>
                    <option value="Receivable"<?php if($_REQUEST['paymenttype']=="Receivable"){ echo "selected";}?>>Receivable</option>
                 </select></div>
        <div class="col-sm-3"><!--<input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">-->
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
         </div>
      </div>
      </form> 
      <?php if($_REQUEST['paymenttype']!=''){ ?>
      <?php if($_REQUEST['paymenttype']=="Payable"){ ?>
      <div class="row">
        <div class="col-sm-4"><br/></div>
        <div class="col-sm-5"></div>
        <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-4" align="right"><label class="control-label">Excel Export</label></div>
        <div class="col-sm-5"><a href="excelexport.php?rname=<?=base64_encode("payment_payable")?>&rheader=<?=base64_encode("Payable")?>&loc=<?=base64_encode($_GET['locationcode'])?>" title="Export payable details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export payable details in excel"></i></a></div>
        <div class="col-sm-3"></div>
      </div>
      <?php }?>
      <?php if($_REQUEST['paymenttype']=="Receivable"){ ?>
       <div class="row">
        <div class="col-sm-4"><br/></div>
        <div class="col-sm-5"></div>
        <div class="col-sm-3"></div>
      </div>
      <div class="row">
        <div class="col-sm-4" align="right"><label class="control-label">Excel Export</label></div>
        <div class="col-sm-5"><a href="excelexport.php?rname=<?=base64_encode("payment_receivable")?>&rheader=<?=base64_encode("Receivable")?>&loc=<?=base64_encode($_GET['locationcode'])?>" title="Export receivable details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export receivable details in excel"></i></a></div>
        <div class="col-sm-3"></div>
      </div>
      <?php }?>
      <?php }?>
      </div>  
    </div><!--close tab pane-->
  </div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>