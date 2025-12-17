<?php
require_once("../config/config.php");
$year = $_REQUEST['year'];
$month1 = str_pad($_REQUEST['month'],2,"0",STR_PAD_LEFT);
if($_POST['Submit']=="GO"){
/////////////////////////
if($_REQUEST['type']=='month' && $_REQUEST['year']!='' && $_REQUEST['month']!=''){
$arr_qsstr = "";
$arr_vsstr = "";
$sql = "SELECT sale_date,GROUP_CONCAT(challan_no) as invs FROM billing_master WHERE sale_date LIKE '$year-$month1-%' and document_type='INVOICE' and status not in ('Invoice Cancelled') and from_location='".$_POST['locationcode']."' group by sale_date";
$st = mysqli_query($link1,$sql);
while ($str2 = mysqli_fetch_assoc($st)){
	$qty = 0;
    $value = 0;
	$pos=strpos($str2['invs'], ",");
	if ($pos === false) { $challanstr="'".$str2['invs']."'";}else{$challanstr="'".str_replace(",","','",$str2['invs'])."'";}
        $sql1 ="SELECT sum(qty) as q ,sum(value) as v FROM billing_model_data WHERE challan_no in ($challanstr)";
    if ($_REQUEST['prod_code'] != '') {
        $sql1.=" and prod_code='$_REQUEST[prod_code]'";
    }
	$st1 = mysqli_query($link1,$sql1);
	$rowss = mysqli_fetch_assoc($st1);
	$qty+=$rowss['q'];
	$value+=$rowss['v'];
    if ($arr_qsstr == "" or $arr_vsstr == ""){
        $arr_qsstr.=$qty;
        $arr_vsstr.=$value;
    } else {
        $arr_qsstr.=','.$qty;
        $arr_vsstr.=','.$value;
    }
    if($str2['sale_date']!=''){
       $splitTimeStamp = explode("-",$str2['sale_date']);   
       if($days==""){
          $days.=$splitTimeStamp[2];
       }else{
          $days.=",".$splitTimeStamp[2];   
	   }   
	}
}//// while loop close
}
if($_REQUEST['type']=='year' && $_REQUEST['year']!=''){
 $sql = "SELECT YEAR(sale_date) AS y ,MONTH(sale_date) as m ,sale_date,GROUP_CONCAT(challan_no) as invs FROM billing_master WHERE YEAR(sale_date) LIKE '$year%' and document_type='INVOICE' and status not in ('Invoice Cancelled') and from_location='".$_POST['locationcode']."' GROUP BY MONTH(sale_date)";   
 $st =  mysqli_query($link1,$sql);
 $arr_qsstr = "";
 $arr_vsstr = "";
 while ($str2 = mysqli_fetch_assoc($st)){
		$qty = 0;
		$value = 0;
		$pos=strpos($str2['invs'], ",");
		if ($pos === false) { $challanstr="'".$str2['invs']."'";}else{$challanstr="'".str_replace(",","','",$str2['invs'])."'";}
        $sql1 ="SELECT sum(qty) as q ,sum(value) as v FROM billing_model_data WHERE challan_no in ($challanstr)";
        if ($_REQUEST['prod_code'] != '') {
            $sql1.=" and prod_code='$_REQUEST[prod_code]'";
		}
		$st1 = mysqli_query($link1,$sql1);
		$rowss = mysqli_fetch_assoc($st1);
		$qty+=$rowss['q'];
		$value+=$rowss['v'];
		if ($arr_qsstr == "" or $arr_vsstr == ""){
			$arr_qsstr.=$qty;
			$arr_vsstr.=$value;
        } else {
			$arr_qsstr.=','.$qty;
			$arr_vsstr.=','.$value;
		}
	    if($str2['sale_date']!=''){
		   $m2=$str2['m']-1;
	       $m1 = date("F", strtotime("January +$m2 months"));
	       if($month==""){
	          $month.="'".$m1."'";
	       }else{
	          $month.=","."'".$m1."'";   
		   } 
		}
 }///close while loop   
}
}
/*echo $arr_qsstr;
echo "<br/>fdff";
echo $arr_vsstr;*/
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<?php if($_POST['Submit']=="GO"){ ?>
<script type="text/javascript">
$(function() {
	 <?php if ($_REQUEST['type'] == 'month') {?>
	Highcharts.chart('container', {
		title: {
			text: 'Sale Status By Month (<?php echo date('F', mktime(0, 0, 0, $_REQUEST['month'], 10))." ".$_REQUEST['year'];?>)',
			x: -20 //center
		},
		subtitle: {
			text: '',
			x: -20
		},
		xAxis: {
			categories: [<?=$days?>]
		},
		yAxis: {
			title: {
				text: 'QTY'
			},
			plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
		},
		tooltip: {
			valueSuffix: ''
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'middle',
			borderWidth: 0
		},
		series: [{
				name: 'QTY',
				data: [<?=$arr_qsstr;?>]
			}, {
				name: 'Value',
				data: [<?=$arr_vsstr;?>]
			}]
	});
   <?php } if ($_REQUEST['type'] == 'year') { ?>
	Highcharts.chart('container1', {
		title: {
			text: 'Sale Status By Year (<?php echo $_REQUEST['year'];?>)',
			x: -20 //center
		},
		subtitle: {
			text: '',
			x: -20
		},
		xAxis: {
			categories: [<?=$month?>]
		},
		yAxis: {
			title: {
				text: 'QTY'
			},
			plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
		},
		tooltip: {
			valueSuffix: ''
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'middle',
			borderWidth: 0
		},
		series: [{
				name: 'QTY',
				data: [<?=$arr_qsstr;?>]
			}, {
				name: 'Value',
				data: [<?=$arr_vsstr;?>]
			}]
	}); 
<?php } ?>
});
</script>
<?php }?>
<script src="../high/highcharts.js" type="text/javascript"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-line-chart"></i> Sale Dashboard</h2><br/><br/>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Period Type</label>
              <div class="col-md-6">
                <select name="type" id="type" class="form-control" onChange="document.form1.submit();">
                    <option value="" selected="selected">--Select--</option>
                    <option value="year" <?php if ($_REQUEST[type] == 'year') echo ' selected="selected"'; ?>>Year</option>
                    <option value="month" <?php if ($_REQUEST[type] == 'month') echo ' selected="selected"'; ?>>Month</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
            <label class="col-md-2 control-label"><?php if($_REQUEST[type] == 'year') { ?>Year<?php } else { ?>Month<?php } ?><span class="red_small">*</span></label>
              <div class="col-md-6">
                <div class="col-md-6">
               <select name="year" class="form-control" style="width:100px;" id="year">
                   <option value="" selected="selected">--Select--</option>
                        <?php for ($i = 0, $j = 20; $i < 6; $i++, $j++) { ?>
                        <option value="<?php echo '20' . $j; ?>" <?php if ($_POST[year] == '20' . $j) echo ' selected="selected"'; ?>><?php echo '20' . $j; ?></option>
                    <?php } ?>
                </select>           
                </div>     
                  <div class="col-md-6">                                     
                <?php if ($_REQUEST[type] != 'year') { ?>
                    <select name="month" id="month" class="form-control" style="width:120px;">
                        <option value="" selected="selected">--Select--</option><?php
                        for ($i = 0, $m2 = 1; $i < 12; ++$i, $m2++) {
                            $months[$m] = $m = date("F", strtotime("January +$i months"));
                            ?>
                            <option value="<?php echo $m2; ?>" <?php if ($_POST[month] == $m2) echo ' selected="selected"'; ?>><?php echo $months[$m]; ?></option>
                        <?php } ?>
                    </select>
                <?php } ?> 
                </div>
              </div>
            </div>
        </div>
       <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Location</label>
              <div class="col-md-6">
                 <select name="locationcode" id="locationcode" class="form-control selectpicker required" required data-live-search="true" onChange="document.form1.submit();">
                   <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['locationcode'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
					}
                    ?>
              </select>
              </div>
            </div>
          <div class="col-md-6"><label class="col-md-2 control-label"> Product</label>
            <div class="col-md-6">
               <select name="prod_code" id="prod_code" class="form-control selectpicker" required data-live-search="true" onChange="document.form1.submit();">
                    <option value="">--None--</option>
                    <?php 
					$model_query="select productcode,productname from product_master where status='active'";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"<?php if($br['productcode']==$_REQUEST['prod_code'])echo "selected";?>><?php echo $br['productname'];?></option>
                    <?php }?>
              </select>
              </div>
            <div class="col-md-2">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
                  <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
              </div>
          </div>
	   </div><!--close form group-->
	  </form>
         <?php if($_POST['Submit']=="GO"){ ?>
        <?php if ($_REQUEST['type'] == 'month') {?>
        <div id="container" class="form-group table-responsive" style="min-width: 800px; height: 400px; margin: 0 auto"></div>
         <?php } if ($_REQUEST['type'] == 'year') { ?>
        <div id="container1" class="form-group table-responsive" style="min-width: 800px; height: 400px; margin: 0 auto"></div>
        <?php }?> 
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