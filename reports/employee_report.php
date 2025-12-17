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
</script>

        
<title><?= siteTitle ?></title>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
                <h2 align="center"><i class="fa fa-users"></i> Employee Details Report </h2>
                <!--<?php if ($_REQUEST[msg]) { ?><br>
                                <h4 align="center" style="color:#FF0000"><?= $_REQUEST[msg] ?></h4>
                <?php } ?>-->
                <form class="form-horizontal" role="form" name="form1" action="" method="get">
                <br/>

                    <div class="form-group">
                        <div class="col-md-6"><label class="col-md-5 control-label">Employee Name</label>	  
                            <div class="col-md-5" align="left">
                                <select name="username" id="username" class="form-control"  onChange="document.form1.submit();">
                                    <option value="">Select Name</option>
                                    <?php
                                    $sql = mysqli_query($link1, "Select empname,loginid from hrms_employe_master ");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                        ?>
                                        <option value="<?= $row['loginid']; ?>" <?php
                                        if ($_REQUEST['username'] == $row['loginid']) {
                                            echo "selected";
                                        }
                                        ?>><?= $row['empname']; ?></option>
                                            <?php } ?>
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
                       
                       <?php if($_REQUEST['Submit']) {?>
                    <div class="form-group">
                        <div class="col-md-6">&nbsp;</div>
                        <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-5" align="left">
                                
                                    <a href="excelexport.php?rname=<?= base64_encode("employeedetails") ?>&rheader=<?= base64_encode("Employee Details") ?>&user_id=<?= base64_encode($_REQUEST['username']) ?>" title="Export emp details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export emp details in excel"></i></a>
                                   
                            </div>
                        </div>
                    </div><!--close form group-->
                    <?php } ?>
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