    <?php
    
    //tadaApprovalPage.php?id=RVhQLzIwMjUwOTAzL1NVVVNSMDYxLzAwMDE=&pid=51&hid=FN10
    require_once("../config/config.php");
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: installations_register.php?pid=154&hid=FN10&msg=Invalid  value");
        exit();
    }
    
    $id = intval($_GET['id']); // 🔒 basic security
    $sql = "SELECT ind.*, au.name , au.phone as au_phone , au.emailid as au_email FROM installation_data ind LEFT JOIN admin_users au ON ind.userid = au.username WHERE ind.id = $id LIMIT 1";
    //var_dump($sql);exit();
    $res = mysqli_query($link1, $sql);
    $recordFound = true;
    if (mysqli_num_rows($res) == 0) {
        $recordFound = false;
    }else{
        $row = mysqli_fetch_assoc($res);
    }
    
    ?>

    <?php
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        if (!isset($_POST['actiontaken'])) {
            header("Location: installations_register.php?pid=154&hid=FN10&msg=Invalid action value");
            exit();
        }
        // 🔒 Remark validation
        if (empty($_POST['remark'])) {
            header("Location: installations_register.php?pid=154&hid=FN10&msg=Invalid remark value");
            exit();
        }
    
        $actiontaken = $_POST['actiontaken'];
        $remark      = trim($_POST['remark']);
    
        //  Validate action value
        if ($actiontaken !== "1" && $actiontaken !== "2") {
            header("Location: installations_register.php?pid=154&hid=FN10&msg=Invalid action value");
            exit();
        }
    
        //  Minimum remark length
        if (strlen($remark) < 5) {
            header("Location: installations_register.php?pid=154&hid=FN10&msg=Remark is too short");
            exit();
        }
    
    
        $action = ($actiontaken === "1") ? "Approved" : "Rejected";
        $remark = mysqli_real_escape_string($link1, $remark);
        $approveBy = $_SESSION['userid'] ?? 'system';
    
        $approvedDate = date('Y-m-d');   // 2025-12-18
        $approvedTime = date('H:i:s');
        // 🔥 UPDATE QUERY
        $sqlupdate = "
            UPDATE installation_data 
            SET status = '$action' ,
                remark = '$remark',
                approve_by = '$approveBy',
                approved_date = '$approvedDate',
             approved_time = '$approvedTime'
            WHERE id = $id 
            LIMIT 1
        ";

        if (mysqli_query($link1, $sqlupdate)) {
    
            if (mysqli_affected_rows($link1) > 0) {
                header("Location: installations_register.php?pid=154&hid=FN10&msg=Status updated successfully");
                exit();
            } else {
                header("Location: installations_register.php?pid=154&hid=FN10&msg=No change detected");
                exit();
            }
    
        } else {
            header("Location: installations_register.php?pid=154&hid=FN10&msg=No Record found");
            exit();
        }
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
    
        <style>
            :root{
                user-select: none;
            }
            #dragRow {
                display: flex;
                flex-wrap: nowrap;
                gap: 15px;
            }
    
            .draggable-card {
                cursor: grab;
            }
    
            .drag-handle {
                cursor: grab;
                user-select: none;
            }
    
            .drag-ghost {
                opacity: 0.4;
            }
    
            .drag-chosen {
                cursor: grabbing;
            }
            .panel {
                transition: box-shadow .2s ease;
            }
            .panel:hover {
                box-shadow: 0 8px 20px rgba(0,0,0,.12);
            }
        </style>
    
    
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    
        <script>
            document.addEventListener("DOMContentLoaded", function () {
    
                var row = document.getElementById("dragRow");
                if (!row) return;
    
                new Sortable(row, {
                    animation: 200,
                    direction: 'horizontal',        // 🔒 LEFT ↔ RIGHT ONLY
                    draggable: '.draggable-card',
                    handle: '.drag-handle',         // 🔥 header se drag
                    forceFallback: true,
                    fallbackTolerance: 5
                });
    
            });
        </script>
    
    
    
    </head>
    <body>
    
    <div class="container-fluid">
        <div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <?php if(!$recordFound): ?>
                <div class="col-sm-9">
                    <div class="row" style="margin-top:60px;">
                        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
    
                            <div class="panel panel-default text-center" style="border-top:4px solid #d9534f;">
                                <div class="panel-body" style="padding:40px 20px;">
    
                                    <div style="font-size:60px;color:#d9534f;margin-bottom:15px;">
                                        <i class="fa fa-file-text-o"></i>
                                    </div>
    
                                    <h3 style="margin-top:0;font-weight:600;color:#333;">
                                        Installation Record Not Found
                                    </h3>
    
                                    <p style="color:#777;font-size:15px;max-width:420px;margin:15px auto;">
                                        We couldn’t find the installation record you’re looking for.
                                        It may have been deleted, moved, or the link might be incorrect.
                                    </p>
    
                                    <hr style="max-width:120px;margin:25px auto;">
    
                                    <a href="installations_register.php?pid=154&hid=FN10"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-arrow-left"></i> Back to Installations
                                    </a>
    
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
                <?php return; endif; ?>
            <div class="col-sm-9">
    
                <!-- ================= PAGE HEADER ================= -->
                <div class="text-center" style="margin-bottom:20px;">
                    <h3 style="margin-bottom:5px;">
                        <i class="fa fa-check-circle text-success"></i> Installation Approval
                    </h3>
                    <span class="label label-default">
                Document No: <?= htmlspecialchars($row['document_no']) ?>
            </span>
                    <p style="margin-top:8px;color:#777;">
                        Entry on <?= $row['entry_date'] ?> at <?= $row['entry_time'] ?>
                    </p>
                </div>
    
                <!-- ================= ENGINEER + CUSTOMER ================= -->
                <div class="row equal-height" id="dragRow">
    
                    <!-- ================= ENGINEER ================= -->
                    <div class="col-md-6 draggable-card" data-card="engineer">
                        <div class="panel panel-primary">
    
                            <div class="panel-heading drag-handle">
                                <i class="fa fa-arrows"></i>
                                <i class="fa fa-user"></i> Engineer Details
                            </div>
    
                            <div class="panel-body">
    
                                <?php
                                $profileImg = (!empty($row['profile_img_path']))
                                        ? $row['profile_img_path']
                                        : "../img/user.png";
                                ?>
    
                                <div class="row">
                                    <div class="col-sm-4 text-center">
                                        <img src="<?= $profileImg ?>"
                                             class="img-thumbnail"
                                             style="width:120px;height:120px;">
                                    </div>
    
                                    <div class="col-sm-8">
                                        <table class="table table-bordered table-condensed">
                                            <tr><th>User ID</th><td><?= $row['userid'] ?></td></tr>
                                            <tr><th>Name</th><td><?= $row['name'] ?></td></tr>
                                            <tr><th>Phone</th><td><?= $row['au_phone']==''?'+91XXXXXXXXXX':$row['au_phone'] ?></td></tr>
                                            <tr><th>Email</th><td><?= $row['au_email']==''?'xxxx@gmail.com':$row['au_email'] ?></td></tr>
                                            <tr><th>City</th><td><?= $row['city'] ?></td></tr>
                                            <tr><th>State</th><td><?= $row['state'] ?></td></tr>
                                            <tr>
                                                <th>Address</th>
                                                <td><?= $row['address'] ?> - <?= $row['pincode'] ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
    
                            </div>
                        </div>
                    </div>
    
                    <!-- ================= CUSTOMER ================= -->
                    <div class="col-md-6 draggable-card" data-card="customer">
                        <div class="panel panel-success">
    
                            <div class="panel-heading drag-handle">
                                <i class="fa fa-arrows"></i>
                                <i class="fa fa-home"></i> Customer Details
                            </div>
    
                            <div class="panel-body">
                                <table class="table table-bordered table-condensed">
                                    <tr><th width="35%">Customer Name</th><td><?= $row['customer_Name'] ?></td></tr>
                                    <tr><th>Mobile</th><td><?= $row['mobile_no'] ?></td></tr>
                                    <tr><th>Email</th><td><?= $row['email'] ?></td></tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>
                                            <?= $row['address'] ?>,
                                            <?= $row['city'] ?>,
                                            <?= $row['state'] ?> - <?= $row['pincode'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Installation Date</th>
                                        <td><?= $row['installation_date'] ?></td>
                                    </tr>
                                </table>
                            </div>
    
                        </div>
                    </div>
    
                </div>
    
    
                <!-- ================= PRODUCT DETAILS ================= -->
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-cube"></i> Product Details
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Product Code</th>
                                <th>Serial No</th>
                                <th>Date of Purchase</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $row['invoice_no'] ?></td>
                                <td><?= $row['product_code'] ?></td>
                                <td><?= $row['serial_no'] ?></td>
                                <td><?= $row['dop'] ?></td>
                                <td>
                            <span style="border-radius: 50px" class="label
                                <?= ($row['status']=='Approved')?'label-success':(($row['status']=='Pending')?'label-warning':'label-danger') ?>">
                                <?= $row['status'] ?>
                            </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

<!--                start image section-->
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-image"></i> Installation Images
                            </div>

                            <div class="panel-body">
                                <table class="table table-bordered table-condensed text-center">
                                    <thead>
                                    <tr>
                                        <th width="40%">Type</th>
                                        <th>Image</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <!-- PRODUCT IMAGE -->
                                    <tr>
                                        <td><strong>Product</strong></td>
                                        <td>
                                            <?php if (!empty($row['img_product'])): ?>
                                                <img
                                                        src="../installation_uploads/2025-12/<?= htmlspecialchars($row['img_product']) ?>"
                                                        width="100"
                                                        height="100"
                                                        class="img-thumbnail"
                                                        style="object-fit:cover;cursor:pointer;"
                                                        onerror="handleImageError(this)"
                                                        onclick="openImageModal(this.src)">
                                            <?php else: ?>
                                                <span class="text-muted">No image found</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- INSTALLATION IMAGE -->
                                    <tr>
                                        <td><strong>Installation</strong></td>
                                        <td>
                                            <?php if (!empty($row['img_installation'])): ?>
                                                <img
                                                        src="../installation_uploads/2025-12/<?= htmlspecialchars($row['img_installation']) ?>"
                                                        width="100"
                                                        height="100"
                                                        class="img-thumbnail"
                                                        style="object-fit:cover;cursor:pointer;"
                                                        onerror="handleImageError(this)"
                                                        onclick="openImageModal(this.src)">
                                            <?php else: ?>
                                                <span class="text-muted">No image found</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- SHEET IMAGE -->
                                    <tr>
                                        <td><strong>Sheet</strong></td>
                                        <td>
                                            <?php if (!empty($row['img_sheet'])): ?>
                                                <img
                                                        src="../installation_uploads/2025-12/<?= htmlspecialchars($row['img_sheet']) ?>"
                                                        width="100"
                                                        height="100"
                                                        class="img-thumbnail"
                                                        style="object-fit:cover;cursor:pointer;"
                                                        onerror="handleImageError(this)"
                                                        onclick="openImageModal(this.src)">
                                            <?php else: ?>
                                                <span class="text-muted" style="color: red;">No image found</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>



                <!--                end image-->
                <?php if ($row['status'] !== 'Pending'): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-comment"></i> Approval Remark
                        </div>
                        <div class="panel-body">
    
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <th width="25%">Status</th>
                                    <td>
                            <span style="border-radius: 60px;" class="label
                                <?= ($row['status']=='Approved')?'label-success':'label-danger' ?>">
                                <?= $row['status'] ?>
                            </span>
                                    </td>
                                </tr>
    
                                <tr>
                                    <th>Remark</th>
                                    <td style="white-space:pre-line;">
                                        <?= !empty($row['remark'])
                                                ? htmlspecialchars($row['remark'])
                                                : '<span class="text-muted">No remark available</span>' ?>
                                    </td>
                                </tr>
    
                                <tr>
                                    <th>Approved By</th>
                                    <td><?= htmlspecialchars($row['approve_by']==''?'System':$row['approve_by']) ?></td>
                                </tr>
                            </table>
    
                        </div>
                    </div>
                <?php endif; ?>
    
    
                <!-- ================= APPROVAL ACTION ================= -->
                <?php if ($row['status'] === 'Pending'): ?>
                    <form method="post" onsubmit="return formSubmitValidate()">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <i class="fa fa-gavel"></i> Approval Action
                            </div>
    
                            <div class="panel-body">
                                <div class="row">
    
                                    <!-- 🔹 LEFT : REMARK -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                Remark <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="remark"
                                                      class="form-control"
                                                      rows="4"
                                                      id="remark"
                                                      placeholder="Enter approval / rejection remark..."
                                                      required></textarea>
                                        </div>
                                    </div>
    
                                    <!-- 🔹 RIGHT : ACTION -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                Select Action <span class="text-danger">*</span>
                                            </label>
                                            <select name="actiontaken"
                                                    id="actiontaken"
                                                    class="form-control"
                                                    required>
                                                <option value="0">-- Select Action --</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div>
    
                                </div>
    
                                <hr>
    
                                <!-- 🔘 BUTTONS -->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success" >
                                        <i class="fa fa-check"></i> Update Status
                                    </button>
    
                                    <a href="installations_register.php?pid=154&hid=FN10"
                                       class="btn btn-default">
                                        Back
                                    </a>
                                </div>
    
                            </div>
                        </div>
                    </form>
                    <script>
                        function formSubmitValidate(){
    
                            let remark = document.getElementById("remark").value;
                            let actiontaken = document.getElementById("actiontaken").value;
    
                            if(actiontaken == "0"){
                                alert("Please select an option");
                                return false; // ❌ form submit stop
                            }
    
                            if(remark.trim().length < 5){
                                alert("Please enter valid remark (minimam 5 chars)");
                                return false; // ❌ form submit stop
                            }
    
                            // ✅ sab kuch sahi
                            return true; // form submit allowed
                        }
                    </script>
    
                <?php else: ?>
                    <div class="text-center">
                        <a href="installations_register.php?pid=154&hid=FN10"
                           class="btn btn-primary">
                            Back to List
                        </a>
                    </div>
                <?php endif; ?>
    
    
            </div>
    
            <!--close col-sm-9-->
        </div><!--close row content-->
    </div><!--close container-fluid-->
    
    
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>

    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Full Image</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body text-center">
                    <img
                            id="modalPreviewImage"
                            src=""
                            style="max-width:100%;max-height:80vh;">
                </div>

            </div>
        </div>
    </div>
    <script>
        function openImageModal(src) {
            document.getElementById("modalPreviewImage").src = src;
            $('#imagePreviewModal').modal('show');
        }
    </script>

    <script>
        function handleImageError(img) {
            // remove broken image
            img.style.display = 'none';

            // create text node
            const text = document.createElement('span');
            text.className = 'text-danger';
            text.innerText = 'Image not found';

            // insert text in same place
            img.parentNode.appendChild(text);
        }
    </script>

    </body>
    </html>