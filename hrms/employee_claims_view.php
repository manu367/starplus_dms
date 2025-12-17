<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);
////// all total var /////////
$tot_travel = 0.00;
$tot_food = 0.00;
$tot_lodge = 0.00;
/////// all info //////
$claim_info = mysqli_fetch_assoc(mysqli_query($link1, " SELECT * FROM hrms_claim_request WHERE sno = '".$id."' " ));

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap-datepicker.js"></script>
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-legal"></i> View Claim </h2>
      <h5 align="center"> ( Claim No. -  <?=$claim_info['claim_id']?> ) </h5><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
                     
          <div class="form-group">
              <div class="col-md-12">
                  <label class="col-md-2 control-label">Employee Code <span style="color:#F00">*</span></label>
                  <div class="col-md-4">
                    <input type="text" name="emp_code" id="emp_code" class="form-control required" value="<?=$_SESSION['userid']?>" required readonly/>
                  </div>
                  <label class="col-md-2 control-label">Claim Type <span style="color:#F00">*</span></label>
                  <div class="col-md-4">
                  	<input type="text" name="claim_type" id="claim_type" class="form-control required" value="<?=$claim_info['claim_type']?>" required readonly/>
                  </div>
              </div>
          </div>   
               
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-2 control-label"> Remark </label> 
                  <div class="col-md-10">
                    <textarea name="remark" id="remark" class="form-control addressfield"  readonly ><?=$claim_info['remark']?></textarea>
                  </div>    
              </div>  
          </div>
          
          <br>
          <div class="form-group">
          	  <div class="col-md-12" > 
              <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-bus fa-lg"></i>&nbsp;&nbsp;Travelling Details</div>
                 <div class="panel-body">
              
                  <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
                    <thead>
                      <tr class="<?=$tableheadcolor?>" >
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >Date</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >From</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >To</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >K.M.</th>
                        <th style="text-align:center;font-size:13px;width: 130px;" data-hide="phone,tablet" >Mode</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone,tablet" >Purpose</th>
                        <th style="text-align:center;font-size:13px;" data-hide="phone" >Amt</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
					  $travelling_details = mysqli_query($link1, " SELECT * FROM hrms_travelling_details WHERE claim_id = '".$claim_info['claim_id']."' ");
					  if($travelling_details != ""){
					  	while($row1 = mysqli_fetch_assoc($travelling_details)){
					  ?>
                      <tr>
                        <td style="text-align:center;"><?=dt_format($row1['tv_date']);?></td>
                        <td><?=$row1['from_place'];?></td>
                        <td><?=$row1['to_place'];?></td>
                        <td style="text-align:center;"><?=$row1['distance'];?></td>
                        <td><?=$row1['tv_mode'];?></td>
                        <td><?=$row1['purpose'];?></td>
                        <td style="text-align:right;"><?=currencyFormat($row1['amt']);?></td>
                      </tr>
                      <?php
					  	$tot_travel += $row1['amt'];
					  	}
					  ?>
                      <tr>
					  	<td colspan="3">
							 <span style="font-weight: 800; font-size:13px; padding-right: 10px;">Download Document : </span><?php if($claim_info['travelling_doc_path']) {?><a href='<?=$claim_info['travelling_doc_path']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?>
						</td>
                      	<td colspan="3" style="text-align:right; font-weight: 800; font-size:13px;">
                        	Total Travelling Amount : 
                        </td>    
                        <td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">    
                           <?=currencyFormat($tot_travel);?>
                        </td>
                      </tr>
                      <?php	
					  }
					  ?>
                    </tbody>
                  </table>
              </div>
          </div>
          </div><!--close panel body-->
          </div><!--close panel-->
          </div>
          
          <!--------------- Food details ------------------->
          <div class="form-group">
          	  <div class="col-md-12" > 
                  <div class="panel-group">
                <div class="panel panel-info table-responsive">
                    <div class="panel-heading heading1"><i class="fa fa-cutlery fa-lg"></i>&nbsp;&nbsp;Fooding/Others Details</div>
                     <div class="panel-body">
                  
                      <table width="100%" id="foodingitemsTable" class="table table-bordered table-hover">
                        <thead>
                          <tr class="<?=$tableheadcolor?>" >
                            <th style="text-align:center;font-size:13px;" data-hide="phone" > Date </th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" > Remark/details </th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" > Amt </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
						  $fooding_details = mysqli_query($link1, " SELECT * FROM hrms_fooding_details WHERE claim_id = '".$claim_info['claim_id']."' ");
						  if($fooding_details != ""){
							while($row2 = mysqli_fetch_assoc($fooding_details)){
						  ?>
                          <tr>
                            <td style="text-align:center;"><?=dt_format($row2['fd_date']);?></td>
                            <td><?=$row2['location']?></td>
                            <td style="text-align:right;"><?=currencyFormat($row2['amt']);?></td>
                          </tr>
                          <?php
						  	$tot_food += $row2['amt'];
							}
						  ?>
						  <tr>
						  	<td colspan="1">
								 <span style="font-weight: 800; font-size:13px; padding-right: 10px;" >Download Document : </span><?php if($claim_info['fooding_doc_path']) {?><a href='<?=$claim_info['fooding_doc_path']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?>
							</td>
							<td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">
								Total Fooding Amount : 
							</td>    
							<td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">    
							   <?=currencyFormat($tot_food);?>
							</td>
						  </tr>
						  <?php	
						  }
						  ?>
                        </tbody>
                      </table>
                  </div>
              </div>
              </div><!--close panel body-->
              </div><!--close panel-->
          </div>
          <!-------------- Food details end ---------------->
          
          <!--------------- Lodging details ------------------->
          <div class="form-group">
          	  <div class="col-md-12" > 
                  <div class="panel-group">
                <div class="panel panel-info table-responsive">
                    <div class="panel-heading heading1"><i class="fa fa-hotel fa-lg"></i>&nbsp;&nbsp;Lodging Details</div>
                     <div class="panel-body">
                  
                      <table width="100%" id="lodgingitemsTable" class="table table-bordered table-hover">
                        <thead>
                          <tr class="<?=$tableheadcolor?>" >
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Date</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Location</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Hotel Name</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Days</th>
                            <th style="text-align:center;font-size:13px;" data-hide="phone" >Amt</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
						  $lodging_details = mysqli_query($link1, " SELECT * FROM hrms_lodging_details WHERE claim_id = '".$claim_info['claim_id']."' ");
						  if($lodging_details != ""){
							while($row3 = mysqli_fetch_assoc($lodging_details)){
						  ?>
                          <tr>
                            <td style="text-align:center;"><?=dt_format($row3['log_date']);?></td>
                            <td><?=$row3['location']?></td>
                            <td><?=$row3['hotel_name']?></td>
                            <td style="text-align:center;"><?=$row3['days']?></td>
                            <td style="text-align:right;"><?=currencyFormat($row3['amt']);?></td>
                          </tr>
                          <?php
						  	$tot_lodge += $row3['amt'];
							}
						  ?>
						  <tr>
						  	<td colspan="2">
								 <span style="font-weight: 800; font-size:13px; padding-right: 10px;" >Download Document : </span><?php if($claim_info['lodging_doc_path']) {?><a href='<?=$claim_info['lodging_doc_path']?>' target='_blank' title='download'><i class='fa fa-download fa-lg' title='Download Document'></i></a><?php }?>
							</td>
							<td colspan="2" style="text-align:right; font-weight: 800; font-size:13px;">
								Total Lodging Amount : 
							</td>    
							<td colspan="1" style="text-align:right; font-weight: 800; font-size:13px;">    
							   <?=currencyFormat($tot_lodge);?>
							</td>
						  </tr>
						  <?php	
						  }
						  ?>
                        </tbody>
                      </table>
                  </div>
              </div>
              </div><!--close panel body-->
              </div><!--close panel-->
          </div>
          <!-------------- Lodging details end ---------------->
          
          <br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='employee_claims_list.php?<?=$pagenav?>'">
              </div>  
          </div>
          <br><br>
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