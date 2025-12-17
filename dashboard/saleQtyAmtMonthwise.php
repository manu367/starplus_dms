<?php
require_once("../config/config.php");
function days_in_month($month, $year)
{
// calculate number of days in a month
return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}

$year = $_REQUEST['year'];
$month1 = str_pad($_REQUEST['month'],2,"0",STR_PAD_LEFT);
$daytotal= days_in_month($month1, $year);
///////////////////////////////////////Make total daysdaytotal
 $statdate=date($year."-".$month1."-"."01");
 $enddate=date($year."-".$month1."-".$daytotal);



// Function to get all the dates in given range 
function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
     
    // Declare an empty array 
     $a=array();
    // Variable that store the date interval 
    // of period 1 day 
    $interval = new DateInterval('P1D'); 
    $realEnd = new DateTime($end); 
    $realEnd->add($interval); 
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
    // Use loop to store date into array 
    foreach($period as $date) {                  
        $array[] = $date->format($format);  
    } 
    // Return the array elements
     $a[]=$array;	
    return $a; 
} 

$alldate = getDatesFromRange($statdate, $enddate); 
  $datemonthdata = $alldate;
  ///print_r($datemonthdata);
##### select location
if($_REQUEST["locationcode"]){
	$loc_code = "m.from_location = '".$_REQUEST["locationcode"]."'";
	
}
else{
	$loc_code = "1";
	
}
##### select partcode
if($_REQUEST["prod_code"]){
	$prodcode = "d.prod_code = '".$_REQUEST["prod_code"]."'";
	
}
else{
	$prodcode = "1";
	
}
///////////////////////////////////////Make total days
if($_POST['Submit']=="GO"){	
/////////////////////////
	if($_REQUEST['type']=='month' && $_REQUEST['year']!='' && $_REQUEST['month']!=''){
		$tot_qty = array();
		$tot_val = array();
			$top_modres = mysqli_query($link1,"SELECT m.from_location, m.entry_date, d.prod_code, d.qty, d.value FROM billing_master m, billing_model_data d WHERE m.challan_no = d.challan_no AND m.status != 'Cancelled' AND m.entry_date LIKE '$year-$month1-%' and  $loc_code and $prodcode");
		while($top_modrow = mysqli_fetch_assoc($top_modres))
				{
				$tot_qty[substr($top_modrow["entry_date"],0,10)] += $top_modrow["qty"];
				$tot_val[substr($top_modrow["entry_date"],0,10)] += $top_modrow["value"];
				}

		$datemonthstr = "'".implode("','",$datemonthdata[0])."'";
		$monthdatastrqty = "";
		$monthdatastrval = "";
		 ///echo "<pre>";
		 ///print_r($tot_qty);
		 ///echo "</pre>";
		$innerdataqty = "";
		$innerdataval = "";
		foreach($datemonthdata[0] as $keyy => $dateval){
				if($tot_qty[$dateval]){ 
					$salqty = $tot_qty[$dateval];}else{ $salqty=0;}
				if(strlen($innerdataqty)>0){
					$innerdataqty .= ",".$salqty;
				}else{
					$innerdataqty .= "".$salqty;
				}
				if($tot_val[$dateval]){ 
				$salval = $tot_val[$dateval];}else{ $salval=0;}
			if(strlen($innerdataval) > 0){
					$innerdataval .= ",".$salval;
				}else{
					$innerdataval .= "".$salval;
				}
			
		}//// for loop close

		////////Qty total string 
		if($monthdatastrqty){
			$monthdatastrqty .= ",{
									data: [".$innerdataqty."]
								 }";
		}else{
			$monthdatastrqty .= "{
									data: [".$innerdataqty."]
								 }";
		}
		////////Value total string 
		if($monthdatastrval){
				$monthdatastrval .= ",{
										
										data: [".$innerdataval."]
									 }";
			}else{
				$monthdatastrval .= "{
									
										data: [".$innerdataval."]
									 }";
			}	


	}////month and yeay empty case

}  ///submit close
 
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
  chart: {
    zoomType: 'xy'
  },
  title: {
    text: 'Sale QTY/AMT Status By Month (<?php echo date('F', mktime(0, 0, 0, $_REQUEST['month'], 10))." ".$_REQUEST['year'];?>)'
  },
  subtitle: {
    text: ''
  },
  xAxis: [{
    categories: [<?=$datemonthstr?>],
    crosshair: true
  }],
  yAxis: [{ // Primary yAxis
    labels: {
      format: '{value}',
      style: {
        color: Highcharts.getOptions().colors[1]
      }
    },
    title: {
      text: 'QTY (Pcs)',
      style: {
        color: Highcharts.getOptions().colors[1]
      }
    }
  }, { // Secondary yAxis
    title: {
      text: 'AMT (Rs)',
      style: {
        color: Highcharts.getOptions().colors[0]
      }
    },
    labels: {
      format: '{value} (Rs)',
      style: {
        color: Highcharts.getOptions().colors[0]
      }
    },
    opposite: true
  }],
  tooltip: {
    shared: true
  },
  legend: {
    layout: 'vertical',
    align: 'left',
    x: 120,
    verticalAlign: 'top',
    y: 100,
    floating: true,
    backgroundColor:
      Highcharts.defaultOptions.legend.backgroundColor || // theme
      'rgba(255,255,255,0.25)'
  },
  series: [{
    name: 'AMT (Rs)',
    type: 'column',
    yAxis: 1,
    data: [<?=$innerdataval;?>],
    tooltip: {
      valueSuffix: ' Rs'
    }

  }, {
    name: 'QTY (Pcs)',
    type: 'spline',
    data: [<?=$innerdataqty;?>],
    tooltip: {
      valueSuffix: ''
    }
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
      <h2 align="center"><i class="fa fa-line-chart"></i> Sale QTY/AMT Dashboard</h2><br/><br/>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Period Type</label>
              <div class="col-md-6">
                <select name="type" id="type" class="form-control" required onChange="document.form1.submit();">
                    <option value="" selected="selected">--Select--</option>
                    <!-- <option value="year" <?php if ($_REQUEST[type] == 'year') echo ' selected="selected"'; ?>>Year</option> -->
                    <option value="month" <?php if ($_REQUEST[type] == 'month') echo ' selected="selected"'; ?>>Month</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
            <label class="col-md-2 control-label"><?php if($_REQUEST[type] == 'year') { ?>Year<?php } else { ?>Month<?php } ?><span class="red_small">*</span></label>
              <div class="col-md-6">
                <div class="col-md-6">
               <select name="year" class="form-control" required style="width:100px;" id="year">
                   <option value="" selected="selected">--Select--</option>
                        <?php for ($i = 0, $j = 16; $i < 7; $i++, $j++) { ?>
                        <option value="<?php echo '20' . $j; ?>" <?php if ($_POST[year] == '20' . $j) echo ' selected="selected"'; ?>><?php echo '20' . $j; ?></option>
                    <?php } ?>
                </select>           
                </div>     
                  <div class="col-md-6">                                     
                <?php if ($_REQUEST[type] != 'year') { ?>
                    <select name="month" id="month"  required class="form-control" style="width:120px;">
                        <option value="" selected="selected">--Select--</option><?php
                        for ($i = 0, $m2 = 1; $i < 12; ++$i, $m2++) {
                            //$months[$m] = $m = date("F", strtotime("January +$i months"));
							
							$dateObj   = DateTime::createFromFormat('!m', $m2);
							$months[$m]= $m = $dateObj->format('F');
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
                 <select name="locationcode" id="locationcode" class="form-control selectpicker"  data-live-search="true" onChange="document.form1.submit();">
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
               <select name="prod_code" id="prod_code" class="form-control selectpicker"  data-live-search="true" onChange="document.form1.submit();">
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