<?php
////// Function ID ///////
$fun_id = array("u"=>array(138)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
function unixstamp( $excelDateTime ) {
    $d = floor( $excelDateTime ); // seconds since 1900
    $t = $excelDateTime - $d;
    return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
}
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
	if ($_FILES["attchfile"]["name"]) {
		require_once "../includes/simplexlsx.class.php";
		$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
		move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload/claimbgt_upload/".$now.$_FILES["attchfile"]["name"]);
		$f_name=$now.$_FILES["attchfile"]["name"];
		//////insert into upload file data////////////
		mysqli_query($link1,"insert into upload_file_data set file_name='".$f_name."',entry_date='".$today."',entry_time='".$currtime."'");
		$file_id=mysqli_insert_id($link1);
		$r=array();
		////////
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg = "";
		$arr_party = array();
		$arr_claimtyp = array();
		$arr_err_pty = array();
		$arr_err_clm = array();
		$arr_err_year = array();
		$arr_err_amt = array();
		$arr_err_month = array();
		$arr_err_monthly = array();
		$arr_err_manpow = array();
		
		$arr_total = array();
		////////
		list($cols) = $xlsx->dimension();
		foreach( $xlsx->rows() as $k => $r) {
	 		if ($k == 0) continue; // skip first row 
	  		for( $i = 0; $i < count($k); $i++)
	  		{
		  		/// check excel row data
	      		if($r[0]=='' && $r[1]=='' && $r[2]=='' && $r[3]=='' && $r[4]=='' && $r[5]=='' && $r[6]=='' && $r[7]=='' && $r[8]=='' && $r[9]==''){
				
		  		}
		  		else{
					////Make Variable for each element of excel//////
					$line_no = trim($r[0]);
					$claimbgt_year = trim($r[1]);
					$claimbgt_partyid = $r[2];
					$claimbgt_claimtypeid = trim($r[4]);
					$claimbgt_yearly = trim($r[6]);
					$claimbgt_month = trim($r[7]);
					$claimbgt_monthly = trim($r[8]);
					$claimbgt_manpower = trim($r[9]);
					///// check party id and claim id is valid or not
					$party_name = str_replace("~",",",getAnyDetails($claimbgt_partyid,"name,city,state","asc_code","asc_master",$link1));
					$claim_type = getAnyDetails($claimbgt_claimtypeid,"claim_type","id","claim_type_master",$link1);
					///// check party
					if($party_name!=""){
						$arr_party[$claimbgt_partyid] = $party_name;
					}else{
						$arr_err_pty[] = "Line ".$line_no."- ".$claimbgt_partyid;
					}
					//// check claim type
					if($claim_type!=""){
						$arr_claimtyp[$claimbgt_claimtypeid] = $claim_type;
					}else{
						$arr_err_clm[] = "Line ".$line_no."- ".$claimbgt_claimtypeid;
					}
					///// check budget year
					if(checkdate(1,1,$claimbgt_year)){
					
					}else{
						$arr_err_year[] = "Line ".$line_no."- ".$claimbgt_year;
					}
					////// check budget amount
					if(is_numeric($claimbgt_yearly)){
					
					}else{
						$arr_err_amt[] = "Line ".$line_no."- ".$claimbgt_yearly;
					}
					$arr_total[] = $claimbgt_year."~".$claimbgt_partyid."~".$claimbgt_claimtypeid."~".$claimbgt_yearly."~".$claimbgt_month."~".$claimbgt_monthly."~".$claimbgt_manpower;
					
		  		}
	  		}//Close For loop
		}//Close Foreach loop
		if($arr_err_pty){
			$msg = "Invalid party code";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_err_pty];
			header("location:claim_budget_upload.php?".$pagenav);
			exit;
		}else if($arr_err_clm){
			$msg = "Invalid claim type";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_err_clm];
			header("location:claim_budget_upload.php?".$pagenav);
			exit;
		}else if($arr_err_year){
			$msg = "Invalid year";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_err_year];
			header("location:claim_budget_upload.php?".$pagenav);
			exit;
		}else if($arr_err_amt){
			$msg = "Invalid budget amount";
			///// move to parent page
			$_SESSION["logres"] = [ "status"=>"failed", "msg"=> $msg, "invalid"=>$arr_err_amt];
			header("location:claim_budget_upload.php?".$pagenav);
			exit;
		}
		else{
			///////retrive filtered data
			for($i=0; $i<count($arr_total); $i++){
				$expl_val = explode("~",$arr_total[$i]);
				//////////
				$party_code = $expl_val[1];
				$party_name = $arr_party[$expl_val[1]];
				$claim_type = $arr_claimtyp[$expl_val[2]];
				$claim_typeid = $expl_val[2];
				$bgt_year = $expl_val[0];
				$yearly_bgt = $expl_val[3];
				if($expl_val[5]){
					$monthly_bgt = $expl_val[5];
				}else{
					$monthly_bgt = $expl_val[3]/12;
				}
				$bgt_month = $expl_val[4];
				$bgt_manpow = $expl_val[6];
				//////// 
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM claim_budget WHERE party_id ='".$party_code."' AND claim_typeid = '".$claim_typeid."' AND budget_year = '".$bgt_year."' AND status='1'"))==0){
					$sql = "INSERT INTO claim_budget SET party_id ='".$party_code."', party_name = '".$party_name."', claim_type = '".$claim_type."', claim_typeid = '".$claim_typeid."', budget_year = '".$bgt_year."', budget_yearly = '".$yearly_bgt."', budget_monthly = '".$monthly_bgt."', budget_month='".$bgt_month."', man_power='".$bgt_manpow."', status = '1', entry_screen = 'UPLOAD', entry_by = '".$_SESSION['userid']."', entry_date = '".$datetime."', entry_ip = '".$ip."'";
					$res1 = mysqli_query($link1,$sql);
					if(!$res1){
						$flag = false;
						$err_msg = "Error 1". mysqli_error($link1) . ".";
					}
				}else{
					$flag = false;
					$err_msg = "Error 2: Claim budget of ".$claim_type." is already in system for party ".$party_name." for year ".$bgt_year;
				}
			}
		}
		mysqli_query($link1,"UPDATE upload_file_data SET status='1' WHERE id='".$file_id."'");			
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$ref_no,"CLAIM BUDGET","UPLOAD",$ip,$link1,$flag);	
		///// check all query are successfully executed
		if ($flag) {		
			mysqli_commit($link1);
			$msg = "Claim Budget is successfully uploaded.";
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
   	}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 
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
  });
  // When the document is ready
 </script>
 <script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> Upload Claim Budget</h2>
      <div style="display:inline-block;float:left"><a href="../admin/excelexport.php?rname=<?=base64_encode("locationmaster")?>&rheader=<?=base64_encode("Location Master")?>&locstate=<?=base64_encode($_POST['locationstate'])?>&loccity=<?=base64_encode($_POST['locationcity'])?>&loctype=<?=base64_encode("DS")?>&locstatus=<?=base64_encode($_POST['locationstatus'])?>" title="Export Product Sub-category details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Party details in excel"></i></a><br/>
        Download Party list</div>
      <div style="display:inline-block;float:right"><a href="../templates/UPLOAD_CLAIM_BUDGET.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div>
      <br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
        <?php if($_REQUEST['msg']){?>
        <br>
        <h4 align="center" style="color:#FF0000">
          <?=$_REQUEST['msg']?>
        </h4>
        <?php }?>
        <?php
		if(isset($_SESSION["logres"]) && $_SESSION["logres"]){
			echo "<br/><br/>";
			echo '<div class="py-2 overflow-hidden" style="background:#f1f1f1;padding:15px;line-height:20px;color:#e51111;margin:15px;font-size:12px;">';
			echo '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["logres"]["msg"];
			echo '<br/><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.implode(" , ",$_SESSION["logres"]["invalid"]);
			echo '</div>';
		}
		unset($_SESSION["logres"]);
		?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                <div class="input-group">
                  <label class="input-group-btn"> <span class="btn btn-primary"> Browse&hellip;
                  <input type="file" name="attchfile" id="attchfile" class="form-control required" required style="display:none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                  </span> </label>
                  <input type="text" class="form-control required" name="beatfile"  id="beatfile" readonly required/>
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">NOTE<span class="red_small">**</span></label>
              <div class="col-md-4"> <span class="text-danger">Below mentioned <strong>Claim Type</strong> must be filled in template only.</span><br/>
                  <br/>
                  <table width="100%" border="1" class="table">
                    <tr class="<?=$tableheadcolor?>">
                      <th>Claim Type Id</th>
                      <th>Claim Type</th>
                    </tr>
                    <?php
					  $res_claim = mysqli_query($link1,"SELECT id, claim_type FROM claim_type_master WHERE status='1'");
					  while($row_claim=mysqli_fetch_assoc($res_claim)){
					  ?>
                    <tr>
                      <td><?=$row_claim['id']?></td>
                      <td><?=$row_claim['claim_type']?></td>
                    </tr>
                    <?php }?>
                  </table>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn  <?=$btncolor?>" value="Back" onClick="window.location.href='claim_budget_list.php?<?=$pagenav?>'">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>