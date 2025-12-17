<?php
////// Function ID ///////
$fun_id = array("a"=>array(100));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$date=date("Y-m-d");
?>
<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
 <title><?=siteTitle?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});

$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		 todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		 todayHighlight: true,
		autoclose: true
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-shopping-cart fa-lg"></i>&nbsp;GRN Report </h2><br/>
                         
   <div class="panel-group">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
             
              <div class="col-md-3">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 ">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
           
          </div>
        </div>
		 <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Select Vendor</label>
              <div class="col-md-9">
                 <select name="po_to" id="po_to" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                 <option value="" selected="selected">All</option>
                    <?php 
					$sql_parent="select * from vendor_master where status='active'";
					$res_parent=mysqli_query($link1,$sql_parent);
					while($result_parent=mysqli_fetch_array($res_parent)){
                          ?>
                    <option data-tokens="<?=$result_parent['name']." | ".$result_parent['id']?>" value="<?=$result_parent['id']?>" <?php if($result_parent['id']==$_REQUEST['po_to'])echo "selected";?> >
                       <?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['country']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Select Location</label>
              <div class="col-md-9">
                 <select name="po_from" id="po_from" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">All</option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
	                     
                          ?>
                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl[location_id]?>" <?php if($result_chl[location_id]==$_REQUEST[po_from])echo "selected";?> >
                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                    </option>
                    <?php
						  }
					
                    ?>
                 </select>
              </div>
            </div>
			</div>
          
		 

          <?php /*?><div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label">Document Type</label>
            <div class="col-md-5">
                <select  name='doctype' id="doctype" class='form-control selectpicker' onChange="document.form1.submit();">
                  <option value=''>All</option>
				  <option value="GRN"<?php if($_REQUEST['doctype']=="GRN"){echo 'selected';}?>>GRN</option>
                  <option value="LP"<?php if($_REQUEST['doctype']=="GRN"){echo 'selected';}?>>LP</option>
                  <option value="DIRECT SALE RETURN"<?php if($_REQUEST['doctype']=="DIRECT SALE RETURN"){echo 'selected';}?>>DIRECT SALE RETURN</option>
                  <option value="CLP"<?php if($_REQUEST['doctype']=="CLP"){echo 'selected';}?>>CLP</option>
                  <option value="STN"<?php if($_REQUEST['doctype']=="STN"){echo 'selected';}?>>STN</option>
               </select>
            </div>
          </div> <?php */?>
		  
          <div class="col-md-6">
              <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
          </div>
	    </div><!--close form group-->
          </div>
       
        <div class="form-group">
        	<div class="col-md-2" align="center"></div>
			<div class="col-md-8" align="center">
               <?php
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("Vendor",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
					if(isset($_REQUEST['Submit']) && ($_REQUEST['Submit']=='GO')){
						$head = $imeitag."GRN Data";
			   ?>
			  <div style="margin-top:30px;">
              <div class="col-md-4" style="color:#FF0033"> <a href="excelexport.php?rname=<?=base64_encode("grndetail")?>&rheader=<?=base64_encode("GRNdetail")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&loc=<?=base64_encode($_REQUEST['po_from'])?>&ven=<?=base64_encode($_REQUEST['po_to'])?>" title="Export GRN detail  in excel"><i class="fa fa-file-excel-o fa-2x" title="Export GRN Detail excel"></i> GRN Detail</a></div>
               
			 <div class="col-md-4" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("summerizegrndata")?>&rheader=<?=base64_encode("Summerize GRN Data")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&loc=<?=base64_encode($_REQUEST['po_from'])?>&ven=<?=base64_encode($_REQUEST['po_to'])?>" title="Export summerize GRN  in excel"><i class="fa fa-file-excel-o fa-2x" title="Export summerize GRN data  in excel"></i> Summerize GRN Data </a></div>
             
             <div class="col-md-4" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("imeiGRNdata")?>&rheader=<?=base64_encode($head)?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&tloc=<?=base64_encode($_REQUEST['po_from'])?>&floc=<?=base64_encode($_REQUEST['po_to'])?>" title="Export GRN data in excel"><i class="fa fa-file-excel-o fa-2x" title="Export GRN data  in excel"></i><?=$imeitag?>GRN Data </a></div>
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