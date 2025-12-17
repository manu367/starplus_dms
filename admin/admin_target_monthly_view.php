<?php
require_once("../config/config.php");
$user = base64_decode($_REQUEST['user']);

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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
 	$(document).ready(function(){
		$("#frm1").validate();
		$("#frm2").validate();
	});
 </script>
 <style> .bgyallow {background-color: #ffff66;} </style>
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-bullseye"></i> Month Wise Target </h2>
      <h4 align="center"> <?=getAnyDetails($user,'name','username','admin_users',$link1)."  (".$user.")";?> </h4>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br>   
	  
	  <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
		  <div class="panel-group">
			<div class="panel panel-info table-responsive">
				<div class="panel-heading heading1"><i class="fa fa-bullseye fa-lg"></i>&nbsp;&nbsp;Target List</div>
				 <div class="panel-body">
				 
					<div class="form-group">
					  <div class="col-md-12" > 
						  <label class="col-md-4 control-label"> Month <span class="red_small">*</span></label> 
						  <div class="col-md-6">
							<select id="target_month" name="target_month" class="form-control required" required>
								<option value="" <?php if($_REQUEST['target_month'] == "") { echo "selected" ;} ?> > -- Please Select -- </option>
								<option value="01" <?php if($_REQUEST['target_month']=='01')echo "selected";?>>JAN</option>
								<option value="02" <?php if($_REQUEST['target_month']=='02')echo "selected";?>>FEB</option>
								<option value="03" <?php if($_REQUEST['target_month']=='03')echo "selected";?>>MAR</option>
								<option value="04" <?php if($_REQUEST['target_month']=='04')echo "selected";?>>APR</option>
								<option value="05" <?php if($_REQUEST['target_month']=='05')echo "selected";?>>MAY</option>
								<option value="06" <?php if($_REQUEST['target_month']=='06')echo "selected";?>>JUN</option>
								<option value="07" <?php if($_REQUEST['target_month']=='07')echo "selected";?>>JUL</option>
								<option value="08" <?php if($_REQUEST['target_month']=='08')echo "selected";?>>AUG</option>
								<option value="09" <?php if($_REQUEST['target_month']=='09')echo "selected";?>>SEP</option>
								<option value="10" <?php if($_REQUEST['target_month']=='10')echo "selected";?>>OCT</option>
								<option value="11" <?php if($_REQUEST['target_month']=='11')echo "selected";?>>NOV</option>
								<option value="12" <?php if($_REQUEST['target_month']=='12')echo "selected";?>>DEC</option>	 
							</select> 
						  </div>    
					  </div>  
					</div>
					
					<div class="form-group">
					  <div class="col-md-12" > 
						  <label class="col-md-4 control-label"> Year <span class="red_small">*</span></label> 
						  <div class="col-md-6">
							<select id="target_year" name="target_year" class="form-control required" required>
								<option value="" <?php if($_REQUEST['target_year']==""){ echo "selected";}?> > -- Please Select -- </option>
								<?php
								$currrent_year=date('Y');
								$last_year=$currrent_year-1;
								$sec_last_year=$currrent_year-2;
								?>
								<option value="<?=$sec_last_year?>" <?php if($_REQUEST['target_year']==$sec_last_year)echo "selected";?>><?=$sec_last_year?></option>
								<option value="<?=$last_year?>" <?php if($_REQUEST['target_year']==$last_year)echo "selected";?>><?=$last_year?></option>
								<option value="<?=$currrent_year?>" <?php if($_REQUEST['target_year']==$currrent_year)echo "selected";?>><?=$currrent_year?></option>
							</select> 
						  </div>    
					  </div>  
					</div>
										
					<br><br>
					<div class="form-group">
						<div class="col-md-12" style="text-align:center;" > 
						   <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Show"> Show </button>  
						   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='work_related_status.php?id=<?=base64_encode($user);?><?=$pagenav?>'">
						</div>  
					</div>                        
				</div><!--close panel body-->
			</div><!--close panel-->
		  </div>
	  </form>
	    
	  <?php if($_REQUEST['submit'] == "Show"){ 
	  	$mon = "";
	  	if($_REQUEST['target_month']=="01"){ echo "JAN"; }
		else if($_REQUEST['target_month']=="02"){ $mon = "FEB"; }
		else if($_REQUEST['target_month']=="03"){ $mon = "MAR"; }
		else if($_REQUEST['target_month']=="04"){ $mon = "APR"; }
		else if($_REQUEST['target_month']=="05"){ $mon = "MAY"; }
		else if($_REQUEST['target_month']=="06"){ $mon = "JUN"; }
		else if($_REQUEST['target_month']=="07"){ $mon = "JUL"; }
		else if($_REQUEST['target_month']=="08"){ $mon = "AUG"; }
		else if($_REQUEST['target_month']=="09"){ $mon = "SEP"; }
		else if($_REQUEST['target_month']=="10"){ $mon = "OCT"; }
		else if($_REQUEST['target_month']=="11"){ $mon = "NOV"; }
		else if($_REQUEST['target_month']=="12"){ $mon = "DEC"; }
		else{ $mon = ""; }
	  ?>
	  <br>	
      <form name="frm2" id="frm2" class="form-horizontal" action="" method="post" >
                               
          <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-calendar fa-lg"></i>&nbsp;&nbsp; <?php echo $mon." - ".$_REQUEST['target_year']; ?></div>
                 <div class="panel-body">
				 <?php
				 	///// calculate target values ///////////
				 	$prd_tar_val = mysqli_fetch_array(mysqli_query($link1, "SELECT sum(target_val) FROM sf_target_master WHERE month = '".$_REQUEST['target_month']."' and year = '".$_REQUEST['target_year']."' and emp_id = '".$user."' and target_type = 'Value'  "));
					///// calculate product target qty /////////									
					$prd_tar_sql = mysqli_query($link1, "SELECT distinct(prod_code) as PC FROM sf_target_data WHERE  month = '".$_REQUEST['target_month']."' and year = '".$_REQUEST['target_year']."' and emp_id = '".$user."' order by prod_code asc "); 
				 ?>
				 <?php 
				 	if(($prd_tar_val[0]!="")&&($prd_tar_val[0]!=0.00)&&($prd_tar_val[0]!=0)){
				 	
						///// find no of days in month ////////
						$ldate = cal_days_in_month(CAL_GREGORIAN,$_REQUEST['target_month'],$_REQUEST['target_year']);
						$yr = $_REQUEST['target_year'];
						$mth = $_REQUEST['target_month'];
						$frmdate = $yr."-".$mth."-01";
						$todate = $yr."-".$mth."-".$ldate;
									
						$ach_sq = mysqli_query($link1, "SELECT sum(po_value) as PV FROM purchase_order_master WHERE  entry_date BETWEEN  '$frmdate' AND  '$todate' and status != 'Cancel' and sales_executive = '".$user."' ");	
						$ach_amt = mysqli_fetch_assoc($ach_sq);
				  ?>
				  <table class="table table-bordered" width="100%">
                    <tbody>
					  <tr>
                        <td width="50%"><label class="control-label"> Target Amount : </label> &nbsp;&nbsp;&nbsp; <span style="color:#800000;font-weight: 800;"> <?=$prd_tar_val[0];?> </span> </td>
						<td width="50%" class="<?php if($prd_tar_val[0]>$ach_amt['PV']){ echo "bgyallow"; } ?>"><label class="control-label"> Achieved Amount : </label> &nbsp;&nbsp;&nbsp; 
							<span  style="color:#014709;font-weight: 800;" >
								<?php 
									if($ach_amt['PV']!=""){
										echo $ach_amt['PV'];
									}else{
										echo "0.00";
									}								
								?>
							</span> 
						</td>
                      </tr>
                    </tbody>
                  </table>
				  <?php 
				  		$ach_sq1 = mysqli_query($link1, "SELECT po_no FROM purchase_order_master WHERE  entry_date BETWEEN  '$frmdate' AND  '$todate' and status != 'Cancel' and sales_executive = '".$user."' ");									
						$achstr = "";
						while($pp = mysqli_fetch_assoc($ach_sq1)){
							$achstr .= $pp['po_no']."','";
						}
						////// remove three charector from end of the string /////////
						$newachstr = substr($achstr, 0, -3);
				  	}				  
				   ?>
				  <table class="table table-bordered" width="100%">
                    <tbody>
					  <tr>
                        <td width="60%"><label class="control-label"> Product </label></td>
                        <td style="text-align:center;" width="20%"><label class="control-label"> Target Qty </label></td>
						<td style="text-align:center;" width="20%"><label class="control-label"> Achieved Qty </label></td>
                      </tr>
					  <?php while($prd_tar_qt = mysqli_fetch_assoc($prd_tar_sql)){ if($prd_tar_qt['PC']!=""){ ?>
                      <tr>
                        <td width="60%">
							<?php 
								$prd_info = explode("~",getAnyDetails($prd_tar_qt['PC'],'productname,productcolor,productdesc','productcode','product_master',$link1)); 
								echo $prd_info[0]." | ".$prd_info[1]." | ".$prd_tar_qt['PC'];
							?>
						</td>
                        <td style="text-align:center;" width="20%">
							<?php 
								$qt_count = mysqli_fetch_assoc(mysqli_query($link1, "SELECT sum(qty) as QT FROM sf_target_data WHERE prod_code ='".$prd_tar_qt['PC']."' and month = '".$_REQUEST['target_month']."' and year = '".$_REQUEST['target_year']."' and emp_id = '".$user."' "));
								echo "<span style='color:#800000;font-weight: 800;'>".$qt_count['QT']."</span>"; 
								///// find the acheavement qty ////////
								$qt_count1 = mysqli_fetch_assoc(mysqli_query($link1, "SELECT sum(qty) as QTY FROM purchase_order_data WHERE prod_code ='".$prd_tar_qt['PC']."' and po_no in ('$newachstr')"));
							?>
						</td>
						<td style="text-align:center;" width="20%" class="<?php if($qt_count['QT']>$qt_count1['QTY']){ echo "bgyallow"; } ?>">
							<span style="color:#014709;font-weight: 800;">
								<?php 
									echo "<span style='color:#014709;font-weight: 800;'>".(int)$qt_count1['QTY']."</span>"; 
								?>
							</span>
						</td>
                      </tr>
					  <?php }} ?>
                    </tbody>
                  </table>
                </div><!--close panel body-->
            </div><!--close panel-->
              
          </div>
                    
          <br><br>         
      </form>  
	  <?php } ?> 
	    
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>