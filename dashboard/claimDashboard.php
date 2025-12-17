<?php
////// Function ID ///////
$fun_id = array("a"=>array(105)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}


$str_clmtype = "";
$str_clmamt = "";
$sql = "SELECT SUM(total_amount) AS pendamt ,claim_type FROM claim_master WHERE status NOT IN ('Approved','Rejected','Cancelled') group by claim_type";
$res = mysqli_query($link1,$sql);
while($row = mysqli_fetch_assoc($res)){
	if($str_clmtype){
		$str_clmtype .= ",'".$row['claim_type']."'";
	}else{
		$str_clmtype .= "'".$row['claim_type']."'";
	}
	if($str_clmamt){
		$str_clmamt .= ",".$row['pendamt'];
	}else{
		$str_clmamt .= "".$row['pendamt'];
	}
}//// while loop close
			

////// get claim status pie chart code ////////////////////////////////////
$status_str = "";
$arr_status = array();
$cnt_res = mysqli_query($link1,"SELECT status, COUNT(status) AS cnt FROM claim_master GROUP BY status");
while($cnt_row = mysqli_fetch_assoc($cnt_res)){
	$arr_status[$cnt_row["status"]] = $cnt_row["cnt"];
}
/////// 
$tot = array_sum($arr_status);
foreach($arr_status as $statuss => $cont){
	if($status_str){
		$status_str .= ",{ name: '".$statuss."', y: ".round($cont*100/$tot).",url: '".$root."/claim/claim_list.php?status=$statuss'}";
	}else{
		$status_str .= "{ name: '".$statuss."', y: ".round($cont*100/$tot).",url: '".$root."/claim/claim_list.php?status=$statuss'}";
	}
}

//// claim type and status wise claim amount
if($_REQUEST['tdate']!='' && $_REQUEST['fdate']!=''){
	$datefilter = " and (entry_date between '".$_REQUEST['fdate']."' and '".$_REQUEST['tdate']."') ";
}else {
	$datefilter = "";
}
$arr_clm = array();
$res_2 = mysqli_query($link1,"SELECT status, claim_type, SUM(total_amount) AS amt FROM claim_master WHERE 1 ".$datefilter." GROUP BY status, claim_type");
while($row_2 = mysqli_fetch_assoc($res_2)){
	$arr_clm[$row_2["claim_type"]][$row_2['status']] = $row_2['amt'];
}
//echo $status_str;
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="../css/datepicker.css">

<script src="../js/bootstrap-datepicker.js"></script>
<script>

$(document).ready(function(){

	$('#myTable123').dataTable({
		searching: false,
		paging: true,
		info: false,
		ordering: true
	});
	$('#myTable4').dataTable({
		searching: false,
		paging: false,
		info: false,
		ordering: false
	});
	$('#myTable12').dataTable({
		searching: false,
		paging: false,
		info: false,
		ordering: false
	});
	$('.selectpicker').selectpicker({
		width : "300px"
	});
});	
$(document).ready(function() {
    var table = $('#myTable123').DataTable();
     
    $('#myTable123 tbody')
        .on( 'mouseenter', 'td', function () {
            var colIdx = table.cell(this).index().column;
 
            $( table.cells().nodes() ).removeClass( 'highlight' );
            $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
        } );
} );
$(document).ready(function() {
    var table = $('#myTable4').DataTable();
     
    $('#myTable4  tbody')
        .on( 'mouseenter', 'td', function () {
            var colIdx = table.cell(this).index().column;
 
            $( table.cells().nodes() ).removeClass( 'highlight' );
            $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
        } );
} );
$(document).ready(function() {
    var table = $('#myTable12').DataTable();
     
    $('#myTable12 tbody')
        .on( 'mouseenter', 'td', function () {
            var colIdx = table.cell(this).index().column;
 
            $( table.cells().nodes() ).removeClass( 'highlight' );
            $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
        } );
} );
 </script>
 <script type="text/javascript">

$(document).ready(function () {
	$('#fdate,#fdate2').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight: true,
		endDate:"<?php echo $today;?>"
	});
});
$(document).ready(function () {
	$('#tdate,#tdate2').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight: true,
		endDate:"<?php echo $today;?>"
		
	});
});
function openDocModel(docid){
	$.get('../claim/claim_modelview.php?doc_id=' + docid, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
		$('#viewhead').html("<i class='fa fa-pencil-square-o fa-lg faicon'></i> Claim Details");
	 });
}
 </script>
 
 <style type="text/css">

