<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['doc_id']);

$sql_master = "SELECT * FROM sf_ticket_master WHERE id='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);
?>
        	<table class="table table-bordered" width="100%" id="itemsTable3">
                <tbody>
                  <tr>
                    <td width="35%"><strong>Lead Id</strong></td>
                    <td width="65%"><?=$row_master["lead_id"]?></td>
                  </tr>
                  <tr>
                    <td><strong>Subject</strong></td>
                    <td><?=$row_master["subject"]?></td>
                  </tr>
                  <tr>
                    <td><strong>Note Type</strong></td>
                    <td><?=$row_master["type"]?></td>
                  </tr>
                  <tr>
                    <td><strong>Note</strong></td>
                    <td><?php if($row_master['internal_note']!=''){echo ucwords(htmlspecialchars_decode($row_master['internal_note']));} else {echo ucwords(htmlspecialchars_decode($row_master['client_note']));}?></td>
                  </tr>
                  <tr>
                    <td><strong>Contact Person</strong></td>
                    <td><?=$row_master["contact_person"]?></td>
                  </tr>
                  <tr>
                    <td><strong>Call By</strong></td>
                    <td><?php echo get_communication($row_master['comm_type'],$link1);?></td>
                  </tr>
                  <tr>
                    <td><strong>Scheduled Date</strong></td>
                    <td><?=dt_format($row_master["schedule_date"])?></td>
                  </tr>
                  <tr>
                    <td><strong>Scheduled Time</strong></td>
                    <td><?=$row_master["schedule_time"]?></td>
                  </tr>
                  <tr>
                    <td><strong>Post By</strong></td>
                    <td><?php echo getAdminDetails($row_master['ticket_loggedby'],"name",$link1);?></td>
                  </tr>
                  <tr>
                    <td><strong>Post On</strong></td>
                    <td><?=dt_format($row_master["ticket_dt"])?></td>
                  </tr>
                </tbody>
            </table>
