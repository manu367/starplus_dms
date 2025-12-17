<?php
require_once("../config/config.php");
$_SESSION['messageIdentDSR']="";
$_SESSION['msgCnlDSR']="";
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
      <h2 align="center"><i class="fa fa-reply-all fa-lg"></i> Direct Sale Return List</h2>
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
        <button title="Add New Direct Return" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='direct_sale_return.php?op=add<?=$pagenav?>'"><span>Add New Direct Return</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th>Tally Sync</th>
              <th><a href="#" name="name" title="asc" ></a>Return From</th>
              <th><a href="#" name="name" title="asc" ></a>Return To</th>
			  <th><a href="#" name="name" title="asc" ></a>Invoice No.</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a> Date </th>
				<th><a href="#" name="name" title="asc" ></a>Ref. Invoice No.</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a> Entry By </th>
			  <th data-hide="phone,tablet">Status</th>
			  <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a> Upload<?=$imeitag?></th>
              <th width="8%" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Print</th>
              <th data-hide="phone,tablet">View</th>
              <th data-hide="phone,tablet">Cancel</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
				//// get cancel rights
			$isCnlRight = getCancelRightNew($_SESSION['userid'],"10",$link1);
			/// get access location ///
			if($_SESSION['userid']=="admin"){
				$sql=mysqli_query($link1,"SELECT * FROM billing_master WHERE type='DIRECT SALE RETURN' order by id desc");
			}else{
				$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
				$sql=mysqli_query($link1,"SELECT * FROM billing_master WHERE from_location IN (".$accesslocation.") AND type='DIRECT SALE RETURN' ORDER BY id DESC");
			}
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  //// check serial no. is uploaded or not
				  $rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$row['challan_no']."'");
				  $check=1;
				  $opt = 0;
				  while($row12=mysqli_fetch_array($rs12)){
					$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
					if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;$opt+=1;}else{ $check*=0;}}else{ $check*=1;$opt+=1;}
				  }
				  ///
				  $post_in_tally = getAnyDetails($row['challan_no'],"post_in_tally","challan_no","credit_note",$link1);
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td align="center"><?php if($post_in_tally=="Y"){?><i class="fa fa-refresh fa-lg text-success" aria-hidden="true" title="sync in tally"></i><?php }else{?><i class="fa fa-ban fa-lg  text-danger" aria-hidden="true" title="not sync in tally"></i><?php }?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city,state",$link1));?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['to_location'],"name,city,state",$link1));?></td>
              <td><?php echo $row['challan_no'];?></td>
              <td><?php echo $row['sale_date'];?></td>
				<td><?php echo $row['inv_ref_no'];?></td>
              <td><?php echo getAdminDetails($row['entry_by'],"name",$link1);?></td>
               
			  <td><?php echo $row['status'];?></td>                  
                  <td align="center"><?php if($row['status']!="Cancelled" && $row['status']!="Rejected"){if($row['imei_attach']==""){ if($opt==0){?><a href='directReturnUploadSerial.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='<?=$imeitag?>Attach'><i class="fa fa-upload fa-lg"></i></a> &nbsp;&nbsp;&nbsp;<?php /*?><a href='invoiceScanImei_retrun_inv.php?id=<?php echo base64_encode($row['challan_no']);?>&invdate=<?php echo base64_encode($row['sale_date']);?>&invloc=<?php echo base64_encode($row['from_location']);?>&invto=<?php echo base64_encode($row['to_location']);?><?=$pagenav?>'  title='<?=$imeitag?>Scan'><i class="fa fa-qrcode fa-lg"></i></a><?php */?><?php }else{ echo "Not Applicable";}}else{ echo "YES";}}?></td>
                  
                  <td align="center"><?php 
			  if($check==1 || $row['status']=="Cancelled"){
			  ?><a href='../print/print_invoice.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print Invoice'><i class="fa fa-print fa-lg" title="Print Invoice"></i></a><?php if($row['imei_attach']){ ?>  &nbsp;&nbsp;<a href='../print/print_imei.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print<?=$imeitag?>'><i class="fa fa-print fa-lg" title="Print<?=$imeitag?>"></i></a><?php }?><?php }else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?></td>
                  
              <td align="center"><a href='directSaleReturnDetails.php?op=edit&id=<?php echo base64_encode($row['challan_no']);?>&from=<?php echo base64_encode($row['to_location']);?>&to=<?php echo base64_encode($row['from_location']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
              
              <td align="center"><?php if($isCnlRight==1){if($row['status']=="Received"){?><a href='cancelDirectReturn.php?id=<?php echo base64_encode($row['challan_no']);?>&from=<?php echo base64_encode($row['to_location']);?>&to=<?php echo base64_encode($row['from_location']);?><?=$pagenav?>' title='cancel'><i class="fa fa-remove fa-lg" title="cancel details"></i></a><?php }}?></td>  
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