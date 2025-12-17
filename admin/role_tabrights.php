<?php
////// Function ID ///////
$fun_id = array("a"=>array(107)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
/////////
$usertype = base64_decode($_REQUEST['id']);	
////// final submit form ////
@extract($_POST);
if($_POST){	
	/////////// initialize transcation parameter ////////
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = ""; 
	############# if form 1 is submitted #################
	if($_POST['Submit1']=='Update'){
		// Update Function Rights
		$result = mysqli_query($link1,"UPDATE access_role_tab SET status='' WHERE role_id='".$usertype."' AND tab_type='M'");
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "Error details1: ".mysqli_error($link1).".";
		}
		$rrr="report1";
		$rep1=$_REQUEST[$rrr];
		$tab_type1 = $_REQUEST["tabtype1"];
		$count=count($_REQUEST[$rrr]);
		$j=0;
		while($j < $count){
			 if($rep1[$j]==''){
				$newstatus="0";
			 }else{
				$newstatus="1";
			 }
			 // alrady exist
			 if(mysqli_num_rows(mysqli_query($link1,"select tab_id from access_role_tab where role_id='".$usertype."' and tab_id='".$rep1[$j]."' AND function_id='".$tab_type1[$rep1[$j]]."' AND tab_type='M'"))>0){
				$result = mysqli_query($link1,"update access_role_tab set status='".$newstatus."' where role_id='".$usertype."' and tab_id='".$rep1[$j]."' AND function_id='".$tab_type1[$rep1[$j]]."' AND tab_type='M'")or die(mysqli_error($link1));
			 }else{
				$result = mysqli_query($link1,"insert into access_role_tab set role_id='".$usertype."' ,tab_id='".$rep1[$j]."',status='".$newstatus."', function_id='".$tab_type1[$rep1[$j]]."', tab_type='M'")or die(mysqli_error($link1));
			 }
			 //// check if query is not executed
			if (!$result) {
				 $flag = false;
				 $err_msg = "Error details2: ".mysqli_error($link1).".";
			}
		   $j++;
		}
		////// insert in activity table////
		$flag=dailyActivity($_SESSION['userid'],$usertype,"Master Role Rigths","Updated",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		////// return message
		$msg="You have successfully updated master role rights for user type ".$usertype;
		// end Function Rights
	}
	############# if form 2 is submitted #################
	if($_POST['Submit2']=='Update'){
		// Update Function Rights
		$result = mysqli_query($link1,"UPDATE access_role_tab SET status='' WHERE role_id='".$usertype."' AND tab_type='P'");
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $err_msg = "Error details1: ".mysqli_error($link1).".";
		}
		$rrr="report2";
		$rep2=$_REQUEST[$rrr];
		$tab_type2 = $_REQUEST["tabtype2"];
		$count=count($_REQUEST[$rrr]);
		$j=0;
		while($j < $count){
			 if($rep2[$j]==''){
				$newstatus="0";
			 }else{
				$newstatus="1";
			 }
			 // alrady exist
			 if(mysqli_num_rows(mysqli_query($link1,"select tab_id from access_role_tab where role_id='".$usertype."' and tab_id='".$rep2[$j]."' AND function_id='".$tab_type2[$rep2[$j]]."' AND tab_type='P'"))>0){
				$result = mysqli_query($link1,"update access_role_tab set status='".$newstatus."' where role_id='".$usertype."' and tab_id='".$rep2[$j]."' AND function_id='".$tab_type2[$rep2[$j]]."' AND tab_type='P'")or die(mysqli_error($link1));
			 }else{
				$result = mysqli_query($link1,"insert into access_role_tab set role_id='".$usertype."' ,tab_id='".$rep2[$j]."',status='".$newstatus."' , function_id='".$tab_type2[$rep2[$j]]."', tab_type='P'")or die(mysqli_error($link1));
			 }
			 //// check if query is not executed
			if (!$result) {
				 $flag = false;
				 $err_msg = "Error details2: ".mysqli_error($link1).".";
			}
		   $j++;
		}
		////// insert in activity table////
		$flag=dailyActivity($_SESSION['userid'],$usertype,"Process Role Rigths","Updated",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		////// return message
		$msg="You have successfully updated process role rights for user type ".$usertype;
		// end Function Rights
	}
	///// check  query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	   ///// move to parent page
 	header("location:role_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
		$("#frm2").validate();
    });
	///// multiple check all function
 function checkFunc(field,ind,val){
	var chk=document.getElementById(val+""+ind).checked;
	if(chk==true){ checkAll(field); }
	else{ uncheckAll(field);}
 }
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
     include("../includes/leftnav2.php");
    ?>
    	<div class="<?=$screenwidth?>">
      		<h2 align="center"><i class="fa fa-users"></i> View/Edit Role</h2>
      		<h4 align="center"><?=$usertype?>
      		<?php if($_POST['Submit1']=='Save' || $_POST['Submit2']=='Save'){ ?>
      		<br/>
      		<span style="color:#FF0000"><?php echo $msg; ?></span>
      		<?php } ?>
      		</h4>
      		<div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      	 		<ul class="nav nav-tabs">
            		<li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card"></i> Master/Report Tab</a></li>
            		<li><a data-toggle="tab" href="#menu1"><i class="fa fa-sitemap"></i> Process Tab</a></li>
          		</ul>
    	  		<div class="tab-content">
            		<div id="home" class="tab-pane fade in active"><br/>
              			<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
      						<div class="table-responsive"> 
                				<table id="myTable" class="table table-hover">
								<?php 
                                $rs=mysqli_query($link1,"SELECT header_id,header FROM report_master WHERE status='Y' group by header_id ORDER BY header_id");
                                $num=mysqli_num_rows($rs);
                                if($num > 0){
                                   $j=1;
                                   while($row=mysqli_fetch_array($rs)){
                                   $report="SELECT * FROM report_master where header_id='".$row['header_id']."' AND status='Y' ORDER BY name";
                                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
                                ?>
                				<thead>
                  					<tr>
                    					<th style="border:none" class="bg-info">&nbsp;<?=$row['header']?>&nbsp;<input style="width:20px"  type="checkbox" id="funcTB1<?=$j?>" name="funcTB1[]" onClick="checkFunc(document.frm1.report1<?=$j?>,'<?=$j?>','funcTB1');"/></th>
                  					</tr>
                				</thead>
                				<tbody>
                 				<?php 
				   				$i=1;
                    			while($row_report=mysqli_fetch_array($rs_report)){
                       				if($i%4==1){?>
                  					<tr>
                  					<?php
                       				}
                    				$state_acc=mysqli_query($link1,"select tab_id from access_role_tab where role_id='".$usertype."' and tab_id='".$row_report['id']."' and status='1' AND function_id='".$row['header_id']."'")or die(mysqli_error($link1));
                    				$num1=mysqli_num_rows($state_acc);?>
                    					<td><input style="width:20px"  type="checkbox" id="report1<?=$j?>" name="report1[]" value="<?=$row_report['id']?>" <?php if($num1 > 0) echo "checked";?> /><?=$row_report['name']?>
                                        <input type="hidden" name="tabtype1[<?=$row_report['id']?>]" value="<?=$row['header_id']?>"/> </td>
                  					<?php if($i/4==0){?>
                  					</tr>
                  					<?php 
                        			}
									$i++;
                    			}////// Close 2nd While Loop of TAB 2
								?>
                                </tbody>
                    			<?php 
									$j++;
				   					}  
								}?>
                				</table>
                			</div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn<?=$btncolor?>" name="Submit1" id="save1" value="Update" title="" <?php if($_POST['Submit1']=='Save'){?>disabled<?php }?>>&nbsp;
                                    <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='role_master.php?<?=$pagenav?>'">
                                </div>
                            </div>      	
              			</form>
            		</div>
            		<div id="menu1" class="tab-pane fade">
              			<br/>
              			<form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
      						<div class="table-responsive"> 
                				<table id="myTable" class="table table-hover">
								<?php 
                                $res2 = mysqli_query($link1,"SELECT function_id FROM sub_function_master WHERE status='Y' group by function_id ORDER BY function_id");
                                $num2 = mysqli_num_rows($res2);
                                if($num2 > 0){
                                   $j=1;
                                   while($row2 = mysqli_fetch_array($res2)){
                                   $sql3 = "SELECT * FROM sub_function_master where function_id='".$row2['function_id']."' AND status='Y' ORDER BY sub_name";
                                   $res3 = mysqli_query($link1,$sql3) or die(mysqli_error($link1));
								   $fun_name = mysqli_fetch_array(mysqli_query($link1,"select function_name,function_id,icon_img from function_master where function_id='".$row2['function_id']."'"));
                                ?>
                				<thead>
                  					<tr>
                    					<th style="border:none" class="bg-info">&nbsp;<?=$fun_name['function_name']?>&nbsp;<input style="width:20px"  type="checkbox" id="funcTB2<?=$j?>" name="funcTB2[]" onClick="checkFunc(document.frm2.report2<?=$j?>,'<?=$j?>','funcTB2');"/></th>
                  					</tr>
                				</thead>
                				<tbody>
                 				<?php 
				   				$i=1;
                    			while($row3 = mysqli_fetch_array($res3)){
                       				if($i%4==1){?>
                  					<tr>
                  					<?php
                       				}
                    				$check_row = mysqli_query($link1,"select tab_id from access_role_tab where role_id='".$usertype."' and tab_id='".$row3['id']."' and status='1' AND function_id='".$row3['function_id']."'")or die(mysqli_error($link1));
                    				$num3 = mysqli_num_rows($check_row);?>
                    					<td><input style="width:20px"  type="checkbox" id="report2<?=$j?>" name="report2[]" value="<?=$row3['id']?>" <?php if($num3 > 0) echo "checked";?> /><?=$row3['sub_name']?>
                                        <input type="hidden" name="tabtype2[<?=$row3['id']?>]" value="<?=$row3['function_id']?>"/></td>
                  					<?php if($i/4==0){?>
                  					</tr>
                  					<?php 
                        			}
									$i++;
                    			}////// Close 2nd While Loop of TAB 2
								?>
                                </tbody>
                    			<?php 
									$j++;
				   					}  
								}?>
                				</table>
                			</div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">
                                    <input type="submit" class="btn<?=$btncolor?>" name="Submit2" id="save2" value="Update" title="" <?php if($_POST['Submit2']=='Save'){?>disabled<?php }?>>&nbsp;
                                    <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='role_master.php?<?=$pagenav?>'">
                                </div>
                            </div>      	
              			</form>
            		</div>
				</div>
      		</div>
    	</div><!--End col-sm-9-->
	</div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>