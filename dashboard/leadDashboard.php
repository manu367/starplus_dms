<?php
require_once("../config/config.php");


			$arr_qsstr = "";
			$arr_vsstr = "";
			 $sql = "SELECT YEAR(tdate) AS y ,MONTH(tdate) as m ,tdate, GROUP_CONCAT(create_by) as invs FROM sf_lead_master WHERE  status ='1' group by tdate";
			$st = mysqli_query($link1,$sql);
			while ($str2 = mysqli_fetch_assoc($st)){
				$qty = 0;
				$value = "";
				$pos=strpos($str2['invs'], ",");
				if ($pos === false) { $challanstr="'".$str2['invs']."'";}else{$challanstr="'".str_replace(",","','",$str2['invs'])."'";}
					 $sql1 ="SELECT count(lid) as q , create_by   FROM sf_lead_master WHERE status ='1' and create_by in ($challanstr) and tdate = '".$str2['tdate']."' ";
				
				$st1 = mysqli_query($link1,$sql1);
				$rowss = mysqli_fetch_assoc($st1);
				$qty+=$rowss['q'];
				
				
				#######################
				
				if ($arr_qsstr == "" or $arr_vsstr == ""){
					$arr_qsstr.=$qty;
					$arr_vsstr.=$rowss['create_by'];
				} else {
					$arr_qsstr.=','.$qty;
					$arr_vsstr.=','.$rowss['create_by'];
				}
				
				if($str2['tdate']!=''){
				   $m2=$str2['m']-1;
				   $m1 = date("F", strtotime("January +$m2 months"));
				   if($month==""){
					  $month.="'".$m1."'";
				   }else{
					  $month.=","."'".$m1."'";   
				   } 
				}
				
			}//// while loop close
			
			
            //print_r($arr_qsstr);

                    	////// get lead pie chart code ////////////////////////////////////
						$postatus_str = "";
						$arr_postatus = array();
						$pocnt_res = mysqli_query($link1,"SELECT status, COUNT(status) as cnt FROM sf_lead_master  GROUP BY status");
						while($pocnt_row = mysqli_fetch_assoc($pocnt_res)){
							$arr_postatus[$pocnt_row["status"]] = $pocnt_row["cnt"];
						}
						/////// 
						$totpo = array_sum($arr_postatus);
						foreach($arr_postatus as $statuss => $cont){
							$status =mysqli_fetch_array(mysqli_query($link1,"select status_name  from sf_status_master where id = '".$statuss."' "));
							if($postatus_str){
								$postatus_str .= ",{ name: '".$status['status_name']."', y: ".round($cont*100/$totpo).",url: 'https://pre.cansale.in/demodms/salesforce/lead_list.php?status=$statuss' }";
							}else{
								$postatus_str .= "{ name: '".$status['status_name']."', y: ".round($cont*100/$totpo)." ,url: 'https://pre.cansale.in/demodms/salesforce/lead_list.php?status=$statuss'}";
							}
						}
						



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
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true,
		max:<?php echo $today;?>
		
	});
});
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
     <h2 align="center"><i class="fa fa-child"></i> Lead Dashboard</h2><br/><br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
 
        <div class="row">
          <div class="col-md-6" id="po_status_pie" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
           
          <div class="col-md-6" id="outstanding" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
       
           	  
            </div>     
         
        </div>
        
        <br/>
         
        <div class="form-group table-responsive" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";><br/>
           <form class="form-horizontal" role="form" name="form1" action="" method="post"  >
           <div class="form-group">
             <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
              <div class="col-md-3 input-append date">
  				<div style="display:inline-block;float:left;">	<input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:100px;" value="<?php if(isset($_REQUEST['fdate'])){ echo $_REQUEST['fdate']; } else{echo $today;}?>"></div>
			   </div>             
              <label class="col-md-3 control-label">To Date</label>
             <div class="col-md-3 input-append date">
  				<div style="display:inline-block;float:left;">	<input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if(isset($_REQUEST['tdate'])){echo $_REQUEST['tdate']; } else{echo $today;}?>"style="width:100px;"></div>
			   </div>
             </div>
             <input type="submit" class="btn btn-primary" name="upd" id="upd" value="GO" title="Make Invoice">
           </div>                
          </form>
           
           <table  width="70%" id="myTable12" class="table-striped table-bordered table-hover" align="center">
    				<thead>
                    	<tr align="center"  style="background-color:#ff9900">
                    	  <th height="20" colspan="7" style="text-align:center">Lead Conversion</th>
                   	  	</tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
      						
      						
                            <th width="10%" style="text-align:center"><strong>Area</strong></th>
                            <th width="15%" style="text-align:center"><strong>Lead</strong></th>
                            <th width="15%" style="text-align:center"><strong>Quote</strong></th>
                            <th width="15%" style="text-align:center"><strong>Quote %</strong></th>
                            <th width="15%" style="text-align:center"><strong>Sale Order</strong></th>
                            <th width="15%" style="text-align:center"><strong>Sale Order %</strong></th>
    					</tr>
                        
                  	</thead>
                  	<tbody>
                    	<?php
                    	////// get invoice status count
						if($_REQUEST['tdate'] || $_REQUEST['fdate'] ){
							$tdate = " and (tdate between '".$_REQUEST['fdate']."' and '".$_REQUEST['tdate']."') ";
							$stdate = " and (create_dt between '".$_REQUEST['fdate']."' and '".$_REQUEST['tdate']."') ";
							}else {
							$tdate = "";
							$stdate = "";
								}
							
						$i=1;
						//echo "SELECT count(lid) as leadnorth FROM sf_lead_master where status != '17' and party_country = 'NORTH' $tdate " ;
						$north_lead = mysqli_fetch_array(mysqli_query($link1,"SELECT count(lid) as leadnorth FROM sf_lead_master where status != '17' and party_country = 'NORTH' $tdate "));
					
						$quote = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totquote from sf_quote_master where country = 'NORTH'$stdate "));
						$sale_order = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totsale from sf_quote_master where country = 'NORTH'and status = '14' $stdate "));
						
						$south_lead = mysqli_fetch_array(mysqli_query($link1,"SELECT count(lid) as leadsouth FROM sf_lead_master where status != '17' and party_country = 'SOUTH' $tdate"));
						$south_quote = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totsquote from sf_quote_master where country = 'SOUTH' $stdate "));
						$south_sale_order = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totsales from sf_quote_master where country = 'SOUTH'and status = '14' $stdate"));
						
						$east_lead = mysqli_fetch_array(mysqli_query($link1,"SELECT count(lid) as leadeast FROM sf_lead_master where status != '17' and party_country = 'EAST' $tdate"));
						$east_quote = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totequote from sf_quote_master where country = 'EAST' $stdate "));
						$east_sale_order = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totsalee from sf_quote_master where country = 'EAST'and status = '14' $stdate"));
						
						$west_lead = mysqli_fetch_array(mysqli_query($link1,"SELECT count(lid) as leadwest FROM sf_lead_master where status != '17' and party_country = 'WEST' $tdate"));
						$west_quote = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totwquote from sf_quote_master where country = 'WEST' $stdate "));
						$west_sale_order = mysqli_fetch_array(mysqli_query($link1 , "select count(quote_id) as totsalew from sf_quote_master where country = 'WEST'and status = '14' $stdate"));
							
						?>
                    	<tr>
                            <td align="left" height="20">NORTH</td>
    				    	<td align="right" height="20"><?=$north_lead["leadnorth"]?></td>
    				  		<td align="right" height="20"><?=$quote["totquote"]?></td>
                            <td align="right" height="20"><?=round(($quote["totquote"]*100) / $north_lead["leadnorth"],2)?>%</td>
                            <td align="right" height="20"><?=$sale_order["totsale"]?></td>
                            <td align="right" height="20"><?=round(($sale_order["totsale"]*100) / $north_lead["leadnorth"],2)?>%</td>

  				  		</tr>
                        <tr>
                            <td align="left" height="20">SOUTH</td>
    				    	<td align="right" height="20"><?=$south_lead["leadsouth"]?></td>
    				  		<td align="right" height="20"><?=$south_quote["totsquote"]?></td>
                            <td align="right" height="20"><?=round(($south_quote["totsquote"]*100) / $south_lead["leadsouth"],2)?>%</td>
                            <td align="right" height="20"><?=$south_sale_order["totsales"]?></td>
                            <td align="right" height="20"><?=round(($south_sale_order["totsales"]*100) / $south_lead["leadsouth"],2)?>%</td>

  				  		</tr>
                        <tr>
                            <td align="left" height="20">EAST</td>
    				    	<td align="right" height="20"><?=$east_lead["leadeast"]?></td>
    				  		<td align="right" height="20"><?=$east_quote["totequote"]?></td>
                            <td align="right" height="20"><?=round(($east_quote["totequote"]*100) / $east_lead["leadeast"],2)?>%</td>
                            <td align="right" height="20"><?=$east_sale_order["totsalee"]?></td>
                            <td align="right" height="20"><?=round(($east_sale_order["totsalee"]*100) / $east_lead["leadeast"],2)?>%</td>

  				  		</tr>
                        <tr>
                            <td align="left" height="20">WEST</td>
    				    	<td align="right" height="20"><?=$west_lead["leadwest"]?></td>
    				  		<td align="right" height="20"><?=$west_quote["totwquote"]?></td>
                            <td align="right" height="20"><?=round(($west_quote["totwquote"]*100) / $west_lead["leadeast"],2)?>%</td>
                            <td align="right" height="20"><?=$west_sale_order["totsalew"]?></td>
                            <td align="right" height="20"><?=round(($west_sale_order["totsalew"]*100) / $west_lead["leadeast"],2)?>%</td>

  				  		</tr>
                        <?php 
						
						/////////
						?>
                  	</tbody>
  				</table>
                <br/>
           
            
           
        </div>
         
         <br/>
         
         <div class="form-group table-responsive" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";>
            <br/>
           	  <table  width="70%" id="myTable123" class="table-striped table-bordered table-hover table-responsive" align="center">
    				<thead>
                    	<tr align="center" style="background-color:#ff9900">
                    	  <th height="20" colspan="6" style="text-align:center">Upcoming Task</th>
                   	  	</tr>
                    	<tr align="center" class="<?=$tableheadcolor?>">
      						<th width="2%" ><strong>Sno</strong></th>
      						<th width="7%" style="text-align:center"><strong>Lead</strong></th>
                            <th width="10%" style="text-align:left"><strong>Party Name</strong></th>
                            <th width="7%" style="text-align:center"><strong>Priority</strong></th>
                            <th width="7%" style="text-align:center"><strong>Status</strong></th>
                            <th width="20%" style="text-align:center"><strong>Create Date</strong></th>
    					</tr>
                  	</thead>
                  	<tbody>
                    	<?php
                    	////// get invoice status count
						$j=1;
						$invcnt_res = mysqli_query($link1,"SELECT * FROM sf_lead_master order by lid desc ");
						while($invcnt_row = mysqli_fetch_assoc($invcnt_res)){
							$status_name =mysqli_fetch_array(mysqli_query($link1,"select status_name  from sf_status_master where id = '".$invcnt_row['status']."' "));
						?>
                    	<tr>
                            <td align="left" height="20"><?=$j?></td>
    				    	<td align="left" height="20"><a  href="https://pre.cansale.in/demodms/salesforce/lead_list.php" target="_blank"><?=$invcnt_row["reference"]?></a></td>
    				  		<td align="left" height="20"><?=$invcnt_row["partyid"]?></td>
                            <td align="left" height="20"><?=$invcnt_row["priority"]?></td>
                            <td align="left" height="20"><?php if($status_name["status_name"] == 'Open'){?><div class="badge badge-warning badge-pill"><?=$status_name["status_name"]?></div><?php } else if($status_name["status_name"] == 'Pending') {?><div class="badge badge-secondary badge-pill"><?=$status_name["status_name"]?></div> <?php } else if($status_name["status_name"] == 'Approve') {?><div class="badge badge-primary badge-pill"><?=$status_name["status_name"]?></div> <?php } else if($status_name["status_name"] == 'Cancelled') {?><div class="badge badge-info badge-pill"><?=$status_name["status_name"]?></div> <?php }  else {?><div class="badge badge-primary badge-pill"><?=$status_name["status_name"]?></div> <?php } ?></td>
                            <td align="center" height="20"><?=$invcnt_row["tdate"]?></td>

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
         
         <div class="row" style="border:solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";>
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
         </div>
        
           
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
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
Highcharts.chart('po_status_pie', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Lead Status'
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
        data: [<?=$postatus_str?>]
    }]
});


///// create chart for PO status
Highcharts.chart('outstanding', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Monthwise Lead Pending  Data'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        categories: [<?=$month?>],
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
        name: 'Pending Task Count',
        data: [<?=$arr_qsstr?>]

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