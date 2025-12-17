<?php
////// Function ID ///////
$fun_id = array("u"=>array(148)); // User:, Admin:24:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
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
<link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
<script src='../js/select2.min.js'></script>
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	////// from date
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
	/////// to date
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
		todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function(){
	$("#location_code").select2({
  		ajax: {
   			url: "../includes/getAzaxFields.php",
			type: "post",
			dataType: 'json',
			delay: 250,
   			data: function (params) {
    			return {
					searchFromLoc: params.term, // search term
					requestFor: "srchlocaion",
					userid: '<?=$_SESSION['userid']?>'
    			};
   			},
   			processResults: function (response) {
     			return {
        			results: response
     			};
   			},
   			cache: true
  		}
	});	
});	
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>	
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9 tab-pane fade in active" id="home">
       			<h2 align="center"><i class="fa fa-balance-scale"></i> Reward Ledger</h2>
                <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">From Date</label>
                            <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                            <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>" required>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Location</label>
                        	<select name='location_code' id="location_code" class='form-control'>
                              	<option value=''>--Please Select--</option>
								<?php
                                if(isset($_POST["location_code"])){
                                  $loc_name = explode("~",getAnyDetails($_POST["location_code"],"name, city, state","asc_code","asc_master",$link1));
                                  echo '<option value="'.$_POST["location_code"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["location_code"].'</option>';
                                }
                                ?>
                        	</select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2"><br/>
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1"><br/>
                            <a href="excelexport.php?rname=<?=base64_encode("rewardLedgerReport")?>&rheader=<?=base64_encode("RewardLedger")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>" title="Export reward ledger details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export reward ledger details in excel"></i></a>
                        </div>
                      </div>
                  </form>
                <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                    <table id="myTable" class="table table-bordered" width="100%">
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
                        $podata_sql="SELECT * FROM reward_points_ledger WHERE location_code='".$_REQUEST['location_code']."' AND transaction_date>='".$_REQUEST['fdate']."' AND transaction_date<='".$_REQUEST['tdate']."' ORDER BY id ASC";
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
                              <td>&nbsp;</td>
							  <td>&nbsp;</td>
							  <td>&nbsp;</td>
							  <td>&nbsp;</td>
						 	  <td align="right"><strong>Total</strong></td>
                              <td style="text-align:right"><strong><?=$total_dr?></strong></td>
                              <td style="text-align:right"><strong><?=$total_cr?></strong></td>
                          </tr>
                    </tbody>
                </table>  
       		  </div>
           		<!--End form group--> 
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