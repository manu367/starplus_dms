<?php
require_once("../config/config.php");
$year = $_REQUEST['year'];
  ///print_r($datemonthdata);
##### select location
if($_REQUEST["locat_type"]=='RETAIL'){
	////$loc_type = "m.type = '".$_REQUEST["locat_type"]."'";
    $loc_type ="m.type IN ('RETAIL','Customer Retail Return')";
	
}
else{
	$loc_type =  $loc_type ="m.type IN ('CORPORATE','PURCHASE R')";
	
}

///////////////////////////////////////Make total days
if($_POST['Submit']=="GO"){	
/////////////////////////
	if($_REQUEST['year']!=''){
      /////////////////////top 5 location  find start      
 $invstatus_str = "";
 $arr_invstatus = array();
 $invcnt_res = mysqli_query($link1,"SELECT m.to_location,d.qty, d.value FROM billing_master m, billing_model_data d WHERE m.challan_no = d.challan_no AND m.status != 'Cancelled' AND m.entry_date LIKE '$year-%' and  $loc_type");
						while($invcnt_row = mysqli_fetch_assoc($invcnt_res)){
							$arr_invstatus[$invcnt_row["to_location"]] = $invcnt_row["qty"];
						}
						/////// 
						///print_r($arr_invstatus);
						///echo "<pre>";
						arsort($arr_invstatus);
						////echo "</pre>";
						$top_5qty = array_slice($arr_invstatus, 0, 5);
					    $totinvqtySaleyear = array_sum($arr_invstatus);
						///print_r($top_5qty);
						foreach($top_5qty as $statuss => $cont){
						
						////// added by priya on 26 december 2019 //////////////////////////////////////////
						  $dataname = getCustomerDetails($statuss,"customername",$link1);
						  if($dataname == ''){
						   $namevalue = getLocationDetails($statuss , "name" ,$link1);
						   }
						   else {
						     $namevalue = $dataname ;
						   }
						  /////////////////////////////////////////////////////////////////
						  
							if(strlen($invstatus_str)>0){
								$invstatus_str .= ",{ name: '".$namevalue."', y: ".$top_5qty[$statuss]." }";
							}else{
								$invstatus_str .= "{ name: '".$namevalue."', y: ".$top_5qty[$statuss]." }";
							}
						}
						///echo $invstatus_str;
						//print_r($invstatus_str);
 
 /////////////////////top 5 location  find end 
 
/////////////////////top 5 part find 
						
 $product_str = "";
 $arr_product = array();
 $invcnt_res = mysqli_query($link1,"SELECT d.qty, d.prod_code FROM billing_master m, billing_model_data d WHERE m.challan_no = d.challan_no AND m.status != 'Cancelled' AND m.entry_date LIKE '$year-%' and  $loc_type ");
						while($invcnt_row = mysqli_fetch_assoc($invcnt_res)){
							$arr_product[$invcnt_row["prod_code"]] = $invcnt_row["qty"];
						}
						/////// 
						 $totinvproductSaleyear = array_sum($arr_product);
					///	echo "<pre>";
						////print_r($arr_product);
					////	echo "</pre>";
							arsort($arr_product);
						$top_5product = array_slice($arr_product, 0, 5);
						foreach($top_5product as $statuss => $cont){

							if(strlen($product_str)>0){
								$product_str .= ",{ name: '".getProductDetails($statuss,"productname",$link1)."', y: ".$top_5product[$statuss]." }";
							}else{
								$product_str .= "{ name: '".getProductDetails($statuss,"productname",$link1)."', y: ".$top_5product[$statuss]." }";
							}
						}
/////////////////////top 5 part find

//////Sale return query data
						
    $sale_returnproduct_str = "";
    $sale_returnarr_product = array();
    $sale_returnproduct = "";

 $sale_returninvcnt_res = mysqli_query($link1,"SELECT d.qty, d.prod_code FROM billing_master m, billing_model_data d WHERE m.challan_no = d.challan_no AND m.status != 'Cancelled' AND m.entry_date LIKE '$year-%' and $loc_type  and m.type='Customer Retail Return'");
						while($sale_returninvcnt_row = mysqli_fetch_assoc($sale_returninvcnt_res)){
				$sale_returnarr_product[$sale_returninvcnt_row["prod_code"]] = $sale_returninvcnt_row["qty"];
						}
						/////// 
						 $sale_returntotinvproductSaleyear = array_sum($arr_product);
					///	echo "<pre>";
						////print_r($arr_product);
					////	echo "</pre>";
							arsort($sale_returnarr_product);
						$sale_returntop_5product = array_slice($sale_returnarr_product, 0, 5);
						foreach($sale_returntop_5product as $sale_returnstatuss => $cont){
							if(strlen($sale_returnproduct_str)>0){
								$sale_returnproduct_str .= ",{ name: '".getProductDetails($sale_returnstatuss,"productname",$link1)."', y: ".$sale_returntop_5product[$sale_returnstatuss]." }";
                                $sale_returnproduct.= ",'".getProductDetails($sale_returnstatuss,"productname",$link1)."'";
							}else{
								$sale_returnproduct_str .= "{ name: '".getProductDetails($sale_returnstatuss,"productname",$link1)."', y: ".$sale_returntop_5product[$sale_returnstatuss]." }";
                                $sale_returnproduct.= "'".getProductDetails($sale_returnstatuss,"productname",$link1)."'";
							}
						}
						
		//////Sale return 	

///////////////payment model query start
			$pay_str = "";
			$arr_pay = array();
						$invcnt_res = mysqli_query($link1,"SELECT m.payment_mode, COUNT(m.payment_mode) as cnt FROM payment_receive m WHERE m.entry_dt LIKE '$year-%'  GROUP BY m.payment_mode");
						while($invcnt_row = mysqli_fetch_assoc($invcnt_res)){
							$arr_pay[$invcnt_row["payment_mode"]] = $invcnt_row["cnt"];
						}
						///////
							arsort($arr_pay);
						$top_5pay = array_slice($arr_pay, 0, 5);
						foreach($top_5pay as $statuss => $cont){
							if(strlen($pay_str)>0){
								$pay_str .= ",{ name: '".$statuss."', y: ".$top_5pay[$statuss]." }";
							}else{
								$pay_str .= "{ name: '".$statuss."', y: ".$top_5pay[$statuss]." }";
							}
						}
						
	///////////////payment model query					
	

        ////two year compare logic start
            function getLast12Months($year ){
                $a = array();
                for ($i = 0; $i < 12; $i++) 
                {
                   $months[] = date("$year-m", strtotime( date( 'Y-m-01' )." -$i months"));
                   $mnthname[] = date("$year-M", strtotime( date( 'Y-m-01' )." -$i months"));
                   $mnthname_name[] = date("M", strtotime( date( 'Y-m-01' )." +$i months"));
                }
                $a[] = $months;
                $a[] = $mnthname;
                $a[] = $mnthname_name;
                return $a;
            }
            $pre_year=($year-1);
        //echo "<pre>";
            $select_12month = getLast12Months($year);
        ///echo "</pre>";
            $pre_last_12month = getLast12Months($pre_year);
            $select_12monthdata = array();
            $pre_last_12monthdata = array();
         ////// get top 5 model sale of the month
        $top_modres = mysqli_query($link1,"SELECT m.from_location, m.entry_date, d.prod_code, d.qty FROM billing_master m, billing_model_data d WHERE m.challan_no = d.challan_no AND m.status != 'Cancelled' AND (m.entry_date LIKE '$year-%' or m.entry_date LIKE '$pre_year-%' and $loc_type)");
        while($top_modrow = mysqli_fetch_assoc($top_modres)){
                 if(substr($top_modrow["entry_date"],0,4)==$year){
                $select_12monthdata[substr($top_modrow["entry_date"],0,7)] +=  $top_modrow["qty"];
                 }
                 if(substr($top_modrow["entry_date"],0,4)==$pre_year){
                $pre_last_12monthdata[substr($top_modrow["entry_date"],0,7)] +=  $top_modrow["qty"];
                 }

        }			
        $select_12monthstrname = "'".implode("','",$select_12month[2])."'";						
        $select_12monthstr = "'".implode("','",$select_12month[1])."'";					
///////////// year data find
            $inner12data='';
            foreach($select_12month[0] as $keyy => $dateval){
        
                        if($select_12monthdata[$dateval]){ 
                            $salqty = $select_12monthdata[$dateval];}else{ $salqty=0;}
                        if(strlen($inner12data)>0){
                            $inner12data .= ",".$salqty;
                        }else{
                            $inner12data .= "".$salqty;
                        }

                }//// for loop close
///////////// year data find

/////////////prevoius year data find
            $pre_inner12data = '';
            foreach($pre_last_12month[0] as $keyy => $dateval){
               
                        if($pre_last_12monthdata[$dateval]){ 
                            $salqty = $pre_last_12monthdata[$dateval];}else{ $salqty=0;}
                        if(strlen($pre_inner12data)>0){
                            $pre_inner12data .= ",".$salqty;
                        }else{
                            $pre_inner12data .= "".$salqty;
                        }

                }//// for loop close
/////////////prevoius year data find
        ////two year compare


/*echo "<pre>";
print_r($select_12monthdata);
print_r($pre_last_12monthdata);
echo "</pre>";
        */

	}////month and yeay empty case

}  ///submit close
 
 
 /////////////////////top 5 location  find start
 
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
 <script src="../high/highcharts.js" type="text/javascript"></script>


 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<?php if($_POST['Submit']=="GO"){ ?>
<script type="text/javascript">

$(function() {
	 <?php if ($_REQUEST['type'] == 'year') {?>
Highcharts.chart('container', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Top 5 Customer - Sale Qty'
    },
    subtitle: {
        text: ''
    },
	credits: {
		enabled: false
	},
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
    series: [{
        name: 'Delivered amount',
        data: [<?=$invstatus_str?>]
    }]
});


Highcharts.chart('container1', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Top 5 Product - Sales'
    },
    subtitle: {
        text: ''
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
	credits: {
		enabled: false
	},
    series: [{
        name: 'Sale Product',
        data: [<?=$product_str?>]
    }]
});


Highcharts.chart('container2', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Top 5 payment Medhods'
    },
    subtitle: {
        text: ''
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
	credits: {
		enabled: false
	},
    series: [{
        name: 'Payment Mode',
        data: [<?=$pay_str?>]
    }]
});


