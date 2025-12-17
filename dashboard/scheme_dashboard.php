<?php
require_once("../config/config.php");
///////
if($_SESSION["utype"]=="1"){                              
	//////state filter
	if($_REQUEST['from_state']){ 
		$stat = " AND state='".$_REQUEST['from_state']."'";
	}else{ 
		$stat = "";
	}
	////location filter
	if($_REQUEST['locationcode']){
		$loc = " AND asc_code='".$_REQUEST['locationcode']."'";
	}
	else{
		$loc = "";
	}
}else{
	///// get access location ///
	$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
	///// get access product sub cat
	$accesspsc=getAccessProduct($_SESSION['userid'],$link1);
	///// get access brand
	$accessbrand=getAccessBrand($_SESSION['userid'],$link1);
	///// get access state
	$accessstate = getAccessState($_SESSION['userid'],$link1);
	////state filter
	if($_REQUEST['from_state']){ 
		$stat = " AND state='".$_REQUEST['from_state']."'";
	}else{ 
		$stat = " AND state IN (".$accessstate.")";
	}
	////location filter
	if($_REQUEST['locationcode']){
		$loc = " AND asc_code='".$_REQUEST['locationcode']."'";
	}
	else{
		$loc = " AND asc_code IN (".$accesslocation.")";
	}
}
///////// fetch data for dashboard
$arr_location = array();
$res_loc = mysqli_query($link1,"SELECT asc_code FROM asc_master WHERE 1 ".$stat." ".$loc."");
while($row_loc = mysqli_fetch_assoc($res_loc)){
	$arr_location[] = $row_loc['asc_code'];
}
$arr_prod = array();
$arr_loc = array();
$arr_datewise = array();
if($_POST['sale_type']=="PRIMARY"){
	$res_sale = mysqli_query($link1,"SELECT * FROM sale_uploader WHERE sale_type='PRIMARY' AND status IN ('Dispatched','Received') AND from_location IN ('".implode("','",$arr_location)."')");
	while($row_sale = mysqli_fetch_assoc($res_sale)){
		$arr_prod[$row_sale['prod_code']] += 1;
		$arr_loc[$row_sale['prod_code']][$row_sale['from_location']] += 1;
		$arr_datewise[$row_sale['from_location']][$row_sale['prod_code']][$row_sale['doc_date']] += 1;
	}
}else if($_POST['sale_type']=="SECONDARY"){
	$res_sale = mysqli_query($link1,"SELECT * FROM sale_uploader WHERE sale_type='SECONDARY' AND status IN ('Dispatched','Received') AND from_location IN ('".implode("','",$arr_location)."')");
	while($row_sale = mysqli_fetch_assoc($res_sale)){
		$arr_prod[$row_sale['prod_code']] += 1;
		$arr_loc[$row_sale['prod_code']][$row_sale['from_location']] += 1;
		$arr_datewise[$row_sale['from_location']][$row_sale['prod_code']][$row_sale['doc_date']] += 1;
	}

}else if($_POST['sale_type']=="TERTIARY"){
	$res_sale = mysqli_query($link1,"SELECT * FROM sale_registration WHERE status IN ('Registered') AND location_code IN ('".implode("','",$arr_location)."')");
	while($row_sale = mysqli_fetch_assoc($res_sale)){
		$arr_prod[$row_sale['prod_code']] += 1;
		$arr_loc[$row_sale['prod_code']][$row_sale['location_code']] += 1;
		$arr_datewise[$row_sale['location_code']][$row_sale['prod_code']][$row_sale['invoice_date']] += 1;
	}
}else{
	
}
arsort($arr_prod);
///// make graph string of top 5 model of the month
$topModelStr = "";
$topModelDrill = "";
$top_5model = array_slice($arr_prod, 0, 5);
foreach($top_5model as $model => $val){
	$modelname = getProductDetails($model,"productname",$link1);
	if($topModelStr){
		$topModelStr .= ",{ name: '".$modelname."',
						    y: ".$val.",
						    drilldown: '".$model."'}";
	}else{
		$topModelStr .= " { name: '".$modelname."',
						    y: ".$val.",
						    drilldown: '".$model."'}";
	}
	///////get location wise sale
	$str1 = "";
	foreach($arr_loc[$model] as $locationcode => $qty){
		$locationname = getLocationDetails($locationcode,"name",$link1);
		if($str1){
			$str1 .= ",[ 
								  '".$locationname."',
								  ".$qty."
								]";
		}else{
			$str1 .= "[ 
								  '".$locationname."',
								  ".$qty."
								]";
		}
	}
	/////
	if($topModelDrill){
		$topModelDrill .= ",{
							 name: '".$modelname."',
							 id: '".$model."',
							 data: [".$str1."]
							}";
	}else{
		$topModelDrill .= "{
							 name: '".$modelname."',
							 id: '".$model."',
							 data: [".$str1."]
							}";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<script src="../js/jquery-1.10.1.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link href='../css/select2.min.css' rel='stylesheet' type='text/css'>
