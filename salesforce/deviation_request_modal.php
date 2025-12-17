<?php
require_once("../config/config.php");
////// get pjp details
$rs=mysqli_query($link1,"select visit_area from pjp_data where id='".$_REQUEST['task_id']."'")or die(mysqli_error($link1));
$row2=mysqli_fetch_array($rs);
?>
       <div class="row">
        <form  name="frm3"  id="frm3"  class="form-horizontal" action="" method="post">
    	<div class="form-group">
            <div class="col-md-12"><label class="col-md-5 control-label">Scheduled Visit</label>
            <div class="col-md-4">
                <input type="text" name="sch_visit" class="form-control"  id="sch_visit" value="<?=$row2["visit_area"]?>" readonly/>
	    	</div>
            </div>
          </div>
           <div class="form-group"> 
		   <div class="col-md-12"><label class="col-md-5 control-label">Change Visit <span class="red_small">*</span></label>
              <div class="col-md-4">
                <input type="text" name="chng_visit" class="form-control required"  id="chng_visit" required/>
              </div>
            </div>
           </div>
           <div class="form-group"> 
		   <div class="col-md-12"><label class="col-md-5 control-label">Remark</label>
              <div class="col-md-4">
                <textarea type="text" name="remark" class="form-control addressfield"  id="remark" style="resize:vertical"></textarea>
              </div>
            </div>
           </div>
        <div class="form-group">
        	<div class="col-sm-12" align="center">
               <input id='btnSave3' name='btnSave3' title="Save Details" type="submit" class="btn btn-primary" value="Submit"/>
               &nbsp;
               <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
		</div>
        </form>
        </div>
    