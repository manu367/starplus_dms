<?php
////// Function ID ///////
$fun_id = array("u"=>array(44)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$_SESSION["messageIdentLP"]="";
$_SESSION['messageIdentCLP']="";
$_SESSION['messageIdentLPC']="";
$_SESSION['messageIdentLP1']="";
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
      <h2 align="center"><i class="fa fa-ship"></i> Local Purchase</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <?php
		if(isset($_SESSION["logres"]) && $_SESSION["logres"]){
		echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:#e51111;margin:15px;font-size:12px;">';
		echo '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"];
		echo '<br/><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.implode(" , ",$_SESSION["logres"]["invalid"]);
		echo '</div>';
		}
		unset($_SESSION["logres"]);
		?>
      <form class="form-horizontal" role="form">
        <button title="Add New PO" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addNewLocalPurchaseN.php?op=add<?=$pagenav?>'"><span>Add New Local Purchase</span></button>&nbsp;&nbsp;
        <button title="Add New LP From Customer" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addLPFromCustomer.php?op=add<?=$pagenav?>'"><span>Add New Local Purchase From Customer</span></button>&nbsp;&nbsp;
        <button title="Add New Combo Purchase" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='comboPurchaseN.php?op=add<?=$pagenav?>'"><span>Add New Combo Purchase</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>              
              <th>Tally Sync</th>
              <th><a href="#" name="name" title="asc" ></a>Purchase From</th>
              <th><a href="#" name="name" title="asc" ></a>Purchase To</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>LP No.</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>LP Date</th>
				<th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Invoice No.</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Entry By</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Scan<?=$imeitag?></th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Print</th>
              <th data-hide="phone,tablet">View</th>
              <th data-hide="phone,tablet">Receive</th>
			  <th data-hide="phone,tablet">Cancel</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
				//// get cancel rights
			$isCnlRight = getCancelRightNew($_SESSION['userid'],"2",$link1);
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql=mysqli_query($link1,"Select * from vendor_order_master where po_to in (".$accesslocation.") order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  //// check serial no. is uploaded or not
				  $rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM vendor_order_data WHERE po_no='".$row['po_no']."'");
				  $check=1;
				  while($row12=mysqli_fetch_array($rs12)){
					$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
					if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
				  }
				  ////
				  $post_in_tally = getAnyDetails($row['po_no'],"post_in_tally2","challan_no" ,"billing_master",$link1);
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td align="center"><?php if($post_in_tally=="Y"){?><i class="fa fa-refresh fa-lg text-success" aria-hidden="true" title="sync in tally"></i><?php }else{?><i class="fa fa-ban fa-lg  text-danger" aria-hidden="true" title="not sync in tally"></i><?php }?></td>
              <td><?php echo getAnyParty($row['po_from'],$link1);?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['po_to'],"name,city,state",$link1));?></td>
              <td><?php echo $row['po_no'];?></td>
              <td><?php echo $row['requested_date'];?></td>
				<td><?php echo $row['invoice_no'];?></td>
              <td><?php echo getAdminDetails($row['create_by'],"name",$link1);?></td>
              <td <?php if($row['status']=="Pending"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              
              <td align="center"><?php if($row['status']!="Cancelled"){if($row['imei_attach']==""){ if($check==0){?>
              <?php if($row["req_type"]=="LP"){ ?>
              <a href='poUploadImeiLP.php?id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='<?=$imeitag?>Attach'><i class="fa fa-upload fa-lg"></i></a>
              <?php }else{?>
              <a href='poUploadImei.php?id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='<?=$imeitag?>Attach'><i class="fa fa-upload fa-lg"></i></a><?php }?> &nbsp;&nbsp;    
              <a href='poScanImei.php?id=<?php echo base64_encode($row['po_no']);?>&invdate=<?php echo base64_encode($row['requested_date']);?>&invloc=<?php echo base64_encode($row['po_from']);?>&invto=<?php echo base64_encode($row['po_to']);?><?=$pagenav?>'  title='<?=$imeitag?>Scan'><i class="fa fa-qrcode fa-lg"></i></a><?php }else{ echo "Not Applicable";}}else{ echo "YES";}}?></td>
              
              <td align="center">
              <?php  if($check==1 || $row['status']=="Cancelled"){ ?>
              <a href='../print/vendor_print_invoice.php?rb=view&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>' target="_blank"  title='Print Invoice'><i class="fa fa-print fa-lg" title="Print Invoice"></i>              </a><?php if($row['imei_attach']){ ?>  &nbsp;&nbsp;<a href='../print/vendor_print_imei.php?rb=view&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>' target="_blank"  title='Print<?=$imeitag?>'><i class="fa fa-print fa-lg" title="Print<?=$imeitag?>"></i></a><?php }?><?php }else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?>              </td>
              
              <td align="center"><a href='localPurchaseDetails.php?op=edit&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
              <td align="center"><?php if($row['status']=="Pending"){?><?php  if($check==1){ ?><a href='recLocalPurchaseDetailsN.php?op=edit&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='Receive PO'><i class="fa fa-shopping-bag fa-lg" title="Receive PO"></i></a><?php }else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?><?php } ?></td>
	      <td align="center"><?php
	 if($isCnlRight == 1){ if($row['status']!='Cancelled') { ?><a href='cancelvpoDetails.php?op=cancel&id=<?php echo base64_encode($row['po_no']);?><?=$pagenav?>'  title='Cancel PO'><i class="fa fa-trash fa-lg" title="Cancel PO"></i></a><?php } }?> </td>
            </tr>
            <?php }?>
          </tbody>
          </table>
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