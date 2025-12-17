<?php
require_once("../config/config.php");
@extract($_GET);
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
        <script>
            $(document).ready(function() {
                $('#myTable').dataTable();
            });
            $(document).ready(function() {
                $('#fromDate').datepicker({
                    format: "yyyy-mm-dd",
                    //startDate: "<?= $row['sale_date'] ?>",
                    endDate: "<?= $today ?>",
                    todayHighlight: true,
                    autoclose: true
                });
                $('#toDate').datepicker({
                    format: "yyyy-mm-dd",
                    //startDate: "<?= $row['sale_date'] ?>",
                    endDate: "<?= $today ?>",
                    todayHighlight: true,
                    autoclose: true
                });
            });
        </script>
		<script>
 <link rel="stylesheet" href="../css/datepicker.css"></script>
<script src="../js/bootstrap-datepicker.js"></script>
        <title><?= siteTitle ?></title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9 tab-pane fade in active" id="home">
                    <h2 align="center"><i class="fa fa-users"></i> ISP Report</h2>
                    <!--<?php if ($_REQUEST[msg]) { ?><br>
                                <h4 align="center" style="color:#FF0000"><?= $_REQUEST[msg] ?></h4>
                    <?php } ?>-->
                    <form class="form-horizontal" role="form" name="form1" action="" method="get">

                        <div class="form-group">                         
                            <div class="col-md-5"><label class="col-md-4 control-label">From Date</label>	  
                                <div class="col-md-8" align="left">
                                    <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="fromDate" value="<?= $_REQUEST['fromDate'] ? $_REQUEST['fromDate'] : ''; ?>" id="fromDate" onChange="document.form1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5"><label class="col-md-4 control-label">To Date</label>	  
                                <div class="col-md-8" align="left">
                                    <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="toDate" value="<?= $_REQUEST['toDate'] ? $_REQUEST['toDate'] : ''; ?>" id="toDate" onChange="document.form1.submit();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
                            </div>
                        </div><!--close form group-->
                        <div class="form-group">
                            <div class="col-md-6">&nbsp;</div>
                            <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                                <div class="col-md-5" align="left">
                                    <?php
                                    //// get excel process id ////
                                    $processid = getExlCnclProcessid("Admin Users", $link1);
                                    ////// check this user have right to export the excel report
                                    if (getExcelRight($_SESSION['userid'], $processid, $link1) == 1) {
                                        ?>
                                        <a href="excelexport.php?rname=<?= base64_encode("isdReport") ?>&rheader=<?= base64_encode("ISD") ?>&fromDate=<?= base64_encode($_REQUEST['fromDate']) ?>&toDate=<?= base64_encode($_REQUEST['toDate']) ?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export user details in excel"></i></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div><!--close form group-->
                    </form>
                    <form class="form-horizontal table-responsive" role="form">
                        <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                            <thead>
                                <tr>   
                                    <th>S.NO.</th>
                                    <th>ISP Name</th>
                                    <th><?=$imeitag?></th>
                                    <th>Customer Name</th>
                                    <th>Location</th>
                                    <th>Contact No.</th>
                                    <th>State</th>
                                    <th>City</th>                                    
                                    <th>Entry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $fromd = date_format(date_create($_REQUEST['fromDate']), "Y-m-d");
                                $tod = date_format(date_create($_REQUEST['toDate']), "Y-m-d");
                                $sqldata = "Select * from sale_data where 1=1";
                                if ($_REQUEST['fromDate'] != '' or $_REQUEST['toDate'] != '') {
                                    $sqldata.=" and sync_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
                                }
                                $sqldata.=" order by id desc";
                                $sql = mysqli_query($link1, $sqldata);
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($sql)) {
                                    $sql1 = mysqli_query($link1, "select owner_code from billing_imei_data where imei1 = '" . $row['imei'] . "' group by imei1 order by id desc")or die(mysql_error());
                                    $location = mysqli_fetch_assoc($sql1);
                                    $sql2 = mysqli_query($link1, "select name from asc_master where asc_code = '" . $location['owner_code'] . "'")or die(mysql_error());
                                    $lname = mysqli_fetch_assoc($sql2);
                                    $sql3 = mysqli_query($link1, "select name from admin_users where username = '" . $row['user_name'] . "'")or die(mysql_error());
                                    $name = mysqli_fetch_assoc($sql3);
                                    ?>
                                    <tr class="even pointer">
                                        <td><?= $i; ?></td>
                                        <td><?=$name['name']?></td>
                                        <td><?= $row['imei']; ?></td>
                                        <td><?= $row['cust_name']; ?></td> 
                                        <td><?=$lname['name']?></td>
                                        <td width='15%'><?= $row['contact_no']; ?></td>
                                        <td><?= $row['state']; ?></td>
                                        <td><?= $row['city']; ?></td>
                                        <td><?php echo date_format(date_create($row['sync_date']), "d-m-Y"); ?></td>
                                    </tr>
                                    <?php $i++;
                                } ?>
                            </tbody>
                        </table>
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