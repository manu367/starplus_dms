<?php
require_once("../config/config.php");
@extract($_GET);

function dt_format1($dt_sel) {
    return substr($dt_sel, 8, 2) . "-" . substr($dt_sel, 5, 2) . "-" . substr($dt_sel, 0, 4);
}

function time_format($t_sel) {
    return substr($t_sel, 11, 2) . '' . substr($t_sel, 13, 3) . ':' . substr($t_sel, 17, 3);
}
$today = date("Y-m-d");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="../js/jquery.min.js"></script>
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
<script>
 <link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
        <style>
            /* The Modal (background) */
            .modal {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                padding-top: 100px; /* Location of the box */
                left: 0;
                top: 0;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }

            /* Modal Content */
            .modal-content {
                background-color: #fefefe;
                margin: auto;
                padding: 20px;
                border: 1px solid #888;
                width: 33%;
                height: 50%;
                margin-top: 90px;
            }

            /* The Close Button */
            .close {
                color: #aaaaaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: #000;
                text-decoration: none;
                cursor: pointer;
            }
        </style>
    <title><?= siteTitle ?></title>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
                <h2 align="center"><i class="fa fa-lightbulb-o"></i> Leave Report </h2>
               
                <form class="form-horizontal" role="form" name="form1" action="" method="get">
 				<div class="form-group">
                        <div class="col-md-6"><label class="col-md-5 control-label">From Date</label>	  
                            <div class="col-md-5" align="left">
                               <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                                </div>
                            </div>
                        </div>                      
                        <div class="col-md-6"><label class="col-md-5 control-label">To Date</label>
                          <div class="col-md-5" align="left">	  
                              <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                                </div>
                            </div>
                        </div>
                       </div>
                       
                       
                     <div class="form-group">
                        <div class="col-md-6"><label class="col-md-5 control-label">Leave Status</label>	  
                            <div class="col-md-5" align="left">
                              <select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
                                    <option value="">Select Status</option>
                                    <option value="3">Pending for Approval</option>
                                    <option value="4">Approved</option> 
                                    <option value="5">Reject</option>
                                    
                                </select>
                            </div>
                        </div>                      
                        <div class="col-md-6"><label class="col-md-5 control-label"></label>
                          <div class="col-md-5" align="left">	  
                              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
                               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                            </div>
                        </div>
                       </div>  
                    <!--close form group-->
                    <?php if($_REQUEST['Submit']) {?>
                    <div class="form-group">
                        <div class="col-md-6">&nbsp;</div>
                        <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-5" align="left">
                                
                                    <a href="excelexport.php?rname=<?= base64_encode("leaveStatusReport") ?>&rheader=<?= base64_encode("Leave Details") ?>&status=<?= base64_encode($_REQUEST['status']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export leave details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export leave details in excel"></i></a>
                                   
                            </div>
                        </div>
                    </div><!--close form group-->
                    <?php }?>
                </form>
                
            </div>
        </div>
    </div>
    

    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
    
    
</body>
</html>