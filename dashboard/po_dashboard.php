<?php
////// Function ID ///////
$fun_id = array("a"=>array(15)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

@extract($_POST);
////// if we hit process button
$sql_poto='1';
$sql_pofrom='1';
$sql_status='1';
$sql_fdate='1';
$orders1='';
 if (($_POST['upd']!="" && $_POST['upd']=='GO'))
 {
	
    	if($po_to!='')
		{
			$sql_poto="(po_to='".$po_to."')";
		}
	
	 if($po_from=="")
		{
			$sql_pofrom="1";
		}
	else
	{
		$sql_pofrom="(po_from='".$po_from."')";
	}
	
	if($status=='All')
	{
		$sql_status="1";
	}
	else
	{
		$sql_status="(status='".$status."')";
	}
	
	if($fdate!='' and $tdate!='')
	{
		$sql_fdate="(entry_date >='".$fdate."' and entry_date <='".$tdate."')";
		
	}
}	
	 $doc=mysqli_query($link1, "select count(id) as total, entry_date, po_to, po_from, status from purchase_order_master where $sql_poto and $sql_pofrom and $sql_status and $sql_fdate group by entry_date");
	 
	  if($doc!=FALSE)
	  {
		 while($drow=mysqli_fetch_assoc($doc))
		 {
			 if($orders1==""){
			  $orders1.="{".'name'.":"."'".date('d-M-Y', strtotime($drow['entry_date']))."'".",".'y'.":".$drow['total']."}";
		  }else{
			  $orders1.=",{".'name'.":"."'" .date('d-M-Y',strtotime($drow['entry_date']))."'".",".'y'.":".$drow['total']."}";
		  }
		 }
	  }
	//echo $orders1;
	

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
 
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function(){
    $("#frm2").validate();
});
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
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>



</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-pie-chart"></i> Purchase Order (PO) Dashboard </h2><br/>
                         
   <div class="panel-group">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Purchase Order To<span style="color:#F00">*</span></label>
              <div class="col-md-9">
              <select name="po_to" id="po_to" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_parent="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
					//$sql_parent="select uid from mapped_master where mapped_code='$_REQUEST[po_from]'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
						
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_parent[location_id]'"));
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_parent['uid']?>" value="<?=$result_parent['location_id']?>" <?php if(isset($_REQUEST['po_to']) && $result_parent['location_id']==$_REQUEST['po_to']) echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['location_id']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
                
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Purchase Order From<span style="color:#F00"></span></label>
              <div class="col-md-9">
                 <select name="po_from" id="po_from"  class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from mapped_master where uid='$_REQUEST[po_to]' and status='Y'";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[mapped_code]'"));
	                      if($party_det[id_type]!='HO'){
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['mapped_code']?>" <?php if(isset($_REQUEST['po_from']) &&$result_chl['mapped_code']==$_REQUEST['po_from'])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['mapped_code']?>
                    </option>
                    <?php
						  }
					}
                    ?>
                 </select>
                
              </div>
            </div>
          </div>
          
          
          <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date<span style="color:#F00">*</span></label>
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if(isset($_REQUEST['fdate'])){ echo $_REQUEST['fdate']; } else{echo $today;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>             
              <label class="col-md-3 control-label">To Date<span style="color:#F00">*</span></label>
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if(isset($_REQUEST['tdate'])){echo $_REQUEST['tdate']; } else{echo $today;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">Status</label>
              <div class="col-md-3">
                <select name="status" id="status" required class="form-control selectpicker required" data-live-search="true">
                 <option value="All" selected="selected">Please Select </option>
                 <option value="PFA"<?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='PFA'){echo "selected='selected'";}?>>Pending For Approval</option>
                 <option value="Approved" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='Approved'){echo "selected='selected'";}?>>Approved</option>
                  <option value="Reject" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='Reject'){echo "selected='selected'";}?>>Reject</option>
                   <option value="Processed" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='Processed'){echo "selected='selected'";}?>>Processed</option>
                 </select>
                 
              </div>
              
                <div class="col-md-3">
  					<input type="submit" class="btn btn-primary" name="upd" id="upd" value="GO" title="Make Invoice">
			   </div>
             
          </div>
        </div>
       
         </form>
       
  </div><!--close panel group-->
  
    <?php //if(isset($_REQUEST['po_to']) && $_REQUEST['po_to']!='' && $_REQUEST['status']!='' && $_REQUEST['fdate']!='' && $_REQUEST['tdate']!='')
			//{?>
   
   <div class="col-sm-12 tab-pane fade in active table-responsive" id="home">
     <!-- <h2 align="center"><i class="fa fa-cubes"></i>PO List</h2>-->
     
              <?php if($doc!=FALSE)
  		 {
	   mysqli_data_seek($doc, 0 );
		
	   ?>
       <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
        <table width="100%" id="itemsTable1" class="table table-bordered table-hover">

          <thead>
            <tr>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>PO Date</th>
               <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>No Of PO</th>
                <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			while($prow=mysqli_fetch_assoc($doc))
				{
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td align="center"><?php echo dt_format($prow['entry_date']);?></td>
                <td align="right"><?php echo $prow['total'];?></td>
               <td <?php if($prow['status']=="PFA"){ echo "class='red_small'";}?>><?php if($prow['status']=="PFA"){echo "Pending For Approval";} else {echo $prow['status'];}?></td>
              </tr>
            <?php }?>
          </tbody>
          </table>
        </div>  
      </div>
      
    

<!-- pie chart js -->

<script src="../high/highcharts.js"></script>
<script src="../high/exporting.js"></script>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<script>
Highcharts.chart('container', {
	
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Purchase Order Details from <?php echo date('d-M-Y',strtotime($_REQUEST['fdate']));?> to <?php echo date('d-M-Y',strtotime($_REQUEST['tdate']))?>'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'PO',
        colorByPoint: true,
        data: [<?php echo $orders1;?>]
    }]
	
});
</script>

    
    <?php } 
	   //} ?>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>