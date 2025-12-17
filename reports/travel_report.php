<?php
require_once("../config/config.php");
@extract($_POST);

function dt_format1($dt_sel) {
    return substr($dt_sel, 8, 2) . "-" . substr($dt_sel, 5, 2) . "-" . substr($dt_sel, 0, 4);
}

function time_format($t_sel) {
    return substr($t_sel, 11, 2) . '' . substr($t_sel, 13, 3) . ':' . substr($t_sel, 17, 3);
}
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$team = getTeamMembers($_SESSION['userid'],$link1);
	if($team){
		$team = $team.",'".$_SESSION['userid']."'"; 
	}else{
		$team = "'".$_SESSION['userid']."'"; 
	}
}
///////filter value
if($department){
	$dept = " AND department ='".$department."'";
	$deptqry = " AND b.department ='".$department."'";
}else{
	$dept = "";
	$deptqry = "";
}
if($subdepartment){
	$subdept = " AND subdepartment ='".$subdepartment."'";
	$subdeptqry = " AND b.subdepartment ='".$subdepartment."'";
}else{
	$subdept = "";
	$subdeptqry = "";
}
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	if($username){
		$team2 = getTeamMembers($username,$link1);
		if($team2){
			$team2 = $team2.",'".$username."'"; 
		}else{
			$team2 = "'".$username."'"; 
		}
		$user_id = " AND b.username IN (".$team2.")";
	}else{
		$user_id = " ";
	}
}else{
	if($username){
		$team3 = getTeamMembers($username,$link1);
		if($team3){
			$team3 = $team3.",'".$username."'"; 
		}else{
			$team3 = "'".$username."'"; 
		}
		$user_id = " AND b.username IN (".$team3.")";
	}else{
		$user_id = " AND b.username IN (".$team.")";
	}
}
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
		endDate: "<?= $today ?>",
        todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?= $today ?>",
        todayHighlight: true,
		autoclose: true
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css">
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
              <form class="form-horizontal" role="form" name="form1" action="" method="post">
              	<div class="row">
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">From Date</label>
                        <input type="text" class="form-control span2" name="fdate"  id="fdate" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $today;}?>" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">To Date</label>
                        <input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $today;}?>" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Department</label>
                        <select name='department' id='department' class="form-control" onChange="document.form1.submit();">
                            <option value="">All</option>
                            <?php
                            $res_dept=mysqli_query($link1,"select * from hrms_department_master where status='1' order by dname")or die("erro1".mysqli_error($link1));
                            while($row_dept=mysqli_fetch_assoc($res_dept)){
                            ?>
                            <option value="<?=$row_dept['departmentid']?>"<?php if($_REQUEST['department'] ==$row_dept['departmentid']) { echo 'selected'; }?>><?=$row_dept['dname'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Sub-Department</label>
                    	<span id="subdptdiv">
                        <select name='subdepartment' id='subdepartment' class="form-control" onChange="document.form1.submit();">
                            <option value="">All</option>
                            <?php
                            $res_sdept=mysqli_query($link1,"select * from hrms_subdepartment_master where status='1' AND departmentid='".$_REQUEST['department']."' order by department,subdept")or die("erro1".mysqli_error($link1));
                            while($row_sdept=mysqli_fetch_assoc($res_sdept)){
                            ?>
                            <option value="<?=$row_sdept['subdeptid']?>"<?php if($_REQUEST['subdepartment'] ==$row_sdept['subdeptid']) { echo 'selected'; }?>><?=$row_sdept['subdept'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </span>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Employee Name</label>
                        <select name="username" id="username" class="form-control selectpicker" data-live-search="true"  onChange="document.form1.submit();">
                        <option value="">All</option>
                        <?php
						if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
							$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' AND oth_empid!='' ".$dept." ".$subdept."  ORDER BY name");
						}else{
							$sql = mysqli_query($link1, "SELECT name,username,oth_empid FROM admin_users WHERE status='active' AND username IN (".$team.") ".$dept." ".$subdept." ORDER BY name");
						}
						while ($row = mysqli_fetch_assoc($sql)) {
                                        ?>
                        <option value="<?= $row['username']; ?>" <?php if ($_REQUEST['username'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
                        <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                        <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                        <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><br/>
                        <a href="excelexport.php?rname=<?= base64_encode("travelReport") ?>&rheader=<?= base64_encode("Travel") ?>&user_id=<?= base64_encode($_REQUEST['username']) ?>&fromDate=<?= base64_encode($_REQUEST['fdate']) ?>&toDate=<?= base64_encode($_REQUEST['tdate']) ?>&department=<?=base64_encode($_REQUEST['department'])?>&subdepartment=<?=base64_encode($_REQUEST['subdepartment'])?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a>
                        
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3"><br/>
                    	
                    </div>
                  </div>
              </form>
              <form class="form-horizontal table-responsive" role="form">
                <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                  <thead>
                    <tr class="<?=$tableheadcolor?>" >
                      <th width="18%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Employee Name</th>
                      <th width="11%" data-hide="phone">Department</th>
                      <th width="11%" data-hide="phone">Sub Department</th>
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
							$fromd = $_REQUEST['fdate'];
                            $tod = $_REQUEST['tdate'];
                            $sqldata = "SELECT a.*, b.name, b.oth_empid, b.department, b.subdepartment FROM user_travel_plan a, admin_users b WHERE 1=1 AND a.user_id=b.username ".$subdeptqry." ".$deptqry." ".$user_id;
                            if ($_REQUEST['fdate'] != '' or $_REQUEST['tdate'] != '') {
                                $sqldata.=" AND a.insert_date BETWEEN '" . $fromd . "' and '" . $tod . "'";
                            }else{
								$sqldata.=" AND a.insert_date BETWEEN '" . $today . "' and '" . $today . "'";
							}
							
                            $sqldata.=" ORDER BY a.id DESC";
                            $sql = mysqli_query($link1, $sqldata);
                            $i=1;
                            while ($row = mysqli_fetch_assoc($sql)) {
                                ?>
                    <tr class="even pointer">
                       <td><?= $row['name']." | ".$row['user_id']." ".$row['oth_empid']; ?></td>
                       <td><?=getAnyDetails($row["department"],"dname","departmentid","hrms_department_master",$link1)?></td>
                      <td><?=getAnyDetails($row["subdepartment"],"subdept","subdeptid","hrms_subdepartment_master",$link1)?></td>
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