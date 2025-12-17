<?php
require_once("../config/config.php");
$year = $_REQUEST['year'];
$month1 = str_pad($_REQUEST['month'],2,"0",STR_PAD_LEFT);
function compareByTimeStamp($time1, $time2) 
{ 
    if (strtotime($time1) < strtotime($time2)) 
        return -1; 
    else if (strtotime($time1) > strtotime($time2))  
        return 1; 
    else
        return 0; 
} 
function getMonth($month_no){
	return date("M",mktime(0,0,0,$month_no));
}
////// selected filter value
if($_POST['locationcode']){
	$loc_code = "from_location = '".$_POST['locationcode']."'";
	$loc_code1 = "a.from_location = '".$_POST['locationcode']."'";
}else{
	$loc_code = "1";
	$loc_code1 = "1";
}
//////
if($_POST['prod_code']){
	$prod_code = "prod_code = '".$_POST['prod_code']."'";
	$prod_code1 = "b.prod_code = '".$_POST['prod_code']."'";
}else{
	$prod_code = "1";
	$prod_code1 = "1";
}
///////////////////////// after hitting filter
if($_POST['Submit']=="GO"){
	/// if we select monthwise sale trend
	if($_REQUEST['type']=='month' && $_REQUEST['year']!='' && $_REQUEST['month']!=''){
		#### LOCATION GRAPH
		///// initialize array
		$arr_location = array();
		$arr_duration = array();
		$arr_data = array();
		///fetch data
		$sql = "SELECT from_location, sale_date, GROUP_CONCAT(challan_no) as invs FROM billing_master WHERE sale_date LIKE '".$year."-".$month1."-%' AND document_type='INVOICE' AND status NOT IN ('Invoice Cancelled') AND ".$loc_code." GROUP BY from_location,sale_date ORDER BY from_location,sale_date";
		$st = mysqli_query($link1,$sql);
		while ($str2 = mysqli_fetch_assoc($st)){
			$pos = strpos($str2['invs'], ",");
			////////////////
			if ($pos === false) { 
				$challanstr="'".$str2['invs']."'";
			}else{
				$challanstr="'".str_replace(",","','",$str2['invs'])."'";
			}
        	$sql1 = "SELECT SUM(qty) as q , SUM(value) as v FROM billing_model_data WHERE challan_no IN (".$challanstr.")";
    		if ($_REQUEST['prod_code'] != '') {
        		$sql1.=" and prod_code='".$_REQUEST['prod_code']."'";
    		}
			$st1 = mysqli_query($link1,$sql1);
			$rowss = mysqli_fetch_assoc($st1);
			$arr_data[$str2["from_location"]][$str2["sale_date"]]["qty"] = $rowss['q'];
			$arr_data[$str2["from_location"]][$str2["sale_date"]]["val"] = $rowss['v'];
			$arr_duration[] = $str2["sale_date"];
			$arr_location[] = $str2["from_location"];
		}//// while loop close
		#### PRODUCT GRAPH
		///// initialize array
		$arr_prod = array();
		$arr_duration1 = array();
		$arr_data1 = array();
		$sql1 = "SELECT b.prod_code, SUM(b.qty) as q, sum(b.value) as v, a.sale_date FROM billing_master a, billing_model_data b WHERE a.sale_date LIKE '".$year."-".$month1."-%' AND a.document_type='INVOICE' AND a.status NOT IN ('Invoice Cancelled') AND ".$loc_code1." AND ".$prod_code1." GROUP BY b.prod_code,a.sale_date ORDER BY b.prod_code,a.sale_date";
		$res1 =  mysqli_query($link1,$sql1);
 		while ($row1 = mysqli_fetch_assoc($res1)){
			$arr_data1[$row1["prod_code"]][$row1["sale_date"]]["qty"] = $row1['q'];
			$arr_data1[$row1["prod_code"]][$row1["sale_date"]]["val"] = $row1['v'];
			$arr_duration1[] = $row1["sale_date"];
			$arr_prod[] = $row1["prod_code"];
		}
	}
	if($_REQUEST['type']=='year' && $_REQUEST['year']!=''){
		#### LOCATION GRAPH
		///// initialize array
		$arr_location = array();
		$arr_duration = array();
		$arr_data = array();
		///fetch data
 		$sql = "SELECT from_location, YEAR(sale_date) AS y, MONTH(sale_date) as m, GROUP_CONCAT(challan_no) as invs FROM billing_master WHERE YEAR(sale_date) LIKE '".$year."%' AND document_type='INVOICE' AND status NOT IN ('Invoice Cancelled') AND ".$loc_code." GROUP BY from_location,YEAR(sale_date),MONTH(sale_date) ORDER BY from_location,YEAR(sale_date),MONTH(sale_date)";   
 		$st =  mysqli_query($link1,$sql);
 		while ($str2 = mysqli_fetch_assoc($st)){
			$pos=strpos($str2['invs'], ",");
			if ($pos === false) { 
				$challanstr="'".$str2['invs']."'";
			}else{
				$challanstr="'".str_replace(",","','",$str2['invs'])."'";
			}
        	$sql1 ="SELECT sum(qty) as q ,sum(value) as v FROM billing_model_data WHERE challan_no IN (".$challanstr.")";
			if ($_REQUEST['prod_code'] != '') {
				$sql1.=" and prod_code='".$_REQUEST['prod_code']."'";
			}
			$st1 = mysqli_query($link1,$sql1);
			$rowss = mysqli_fetch_assoc($st1);
			$arr_data[$str2["from_location"]][$str2["y"]."-".getMonth($str2["m"])]["qty"] = $rowss['q'];
			$arr_data[$str2["from_location"]][$str2["y"]."-".getMonth($str2["m"])]["val"] = $rowss['v'];
			$arr_duration[] = $str2["y"]."-".getMonth($str2["m"]);
			$arr_location[] = $str2["from_location"];
		}///close while loop
		
		#### PRODUCT GRAPH
		///// initialize array
		$arr_prod = array();
		$arr_duration1 = array();
		$arr_data1 = array();
		$sql1 = "SELECT b.prod_code, SUM(b.qty) as q, sum(b.value) as v, YEAR(a.sale_date) AS y, MONTH(a.sale_date) as m FROM billing_master a, billing_model_data b WHERE YEAR(a.sale_date) LIKE '".$year."%' AND a.document_type='INVOICE' AND a.status NOT IN ('Invoice Cancelled') AND ".$loc_code1." AND ".$prod_code1." GROUP BY b.prod_code,YEAR(a.sale_date),MONTH(a.sale_date) ORDER BY b.prod_code,YEAR(a.sale_date),MONTH(a.sale_date)";
		$res1 =  mysqli_query($link1,$sql1);
 		while ($row1 = mysqli_fetch_assoc($res1)){
			$arr_data1[$row1["prod_code"]][$row1["y"]."-".getMonth($row1["m"])]["qty"] = $row1['q'];
			$arr_data1[$row1["prod_code"]][$row1["y"]."-".getMonth($row1["m"])]["val"] = $row1['v'];
			$arr_duration1[] = $row1["y"]."-".getMonth($row1["m"]);
			$arr_prod[] = $row1["prod_code"];
		}
	}
}
////// make unique location 
$uniq_loc = array_unique($arr_location);
////// make unique period for location
$uniq_period = array_unique($arr_duration);
////// make unique product 
$uniq_prod = array_unique($arr_prod);
////// make unique period for product
$uniq_period1 = array_unique($arr_duration1);

