<?php
require_once("../config/config.php");
$docid = $_REQUEST['pk'];
?>
<div class="row">
	<div class="col-sm-12 table-responsive">
<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
    <thead>
        <tr>     
        <th><a href="#" name="name" title="asc" ></a>SNo.</th>         
          <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Location Name</th>
          <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Party Name</th>
            <th><a href="#" name="name" title="asc" ></a>Document No.</th>
            <th><a href="#" name="name" title="asc" ></a>Document Type</th>
            <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Transaction Date</th>
            <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Entry Date</th>
            <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Payment Remark</th>
            <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Approval Remark</th>
            <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Amount CR</th>
            <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Amount DR</th>
        </tr>
    </thead>
    <tbody>
        <?php     

        $locationcode = "(location_code='" .$docid . "' or cust_id='" .$docid . "')" ;
        $i=1;	
        ////////////   fetching data from party ledger table//////////////////////////////////////////////////////////////////	
        $sql = mysqli_query($link1, "Select location_code,cust_id, doc_no , doc_date ,doc_type  ,amount ,cr_dr ,entry_date  from party_ledger where ".$locationcode." and doc_type != 'CANCEL CORPORATE INVOICE' order by doc_date ");			
        while ($row = mysqli_fetch_assoc($sql)) {
        //////  calculation for cr / dr /////////////////////////////////////////////////////////////////
        if ($row['cr_dr'] == "CR" || $row['cr_dr'] == "cr") { 
        $cr_amt = $row["amount"];  $dr_amt = "0" ;}
        else { $dr_amt = $row["amount"];  $cr_amt = "0";  }

            $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name from asc_master where asc_code='" .$row['location_code'] . "'"));
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
                <td><?php if ($row['doc_type'] == 'VPO') { echo "Purchase";} elseif($row['doc_type'] == 'RP') { echo "Payment Received"; }else {echo $row['doc_type']; }?></td>
                <td ><?=dt_format($row['doc_date']); ?></td>   
                <td ><?=dt_format($row['entry_date']); ?></td> 
                <td ><?= $payment_details['remark']; ?></td> 
                <td ><?= $approval['action_remark']; ?></td>            
              <td><?= $cr_amt;?></td>
                <td><?= $dr_amt;?></td>
            </tr>
        <?php $i++; }?>
    </tbody>
</table>
</div>
</div>