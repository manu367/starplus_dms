<?php
require_once("../config/config.php");
@extract($_GET);
$date=date("Y-m-d");
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
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
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
		autoclose: true
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
      <h2 align="center"><i class="fa fa-bullseye"></i> Beat Report </h2><br/>
   <div class="form-group" id="page-wrap" style="margin-left:10px;">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="get">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
          </div>
        </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Select User <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="po_from" id="po_from"  class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select username,name from admin_users where status='active' order by name";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                     
                          ?>
                    <option data-tokens="<?=$result_chl['name']." | ".$result_chl['username']?>" value="<?=$result_chl['username']?>" <?php if($result_chl['username']==$_REQUEST['po_from'])echo "selected";?> >
                       <?=$result_chl['name']?>
                    </option>
                    <?php
						  }
					
                    ?>
                 </select>
              </div>
            </div>
			</div>
       
          
		
     
		
		
		<div class="form-group">
		  <div class="col-md-10"><label class="col-md-3 control-label"></label>
            <div class="col-md-3">
                
            </div>
			<label class="col-md-3 control-label">&nbsp;</label>
            <div class="col-md-3">
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
          </div>
	    </div><!--close form group--> 
		     
        <div class="form-group">
         
		  <div class="col-md-12"><label class="col-md-3 control-label"></label>	  
			<div class="col-md-9" align="left">
               <?php
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("Invoice",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
					if($_GET['Submit']=="GO"){
			   ?>
			  
              <?php /*?><div class="col-md-3" style="color:#FF0033"> <a href="excelexport.php?rname=<?=base64_encode("Beat_Report")?>&rheader=<?=base64_encode("Beat Report")?>&fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&floc=<?=base64_encode($_GET['po_from'])?>" title="Export detail in excel"><i class="fa fa-file-excel-o fa-2x" title="Export detail details in excel"></i> Beat Report</a></div><?php */?>
              
              <div class="col-md-3" style="color:#FF0033"> <a href="../excelReports/beatreport.php?fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&floc=<?=$_GET['po_from']?>" title="Export detail in excel"><i class="fa fa-file-excel-o fa-2x" title="Export detail details in excel"></i> Beat Report</a></div>
       
       
        <div class="col-md-3" style="color:#FF0033"> <a href="../excelReports/pjpPlanVsAch.php?fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&floc=<?=base64_encode($_GET['po_from'])?>" title="Export detail in excel"><i class="fa fa-file-excel-o fa-2x" title="Export detail details in excel"></i> Plan VS Achieve Report</a></div>        
			
               <?php
					}
				//}
				
				?>
            </div>
          </div>
	    </div><!--close form group-->
         </form>
  </div><!--close panel group-->
  
   
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>