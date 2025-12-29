<?php
require_once("../config/config.php");
exec("php ./practice/Baisc.php > /dev/null 2>&1 &");



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
    <link rel="icon" type="image/x-icon" href="../img/inner_logo.png">
    <title>
        <?=siteTitle?>
    </title>
    <style>

        /* ========== SIDE PING CORE ========== */

        .ping-wrap {
            position: relative;
            display: inline-block;
            padding-right: 16px;
        }

        .side-ping {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 10px;
            height: 10px;
            background: #f80a2d;
            border-radius: 50%;
        }

        /* ghost wave */
        .side-ping::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: rgb(244, 7, 7);
            animation: sidePing 1.4s cubic-bezier(0,0,.2,1) infinite;
        }

        @keyframes sidePing {
            0%   { transform: scale(1);   opacity: 1; }
            75%,
            100% { transform: scale(2.5); opacity: 0; }
        }

        /* motion respect */
        @media (prefers-reduced-motion: reduce) {
            .side-ping::after { animation: none; }
        }

    </style>
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
                                <?php
                                $sql="SELECT * FROM status_request";
                                $res = mysqli_query($link1,$sql);
                                while($row = mysqli_fetch_array($res)){
                                    ?>
                                    <option value="<?=$row['name']?>">
                                        <?=$row['name']?>
                                    </option>
                                    <?php
                                }
                                ?>
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
                            <th>Customer Name</th>
                            <th>Customer Mobiel</th>
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
                                <td>
                                    <?php if($row['status']=='Draft'): ?>

                                        <span style="background: black;text-align: center" class="badge ping-wrap">
        <?= htmlspecialchars($row['status']) ?>
        <span class="side-ping"></span>
    </span>

                                    <?php elseif($row['status']=='Pending'): ?>
                                        <span class="label label-warning"><?= htmlspecialchars($row['status']) ?></span>

                                    <?php elseif($row['status']=='Approved'): ?>
                                        <span class="label label-success"><?= htmlspecialchars($row['status']) ?></span>

                                    <?php elseif($row['status']=='Cancelled'): ?>
                                        <span class="label label-danger"><?= htmlspecialchars($row['status']) ?></span>

                                    <?php else: ?>
                                        <span class="label label-danger"><?= htmlspecialchars($row['status']) ?></span>
                                    <?php endif; ?>
                                </td>



                                <!-- Installation Date -->
                                <td><?= htmlspecialchars($row['installation_date']) ?></td>
                                <td><?= htmlspecialchars($row['product_code']) ?></td>
                                <!-- Serial No -->
                                <td><?= htmlspecialchars($row['serial_no']) ?></td>
                                <td><?= htmlspecialchars($row['customer_Name']) ?></td>
                                <td><?= htmlspecialchars($row['mobile_no']) ?></td>

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

<!-- IMAGE PREVIEW MODAL -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Installation Image</h4>
            </div>

            <div class="modal-body text-center">
                <img id="showImage"
                     src=""
                     class="img-responsive center-block"
                     style="max-height:80vh;"
                     alt="Installation Image">
            </div>

        </div>
    </div>
</div>
<script>
    function openImageModel(img) {
        var basePath = "";
        $("#showImage").attr("src",img);
        $("#imageModal").modal("show");
    }
</script>


<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>