/////// sort for location
usort($uniq_period, "compareByTimeStamp");
/////// sort for product
usort($uniq_period1, "compareByTimeStamp");

///////for location
$final_loc_qty = "";
$final_loc_val = "";
$period_str = "";
////// for product
$final_prod_qty = "";
$final_prod_val = "";
$period_str1 = "";

///// make location str
foreach($uniq_period as $dura){
	if($period_str){
		$period_str .= ",'".$dura."'";
	}else{
		$period_str  = "'".$dura."'";
	}
}
///// make product str
foreach($uniq_period1 as $dura1){
	if($period_str1){
		$period_str1 .= ",'".$dura1."'";
	}else{
		$period_str1  = "'".$dura1."'";
	}
}
///// make data str for location
////pick location
foreach($uniq_loc as $loc){
	$loc_qty_str = "";
	$loc_val_str = "";
	///// pick duration 
	foreach($uniq_period as $dura){
		///////// get qty graph
		if($loc_qty_str){
			if($arr_data[$loc][$dura]["qty"]){
				$loc_qty_str .= ",".$arr_data[$loc][$dura]["qty"]."";
			}else{
				$loc_qty_str .= ",0";
			}
		}else{
			if($arr_data[$loc][$dura]["qty"]){
				$loc_qty_str = "".$arr_data[$loc][$dura]["qty"]."";
			}else{
				$loc_qty_str .= "0";
			}
		}
		///////// get value graph
		if($loc_val_str){
			if($arr_data[$loc][$dura]["val"]){
				$loc_val_str .= ",".$arr_data[$loc][$dura]["val"]."";
			}else{
				$loc_val_str .= ",0";
			}
		}else{
			if($arr_data[$loc][$dura]["val"]){
				$loc_val_str = "".$arr_data[$loc][$dura]["val"]."";
			}else{
				$loc_val_str .= "0";
			}
		}
	}
	if($final_loc_qty){
		$final_loc_qty .= ", {
						name: '".str_replace("~",",",getLocationDetails($loc,"name,city",$link1))."',
						data: [".$loc_qty_str."]
					}";
	}else{
		$final_loc_qty  = " {
						name: '".str_replace("~",",",getLocationDetails($loc,"name,city",$link1))."',
						data: [".$loc_qty_str."]
					}";
	}
	if($final_loc_val){
		$final_loc_val .= ", {
						name: '".str_replace("~",",",getLocationDetails($loc,"name,city",$link1))."',
						data: [".$loc_val_str."]
					}";
	}else{
		$final_loc_val  = " {
						name: '".str_replace("~",",",getLocationDetails($loc,"name,city",$link1))."',
						data: [".$loc_val_str."]
					}";
	}
}
///// make data str for product
////pick product
foreach($uniq_prod as $prod){
	$prod_qty_str = "";
	$prod_val_str = "";
	///// pick duration 
	foreach($uniq_period1 as $dura1){
		///////// get qty graph
		if($prod_qty_str){
			if($arr_data1[$prod][$dura1]["qty"]){
				$prod_qty_str .= ",".$arr_data1[$prod][$dura1]["qty"]."";
			}else{
				$prod_qty_str .= ",0";
			}
		}else{
			if($arr_data1[$prod][$dura1]["qty"]){
				$prod_qty_str = "".$arr_data1[$prod][$dura1]["qty"]."";
			}else{
				$prod_qty_str .= "0";
			}
		}
		///////// get value graph
		if($prod_val_str){
			if($arr_data1[$prod][$dura1]["val"]){
				$prod_val_str .= ",".$arr_data1[$prod][$dura1]["val"]."";
			}else{
				$prod_val_str .= ",0";
			}
		}else{
			if($arr_data1[$prod][$dura1]["val"]){
				$prod_val_str = "".$arr_data1[$prod][$dura1]["val"]."";
			}else{
				$prod_val_str .= "0";
			}
		}
	}
	if($final_prod_qty){
		$final_prod_qty .= ", {
						name: '".getProductDetails($prod,"productname",$link1)."',
						data: [".$prod_qty_str."]
					}";
	}else{
		$final_prod_qty  = " {
						name: '".getProductDetails($prod,"productname",$link1)."',
						data: [".$prod_qty_str."]
					}";
	}
	if($final_prod_val){
		$final_prod_val .= ", {
						name: '".getProductDetails($prod,"productname",$link1)."',
						data: [".$prod_val_str."]
					}";
	}else{
		$final_prod_val  = " {
						name: '".getProductDetails($prod,"productname",$link1)."',
						data: [".$prod_val_str."]
					}";
	}
}
/*echo $final_str;
echo "<pre>";
print_r($arr_data);
echo "</pre>";*/
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
<script type="text/javascript">
$(function() {
Highcharts.chart('container1', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Sale Trend Quantity wise'
    },
    subtitle: {
        text: 'For Location'
    },
    xAxis: {
        categories: [<?=$period_str?>]
    },
    yAxis: {
        title: {
            text: 'Qty (Pcs)'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    series: [<?=$final_loc_qty?>]
});
Highcharts.chart('container2', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Sale Trend Value wise'
    },
    subtitle: {
        text: 'For Location'
    },
    xAxis: {
        categories: [<?=$period_str?>]
    },
    yAxis: {
        title: {
            text: 'Value (₹)'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    series: [<?=$final_loc_val?>]
});
Highcharts.chart('container3', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Sale Trend Quantity wise'
    },
    subtitle: {
        text: 'For Product'
    },
    xAxis: {
        categories: [<?=$period_str1?>]
    },
    yAxis: {
        title: {
            text: 'Qty (Pcs)'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    series: [<?=$final_prod_qty?>]
});
Highcharts.chart('container4', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Sale Trend Value wise'
    },
    subtitle: {
        text: 'For Product'
    },
    xAxis: {
        categories: [<?=$period_str1?>]
    },
    yAxis: {
        title: {
            text: 'Value (₹)'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    series: [<?=$final_prod_val?>]
});
});
</script>
<script src="../high/highcharts.js" type="text/javascript"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h4 align="center"><i class="fa fa-line-chart"></i> Sale Dashboard</h4>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="row">
        <div class="col-md-2">
        		<select name="type" id="type" class="form-control" style="width:170px;" onChange="document.form1.submit();">
                    <option value="" selected="selected">Select Period Type</option>
                    <option value="year" <?php if ($_REQUEST['type'] == 'year') echo ' selected="selected"'; ?>>Year</option>
                    <option value="month" <?php if ($_REQUEST['type'] == 'month') echo ' selected="selected"'; ?>>Month</option>
                </select>
      	</div>
        <div class="col-md-2">
        	<select name="year" class="form-control" style="width:170px;" id="year">
               <option value="" selected="selected">Select Year</option>
                    <?php 
                    for ($i = 0; $i < 5; $i++) {
                        $yr = date("Y", strtotime(date("Y")." -$i years"));
                    ?>
                    <option value="<?php echo $yr; ?>" <?php if ($_POST['year'] == $yr) echo ' selected="selected"'; ?>><?php echo $yr; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php if ($_REQUEST['type'] != 'year') { ?>
        <div class="col-md-2">
            <select name="month" id="month" class="form-control" style="width:170px;">
                <option value="" selected="selected">Select Month</option><?php
                for ($i = 0, $m2 = 1; $i < 12; ++$i, $m2++) {
                    $months[$m] = $m = date("F", strtotime("January +$i months"));
                    ?>
                    <option value="<?php echo $m2; ?>" <?php if ($_POST[month] == $m2) echo ' selected="selected"'; ?>><?php echo $months[$m]; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>
        <div class="col-md-2">
        	<select name="locationcode" id="locationcode" class="form-control selectpicker" style="width:170px;" data-live-search="true" onChange="document.form1.submit();">
               <option value="" selected="selected">Select Location</option>
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
        <div class="col-md-2">
        	<select name="prod_code" id="prod_code" class="form-control selectpicker" style="width:170px;" data-live-search="true" onChange="document.form1.submit();">
                    <option value="">Select Product</option>
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
	  </form>
         <?php //if($_POST['Submit']=="GO"){ ?>
        <div id="container1" class="form-group table-responsive" style="height: 570px; margin: 0 auto"></div>
        <div id="container2" class="form-group table-responsive" style="height: 570px; margin: 0 auto"></div>
        <div id="container3" class="form-group table-responsive" style="height: 700px; margin: 0 auto"></div>
        <div id="container4" class="form-group table-responsive" style="height: 700px; margin: 0 auto"></div>
        <?php //} ?>
    </div>
  </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>