<?php
require_once("../config/config.php");
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
				header("location:updateInvLogistic.php?msg=".$msg."".$pagenav);
				exit;
			}else{
				$dirct1 = "../inv_pod/".date("Y-m");
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
				header("location:updateInvLogistic.php?msg=".$msg."".$pagenav);
				exit;
			}else{
				$dirct2 = "../inv_pod/".date("Y-m");
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
  			$result = mysqli_query($link1," update billing_master set pod1 = '".$pod1_path."' , pod2 = '".$pod2_path."' where challan_no='".base64_decode($_POST['ref_no'])."'");
			$msg = "POD is successfully uploaded.";
			header("location:updateInvLogistic.php?msg=".$msg."".$pagenav);
			exit;
  		}
  		else{
			$msg = "You have not uploaded any POD";
			header("location:updateInvLogistic.php?msg=".$msg."".$pagenav);
			exit;
  		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
////// function for open model for POD upload
function openPOD(docid){
	$.get('pod_upload.php?doc_id=' + docid, function(html){
		 $('#actionModel .modal-body').html(html);
		 $('#actionModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-car"></i> Update Dispatch</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th><a href="#" name="name" title="asc" ></a>Invoice To</th>
              <th><a href="#" name="name" title="asc" ></a>Invoice From</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Invoice No.</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>Invoice Date</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>PO No.</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Entry By</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">View</th>
              <th data-hide="phone,tablet">POD</th>
              <th data-hide="phone,tablet">Update</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql=mysqli_query($link1,"Select * from billing_master where from_location in (".$accesslocation.") AND status IN ('Pending') order by id desc");
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
              <td><?php echo str_replace("~",",",getLocationDetails($row['to_location'],"name,city",$link1));?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city",$link1));?></td>
              <td><?php echo $row['challan_no'];?></td>
              <td><?php echo $row['sale_date'];?></td>
              <td><?php echo $row['po_no'];?></td>
              <td><?php echo getAdminDetails($row['entry_by'],"name",$link1);?></td>
              <td <?php if($row['status']=="PFA"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              <td align="center"><a href='invoiceDetails.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='Invoice Details'><i class="fa fa-eye fa-lg" title="Invoice Details"></i></a></td>
              <td align="center"><?php if($row['pod1']=="" && $row['pod2']==""){ ?><a href='#' onClick="openPOD('<?=base64_encode($row['challan_no'])?>')"><i class='fa fa-upload fa-lg' title='POD Attachment'></i></a><?php }else{?><?php if($row['pod1']){?> <a href='<?=$row['pod1']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download POD1"></i></a> <?php }?> &nbsp;&nbsp;<?php if($row['pod2']){?> <a href='<?=$row['pod2']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download POD2"></i></a> <?php }?><?php }?></td>
              <td align="center">
			  <?php 
			  if($check==1){
			  if($row['status']=="Pending"){?><a href='updateCourierDetials.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='Update Courier Details'><i class="fa fa-edit fa-lg" title="Update Courier Details"></i></a><?php }}else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?></td>
            </tr>
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
          <input type="submit" class="btn<?=$btncolor?>" name="updpod" id="updpod" value="Upload" title="" <?php if($_POST['updtoloc']=='Upload'){?>disabled<?php }?>>
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