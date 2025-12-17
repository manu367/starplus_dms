<?php
require_once("../config/config.php");
$_SESSION['msgRecStock']="";
if($_POST){
	if ($_POST['updpod']=='Upload'){
		$allowed = array('pdf', 'png', 'jpg', 'jpeg');
		if($_FILES['pod1']['name']){
			$filepod1 = $_FILES['pod1']['name'];
			$extpod1 = pathinfo($filepod1, PATHINFO_EXTENSION);
			if (!in_array($extpod1, $allowed)) {
				$cflag="danger";
				$cmsg="Failed";
				$msg = "Request could not be processed. Please try again. ".$extpod1." file extension is not allowed in POD 1 attachment";
				header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
				exit;
			}else{
				$dirct1 = "../rec_pod/".date("Y-m");
				if (!is_dir($dirct1)) {
					mkdir($dirct1, 0777, 'R');
				}
				$filepod1_tmp = $_FILES['pod1']['tmp_name'];
				$pod1_path = $dirct1."/".time().$filepod1;
				$up_pod1 = move_uploaded_file($filepod1_tmp, $dirct1."/".time().$filepod1);
			}
		}
		if($_FILES['pod2']['name']){
			$filepod2 = $_FILES['pod2']['name'];
			$extpod2 = pathinfo($filepod2, PATHINFO_EXTENSION);
			if (!in_array($extpod2, $allowed)) {
				$cflag="danger";
				$cmsg="Failed";
				$msg = "Request could not be processed. Please try again. ".$extpod2." file extension is not allowed in POD 2 attachment";
				header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
				exit;
			}else{
				$dirct2 = "../rec_pod/".date("Y-m");
				if (!is_dir($dirct2)) {
					mkdir($dirct2, 0777, 'R');
				}
				$filepod2_tmp = $_FILES['pod2']['tmp_name'];
				$pod2_path = $dirct2."/".time().$filepod2;
				$up_pod2 = move_uploaded_file($filepod2_tmp, $dirct2."/".time().$filepod2);
			}
		}
  		//check if anyone attachment is there either pod1 or pod2
  		if($pod1_path !='' || $pod2_path !=''){
  			$result = mysqli_query($link1," update billing_master set rec_pod1 = '".$pod1_path."' , rec_pod2 = '".$pod2_path."' where challan_no='".base64_decode($_POST['ref_no'])."'");
			$msg = "POD is successfully uploaded.";
			header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
			exit;
  		}
  		else{
			$msg = "You have not uploaded any POD";
			header("location:receiveInvoice.php?msg=".$msg."".$pagenav);
			exit;
  		}
	}
}
////// filters value/////
$filter_str = "";
if($_REQUEST['fdate'] =='' && $_REQUEST['tdate'] ==''){
	//$filter_str	.= " AND entry_date = '".$today."'";
}
if($_REQUEST['fdate'] !=''){
	$filter_str	.= " AND entry_date >= '".$_REQUEST['fdate']."'";
}
if($_REQUEST['tdate'] !=''){
	$filter_str	.= " AND entry_date <= '".$_REQUEST['tdate']."'";
}
if($_REQUEST['from_location'] !=''){
	$filter_str	.= " AND from_location = '".$_REQUEST['from_location']."'";
}
if($_REQUEST['to_location'] !=''){
	$filter_str	.= " AND to_location = '".$_REQUEST['to_location']."'";
}
if($_REQUEST['docType'] !=''){
	$filter_str	.= " AND document_type = '".$_REQUEST['docType']."'";
}
if($_REQUEST['status'] !=''){
	$filter_str	.= " AND status = '".$_REQUEST['status']."'";
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
<script src="../js/jquery-1.10.1.min.js"></script>
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
    $('#myTable').dataTable();
});
////// function for open model for POD upload
function openPOD(docid){
	$.get('../logistic/pod_upload.php?doc_id=' + docid, function(html){
		 $('#actionModel .modal-body').html(html);
		 $('#actionModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
$(document).ready(function(){
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		enddate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		enddate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
</script>
<style type="text/css">
    div.dropdown-menu.open
    {
        max-width:200px !important;
        overflow:hidden;
    }
    ul.dropdown-menu.inner
    {
        max-width:200px !important;
        overflow-y:auto;
    }
</style>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-level-down"></i> Receive Invoice List</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form" name="frm1" action="" method="POST">
      <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
            <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
            <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate" autocomplete="off" id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"style="width:160px;" onChange="document.frm1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Location</label>
            <select name="from_location" id="from_location" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();" >
                <option value="" selected="selected">Please Select </option>
                <?php                                 
                $sql_chl="select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y' AND id_type IN ('SR','DS','RT')";
                $res_chl=mysqli_query($link1,$sql_chl);
                while($result_chl=mysqli_fetch_array($res_chl)){
                $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
                ?>
                <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['from_location'])echo "selected";?>><?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">To Location</label>
            <select name="to_location" id="to_location" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                <option value="" selected="selected">Please Select </option>
                <?php                                 
                $smfm_sql = "SELECT a.asc_code, a.name, a.city, a.state, a.id_type FROM asc_master a, billing_master b WHERE a.asc_code=b.to_location AND b.from_location='".$_REQUEST['from_location']."' GROUP BY b.to_location";
                $smfm_res = mysqli_query($link1,$smfm_sql);
                while($smfm_row = mysqli_fetch_array($smfm_res)){
                ?>
                <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['to_location'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                <?php
                }
                ?>
            </select>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
        </div>
      </div>
 
      <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Type</label>
            <select name="docType" id="docType" class="form-control"  onChange="document.frm1.submit();">
                <option value=''>All</option>
                <option value="INVOICE"<?php if($_REQUEST['docType']=="INVOICE"){ echo "selected";}?>>INVOICE</option>
                <option value="Delivery Challan"<?php if($_REQUEST['docType']=="Delivery Challan"){ echo "selected";}?>>Delivery Challan</option>
            </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Status</label>
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
      </div>
      </form>
      <form class="form-horizontal" role="form">
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th width="5%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th width="5%">Tally Sync</th>
              <th width="15%"><a href="#" name="name" title="asc" ></a>Billing From</th>
              <th width="15%" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Billing To</th>
              <th width="15%" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Invoice No.</th>
              <th width="10%" data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>Invoice Date</th>
              <th width="10%" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Invoice Type</th>
              <th width="10%" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Entry By</th>
              <th width="8%" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th width="8%" data-hide="phone,tablet">Print</th>
              <th width="6%" data-hide="phone,tablet">POD</th>
              <th width="6%" data-hide="phone,tablet">View</th>
              <th width="6%" data-hide="phone,tablet">Receive</th>
      <th width="6%" data-hide="phone,tablet">Receive Stock <br>By Scanning</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			//$sql=mysqli_query($link1,"Select * from billing_master where to_location in (".$accesslocation.") and status in ('Dispatched','Received') AND type NOT IN ('GRN','LP','DIRECT SALE RETURN') AND billing_type!='COMBO' AND entry_date>='2023-01-24' order by id desc");
			if($_SESSION['userid']=="admin"){
				$sql=mysqli_query($link1,"Select * from billing_master where status in ('Dispatched','Received') AND type NOT IN ('GRN','LP','DIRECT SALE RETURN') AND (to_location LIKE 'CABR%' OR to_location LIKE 'CAHO%')  AND entry_date>='2023-01-20' ".$filter_str." order by id desc");
			}else{
				$sql=mysqli_query($link1,"Select * from billing_master where to_location in (".$accesslocation.") and status in ('Dispatched','Received') AND type NOT IN ('GRN','LP','DIRECT SALE RETURN') AND (to_location LIKE 'CABR%' OR to_location LIKE 'CAHO%')  AND entry_date>='2023-01-20' ".$filter_str." order by id desc");
			}
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  //// check serial no. is uploaded or not
				  $rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$row['challan_no']."'");
				  $check=1;
				  while($row12=mysqli_fetch_array($rs12)){
					$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
					if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
				  }
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td align="center"><?php if($row['post_in_tally2']=="Y"){?><i class="fa fa-refresh fa-lg text-success" aria-hidden="true" title="sync in tally"></i><?php }else{?><i class="fa fa-ban fa-lg  text-danger" aria-hidden="true" title="not sync in tally"></i><?php }?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city,state,asc_code",$link1));?></td>
              <td><?php $topty = getLocationDetails($row['to_location'],"name,city,state,id_type",$link1);echo str_replace("~",",",$topty); $exptopty = explode("~",$topty);?></td>
              <td><?php if($row['ref_no']){echo $row['challan_no']."<br/><i>Ref No.-".$row['ref_no']."</i>";}else{ echo $row['challan_no'];};?></td>
              <td><?php echo $row['sale_date'];?></td>
              <td><?php echo $row['type']." ".$row['document_type'];?></td>
              <td><?php echo getAdminDetails($row['entry_by'],"name",$link1);?></td>
              <td <?php if($row['status']=="Dispatched"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              
              <td align="center"><a href='../print/print_invoice.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print Invoice'><i class="fa fa-print fa-lg" title="Print Invoice"></i></a><?php if($row['imei_attach']){ ?>  &nbsp;&nbsp;<a href='../print/print_imei.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print<?=$imeitag?>'><i class="fa fa-print fa-lg" title="Print<?=$imeitag?>"></i></a><?php }?></td>
              
              <td align="center"><?php if($row['rec_pod1']=="" && $row['rec_pod2']==""){ ?><a href='#' onClick="openPOD('<?=base64_encode($row['challan_no'])?>')"><i class='fa fa-upload fa-lg' title='POD Attachment'></i></a><?php }else{?><?php if($row['rec_pod1']){?> <a href='<?=$row['rec_pod1']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download POD1"></i></a> <?php }?> &nbsp;&nbsp;<?php if($row['rec_pod2']){?> <a href='<?=$row['rec_pod2']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download POD2"></i></a> <?php }?><?php }?></td>
              <td align="left"><a href='receiveInvView.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
              <td align="left"><?php 
			  if($check==1){
			  //if($row['status']=="Dispatched" && $exptopty[3]!="BR" && $exptopty[3]!="HO"){
		      if($row['status']=="Dispatched"){
			  if($row["type"]=="PURCHASE RETURN"){
			  ?>
              <a href='receiveInvActionPRN.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='Receive Invoice'><i class="fa fa-shopping-bag fa-lg" title="Receive Invoice"></i></a>
			  <?php }else{
			  ?><a href='receiveInvAction.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='Receive Invoice'><i class="fa fa-shopping-bag fa-lg" title="Receive Invoice"></i></a><?php }}}else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?></td>
				
<td align="left"><?php 
if($check==1){
if($row['status']=="Dispatched"){?><?php /*?><a href='receiveByScanning.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='Serial No.Scan'><i class="fa fa-qrcode fa-lg"></i></a><?php */?><?php }}else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";} ?></td>
            <?php }?>
          </tbody>
          </table>
      </div>
      </form>
    </div>
    
  </div>
</div>
<!-- Start Model Mapped Modal -->
  <div class="modal modalTH fade" id="actionModel" role="dialog">
  <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post" enctype="multipart/form-data">
    <div class="modal-dialog modal-dialogTH modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" align="center"><i class='fa fa-external-link fa-lg faicon'></i> POD Upload</h4>
        </div>
        <div class="modal-body modal-bodyTH">
         <!-- here dynamic task details will show -->
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn<?=$btncolor?>" name="updpod" id="updpod" value="Upload" title="" <?php if($_POST['updpod']=='Upload'){?>disabled<?php }?>>
          <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
    </form>
  </div>
<!--close Model Mapped modal-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>