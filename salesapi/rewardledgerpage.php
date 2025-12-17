<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});
var fontSize = 6;
function zoomIn() {
	fontSize += 2;
	document.getElementById("itemdet").style.fontSize = fontSize + "px";
}
function zoomOut() {
	fontSize -= 2;
	document.getElementById("itemdet").style.fontSize = fontSize + "px";
}
</script>
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
    		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
       			<h2 align="center"><i class="fa fa-balance-scale"></i> Reward Ledger</h2>
                <form class="form-horizontal" role="form" name="frm1" id="frm1" action="" method="post">
                <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                    <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='redeemptionpage.php?&usercode=<?=$_REQUEST['usercode']?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button><br/><br/>
                    <input type="button" value="ZOOM IN +" onClick="zoomIn()"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="ZOOM OUT -" onClick="zoomOut()"/>
                    <select name="locationcode" id="locationcode" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='".$_REQUEST["usercode"]."' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='".$result_chl['location_id']."'"));
	                    
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					
                    ?>
                 </select>
                    <table id="itemdet" class="table table-bordered" width="100%" style="font-size:6px">
                    	<thead>
                            <tr class="<?=$tableheadcolor?>">
                                <th style="text-align:center" width="5%">S.No.</th>
                                <th style="text-align:center" width="15%">Transaction Date</th>
                                <th style="text-align:center" width="15%">Transaction No.</th>
                                <th style="text-align:center" width="25%">Product</th>
                                <th style="text-align:center" width="10%">Type</th>
                                <th style="text-align:center" width="15%">DR</th>
                                <th style="text-align:center" width="15%">CR</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i=1;
                        $total_cr = 0;
                        $total_dr = 0;
                        $podata_sql="SELECT * FROM reward_points_ledger WHERE location_code='".$_REQUEST['locationcode']."' ORDER BY id ASC";
                        $podata_res=mysqli_query($link1,$podata_sql);
                        while($podata_row=mysqli_fetch_assoc($podata_res)){
                            $proddet=explode("~",getProductDetails($podata_row['partcode'],"productname,model_name,productcode",$link1));
							$status = "";
							$class = "";
							if($podata_row['reward_type']=="EARN"){
								$row_sts1 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT status FROM sale_registration WHERE serial_no='".$podata_row['transaction_no']."' AND DATE(entry_date)='".$podata_row['transaction_date']."' ORDER BY id DESC"));
								$status = $row_sts1['status']; 
								$class = "text-success";
							}else if($podata_row['reward_type']=="BURN"){
								$row_sts2 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT status FROM reward_redemption_master WHERE system_ref_no='".$podata_row['transaction_no']."'"));
								$status = $row_sts2['status'];
								$class = "text-danger";
							}else{
								$status = "";
								$class = "";
							}
                        ?>
                            <tr class="<?=$class?>">
                                <td><?=$i?></td>
                                <td><?=$podata_row['transaction_date']?></td>
                                <td><?=$podata_row['transaction_no']."<br/><i><span class='small'>(".$status.")</span></i>";?></td>
                                <td><?=$proddet[0]." , ".$proddet[1]." (".$proddet[2].")";?></td>
                                <td><?=$podata_row['reward_type']?></td>
                                <td style="text-align:right"><?=$podata_row['dr_reward']?></td>
                                <td style="text-align:right"><?=$podata_row['cr_reward']?></td>
                            </tr>
                        <?php
                            $total_cr += $podata_row['cr_reward'];
                            $total_dr += $podata_row['dr_reward'];
                            $i++;
                        }
                        ?>
                        <tr>
                              <td colspan="5" align="right"><strong>Total</strong></td>
                              <td style="text-align:right"><strong><?=$total_dr?></strong></td>
                              <td style="text-align:right"><strong><?=$total_cr?></strong></td>
                          </tr>
                    </tbody>
                </table>  
       		  </div>
           		<!--End form group--> 
                </form>
     		</div>
        	<!--End col-sm-9--> 
		</div>
    	<!--End row content--> 
 	</div>
    <!--End container fluid-->
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>