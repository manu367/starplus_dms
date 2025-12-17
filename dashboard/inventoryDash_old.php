<?php
require_once("../config/config.php");
@extract($_GET);
## selected  Product
if($prod_code!=""){
	$prdstr="partcode='".$prod_code."'";
}else{
	$prdstr="1";
}
			$arr_prdstr = "";
			$arr_okstr = "";
			$arr_dmgstr = "";
			$arr_misstr = "";
			$arr_prod=array();
			$arr_okcnt=array();
			$arr_dmgcnt=array();
			$arr_miscnt=array();
            $stock_query="select * from stock_status where asc_code='".$_REQUEST['locationcode']."' and ".$prdstr."";
			$stock_res=mysqli_query($link1,$stock_query);
			$i=1;
			while($stock_row = mysqli_fetch_array($stock_res)){
				$proddet=explode("~",getProductDetails($stock_row['partcode'],"productname,productcolor",$link1));
				///// make product string
				if ($arr_prdstr == "") {
					$arr_prdstr.="'".$proddet[0]."'";
				} else {
					$arr_prdstr.=",'".$proddet[0]."'";
				}
				///// make ok qty string
				if ($arr_okstr == "") {
					$arr_okstr.="".$stock_row['okqty']."";
				} else {
					$arr_okstr.=",".$stock_row['okqty']."";
				}
				///// make damage qty string
				if ($arr_dmgstr == "") {
					$arr_dmgstr.="".$stock_row['broken']."";
				} else {
					$arr_dmgstr.=",".$stock_row['broken']."";
				}
				///// make missing qty string
				if ($arr_misstr == "") {
					$arr_misstr.="".$stock_row['missing']."";
				} else {
					$arr_misstr.=",".$stock_row['missing']."";
				}
				$arr_prod[]=$stock_row['partcode'];
				$arr_okcnt[]=$stock_row['okqty'];
				$arr_dmgcnt[]=$stock_row['broken'];
				$arr_miscnt[]=$stock_row['missing'];
			}
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
<script type="text/javascript">
$(function () {
        $('#container').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Inventory Status'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [
                    <?=$arr_prdstr?>
                ]
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Quantity (Pcs)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} Pcs</b></td></tr>',
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
                name: 'Ok',
                data: [<?=$arr_okstr?>]

            }, {
                name: 'Damage',
                data: [<?=$arr_dmgstr?>]

            }, {
                name: 'Missing',
                data: [<?=$arr_misstr?>]

            }]
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
      <h2 align="center"><i class="fa fa-area-chart"></i> Inventory Dashboard</h2><br/><br/>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	    <div class="form-group">
         <div class="col-md-12"><label class="col-md-4 control-label"> Location</label>	  
			<div class="col-md-4" align="left">
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
        </div>
        <div class="form-group">
         <div class="col-md-12"><label class="col-md-4 control-label"> Product</label>	  
			<div class="col-md-4" align="left">
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
            <div class="col-md-2" align="left">
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
                  <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
            </div>
          </div>
	   </div><!--close form group-->
	  </form>
      <div class="form-group table-responsive" id="page-wrap" style="margin-left:10px;">
          <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
           <thead>
            <tr>
              <th width="5%" class="col-md-1">S.No.</th>
              <th width="50%" class="col-md-4">Product</th>
              <th width="15%" class="col-md-1">Ok Qty</th>
              <th width="15%" class="col-md-1">Damage Qty</th>
              <th width="15%" class="col-md-1">Missing Qty</th>
            </tr>
            </thead>
            <tbody>
            <?php
			$stock_query="select * from stock_status where asc_code='".$_REQUEST['locationcode']."' and ".$prdstr."";
			$stock_res=mysqli_query($link1,$stock_query);
			$i=1;
			while($stock_row = mysqli_fetch_array($stock_res)){
				$proddet=explode("~",getProductDetails($stock_row['partcode'],"productname,productcolor",$link1));
            ?>
            <tr>
              <td><?=$i;?></td>
              <td class="lable"><?=$proddet[0]." (".$proddet[1].")"?></td>
              <td align="right"><?=$stock_row['okqty']?></td>
              <td align="right"><?=$stock_row['broken']?></td>
              <td align="right"><?=$stock_row['missing']?></td>
            </tr>
            <?php
			$i++;
            }
            ?>
            </tbody>
          </table>
         </div> 
      <div id="container" class="form-group table-responsive" style="min-width: 800px; height: 400px; margin: 0 auto"></div>
    </div>
  </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>