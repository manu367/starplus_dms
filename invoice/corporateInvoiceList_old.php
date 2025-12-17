<?php
require_once("../config/config.php");
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
      <h2 align="center"><i class="fa fa-user-circle-o"></i> Invoice List</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
        <button title="Back to invoicing" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='corporateInvoice.php?<?=$pagenav?>'">
        <span>Back to invoicing</span>
        </button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th><a href="#" name="name" title="asc" ></a>Invoice From</th>
              <th><a href="#" name="name" title="asc" ></a>Invoice To</th>
              
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Invoice No.</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>Invoice Date</th>
              <th data-hide="phone,tablet">Pending Aging</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>PO No.</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Scan<?=$imeitag?></th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Print</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
              <th data-hide="phone,tablet">Cancel</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
                        
			///// get access location ///
                        
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
			$sql=mysqli_query($link1,"Select * from billing_master where from_location in (".$accesslocation.") and type!='RETAIL' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  $cancel_right=mysqli_num_rows(mysqli_query($link1,"select id from access_cancel_rights where  uid='".$_SESSION['userid']."' and status='Y' and cancel_type='10'"));
				  //// check serial no. is uploaded or not
				  $rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$row['challan_no']."'");
				  $check=1;
				  while($row12=mysqli_fetch_array($rs12)){
					$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
					if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
				  }
				  if($row['status']=="Pending"){$pend_aging = daysDifference($today,$row['sale_date']);}else{ $pend_aging="";}
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city",$link1));?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['to_location'],"name,city",$link1));?></td>
              <td><?php echo $row['challan_no'];?></td>
              <td><?php echo $row['sale_date'];?></td>
              <td><?=$pend_aging?></td>
              <td><?php echo $row['po_no'];?></td>
              <td <?php if($row['status']=="PFA"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              
              <!---<td align="center"><?php //if($row['imei_attach']=='Y'){ echo 'imei_attached';}else{ ?><a href='invoiceUploadImei.php?challan_no=<?php //echo $row['challan_no']; ?><?php //$pagenav; ?>' title='Upload IMEI'><i class="fa fa-upload fa-lg" aria-hidden="true"></i></a><?php //} ?></td>----->
              
              <td align="center"><?php if($row['status']!="Cancelled"){if($row['imei_attach']==""){ ?><a href='invoiceUploadImei.php?challan_no=<?php echo $row['challan_no'];?><?=$pagenav?>'  title='<?=$imeitag?>Attach'><i class="fa fa-upload fa-lg"></i></a> &nbsp;&nbsp;&nbsp;    <a href='invoiceScanImei.php?id=<?php echo base64_encode($row['challan_no']);?>&invdate=<?php echo base64_encode($row['sale_date']);?>&invloc=<?php echo base64_encode($row['from_location']);?>&invto=<?php echo base64_encode($row['to_location']);?><?=$pagenav?>'  title='<?=$imeitag?>Scan'><i class="fa fa-qrcode fa-lg"></i></a><?php }else{ echo "YES";}}?></td>
              
              <td align="center">
              <?php  if($check==1){ ?>
              <a href='../print/print_invoice.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print Invoice'><i class="fa fa-print fa-lg" title="Print Invoice"></i></a><?php if($row['imei_attach']){ ?>  &nbsp;&nbsp;<a href='../print/print_imei.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print<?=$imeitag?>'><i class="fa fa-print fa-lg" title="Print<?=$imeitag?>"></i></a><?php }?><?php }else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?></td>
              
              <td align="center"><a href='invoiceDetailsN.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='Invoice Details'><i class="fa fa-eye fa-lg" title="Invoice Details"></i></a></td>
              <td align="center"><?php if($cancel_right > 0){ if(($row['status']=="Pending") || ($row['status']=="Dispatched")){?><a href='cancelCorporateInvoiceN.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' title='Cancel Invoice'><i class="fa fa-remove fa-lg" title="Cancel Invoice"></i></a><?php }}?></td>
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