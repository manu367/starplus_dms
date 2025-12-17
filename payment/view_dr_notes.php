<?php
require_once("../config/config.php");

/// Get Master Table
$pi_no = base64_decode($_REQUEST['ref_no']);

$si_master=mysqli_fetch_array(mysqli_query($link1,"select * from debit_note where ref_no='$pi_no'"));
$address=preg_split('/<br[^>]*>/i',$si_master['comp_add']);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
  <script src="../js/jquery.validate.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
     include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa <?=$fa_icon?>"></i> View  Debit Notes</h2>
       <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
         <div class="panel-group">
          <div class="panel panel-default table-responsive">
          <div class="panel-heading heading1" >Information</div>
             <div class="panel-body">
              <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="30%"><label class="control-label">Location</label></td>
                    <td><?php $get_result2=explode("~",getLocationDetails($si_master['location_id'],"name,city,state",$link1)); echo $get_result2[0].",".$get_result2[1].",".$get_result2[2];?></td>
                      <td width="20%" ><label class="control-label">Status</label></td>
                          <td width="29%"><?php if($si_master['app_status']!=''){if($si_master['status']=='Cancelled')echo $si_master['status']; else echo $si_master['app_status']; } else echo $si_master['status'];?>
                          </td> 
                          </tr>                        
                    <tr>
                      <td> <label class="control-label">Customer Name</label></td>
                        <td ><?php $custdet = explode(",",getAnyParty($si_master['cust_id'],$link1)); echo $custdet[0].",".$custdet[1].",".$custdet[2].",".$custdet[3];?> </td>
                      <td ><label class="control-label">Entry By</label></td>
                      <td><?php echo  $get_result_user=getAdminDetails($si_master['create_by'],"name",$link1)."(".$si_master['create_by'].")"; ?></td></tr>
                    <tr>  
                        <td  height="30px"><label class="control-label">System Reference No.</label></td>
                        <td ><?=$si_master['ref_no'];?></td>					
                        <td ><label class="control-label">Amount</label></td>
                        <td ><?php echo $si_master['amount']?>
                       </td>                      
                    </tr>
                    <tr>
                        <td><label class="control-label">Entry Date</label></td>
                        <td colspan="3" ><?=$si_master['create_date'];?><!--<img src="images/attachment.png" width="40" height="40" />--></td>
                      </tr>
                    <tr>
                    <td  height="30px"><label class="control-label">Description</label></td>
                    <td  colspan="3"><?=$si_master['description'];?></td> 
                    </tr>
                    <tr><td height="30px"><label class="control-label">Remark</label></td>
                    <td  colspan="3"><?=$si_master['remark']?></td>
                  </tr>
                  <?php if($si_master['status']=="Cancelled"){ ?>
                    <tr>
                    <td height="30px"><label class="control-label">Cancellation Reason</label></td>
                    <td><?=$si_master['cancel_reason'];?></td>
                    <td  height="30px"><label class="control-label">Cancelled By</label></td>
                    <td ><?=$si_master['cancelled_by']." (".$si_master['cancel_date'].")";?></td>
                    </tr>
                  <?php }?>
                    </tbody>
                   </table>
                  </div><!--close panel body-->
                </div><!--close panel-->
                <br><br>  
                <div class="panel panel-default table-responsive">
                            <div class="panel-heading heading1">Items Information</div>
                            <div class="panel-body">
                            <table class="table table-bordered" width="100%">
                                <thead>
                                  <tr class="<?=$tableheadcolor?>" >
                                    <th style="text-align:center" width="4%">#</th>
                                    <th style="text-align:center" width="19%">Product</th>
                                    <th style="text-align:center" width="5%">Qty</th>
                                    <th style="text-align:center" width="5%">Price</th>
                                    <th style="text-align:center" width="5%">Value</th>
                                    <th style="text-align:center" width="7%">Discount %</th>
                                    <th style="text-align:center" width="7%">Discount Amount</th>
                                    <?php if($get_result2[2] == $custdet[2]){?>
                                    <th style="text-align:center" width="5%">Sgst Per(%)</th>
                                    <th style="text-align:center" width="5%">Sgst Amt</th>
                                    <th style="text-align:center" width="5%">Cgst Per(%)</th>
                                    <th style="text-align:center" width="6%">Cgst Amt</th>
                                    <?php } else {?>
                                    <th style="text-align:center" width="6%">Igst Per(%)</th>
                                    <th style="text-align:center" width="8%">Igst Amt</th>
                                    <?php }?>
                                    <th style="text-align:center" width="13%">Total</th>
                                  </tr>
                                </thead>
                                <tbody>
                               <?php
                                $i=1;
                                $podata_sql = "SELECT * FROM debit_note_data where ref_no='".$pi_no."'";
                                $podata_res = mysqli_query($link1,$podata_sql);
                                while($podata_row = mysqli_fetch_assoc($podata_res)){
                                ?>
                                  <tr>
                                    <td><?=$i?></td>
                                    <td><?php $data = getProductDetails($podata_row['prod_code'],"productname,productcolor,productcode",$link1); $d = explode('~', $data); echo $d[0].' | '.$d[1].' | '.$d[2];?></td>
                                    <td style="text-align:right"><?=$podata_row['req_qty']?></td>
                                    <td style="text-align:right"><?=$podata_row['price']?></td>
                                    <td style="text-align:right"><?=$podata_row['value']?></td>
                                    <td style="text-align:right"><?=$podata_row['discount_per']?></td>
                                    <td style="text-align:right"><?=$podata_row['discount']?></td>
                                    <?php if($get_result2[2] == $custdet[2]){?>
                                    <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                                    <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                                    <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                                    <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                                    <?php }else{?>
                                    <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                                    <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                                    <?php }?>
                                    <td style="text-align:right"><?=$podata_row['totalvalue']?></td>
                                  </tr>
                                <?php
                                $i++;
                                }
                                ?>
                                </tbody>
                              </table>
                            </div><!--close panel body-->
                       	</div><!--close panel-->
    <?php
        if($si_master['app_status']!="" && $si_master['status']!="Cancelled"){
	?>
     <div class="panel panel-default table-responsive">
      <div class="panel-heading heading1">Total Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td><label class="control-label">Approved/Reject By</label></td>
               <td><?php echo $name=getAdminDetails($si_master['app_id'],"name",$link1)?></td>
                <td><label class="control-label">Approved/Reject Status</label></td>
                <td><?=$si_master['app_status']?></td>
              </tr>
              <tr>
                <td><label class="control-label">Approved/Reject Date</label></td>
                <td><?php if($si_master['app_date']!='0000-00-00'){ echo $si_master['app_date']."/".$si_master['app_time'];}?></td>
                <td><label class="control-label">Approved/Reject Remark</label></td>
                <td><?=$si_master['app_remark']?></td>
              </tr>
              <tr>
                <td colspan="9" align="center">
                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location='process_debit_notes.php?from_date=<?=$first_dt?>&to_date=<?=$_REQUEST['challan_date']?><?=$pagenav?>'">
                  </td>
                </tr>
                </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
<?php } else { ?>  
   <div align="center" >
         <tr>
                <td colspan="9" align="center">
                  <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location='process_debit_notes.php?from_date=<?=$first_dt?>&to_date=<?=$_REQUEST['challan_date']?><?=$pagenav?>'">
                  </td>
                </tr>
   </div>   
   <?php }?>     
     </form>
     </div>
     </div>
     </div>
<?php
include("../includes/connection_close.php");
?>
