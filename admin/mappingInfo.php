<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['pk']);
?>
<div class="panel-group">
	<div class="panel panel-info">
		<div class="panel-heading">Claim Approval Hierarchy</div>
        <div class="panel-body">
        	<!--<div class="row">
            	<div class="col-md-6"><label class="col-md-5">Type</label>
                	<div class="col-md-7">
                    	<select name="seltype" id="seltype" class="form-control">
                        	<option value="CLAIM">CLAIM</option>
                        </select>
                	</div>
            	</div>
        	</div>-->
            <div class="stepwizard">
            	<div class="stepwizard-row">
                <?php 
                $i=1;
                $res3 = mysqli_query($link1,"SELECT approval_steps FROM process_approval_step WHERE status='1' AND process_name = 'CLAIM'");
				$row3 = mysqli_fetch_assoc($res3);
				$arr_procid = explode(",",$row3['approval_steps']);
                for($j=0; $j<count($arr_procid); $j++){
                    $id_type = explode("~",getAnyDetails($arr_procid[$j],"id_type,utype","process_id","approval_step_master",$link1));
                    $res_mapusr = mysqli_query($link1,"SELECT a.username, a.name FROM admin_users a, access_location b WHERE a.username=b.uid AND a.utype='".$id_type[1]."' AND a.status='active' AND b.status='Y' AND b.location_id='".$docid."'");
                    $row_mapusr = mysqli_fetch_array($res_mapusr);
                    $stss = "";

					$btnclass = "btn-default"; 
					$stss = "Pending";
					$icn = "<i class='fa fa-gavel fa-lg'></i>";
                ?>
                	<div class="stepwizard-step">
                  		<button type="button" class="btn <?=$btnclass?>" disabled><?=$icn?>&nbsp;<?=$i?>.&nbsp;<?=$id_type[0]?></button>
                  		<p style="text-align:left" class="small">
                        	<strong>Name:</strong> <?=$row_mapusr[1]?><br/>
							<strong>Id:</strong> <?=$row_mapusr[0]?><br/>
							<strong>Designation:</strong> <?php echo gettypeName($id_type[1],$link1);?></p>
                	</div>
                <?php $i++;}?>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
             	</div>
            </div>
        </div>
    </div>
</div>