<?php
////// Function ID ///////
$fun_id = array("u"=>array(138)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

////// final submit form ////
@extract($_POST);
if($_POST['submit']=="Add"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	// insert all details of budget into claim budget master table //
	$party_name = str_replace("~",",",getAnyDetails($party_code,"name,city,state","asc_code","asc_master",$link1));
	$claim_type = getAnyDetails($claim_typeid,"claim_type","id","claim_type_master",$link1);
	if($bgt_month){
		$monthly_bgt = $monthly_bgt;
	}else{
		$monthly_bgt = $yearly_bgt/12;
	}
	/////
	if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM claim_budget WHERE party_id ='".$party_code."' AND claim_typeid = '".$claim_typeid."' AND budget_year = '".$bgt_year."' AND status='1'"))==0){
		$sql_master = "INSERT INTO claim_budget SET party_id ='".$party_code."', party_name = '".$party_name."', claim_type = '".$claim_type."', claim_typeid = '".$claim_typeid."', budget_year = '".$bgt_year."', budget_month='".$bgt_month."', budget_yearly = '".$yearly_bgt."', budget_monthly = '".$monthly_bgt."', man_power='".$no_of_manpower."', status = '".$status."', entry_screen = 'FRONT', entry_by = '".$_SESSION['userid']."', entry_date = '".$datetime."', entry_ip = '".$ip."'";
		$res_master =  mysqli_query($link1,$sql_master)or die("ER 1".mysqli_error($link1));
		/// check if query is execute or not//
		if(!$res_master){
			$flag = false;
			$err_msg = "Error 1". mysqli_error($link1) . ".";
		}	
		$instid = mysqli_insert_id($link1);
		$ref_no = $claim_typeid."/".$party_code."/".$bgt_year."/".$instid;
	}else{
		$flag = false;
		$err_msg = "Error 2: Claim budget of ".$claim_type." is already in system for party ".$party_name." for year ".$bgt_year;
	}
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$ref_no,"CLAIM BUDGET","ADD",$ip,$link1,$flag);	
	///// check all query are successfully executed
	if ($flag) {		
        mysqli_commit($link1);
        $msg = "Claim Budget is successfully added with ref. id. - ".$ref_no;
		///// move to parent page
		header("location:claim_budget_list.php?msg=".$msg."&sts=success".$pagenav);
		exit;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. ".$err_msg;
		///// move to parent page
		header("location:claim_budget_list.php?msg=".$msg."&sts=fail".$pagenav);
		exit;
	} 
    mysqli_close($link1);
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script>
 	$(document).ready(function(){
        $("#frm1").validate({
		submitHandler: function (form) {
			if (!this.wasSent) {
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
						.attr('disabled', 'disabled')
						.addClass('disabled');
				//spinner.show();
				form.submit();
			} else {
				return false;
			}
		}
		});
		$('#bgt_month,#bgt_year').change(function(){
			var ismnth = $('#bgt_month').val(); 
			if(ismnth){
				$('#monthly_bgt').attr('required','required');
				$('#monthly_bgt').addClass('required');
				$('#yearly_bgt').removeAttr('required');
				$('#yearly_bgt').removeClass('required');
			}else{
				$('#monthly_bgt').removeAttr('required');
				$('#monthly_bgt').removeClass('required');
				$('#yearly_bgt').attr('required','required');
				$('#yearly_bgt').addClass('required');
			}
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
      <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-suitcase"></i> Add Claim Budget </h2><br><br>
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Year <span class="red_small">*</span></label> 
                  <div class="col-md-5">
                            <select id="bgt_year" name="bgt_year" class="form-control required" required>
                                <option value="" <?php if($_REQUEST['emp_year']==""){ echo "selected";}?> > -- Please Select -- </option>
                                <?php
                                $currrent_year=date('Y');
                                $next_year=$currrent_year+1;
                                ?>
                                <option value="<?=$currrent_year?>" <?php if($_REQUEST['bgt_year']==$currrent_year)echo "selected";?>><?=$currrent_year?></option>
                                <option value="<?=$next_year?>" <?php if($_REQUEST['bgt_year']==$next_year)echo "selected";?>><?=$next_year?></option>
                            </select> 
                  </div>    
              </div>  
          </div>  
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Month </label> 
                  <div class="col-md-5">
                            <select id="bgt_month" name="bgt_month" class="form-control">
                                <option value="" <?php if($_REQUEST['bgt_month'] == "") { echo "selected" ;} ?> > -- Please Select -- </option>
                                <option value="01" <?php if($_REQUEST['bgt_month']=='01')echo "selected";?>>JAN</option>
                                <option value="02" <?php if($_REQUEST['bgt_month']=='02')echo "selected";?>>FEB</option>
                                <option value="03" <?php if($_REQUEST['bgt_month']=='03')echo "selected";?>>MAR</option>
                                <option value="04" <?php if($_REQUEST['bgt_month']=='04')echo "selected";?>>APR</option>
                                <option value="05" <?php if($_REQUEST['bgt_month']=='05')echo "selected";?>>MAY</option>
                                <option value="06" <?php if($_REQUEST['bgt_month']=='06')echo "selected";?>>JUN</option>
                                <option value="07" <?php if($_REQUEST['bgt_month']=='07')echo "selected";?>>JUL</option>
                                <option value="08" <?php if($_REQUEST['bgt_month']=='08')echo "selected";?>>AUG</option>
                                <option value="09" <?php if($_REQUEST['bgt_month']=='09')echo "selected";?>>SEP</option>
                                <option value="10" <?php if($_REQUEST['bgt_month']=='10')echo "selected";?>>OCT</option>
                                <option value="11" <?php if($_REQUEST['bgt_month']=='11')echo "selected";?>>NOV</option>
                                <option value="12" <?php if($_REQUEST['bgt_month']=='12')echo "selected";?>>DEC</option>	 
                            </select> 
                  </div>    
              </div>  
          </div> 
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Claim Type<span class="red_small">*</span></label> 
                  <div class="col-md-5">
					  <select name="claim_typeid" id="claim_typeid" required class="form-control selectpicker required" data-live-search="true">
                            <option value="" selected="selected">Please Select </option>
                            <?php
                            $sql_claim = "select id,claim_type from claim_type_master where status='1'";
                            $res_claim = mysqli_query($link1, $sql_claim);
                            while ($row_claim = mysqli_fetch_array($res_claim)) {   
                            ?>
                            <option value="<?= $row_claim['id']?>" <?php if ($row_claim['id'] == $_REQUEST['claim_typeid']) echo "selected"; ?> ><?= $row_claim['claim_type']?></option>
                            <?php
                            }
                            ?>
                        </select>
                  </div>    
              </div>  
          </div>
		  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Party Name <span class="red_small">*</span></label> 
                  <div class="col-md-5">
				  <select name="party_code" id="party_code" required class="form-control selectpicker required" data-live-search="true">
                        <option value="" selected="selected">Please Select </option>
                        <?php
                        $sql_parent = "select uid,location_id from access_location where uid='" . $_SESSION['userid'] . "' and status='Y'";
                        $res_parent = mysqli_query($link1, $sql_parent);
                        while ($result_parent = mysqli_fetch_array($res_parent)) {   
                            $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "'"));
                            if($party_det['name']){
                        ?>
                        <option value="<?= $result_parent['location_id']?>" <?php if ($result_parent['location_id'] == $_REQUEST['party_code']) echo "selected"; ?> ><?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $result_parent['location_id']?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                  </div>    
              </div>  
          </div>
		  
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Yearly Budget</label> 
                  <div class="col-md-5">
					  <input type="text" class="form-control number" name="yearly_bgt"  id="yearly_bgt"/>
                  </div>    
              </div>  
          </div>
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Monthly Budget</label> 
                  <div class="col-md-5">
					  <input type="text" class="form-control number" name="monthly_bgt"  id="monthly_bgt"/>
                  </div>    
              </div>  
          </div>
          <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">No. of manpower</label> 
                  <div class="col-md-5">
					  <input type="text" class="form-control digits" name="no_of_manpower"  id="no_of_manpower"/>
                  </div>    
              </div>  
          </div>
		  
		  <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label">Status <span class="red_small">*</span></label> 
                  <div class="col-md-5">
					  <select name="status" id="status" class="form-control required" required >
						  <option value="1" <?php if($_REQUEST['status']=="1"){ echo "selected"; } ?> >Active</option>
						  <option value="2" <?php if($_REQUEST['status']=="2"){ echo "selected"; } ?> >Deactive</option>
					  </select>
                  </div>    
              </div>  
          </div>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <button class="btn <?=$btncolor?>" type="submit" name="submit" id="save" value="Add"> Add </button>  
                  <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='claim_budget_list.php?<?=$pagenav?>'">
              </div>  
          </div>
         
      </form>                      
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>