td {
    border: 1px solid black;
    border-radius: 5px;
    -moz-border-radius: 5px;
    padding: 5px;
}
table.dataTable tr.odd { background-color: #E2E4FF; }
table.dataTable tr.even { background-color: white; }
 </style>
 </head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
     		<h2 align="center"><i class="fa fa-rupee"></i> Claim Dashboard</h2><br/>
      		<div class="form-group" id="page-wrap" style="margin-left:10px;">
        		<div class="row">
          			<div class="col-md-6" id="claim_status_pie" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
           			<div class="col-md-6" id="outstanding" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
       			</div>
        	</div>
        	<br/>
       	 	<div class="form-group" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";><br/>
           		<form class="form-horizontal" role="form" name="form1" action="" method="post">
           			<div class="form-group">
             			<div class="col-md-10"><label class="col-md-3 control-label">From Date</label>
              				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;">
                                	<input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:100px;" value="<?php if(isset($_REQUEST['fdate'])){ echo $_REQUEST['fdate']; } else{echo $today;}?>">
                                </div>
			   				</div>             
              				<label class="col-md-3 control-label">To Date</label>
             				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;">
                                	<input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if(isset($_REQUEST['tdate'])){echo $_REQUEST['tdate']; } else{echo $today;}?>"style="width:100px;">
                               	</div>
                             	<div style="display:inline-block;float:right;">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="GO" title="">
                                </div>   
			   				</div>
                            
             			</div>
           			</div>
          		</form>
           		<table  width="70%" id="myTable12" class="table-striped table-bordered table-hover" align="center">
    				<thead>
                    	<tr align="center">
                    	  	<th height="20" colspan="7" style="text-align:center" class="btn-success">Claim Type Summary</th>
                   	  	</tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
                            <th width="10%" style="text-align:center"><strong>Claim Type</strong></th>
                            <?php foreach($arr_status as $clmstatus => $val){ ?>
                            <th width="10%" style="text-align:center"><strong><?=$clmstatus?></strong></th>
                            <?php }?>
    					</tr>
                  	</thead>
                  	<tbody>
                    	<?php
						foreach($arr_clm as $clmtype => $arr){
						?>
                    	<tr>
                        	<td height="20"><?=$clmtype?></td>
							<?php foreach($arr_status as $clmstatus => $val){ ?>
                            <td align="right"><?=$arr[$clmstatus]?></td>
                            <?php }?>
  				  		</tr>
                        <?php }?>
                  	</tbody>
  				</table>
                <br/>
           	</div>
         	<br/>
         	<div class="form-group" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";><br/>
            <form class="form-horizontal" role="form" name="form2" action="" method="post">
           			<div class="form-group">
             			<div class="col-md-10"><label class="col-md-3 control-label">Party Name</label>
              				<div class="col-md-3">
                                <select name="party_code" id="party_code" class="form-control selectpicker" data-live-search="true">
                                    <option value="">All</option>
                                    <?php
                                    $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                                    $res_parent = mysqli_query($link1, $sql_parent);
                                    while ($result_parent = mysqli_fetch_array($res_parent)) {   
                                        $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
                                        if($party_det['name']){
                                    ?>
                                    <option value="<?= $result_parent['location_id']?>" <?php if ($result_parent['location_id'] == $_REQUEST['party_code']) echo "selected"; ?> ><?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id']?></option>
                                  <?php
                                        }
                                    }
                                    ?>
                                </select>
			   				</div>             
              				<label class="col-md-3 control-label">Claim Type</label>
             				<div class="col-md-3">
  								<select name="claim_type" id="claim_type" class="form-control selectpicker" data-live-search="true">
                                    <option value="" selected="selected">Please Select </option>
                                    <?php
                                    $sql_claim = "select id,claim_type from claim_type_master where status='1'";
                                    $res_claim = mysqli_query($link1, $sql_claim);
                                    while ($row_claim = mysqli_fetch_array($res_claim)) {   
                                    ?>
                                    <option value="<?= $row_claim['claim_type']?>" <?php if ($row_claim['claim_type'] == $_REQUEST['claim_type']) echo "selected"; ?> ><?= $row_claim['claim_type']?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
			   				</div>
                            
             			</div>
           			</div>
                    <div class="form-group">
             			<div class="col-md-10"><label class="col-md-3 control-label">From Date</label>
              				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;">
                                	<input type="text" class="form-control span2" name="fdate"  id="fdate2" style="width:100px;" value="<?php if(isset($_REQUEST['fdate'])){ echo $_REQUEST['fdate']; } else{echo $today;}?>">
                                </div>
			   				</div>             
              				<label class="col-md-3 control-label">To Date</label>
             				<div class="col-md-3 input-append date">
  								<div style="display:inline-block;float:left;">
                                	<input type="text" class="form-control span2" name="tdate"  id="tdate2" value="<?php if(isset($_REQUEST['tdate'])){echo $_REQUEST['tdate']; } else{echo $today;}?>"style="width:100px;">
                               	</div>
                             	<div style="display:inline-block;float:right;">
                                    <input type="submit" class="btn btn-primary" name="upd" id="upd" value="GO" title="">
                                </div>   
			   				</div>
                            
             			</div>
           			</div>
          		</form>
           		<table  width="70%" id="myTable123" class="table-striped table-bordered table-hover" align="center">
    				<thead>
                    	<tr align="center" style="background-color:#ff9900">
                    	 	<th height="20" colspan="8" style="text-align:center">Partywise Claim Status</th>
                   	  	</tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
      						<th width="2%" ><strong>S.No.</strong></th>
                            <th width="15%" style="text-align:left"><strong>Party Name</strong></th>
                            <th width="9%" style="text-align:center"><strong>Claim No.</strong></th>
                            <th width="9%" style="text-align:center"><strong>Claim Type</strong></th>
                            <th width="9%" style="text-align:center"><strong>Status</strong></th>
                            <th width="8%" style="text-align:center"><strong>Amount</strong></th>
                            <th width="8%" style="text-align:center"><strong>Create Date</strong></th>
                            <th width="10%" style="text-align:center"><strong>Aging/TAT</strong></th>
    					</tr>
                  	</thead>
                  	<tbody>
                    	<?php
                    	////// get party wise claim status
						if($_REQUEST['tdate2']!='' && $_REQUEST['fdate2']!=''){
							$datefilter2 = " AND (entry_date between '".$_REQUEST['fdate2']."' AND '".$_REQUEST['tdate2']."') ";
						}else {
							$datefilter2 = "";
						}
						if($_REQUEST['party_code']){
							$partyfilter = " AND party_id='".$_REQUEST['party_code']."'";
						}else{
							$partyfilter = "";
						}
						if($_REQUEST['claim_type']){
							$claimtypefilter = " AND claim_type='".$_REQUEST['claim_type']."'";
						}else{
							$claimtypefilter = "";
						}
						$j=1;
						$invcnt_res = mysqli_query($link1,"SELECT * FROM claim_master WHERE 1 ".$datefilter2." ".$partyfilter." ".$claimtypefilter);
						while($invcnt_row = mysqli_fetch_assoc($invcnt_res)){
							$createon = $invcnt_row["entry_date"]." ".$invcnt_row["entry_time"];
						?>
                    	<tr>
                            <td align="left" height="20"><?=$j?></td>
    				  		<td align="left" height="20"><?=str_replace("~"," , ",getAnyDetails($invcnt_row["party_id"],"name,city,state,asc_code","asc_code","asc_master",$link1))?></td>
                            <td align="left" height="20"><a  href="#" onClick="openDocModel('<?php echo base64_encode($invcnt_row["claim_no"]);?>');" title="Click to view claim details"><?=$invcnt_row["claim_no"]?></a></td>
                            <td align="left" height="20"><?=$invcnt_row["claim_type"]?></td>
                            <td align="left" height="20">
							<?php 
							$pos = strpos($invcnt_row["status"], "Pending");
							if ($pos !== false) {?>
                            <div class="badge badge-warning badge-pill"><?=$invcnt_row["status"]?></div>
							<?php } else if($invcnt_row["status"] == 'Rejected') {?>
                            <div class="badge badge-danger badge-pill"><?=$invcnt_row["status"]?></div>
                            <?php } else if($invcnt_row["status"] == 'Approved') {?>
                            <div class="badge badge-success badge-pill"><?=$invcnt_row["status"]?></div>
							<?php } else if($invcnt_row["status"] == 'Cancelled') {?>
                            <div class="badge badge-info badge-pill"><?=$invcnt_row["status"]?></div>
							<?php } else {?>
                            <div class="badge badge-primary badge-pill"><?=$invcnt_row["status"]?></div>
							<?php } ?>
                            </td>
                            <td align="right" height="20"><?=$invcnt_row["total_amount"]?></td>
							<td align="left" height="20"><?=$createon?></td>
                            <td align="left" height="20"><?php if($invcnt_row["status"] == 'Approved' || $invcnt_row["status"] == 'Rejected' || $invcnt_row["status"] == 'Cancelled'){ 
							/////last app activity
							$res_app = mysqli_query($link1,"SELECT action_date, action_time FROM approval_activities WHERE ref_no='".$invcnt_row["claim_no"]."' ORDER BY id DESC");
							$row_app = mysqli_fetch_assoc($res_app);
							$last_app_activitydate = $row_app['action_date']." ".$row_app['action_time'];
							echo timeDiff($createon,$last_app_activitydate);}else{ echo timeDiff($createon,$datetime);}?></td>
  				  		</tr>
                        <?php 
						$j++;
						}
						/////////
						?>
                  	</tbody>
  				</table>
           <br/>
              </div> 
             <br/>
         
         <?php /*?><div class="row" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";>
             <br/>
           	  <table  width="70%" id="myTable4"  class="table-striped table-bordered table-hover" align="center">
    				<thead>
                    	<tr align="center" style="background-color:#ff9900">
                    	  <th height="20" colspan="6" style="text-align:center">Top 10 Lead Conversion Ratio</th>
                   	  	</tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
      						<th width="2%" ><strong>Sno</strong></th>
      						<th width="20%" style="text-align:center"><strong>User Name</strong></th>
                            <th width="20%" style="text-align:left"><strong>No. of Lead</strong></th>
                            <th width="20%" style="text-align:center"><strong>No. of Sales Order</strong></th>
                            <th width="20%" style="text-align:center"><strong>Value After Sales Order</strong></th>
                           
    					</tr>
                  	</thead>
                  	<tbody>
                    	<?php
                    	////// get invoice status count
						$l=1;
						$lead_cnt = mysqli_query($link1,"SELECT count(quote_id) as totnt, create_by  FROM sf_quote_master group by create_by  ");
						while($lead_cnt_row = mysqli_fetch_assoc($lead_cnt)){
						 $name	= mysqli_fetch_array(mysqli_query($link1,"select name from admin_users where username = '".$lead_cnt_row['create_by']."' "));
						 $quotecnt = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totsalew from sf_quote_master where create_by = '".$lead_cnt_row['create_by']."' and status = '14' "));
						?>
                    	<tr>
                            <td align="left" height="20"><?=$l?></td>
    				    	<td align="left" height="20"><?=$name["name"]?></td>
    				  		<td align="center" height="20"><?=$lead_cnt_row["totnt"]?></td>
                            <td align="center" height="20"><?=$quotecnt["totsalew"]?></td>
                            <td align="right" height="20"><?=round((($quotecnt["totsalew"] *100)/$lead_cnt_row["totnt"]),2)?>%</td>
                           

  				  		</tr>
                        <?php 
						$l++;
						}
						/////////
						?>
                  	</tbody>
  				</table>
             <br/>
         </div><?php */?>
        
           
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<!-- Start Model Mapped Modal -->
  <div class="modal modalTH fade" id="courierModel" role="dialog">
  <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
    <div class="modal-dialog modal-dialogTH modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" align="center" id="viewhead"></h4>
        </div>
        <div class="modal-body modal-bodyTH">
         <!-- here dynamic task details will show -->
        </div>
        <div class="modal-footer">
          <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
    </form>
  </div><!--close Model Mapped modal-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
$(function () { 
///// create chart for Lead
Highcharts.setOptions({
    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
        return {
            radialGradient: {
                cx: 0.5,
                cy: 0.3,
                r: 0.7
            },
            stops: [
                [0, color],
                [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
            ]
        };
    })
});

///// create chart for PO status
Highcharts.chart('claim_status_pie', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Claim Status'
    },
	 tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
	/*tooltip: {
    style: {
      pointerEvents: 'all'
    },
    useHTML: true,
    pointFormatter() {
      return "<a href='http://localhost/cansaledms/salesforce/lead_list.php?status='point.name' target='_blank'>{point.name}: <b>{point.percentage:.1f}%</b></a>"
    }
  },
    plotOptions: {
        pie: {
            allowPointSelect: true,
			series: {
            cursor: 'pointer',
			point: {
                    events: {
                        click: function () {
                            location.href = this.options.url;
                        }
                    }
                }
			}
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                connectorColor: 'silver'
            }
        }
    },*/
	plotOptions: {
        series: {
            cursor: 'pointer',
			dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                connectorColor: 'silver'
            },
            point: {
                events: {
                    click: function() {
                        location.href = this.options.url;
                    }
                }
            }
        }
		
    },
	credits: {
		enabled: false
	},
    series:
	 [{
        name: 'Status',
        data: [<?=$status_str?>]
    }]
});


///// create chart for PO status
Highcharts.chart('outstanding', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Claim Type  Data'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        categories: [<?=$str_clmtype?>],
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
            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr></a>',
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
        name: 'Pending Claim Amount',
        data: [<?=$str_clmamt?>]

    }]
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