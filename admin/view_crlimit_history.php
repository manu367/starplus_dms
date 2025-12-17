<?php
require_once("../config/config.php");
$docid = $_REQUEST['doc_id'];
$po_sql = "SELECT * FROM credit_limit_history WHERE id='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$row1 = mysqli_fetch_assoc($po_res);
?>
<div class="panel-group">
    <div class="panel panel-success table-responsive">
        <div class="panel-heading">Requested Details</div>
        <div class="panel-body">
            <div class="form-group">
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Location Name</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row1["asc_code"],"name,city,state,asc_code","asc_code","asc_master",$link1));?></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>In Favour Of</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row1['parent_code'],"name,city,state,asc_code","asc_code","asc_master",$link1));?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Requested Limit</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=$row1["credit_limit"]?></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approved Limit</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=$row1["approved_limit"]?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Status</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><span class="text-danger"><?=$row1["status"]?></span></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Entry By</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row1['entry_by'],"name,oth_empid,username","username","admin_users",$link1));?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Entry Date</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=$row1["entry_date"]?></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Entry Remark</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=$row1["entry_remark"]?></div>
            </div>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-success table-responsive">
        <div class="panel-heading">Approval Details</div>
        <div class="panel-body">
            <div class="form-group">
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Status</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=$row1["app_status"]?></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approval By</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=str_replace("~",", ",getAnyDetails($row1['app_by'],"name,oth_empid,username","username","admin_users",$link1));?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approval Date</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=$row1["app_date"]?></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><strong>Approval Remark</strong></div>
                <div class="col-sm-3 col-md-3 col-lg-3"><?=$row1["app_remark"]?></div>
            </div>
        </div><!--close panel body-->
    </div><!--close panel-->
</div>