Highcharts.chart('container3', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Monthly Sales This Year Vs Last Year'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        categories: [<?=$select_12monthstrname?>],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: ''
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: '<?=$year?>',
        data: [<?=$inner12data?>]

    }, {
        name: '<?=$pre_year?>',
        data: [<?=$pre_inner12data?>]

    }]
});
Highcharts.chart('container4', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Top 5 Products By Returns'
    },
    xAxis: {
        categories: [<?=$sale_returnproduct?>]
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Produets Returns'
        }
    },
    legend: {
        reversed: true
    },
    plotOptions: {
        series: {
            stacking: 'normal'
        }
    },
    series: [{
        name: 'Returns QTY',
        data: [<?=$sale_returnproduct_str?>]
    }]
});
<?php } ?>
});


</script>
<?php }?>


</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-line-chart"></i> Yearly  Dashboard</h2><br/><br/>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
      <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Period Type</label>
              <div class="col-md-6">
                <select name="type" id="type" class="form-control" required onChange="document.form1.submit();">
                    <option value="" selected="selected">--Select--</option>
                     <option value="year" <?php if ($_REQUEST[type] == 'year') echo ' selected="selected"'; ?>>Year</option> 
                  <!--   <option value="month" <?php if ($_REQUEST[type] == 'month') echo ' selected="selected"'; ?>>Month</option>-->
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
                  <?php /*?><!--  <select name="month" id="month"  required class="form-control" style="width:120px;">
                        <option value="" selected="selected">--Select--</option><?php
                        for ($i = 0, $m2 = 1; $i < 12; ++$i, $m2++) {
                            //$months[$m] = $m = date("F", strtotime("January +$i months"));
							
							$dateObj   = DateTime::createFromFormat('!m', $m2);
							$months[$m]= $m = $dateObj->format('F');
                            ?>
                            <option value="<?php echo $m2; ?>" <?php if ($_POST[month] == $m2) echo ' selected="selected"'; ?>><?php echo $months[$m]; ?></option>
                        <?php } ?>
                    </select>--><?php */?>
                <?php } ?> 
                </div>
              </div>
            </div>
        </div>
       <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Location Tpye</label>
              <div class="col-md-6">
                 <select name="locat_type" id="locat_type" class="form-control selectpicker"  data-live-search="true" onChange="document.form1.submit();">
                   <option value="RETAIL" selected="selected">Customer</option>
                   <option value="CORPORATE" <?php if($_REQUEST['locat_type']=='CORPORATE')echo "selected";?>>Location </option>
                   <?php /*?> <?php 
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
                    ?><?php */?>
              </select>
              </div>
            </div>
          <div class="col-md-6"><!--<label class="col-md-2 control-label"> Product</label>-->
            <div class="col-md-6">
               <?php /*?><select name="prod_code" id="prod_code" class="form-control selectpicker"  data-live-search="true" onChange="document.form1.submit();">
                    <option value="">--None--</option>
                    <?php 
					$model_query="select productcode,productname from product_master where status='active'";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>"<?php if($br['productcode']==$_REQUEST['prod_code'])echo "selected";?>><?php echo $br['productname'];?></option>
                    <?php }?>
              </select><?php */?>
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
        <?php if ($_REQUEST['type'] == 'year') {?>
		<div style="width=100%">
       <div  id="container" class="form-group table-responsive" style="max-width: 50%; height: 400px; float:left; "></div>
		
         
        <div id="container1" class="form-group table-responsive" style="max-width: 50%; height: 400px;  float:left;"></div>
			</div>
            <div style="width=100%">
		<div id="container2" class="form-group table-responsive" style="max-width: 100%; height: 400px;  float:left;"></div>
        	
         
		</div>
        <div style="clear:both;"></div>
       <div style="width=100%;">
       <div  id="container3" class="form-group table-responsive" style="max-width: 54%; height: 400px; float:left; "></div>
		
         
        <div id="container4" class="form-group table-responsive" style="max-width: 45%; height: 400px;  float:right;"></div>
			</div>
		 <?php }  }?>
    </div>
  </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>