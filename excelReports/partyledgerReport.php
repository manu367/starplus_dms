<?php
print("\n");
print("\n");

///////  extract value//////////////////////////////////
$location = base64_decode($_REQUEST['user_id']);
$partyname = base64_decode($_REQUEST['partyname']);
$fromdate = base64_decode($_REQUEST['fromDate']);
$todate = base64_decode($_REQUEST['toDate']);

if ($fromdate != '' or $todate != '') {
      $datefilter =" and entry_date BETWEEN '" . $fromdate . "' and '" . $todate . "'";
              }
else {
		$datefilter =" and entry_date BETWEEN '" . $today . "' and '" . $today . "'";
		} 
		
if($location != ''){
		$locationcode = " and location_code='" .$location . "' " ;
	}else { $locationcode = "1";}
	
$variable = $partyname;

////////////   fetching data from party ledger table//////////////////////////////////////////////////////////////////		
  $sql = mysqli_query($link1, "Select location_code, cust_id,doc_no , doc_date ,doc_type  ,amount ,cr_dr   from party_ledger where  $variable $datefilter $locationcode  and doc_no NOT IN (SELECT doc_no FROM party_ledger where doc_type = 'CANCEL CORPORATE INVOICE' ) order by doc_date ");
                           
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
        <td height="25"><strong>S.No.</strong></td>
        <th>Location Name</th>
		<th>Location Code</th>
		 <th>Party Name</th>
        <th>Document No.</th>
        <th>Document Type</th>
        <th>Document Date</th>
		 <th>Payment Date</th>
		 <th>Payment Remark</th>
		 <th>Approval Remark</th>
        <th>Amount CR</th>
        <th>Amount DR</th>
    </tr>
    <?php
	$i=1;
    while ($row = mysqli_fetch_assoc($sql)) {
							//////  calculation for cr / dr /////////////////////////////////////////////////////////////////
							if ($row['cr_dr'] == "CR" || $row['cr_dr'] == "cr") { 
							$cr_amt = $row["amount"];  $dr_amt = "0" ;}
							else { $dr_amt = $row["amount"];  $cr_amt = "0";  }			
              $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from asc_master where uid='" .$row['location_code'] . "'"));
			  /////////////  fetch  remark and payment date from payment receive table///////////////////////////////////////////////
				$payment_details = mysqli_fetch_assoc(mysqli_query($link1,"select remark , payment_date from payment_receive where doc_no = '".$row['doc_no']."' "));
				////  fetch approval remark from approval tabe ///////////////////////////////////////////////////////////
			$approval = mysqli_fetch_assoc(mysqli_query($link1,"select action_remark from approval_activities where ref_no = '".$row['doc_no']."' "));
        ?>
        <tr>
           <td><?=$i?></td>           
            <td><?= $username['name'];?></td>
				<td><?=$row['location_code']?></td>
			<td><?php   $name =getLocationDetails($row['cust_id'],"name" ,$link1);
								 $vendor = getVendorDetails($row['cust_id'],"name",$link1);
				if($name != ''){ echo $name;} else if ($vendor != ''){echo $vendor;} else {}
									 ?></td>
		
            <td><?= $row['doc_no']; ?></td>
			<td><?php if ($row['doc_type'] == 'VPO') { echo "Purchase";} elseif($row['doc_type'] == 'RP') { echo "Payment Received"; }else {echo $row['doc_type']; }?></td>
            <td ><?=dt_format($row['doc_date']); ?></td>   
			 <td ><?=dt_format($payment_details['payment_date']); ?></td>   
			 <td><?= $payment_details['remark'];?></td> 
			 <td><?= $approval['action_remark'];?></td>            
        	<td><?= $cr_amt;?></td>
			<td><?= $dr_amt;?></td>
        </tr>
        <?php
        $i+=1;
    }
    ?>
</table>
