<?php
require_once("../config/config.php");
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
      <h2 align="center"><i class="fa fa-ticket fa-lg"></i>&nbsp;<?=$imeitag?>Activation Report </h2><br/>
                         
   <div class="panel-group">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="get">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-2 control-label">From Date</label>
             
               <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-2 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
             
             <div class="col-md-2" style="text-align:center;">
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
            </div>
           
          </div>
        </div>
		 
          </div>
       
        <div class="form-group">
        	<div class="col-md-2" align="center"></div>
			<div class="col-md-8" align="center">
               <?php
			    //// get excel process id ////
					if(isset($_GET['Submit']) && ($_GET['Submit']=='GO')){
					$head = $imeitag."Activation Data";
			   ?>
			  <div style="margin-top:30px;">
              <div class="col-md-4" style="color:#FF0033"></div>
               
			 <div class="col-md-4" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("imei_activation_report")?>&rheader=<?=base64_encode($head)?>&fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>" title="Export summerize data  in excel"><i class="fa fa-file-excel-o fa-2x" title="Export summerize data  in excel"></i><?=$imeitag?>Activation Data </a></div>
             
             <div class="col-md-4" style="color:#FF0033"></div>
             </div>
             
			   <?php
					}
				//}
				?>
            </div>
            <div class="col-md-2" align="center"></div>
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