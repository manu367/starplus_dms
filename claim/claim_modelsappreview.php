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
            <div class="col-md-6"><label class="col-md-5">Doc.Type</label>
                <div class="col-md-7">
                    <?=$row_master["claim_type"]?>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5">Company Code</label>
                <div class="col-md-7">
                    <?=$row_master["party_id"]?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6"><label class="col-md-5">Doc. Number</label>
                <div class="col-md-7">
                    <?=$row_master["claim_no"]?>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5">Doc. Currency</label>
                <div class="col-md-7">
                    INR
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6"><label class="col-md-5">Doc. Date</label>
                <div class="col-md-7">
                    <?=$row_master["entry_date"]?>
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5">Posting Date</label>
                <div class="col-md-7">
                    <?=$row_master["entry_date"]." ".$row_master["entry_time"]?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6"><label class="col-md-5">Calculate Tax</label>
                <div class="col-md-7">
                    X
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5">Fiscal Year</label>
                <div class="col-md-7">
                    2023
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6"><label class="col-md-5">Ref.Doc.</label>
                <div class="col-md-7">
                    REF/23/ABC/001
                </div>
            </div>
            <div class="col-md-6"><label class="col-md-5">Period</label>
                <div class="col-md-7">
                    12
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
                            <th width="5%">Item</th>
                            <th width="5%">PK</th>
                            <th width="10%">Account</th>
                            <th width="10%">Account Short Text</th>
                            <th width="7%">Assignment</th>
                            <th width="5%">Tx</th>
                            <th width="7%">Amount</th>
                            <th width="9%">Payment Reference</th>
                            <th width="8%">Amt.in loc.cur.</th>
                            <th width="7%">Profit Ctr</th>
                            <th width="7%">Cost Ctr</th>
                            <th width="10%">Funds Center</th>
                            <th width="10%">Text</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                        <tr>
                            <td>1</td>
                            <td>31</td>
                            <td>21004185</td>
                            <td>R G Agency</td>
                            <td>&nbsp;</td>
                          	<td>2F</td>
                            <td align="right">-4,537.00</td>
                            <td>&nbsp;</td>
                            <td align="right">-4,537.00</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>DUMMY</td>
                            <td>Mark.Surv.Act. M/O _Aug'23 Sanction Attached</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>40</td>
                            <td>410102401</td>
                            <td>BUS PROM - MANUAL</td>
                            <td>Aug'23</td>
                          	<td>2F</td>
                            <td align="right">3,877.96</td>
                            <td>&nbsp;</td>
                            <td align="right">3,877.96</td>
                            <td>2910</td>
                            <td>2910MKT01</td>
                            <td>2910MKT010102</td>
                            <td>B10c(ii)</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>40</td>
                            <td>110300562</td>
                            <td>ITC GST CGST</td>
                            <td>&nbsp;</td>
                          	<td>2F</td>
                            <td align="right">349.02</td>
                            <td>&nbsp;</td>
                            <td align="right">349.02</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>DUMMY</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>40</td>
                            <td>110300563</td>
                            <td>ITC GST CGST</td>
                            <td>&nbsp;</td>
                          	<td>2F</td>
                            <td align="right">349.02</td>
                            <td>&nbsp;</td>
                            <td align="right">349.02</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>DUMMY</td>
                            <td>&nbsp;</td>
                        </tr>
                    
                    </tbody>
                </table>   
          </div>
        </div>

        
        </div>
        </div>

        
        </div>
