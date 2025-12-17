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
                padding-top: 50px; /* Location of the box */
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
                width: 50%;
                height: 50%;
                margin-top: 20px;
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
              <h2 align="center"><i class="fa fa-car"></i> Travel Report</h2>
              <form class="form-horizontal" role="form" name="form1" action="" method="get">
                <div class="form-group">
                  <div class="col-md-3">
                    <label class="col-md-4 control-label">Employee Name</label>
                    <div class="col-md-8" align="left">
                      <select name="username" id="username" class="form-control selectpicker" data-live-search="true"  onChange="document.form1.submit();">
                        <option value="">All</option>
                        <?php
						$sql = mysqli_query($link1, "Select name,username,oth_empid from admin_users where status='active' ORDER BY name");
						while ($row = mysqli_fetch_assoc($sql)) {
                                        ?>
                        <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['username'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="col-md-4 control-label">From Date</label>
                    <div class="col-md-8" align="left">
                      <div style="display:inline-block;float:left;">
                        <input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                      </div>
                      <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i> </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="col-md-4 control-label">To Date</label>
                    <div class="col-md-8" align="left">
                      <div style="display:inline-block;float:left;">
                        <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>"style="width:160px;" required>
                      </div>
                      <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i> </div>
                    </div>
                  </div>
                  <div class="col-md-1">
                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                    <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                  </div>
                </div>
                <!--close form group-->
                <div class="form-group">
                  <div class="col-md-6">&nbsp;</div>
                  <div class="col-md-6">
                    <label class="col-md-5 control-label"></label>
                    <div class="col-md-5" align="left">
                      <?php
                                //// get excel process id ////
                                //$processid = getExlCnclProcessid("Admin Users", $link1);
                                ////// check this user have right to export the excel report
                                //if (getExcelRight($_SESSION['userid'], $processid, $link1) == 1) {
                                    ?>
                      <a href="excelexport.php?rname=<?= base64_encode("travelReport") ?>&rheader=<?= base64_encode("Travel") ?>&user_id=<?= base64_encode($_REQUEST['username']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export user details in excel"></i></a>
                      <?php
                                //}
                                ?>
                    </div>
                  </div>
                </div>
                <!--close form group-->
              </form>
              <form class="form-horizontal table-responsive" role="form">
                <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                  <thead>
                    <tr class="<?=$tableheadcolor?>" >
                      <th width="18%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Employee Name</th>
                      <th width="11%" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>In Time</th>
                      <th width="21%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>IN Address</th>
                      <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>IN Image</th>
                      <th width="10%" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>OUT Time</th>
                      <th width="24%" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>OUT Address</th>
                      <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>OUT Image</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if($_REQUEST['Submit']!=""){
                            $fromd = date_format(date_create($_REQUEST['fdate']), "Y-m-d");
                            $tod = date_format(date_create($_REQUEST['tdate']), "Y-m-d");
                            $sqldata = "Select * from user_travel_plan where 1=1 ";
                            if ($_REQUEST['username'] != '') {
                                $sqldata.=" and user_id = '" . $_REQUEST['username'] . "'";
                            }
                            if ($_REQUEST['fdate'] != '' or $_REQUEST['tdate'] != '') {
                                $sqldata.=" and insert_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
                            }else{
								$sqldata.=" and insert_date BETWEEN '" . $today . "' and '" . $today . "'";
							}
							
                            $sqldata.=" order by id desc";
                            $sql = mysqli_query($link1, $sqldata);
                            $i=1;
                            while ($row = mysqli_fetch_assoc($sql)) {
                                $username = mysqli_fetch_assoc(mysqli_query($link1, "Select name,oth_empid from admin_users where username='" . $row['user_id'] . "'"));
                                ?>
                    <tr class="even pointer">
                      <td><?= $username['name']." | ".$row['user_id']." ".$username['oth_empid']; ?></td>
                      <td><?= ($row['in_datetime']); ?></td>
                      <td><?= $newstring = wordwrap($row['address_in'], 30, "<br>", 1); ?></td>
                      <td align='center' width='8%'><?php if ($row['Image_in'] != '') { ?>
                          <img src="../salesapi/travelimg/<?=substr($row["insert_date"],0,7)?>/<?=$row['Image_in'];?>" alt="" id="image<?=$i?>" onClick="getThisValue(<?=$i?>)" style="width: 100%;"/>
                        <?php } ?></td>
                      <td><?= ($row['out_datetime']); ?></td>
                      <td><?= $newstring = wordwrap($row['address_out'], 30, "<br>", 1); ?></td>
                      <td align='center' width='8%'><?php if ($row['Image_out'] != '') { ?>
                          <img src="../salesapi/travelimg/<?=substr($row["insert_date"],0,7)?>/<?= $row['Image_out']; ?>" alt="" id="image1<?=$i?>" onClick="getThisValue(<?='1'.$i?>)" style="width: 100%;"/>
                        <?php } ?></td>
                    </tr>
                    <?php $i++; }} ?>
                  </tbody>
                </table>
              </form>
            </div>
      </div>
</div>

    <!-- The Modal -->
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="img" style="text-align: center;"></p>
        </div>

    </div>

    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
    <script>
        $(document).ready(function() {
            $('#myTable').dataTable();
        });
        $(document).ready(function() {
            $('#attFromDate').datepicker({
                format: "yyyy-mm-dd",
                //startDate: "<?= $row['sale_date'] ?>",
                endDate: "<?= $today ?>",
                todayHighlight: true,
                autoclose: true
            });
            $('#attToDate').datepicker({
                format: "yyyy-mm-dd",
                //startDate: "<?= $row['sale_date'] ?>",
                endDate: "<?= $today ?>",
                todayHighlight: true,
                autoclose: true
            });
        });
    </script>
    <script>
// Get the modal
        var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
         function getThisValue(i) {
            var img = $("#image"+i).attr('src');
	
           $("#img").html('<img src="'+img+'" width="400px"/>');
            modal.style.display = "block";
        }

// When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

// When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
//        $(document).on('click',"#myBtn1",function(){
//           
//        });
    </script>
</body>
</html>