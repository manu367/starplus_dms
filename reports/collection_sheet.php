<?php
require_once("../config/config.php");
//////////   fetch acess location from function//////////////////////////////////////////////
$acessloc = getAccessLocation($_SESSION['userid'],$link1);
//// location filter
if($_POST["locationName"]){
	$loc_str = "to_location = '".$_POST["locationName"]."'";
}else{
	$loc_str = "1";
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="../js/jquery-1.10.1.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-select.min.css">
    <script src="../js/bootstrap-select.min.js"></script>
    
    <script type="text/javascript">
    $(document).ready(function(){
		$('#fdate').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
    });
	$(document).ready(function(){
		$('#myTable').dataTable();
    });
	$(document).ready(function() {
		$("#frm2").validate();
	});
    </script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="../css/datepicker.css">
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
                <h2 align="center"><i class="fa fa-book"></i> Collection Sheet</h2>
                <?php if($_REQUEST['msg']){?><br>
                  <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
                  <?php }?>
                <form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="form-group">
                    	<div class="col-md-6"><label class="col-md-5 control-label">Date :</label>	  
                            <div class="col-md-6 input-append date" align="left">
                                <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" autocomplete="off" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                            </div>
                        </div>
						<div class="col-md-6"><label class="col-md-5 control-label">Location Name :</label>	  
                            <div class="col-md-6" align="left">
                                <select name="locationName" id="locationName"  class="form-control selectpicker required" data-live-search="true">
                                    <option value="" selected="selected">Please Select </option>
                                    <?php 
                                    $sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
                                    $res_chl=mysqli_query($link1,$sql_chl);
                                    while($result_chl=mysqli_fetch_array($res_chl)){
                                          $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                                         
                                          ?>
                                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl[location_id]?>" <?php if($result_chl['location_id']==$_REQUEST['locationName'])echo "selected";?> >
                                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                                    </option>
                                    <?php
                                          }
                                    
                                    ?>
                                 </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                    	<div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-6" align="left">
                            	<input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
                             	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               					<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>    
                            </div>
                        </div>
						<div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-6" align="left">
                                <?php
               
                                ////// check this user have right to export the excel report
                                if ($_REQUEST['Submit'] != '') {
                                    ?>
                                    <?php /*?><a href="excelexport.php?rname=<?= base64_encode("mappingReport") ?>&rheader=<?= base64_encode("Mapping Report") ?>&user_id=<?=base64_encode($_REQUEST['location'])?>&partyname=<?=base64_encode($val[1])?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export  details in excel"></i></a><?php */?>
                                    <?php
                                }
                                ?>
                                <button title="View Previous Collection" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='viewPreviousCollection.php?op=view<?=$pagenav?>'"><span>View Previous Collection</span></button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php if($_REQUEST['Submit']!="" && $_REQUEST["fdate"]!=""){ ?>
                <form class="table-responsive" id="frm2" name="frm2" role="form" action="collection_process.php" method="post">
                	<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>">     
								<th><a href="#"></a>Doc. No.</th>         
                              	<th data-class="expand"><a href="#"></a>Against Ref. No.</th>
							  	<th data-class="expand"><a href="#"></a>Location Name</th>
                                <th><a href="#"></a>Received Amt</th>
                                <th><a href="#"></a>Payment Mode</th>
                                <th><a href="#"></a>Verify Account</th>
                                <th><a href="#"></a>Verify Amt</th>
                                <th><a href="#"></a>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
							//////// make account array 
							$arr_acc = array();
							$res_acc = mysqli_query($link1,"SELECT a.account_name, a.account_no FROM account_master a, mapped_account b WHERE a.status='A' AND a.id = b.account_id AND b.status='Y' AND b.location_code IN (".$acessloc.")");
							while($row_acc = mysqli_fetch_assoc($res_acc)){
								$arr_acc[] = $row_acc["account_name"]." - ".$row_acc["account_no"];
							}
								$i=1;
								$arr_refkey = array();
								////////////   fetching data //////////////////////////////////////////////////////////////////				
                            	$res_paym = mysqli_query($link1, "SELECT * FROM payment_receive WHERE status = 'Approve' AND payment_date = '".$_REQUEST['fdate']."' AND to_location IN (".$acessloc.") AND ".$loc_str." AND collection_flag = '' ORDER BY payment_date");
                            	while ($row_paym = mysqli_fetch_assoc($res_paym)){
									$arr_refkey[] = $row_paym["id"];
                                ?>
                                <tr class="even pointer">  
									<td><?=$row_paym["doc_no"]?></td>
                                    <td><?=$row_paym["against_ref_no"]?></td>   
									<td><?=getLocationDetails($row_paym['from_location'],"name" ,$link1) ."(" . $row_paym['from_location'].")" ; ?></td>        
                                    <td><?=$row_paym["rec_amount"]?></td>
                                    <td><?=$row_paym["payment_mode"]?></td>
                                    <td><select name="verify_acc<?=$row_paym["id"]?>" id="verify_acc<?=$i?>" class="form-control">
                                    		<option value="">--Please Select--</option>
                                            <?php for($j=0; $j<count($arr_acc); $j++){?>
                                            <option value="<?=$arr_acc[$j]?>"><?=$arr_acc[$j]?></option>
                                            <?php }?>
                                        </select></td>
                                    <td><input name="verify_amt<?=$row_paym["id"]?>" id="verify_amt<?=$i?>" type="text" class="form-control number"/></td>								
									<td><input name="remark<?=$row_paym["id"]?>" id="remark<?=$i?>" type="text" class="form-control addressfield"/></td>
                                </tr>
                            	<?php 
								$i++; 
								}
							if(is_array($arr_refkey)){$passstr = implode("','",$arr_refkey);}else{ $passstr="";}
							?>
                        </tbody>
                    </table>
                    <?php if($i>1){?>
                    <div class="form-group" align="center">
                        <div class="col-md-12">
                        	<input name="refToken" id="refToken" type="hidden" value="<?=base64_encode($passstr)?>"/>
                            <input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Save" title="Save">
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               				<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/> 
                        </div>
          			</div>
                    <?php }?>
                </form>
                <?php }?>
            </div>
        </div>
    </div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>