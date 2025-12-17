<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['doc_id']);

$sql_master = "SELECT * FROM claim_master WHERE claim_no='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);
?>

    <div class="panel-group">
    <div class="panel panel-info">
        <div class="panel-heading">Party Information</div>
        <div class="panel-body">
        <div class="form-group">
            <div class="col-md-6"><label class="col-md-5">Party Name</label>
                <div class="col-md-7">
                    <?=str_replace("~"," , ",getAnyDetails($row_master["party_id"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-4">Claim Type</label>
                <div class="col-md-7">
                    <?=$row_master["claim_type"]?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6 alert-warning"><label class="col-md-5">Claim No.</label>
                <div class="col-md-7">
                    <?=$row_master["claim_no"]?>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-4">Status</label>
                <div class="col-md-7">
                    <?=$row_master["status"]?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6"><label class="col-md-5">Entry By</label>
                <div class="col-md-7">
                    <?=getAnyDetails($row_master["entry_by"],"name","username","admin_users",$link1)." ".$row_master["entry_by"]?>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-4">Entry Date</label>
                <div class="col-md-7">
                    <?=$row_master["entry_date"]." ".$row_master["entry_time"]?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6 alert-success"><label class="col-md-5">Requested Claim Amount</label>
                <div class="col-md-7">
                    <?=$row_master["total_amount"]?>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-4">&nbsp;</label>
                <div class="col-md-7">
                    &nbsp;
                </div>
            </div>
        </div>
        
        </div>
        </div>
        <div class="panel panel-info table-responsive">
        <div class="panel-heading">Claim Summary</div>
        <div class="panel-body">

        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-bordered" width="100%" id="itemsTable2">
                    <thead>
                        <tr class="<?=$tableheadcolor?>" >
                            <th width="20%">Subject</th>
                            <th width="25%">Description</th>
                            <th width="15%">Date</th>
                            <th width="20%">Nos.</th>
                            <th width="20%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i=0;
                    $sql_data = "SELECT * FROM claim_data WHERE claim_no='".$docid."'";
                    $res_data = mysqli_query($link1,$sql_data);
                    while($row_data = mysqli_fetch_assoc($res_data)){
                    ?>
                    
                        <tr id="addr_claim<?=$i?>">
                            <td><input type="text" readonly class="form-control entername cp required" required name="claim_subject[<?=$i?>]" id="claim_subject[<?=$i?>]" value="<?=$row_data['claim_subject']?>"></td>
                            <td><textarea readonly class="form-control addressfield cp required" required name="claim_desc[<?=$i?>]" id="claim_desc[<?=$i?>]" style="resize:vertical"><?=$row_data['claim_desc']?></textarea></td>
                            <td><input readonly type="text" class="form-control required" required name="claim_date[<?=$i?>]" id="claim_date0" value="<?=$row_data['claim_date']?>"></td>
                            <td><input readonly type="text" class="form-control required digits" required name="claim_qty[<?=$i?>]" id="claim_qty[<?=$i?>]" value="<?=$row_data['qty']?>"></td>
                            <td><input readonly type="text" class="form-control required number" required name="claim_amt[<?=$i?>]" id="claim_amt[<?=$i?>]" value="<?=$row_data['amount']?>"></td>
                        </tr>
                    
                    <?php
                    $i++;
                    }
                    ?>
                    </tbody>
                </table>   
            </div>
        </div>

        
        </div>
        </div>
        <?php
        $res_poapp = mysqli_query($link1,"SELECT * FROM approval_activities where ref_no='".$docid."'")or die("ERR1".mysqli_error($link1)); 
        if(mysqli_num_rows($res_poapp)>0){
        ?>
        <div class="panel panel-info table-responsive">
            <div class="panel-heading">Approval History</div>
            <div class="panel-body">

            <div class="form-group">
                <div class="col-sm-12">
                    <table class="table table-bordered" width="100%" id="itemsTable3">
                        <thead>
                            <tr class="<?=$tableheadcolor?>" >
                                <th width="20%">Action Date & Time</th>
                                <th width="30%">Action Taken By</th>
                                <th width="20%">Action</th>
                                <th width="30%">Action Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while($row_poapp=mysqli_fetch_assoc($res_poapp)){
                            ?>
                              <tr>
                                <td><?php echo $row_poapp['action_date']." ".$row_poapp['action_time'];?></td>
                                <td><?php echo getAdminDetails($row_poapp['action_by'],"name",$link1);?></td>
                                <td><?php echo $row_poapp['req_type']." ".$row_poapp['action_taken']?></td>
                                <td><?php echo $row_poapp['action_remark']?></td>
                              </tr>
                            <?php
                            }
                            ?>  
                        </tbody>
                    </table>   
                </div>
            </div>
            </div> 
        </div>
        <?php 
        }
        ?>
        <div class="panel panel-info table-responsive">
        <div class="panel-heading">Supporting Document</div>
        <div class="panel-body">

        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-bordered" width="100%" id="itemsTable3">
                    <thead>
                        <tr class="<?=$tableheadcolor?>" >
                            <th width="30%">Document Name</th>
                            <th width="30%">Description</th>
                            <th width="40%">Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $j=0;
                        $sql_data_doc = "SELECT * FROM document_attachment WHERE ref_no='".$docid."'";
                        $res_data_doc = mysqli_query($link1,$sql_data_doc);
                        while($row_data_doc = mysqli_fetch_assoc($res_data_doc)){
                        ?>
                        <tr id="addr_doc<?=$j?>">
                            <td><input type="text" readonly class="form-control entername cp" name="document_name[<?=$j?>]"  id="document_name[<?=$j?>]" value="<?=$row_data_doc['document_name']?>"></td>
                            <td><input type="text" readonly class="form-control entername cp" name="document_desc[<?=$j?>]"  id="document_desc[<?=$j?>]" value="<?=$row_data_doc['document_desc']?>"></td>
                            <td><a href="<?=$row_data_doc['document_path']?>" target="_blank" class="btn <?=$btncolor?>" title="Attachment"><i class="fa fa-paperclip" title="Attachment"></i></a></td>
                        </tr>
                        <?php
                        $j++;
                        }
                        ?>
                    </tbody>
                </table>   
            </div>
        </div>
        
        </div>
        </div>
        </div>