<script src='../js/select2.min.js'></script>
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function() {
		$('#fdate').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
		$('#tdate').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	$(document).ready(function(){
		$("#locationcode").select2({
			ajax: {
				url: "../includes/getAzaxFields.php",
				type: "post",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						searchLoc: params.term, // search term
						requestFor: "alllocwithfilter",
						userid: '<?=$_SESSION['userid']?>',
						state_name: '<?=$_REQUEST['from_state']?>',
						user_type: '<?=$_SESSION['utype']?>',
						id_type: 'HO,DS,DL',
						requestFilter1: 'state',
						requestFilter3: 'locationtype'
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
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head> 
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
    	<h2 align="center"><i class="fa fa-bar-chart"></i> Dashboard</h2>
        <h4 align="center">Related to schemes</h4>
        <form class="form-horizontal" role="form" name="frm1" action="" method="post">
      		<div class="row">
      			<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
        			<input type="text" class="form-control" name="fdate"  id="fdate" value="<?php if(isset($_REQUEST['fdate'])){ echo $_REQUEST['fdate']; } else{echo $today;}?>">
        		</div>
        		<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">To Date</label>
        			<input type="text" class="form-control" name="tdate"  id="tdate" value="<?php if(isset($_REQUEST['tdate'])){ echo $_REQUEST['tdate']; } else{echo $today;}?>">
        		</div>
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">State</label>
                    <select name="from_state" id="from_state" class="form-control" onChange="document.frm1.submit();" >
                        <option value="" selected="selected">All</option>
                        <?php
						if($_SESSION["utype"]=="1"){                              
                        	$sql_state = "SELECT state,code FROM state_master WHERE 1 ORDER BY state";
						}else{
							$sql_state = "SELECT state,code FROM state_master WHERE 1 AND state IN (".$accessstate.") ORDER BY state";
						}
                        $res_state = mysqli_query($link1,$sql_state);
                        while($row_state = mysqli_fetch_array($res_state)){
                        ?>
                        <option value="<?=$row_state['state']?>" <?php if($row_state['state']==$_REQUEST['from_state'])echo "selected";?>><?=$row_state['state']?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
        		<div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Location Name</label>
                    <select name="locationcode" id="locationcode" class="form-control">        
                        <option value=''>--Please Select--</option>
                        <?php
                        if(isset($_POST["locationcode"])){
                          $loc_name = explode("~",getAnyDetails($_POST["locationcode"],"name, city, state","asc_code","asc_master",$link1));
                        echo '<option value="'.$_POST["locationcode"].'" selected>'.$loc_name[0].' | '.$loc_name[1].' | '.$loc_name[2].' | '.$_POST["locationcode"].'</option>';
                        }
                        ?>
                    </select>
        		</div>
        	</div>
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">Sale Type <span style="color:#F00">*</span></label>
                    <select name="sale_type" id="sale_type" class="form-control required" required>        
                        <option value=''>--Please Select--</option>
                        <option value="PRIMARY"<?php if($_REQUEST['sale_type']=="PRIMARY"){ echo "selected";}?>>PRIMARY</option>
                        <option value="SECONDARY"<?php if($_REQUEST['sale_type']=="SECONDARY"){ echo "selected";}?>>SECONDARY</option>
                        <option value="TERTIARY"<?php if($_REQUEST['sale_type']=="TERTIARY"){ echo "selected";}?>>TERTIARY</option>
                    </select>
        		</div>
        		<div class="col-sm-3 col-md-3 col-lg-3"><br/>
            		<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            		<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            		<input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
        		</div>
      		</div>
	  	</form>
        <br/>
		<div class="row">
    		<div class="col-sm-6">
    			<div class="panel panel-success">
					<div class="panel-heading"><i class="fa fa-cubes" aria-hidden="true"></i> Product Category Wise Sale</div>
			 		<div class="panel-body">
                    	<div id="top_model" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
                    </div>
              	</div>
         	</div>
            <div class="col-sm-6">
                <div class="panel panel-info" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-address-card-o" aria-hidden="true"></i> Dashboard 2</div>
			 		<div class="panel-body"></div>
              	</div>
            </div>
     	</div>   
        <!--<div class="row">
    		<div class="col-sm-6">    
                 <div class="panel panel-danger" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-edit" aria-hidden="true"></i> Dashboard 3</div>
			 		<div class="panel-body"></div>
              </div>
          	</div>
            <div class="col-sm-6">
    			<div class="panel panel-danger" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-comments" aria-hidden="true"></i> Dashboard 4</div>
			 		<div class="panel-body">

                  	</div>
    			</div>
          	</div>
    	</div>
        <div class="row">
    		<div class="col-sm-6">
    			<div class="panel panel-success" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-history" aria-hidden="true"></i> Dashboard 5</div>
			 		<div class="panel-body"></div>
              </div>
          	</div>
            <div class="col-sm-6">
    			<div class="panel panel-warning" style="font-size:11px">
					<div class="panel-heading"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Dashboard 6</div>
			 		<div class="panel-body"></div>
              </div>
          	</div>
    	</div>-->
    </div><!--End col-sm-9-->
  	</div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
$(function () { 
 // Create the chart for top 5 model sale of the month
Highcharts.chart('top_model', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Top 5 Model Sale Of The Month'
    },
    /*subtitle: {
        text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
    },*/
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total Qty'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>'
    },
	credits: {
		enabled: false
	},
    series: [
        {
            name: "Model",
            colorByPoint: true,
            data: [<?=$topModelStr?>]
        }
    ],
    drilldown: {
        series: [<?=$topModelDrill?>]
    }
});
 

});
 </script>
<script src="../high/highcharts.js" type="text/javascript"></script>
<script src="../high/highcharts-more.js"></script>
<script src="../high/js/modules/data.js"></script>
<script src="../high/js/modules/drilldown.js"></script>
<script src="../high/js/highcharts-3d.js"></script>
<script src="../high/js/modules/exporting.js"></script>
</body>
</html>