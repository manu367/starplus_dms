<?php
require_once("../config/config.php");

$where = " WHERE 1=1 ";

// 🔹 From Date
if (!empty($_REQUEST['fdate'])) {
    $fromdate = $_REQUEST['fdate'];
    $where .= " AND ind.installation_date >= '$fromdate'";
}

// 🔹 To Date
if (!empty($_REQUEST['tdate'])) {
    $todate = $_REQUEST['tdate'];
    $where .= " AND ind.installation_date <= '$todate'";
}

// 🔹 User ID
if (!empty($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
    $where .= " AND ind.userid = '$userid'";
}

// 🔹 Status
if (!empty($_REQUEST['status'])) {
    $status = $_REQUEST['status'];
    $where .= " AND ind.status = '$status'";
}

// ✅ USE $where HERE
$sql = "
SELECT ind.*, au.name
FROM installation_data ind
LEFT JOIN admin_users au 
    ON ind.userid = au.username
$where
ORDER BY ind.installation_date DESC
";
$result = mysqli_query($link1, $sql);

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="../js/jquery-1.10.1.min.js"></script>
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
        $(document).ready(function () {
            $('#myTable').dataTable();
            ////// from date
            const today = new Date();
            $('#fdate').datepicker({
                format: "yyyy-mm-dd",
                todayHighlight: true,
                autoclose: true,
                endDate: today
            });
            /////// to date
            $('#tdate').datepicker({
                format: "yyyy-mm-dd",
                todayHighlight: true,
                autoclose: true,
                endDate: today
            });
        });
    </script>
    <link rel="stylesheet" href="../css/datepicker.css">
    </script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <title>
        <?=siteTitle?>
    </title>
</head>

<body>
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
            <h2 align="center"><i class="fa fa-address-card"></i> Installation Approval</h2>
            <?php if($_REQUEST['msg']){?><br>
                <h4 align="center" style="color:#FF0000">
                    <?=$_REQUEST['msg']?>
                </h4>
            <?php }?>

            <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
                <div class="form-group">
                    <div class="col-sm-6 col-md-6 col-lg-6"><label
                            class="col-sm-5 col-md-5 col-lg-5 control-label">Installation From</label>
                        <div class="col-sm-5 col-md-5 col-lg-5 input-append date">
                            <div style="display:inline-block;float:left;">
                                <input type="text" class="form-control span2" name="fdate" autocomplete="off" id="fdate"
                                       style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo "";}?>">
                            </div>
                            <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label">Installation To</label>
                        <div class="col-md-5 input-append date">
                            <div style="display:inline-block;float:left;">
                                <input type="text"
                                       class="form-control span2" name="tdate" autocomplete="off" id="tdate"
                                       value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo "";}?>"
                                       style="width:160px;">
                            </div>
                            <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div><!--close form group-->
                <div class="form-group">
                    <div class="col-sm-6 col-md-6 col-lg-6"><label
                            class="col-sm-5 col-md-5 col-lg-5 control-label">Enginner Name</label>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <select name='user_id' id="user_id" class='form-control selectpicker'
                                    data-live-search="true">
                                <option value=''>All</option>
                                <?php
                                if($_SESSION["userid"]=="admin"){
                                    $sql = "SELECT username,name,oth_empid FROM admin_users where create_by='App'";
                                }else{
                                    $sql = "SELECT username,name,oth_empid FROM admin_users where 1 AND create_by='App'";
                                }
                                $res = mysqli_query($link1,$sql);
                                while($row = mysqli_fetch_array($res)){
                                    ?>
                                    <option value="<?=$row['username']?>" <?php
                                    if($_REQUEST['user_id']==$row['username']){echo 'selected' ;}?>>
                                        <?=$row['name']." | ".$row['username']." ".$row['oth_empid']?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
                        <div class="col-md-4">
                            <select name='status' id="status" class='form-control'>
                                <option value=''>--Select Option--</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO" title="Go!">
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>" />
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>" />
                        </div>
                    </div>
                </div><!--close form group-->
            </form>
            <a href="../excelReports/installation_export_excel.php?fdate=<?= urlencode($_REQUEST['fdate'] ?? '') ?>&tdate=<?= urlencode($_REQUEST['tdate'] ?? '') ?>&user_id=<?= urlencode($_REQUEST['user_id'] ?? '') ?>&status=<?= urlencode($_REQUEST['status'] ?? '') ?>"
               class="btn btn-success btn-sm"
               style="margin-bottom:10px;">
                <i class="fa fa-file-excel-o"></i> Download Sheet
            </a>


            <form class="form-horizontal" role="form">
                <div class="form-group" id="page-wrap" style="margin-left:10px;"><br /><br />
                    <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                        <tr class="<?=$tableheadcolor?>">
                            <th>Sr No</th>
                            <th>Technican ID</th>
                            <th>Technican Name</th>
                            <th>Status</th>
                            <th>Installation Date</th>
                            <th>Product</th>
                            <th>Serial No</th>
                            <th>image</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sno = 0;

                        while ($row = mysqli_fetch_assoc($result)) {
                            $sno++;
                            ?>
                            <tr>
                                <!-- Sr No -->
                                <td><?= $sno ?></td>

                                <!-- User ID -->
                                <td><?= htmlspecialchars($row['userid']) ?></td>
                                <!-- Name -->
                                <td><?= htmlspecialchars($row['name']) ?></td>


                                <!-- Status -->
                                <td><span style="border-radius: 50px; padding: 10px" class="label
                                <?php
                                    if($row['status']=='Pending') echo 'label-warning';
                                    elseif($row['status']=='Approved') echo 'label-success';
                                    else echo 'label-danger'; ?>">
                                        <?= htmlspecialchars($row['status'])
                                        ?>
                                        <span style="margin-left: 5px;color: black">X</span>
                                    </span>
                                </td>

                                <!-- Installation Date -->
                                <td><?= htmlspecialchars($row['installation_date']) ?></td>
                                <td><?= htmlspecialchars($row['product_code']) ?></td>
                                <!-- Serial No -->
                                <td><?= htmlspecialchars($row['serial_no']) ?></td>

                                <td><img src="<?= "../installation_uploads/2025-12/".htmlspecialchars($row['img_url']==''?'693a5bc131810_sheet.jpg':$row['img_url']) ?>" width="50" height="50"/></td>
                                <td align="center">
                                    <a href="installation_view.php?id=<?= urlencode($row['id']) ?>"
                                       class="btn btn-xs btn-info"
                                       title="View Installation">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>

                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
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