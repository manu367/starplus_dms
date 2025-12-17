<?php
require_once("../config/config.php");
@extract($_GET);
//////////   fetch acess location from function//////////////////////////////////////////////
$acessloc = getAccessLocation($_SESSION['userid'],$link1);
$val   = explode("~" , $_REQUEST['party']);
if($val[1] != ''){
		$custid  = " cust_id = '".$val[1]."' ";
} else if($_REQUEST['vendor'] != '') {
		$custid = " cust_id = '".$_REQUEST['vendor']."' ";
}else {
$custid = "1";
}

if($_REQUEST['party']){
$partydetails= $val[1];
} else if ($_REQUEST['vendor']){
$partydetails= $_REQUEST['vendor'];
}else {
$partydetails = "";
}

$today = date("Y-m-d");

?>
<!DOCTYPE html>
<html>
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function(){
    $("#form1").validate();
});

$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		 todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		 todayHighlight: true,
		autoclose: true
	});
});

function getValue(val)
{
if(val != ''){
document.getElementById("show").style.display ="none";

}  
else {
document.getElementById("show1").style.display ="none";
}

}

</script>
<script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
 <title><?= siteTitle ?></title>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
                <h2 align="center"><i class="fa fa-check-circle"></i> Party Ledger</h2><br>
                <!--<?php if ($_REQUEST['msg']) { ?>c
                                <h4 align="center" style="color:#FF0000"><?= $_REQUEST['msg'] ?></h4>
                <?php } ?>-->
                <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">

                    <div class="form-group">
                        <div class="col-md-6" style=" display:inline" id="show"><label class="col-md-5 control-label">Party Name :</label>	  
                            <div class="col-md-6" align="left">
                                <select name="party" id="party" class="form-control"  onChange="document.form1.submit();">
                                    <option value="">Select Name</option>
                                    <?php
                                    $sql = mysqli_query($link1, "Select distinct(mapped_code) , uid   from mapped_master where uid  in ($acessloc)  and status = 'Y'  group by mapped_code order by mapped_code");
                                    while ($row = mysqli_fetch_assoc($sql)) {
								 $name = mysqli_fetch_array(mysqli_query($link1,"select uid, name from asc_master where  uid = '".$row['mapped_code']."' " ));									
                                        ?>
										 <option data-tokens="<?=$row['mapped_code']?>" value="<?=$row['uid']."~".$row['mapped_code']?>" <?php if($row['uid']."~".$row['mapped_code'] ==$_REQUEST['party'])echo "selected";?> >
                       <?=$name['name']." | ".$row['mapped_code']?>
          
										</option>
                                            <?php } ?>											
                                </select>
                            </div>
                        </div>
						<?php if ($_REQUEST['party'] == ''){?>
                        <div class="col-md-6"><label class="col-md-5 control-label"> Vendor Name</label>	  
                            <div class="col-md-6" align="left" id="show1" style=" display:inline">
							 <select name="vendor" id="vendor" class="form-control"  onChange="getValue(this.value);" >
                                    <option value="">Select Name</option>
                                    <?php
                                    $sql = mysqli_query($link1, "Select id, name from  vendor_master   where  status = 'Active' ");
                                    while ($row = mysqli_fetch_assoc($sql)) {				
                                        ?>
                            
										 <option data-tokens="<?=$row['id']?>" value="<?=$row['id']?>" <?php if($row['id'] ==$_REQUEST['vendor'])echo "selected";?> >
                       <?=$row['name']." | ".$row[id]?>
                                            <?php } ?>
                                </select>
                                
                            </div>
                        </div>                  
                    </div><!--close form group-->
					<?php }?>
					
					   <div class="form-group">
                        <div class="col-md-6"><label class="col-md-5 control-label">Location Name :</label>	  
                            <div class="col-md-6" align="left">
							<?php if($_REQUEST['party'] != '') {?>
							<select name="location" id="location" class="form-control"  >
                                    <option value="">Select Name</option>
                                    <?php
                                    $sql = mysqli_query($link1, "Select uid from  mapped_master  where  mapped_code  in ('".$val[1]."') ");
                                    while ($row = mysqli_fetch_assoc($sql)) {
									 $name_pary = mysqli_fetch_array(mysqli_query($link1,"select uid, name from asc_master where  uid = '".$row['uid']."' " ));
                                        ?>
                                        <option value="<?= $name_pary['uid']; ?>" <?php
                                        if ($_REQUEST['location'] == $name_pary['uid']) {
                                            echo "selected";
                                        }
                                        ?>><?= $name_pary['name']."($name_pary[uid])"; ?></option>
                                            <?php } ?>
                                </select>
								<?php }
								 else {	?>
								 <select name="location" id="location" class="form-control"  >
                                    <option value="">Select Name</option>
                                    <?php
                                    $sql_asc = mysqli_query($link1, "Select uid ,name from  asc_master  order by name ");
                                    while ($row_asc = mysqli_fetch_assoc($sql_asc)) {
                                        ?>
                                        <option value="<?= $row_asc['uid']; ?>" <?php
                                        if ($_REQUEST['location'] == $row_asc['uid']) {
                                            echo "selected";
                                        }
                                        ?>><?= $row_asc['name']."('".$row_asc[uid]."')"; ?></option>
                                            <?php } ?>
                                </select>							
							<?php }	?>
							
                             
                            </div>
                        </div>
                        <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-6" align="left">
						
                             
                            </div>
                        </div>                  
                    </div><!--close form group-->
					
					<div class="form-group">
					 <div class="col-md-4"><label class="col-md-4 control-label">From Date</label>	  
                            <div class="col-md-8" align="left">
                                <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"><label class="col-md-4 control-label">To Date</label>	  
                            <div class="col-md-8" align="left">
                                <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
                        </div>
                    </div><!--close form group-->
					
                    <div class="form-group">
                        <div class="col-md-6">&nbsp;</div>
                        <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-5" align="left">
                                <?php
               
                                ////// check this user have right to export the excel report
                                if ($_REQUEST['Submit'] != '') {
                                    ?>
									<div style="float:right">
                                    <a href="excelexport.php?rname=<?= base64_encode("partyledgerReport") ?>&rheader=<?= base64_encode("PartyLedger") ?>&user_id=<?=base64_encode($_REQUEST['location'])?>&partyname=<?=base64_encode($custid)?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export  details in excel"></i></a>
									</div>
								<div style="float:left"><a href="#" onClick="javascript:window.open('../pdfpage/pdf_Ledger.php?user_id=<?=base64_encode($_REQUEST['location'])?>&partyname=<?=base64_encode($custid)?>&partynamedetails=<?=$partydetails?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>','PrintInvoice','toolbar=no, status=no, resizable=yes, scrollbars=yes, width=860,height=700,top=50,left=350')"><img src="../img/invoice11.png" border="0" width="25" title="PDF" height="25"></a>
								</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div><!--close form group-->
                </form>
                <form class="form-horizontal table-responsive" role="form">
                    <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr>     
							<th><a href="#" name="name" title="asc" ></a>SNo.</th>         
                              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Location Name</th>
							  <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Party Name</th>
                                <th><a href="#" name="name" title="asc" ></a>Document No.</th>
                                <th><a href="#" name="name" title="asc" ></a>Document Type</th>
                                <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Document Date</th>
								<th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Payment Date</th>
								<th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Payment Remark</th>
								<th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Approval Remark</th>
                                <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Amount CR</th>
                                <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Amount DR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($_REQUEST['Submit']!=""){
							$fromd = date_format(date_create($_REQUEST['fdate']), "Y-m-d");
                            $tod = date_format(date_create($_REQUEST['tdate']), "Y-m-d");
							if ($_REQUEST['fdate'] != '' or $_REQUEST['tdate'] != '') {
                                $datefilter =" and entry_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
                            }
							else {
							  $datefilter =" and entry_date BETWEEN '" . $today . "' and '" . $today . "'";
							} 
							
							if($_REQUEST['location'] != ''){
							$locationcode = "location_code='" .$_REQUEST['location'] . "' " ;
							}else { $locationcode = "1";}
							
							
							
							$i=1;	
							////////////   fetching data from party ledger table//////////////////////////////////////////////////////////////////	
                            $sql = mysqli_query($link1, "Select location_code,cust_id, doc_no , doc_date ,doc_type  ,amount ,cr_dr   from party_ledger where $locationcode and $custid $datefilter  ");
				
                            while ($row = mysqli_fetch_assoc($sql)) {
							//////  calculation for cr / dr /////////////////////////////////////////////////////////////////
							if ($row['cr_dr'] == "CR" || $row['cr_dr'] == "cr") { 
							$cr_amt = $row["amount"];  $dr_amt = "0" ;}
							else { $dr_amt = $row["amount"];  $cr_amt = "0";  }
			                //////////////////////// get location name ////////////////////////////////////////////////////////////////////////////////////////////////
                                $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from asc_master where uid='" .$row['location_code'] . "'"));
								/////////////  fetch  remark and payment date from payment receive table///////////////////////////////////////////////
								$payment_details = mysqli_fetch_assoc(mysqli_query($link1,"select remark , payment_date from payment_receive where doc_no = '".$row['doc_no']."' "));
								////  fetch approval remark from approval tabe //////////////////////////////////////////////////////
								$approval = mysqli_fetch_assoc(mysqli_query($link1,"select action_remark from approval_activities where ref_no = '".$row['doc_no']."' "));
                                ?>
                                <tr class="even pointer">  
									<td><?=$i?></td>           
                                    <td><?= $username['name'];?></td>
									<td><?php   $name =getLocationDetails($row['cust_id'],"name" ,$link1);
												 $vendor = getVendorDetails($row['cust_id'],"name",$link1);
												if($name != ''){ echo $name;} else if ($vendor != ''){echo $vendor;} else {}
									 ?></td>
                                    <td><?= $row['doc_no']; ?></td>
									<td><?php if ($row['doc_type'] == 'VPO') { echo "Purchase";} else {echo $row['doc_type']; }?></td>
                                    <td ><?=dt_format($row['doc_date']); ?></td>   
									<td ><?=dt_format($payment_details['payment_date']); ?></td> 
									<td ><?= $payment_details['remark']; ?></td> 
									<td ><?= $approval['action_remark']; ?></td>            
                                  <td><?= $cr_amt;?></td>
									<td><?= $dr_amt;?></td>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
	</div>


    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
    <script>
        $(document).ready(function() {
            $('#myTable').dataTable();
        });
   
    </script>
</body>
</html>