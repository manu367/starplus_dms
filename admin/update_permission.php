<?php 
require_once("../config/config.php");
$req_userid = base64_decode($_REQUEST['userid']);
$req_username = base64_decode($_REQUEST['u_name']);
@extract($_POST);
############# if form 1 is submitted #################
if($_POST['submitTab']){
	// Update Masters Rights
	$report=$_REQUEST['report'];
	$count=count($report);
    $j=0;
    mysqli_query($link1,"update access_report set status='' where uid='$req_userid' ")or die("er3".mysqli_error($link1));
    while($j < $count){
         if($report[$j]==''){
            $status='';
         }else{
            $status='Y';
		 }
         // alrady exist
         if(mysqli_num_rows(mysqli_query($link1,"select report_id from access_report where uid='$req_userid' and report_id='$report[$j]'"))>0){
            mysqli_query($link1,"update access_report set status='$status' where uid='$req_userid' and report_id='$report[$j]'")or die(mysqli_error($link1));
         }else{
            mysqli_query($link1,"insert into access_report set uid='$req_userid',report_id='$report[$j]',status='$status'")or die(mysqli_error($link1));
		 }
         $j++;
	}
	// end Master Rights
	// Update Region Rights
    if($_REQUEST['EAST']==''){ $status=''; }else{ $status='Y'; }
    if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$req_userid' and region='EAST'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$req_userid' and region='EAST'")or die(mysqli_error($link1));
    }else{
      mysqli_query($link1,"insert into access_region set uid='$req_userid',region='EAST',status='$status'")or die(mysqli_error($link1));
	}
   if($_REQUEST['WEST']==''){ $status=''; }else{ $status='Y'; }
   if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$req_userid' and region='WEST'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$req_userid' and region='WEST'")or die(mysqli_error($link1));
   }else{
      mysqli_query($link1,"insert into access_region set uid='$req_userid',region='WEST',status='$status'")or die(mysqli_error($link1));
   }
   if($_REQUEST['NORTH']==''){ $status=''; }else{ $status='Y'; }
   if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$req_userid' and region='NORTH'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$req_userid' and region='NORTH'")or die(mysqli_error($link1));
   }else{
      mysqli_query($link1,"insert into access_region set uid='$req_userid',region='NORTH',status='$status'")or die(mysqli_error($link1));
   }
   if($_REQUEST['SOUTH']==''){ $status=''; }else{ $status='Y'; }
   if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$req_userid' and region='SOUTH'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$req_userid' and region='SOUTH'")or die(mysqli_error($link1));
   }else{
      mysqli_query($link1,"insert into access_region set uid='$req_userid',region='SOUTH',status='$status'")or die(mysqli_error($link1));
   }
   // end Region Rights
   // Update State Rights
   $state=$_REQUEST['state'];
   $count=count($state);
   $i=0;
   mysqli_query($link1,"update access_state set status='' where uid='$req_userid' ")or die(mysqli_error($link1));
   while($i < $count){
      if($state[$i]==''){ $status=''; }else{ $status='Y'; }
      if(mysqli_num_rows(mysqli_query($link1,"select state from access_state where uid='$req_userid' and state='$state[$i]'"))>0){
         mysqli_query($link1,"update access_state set status='$status' where uid='$req_userid' and state='$state[$i]'")or die(mysqli_error($link1));
      }else{
         mysqli_query($link1,"insert into access_state set uid='$req_userid',state='$state[$i]',status='$status'")or die(mysqli_error($link1));
	  }
      $i++;	
   }
   // end State Rights
   dailyActivity($_SESSION['userid'],$req_userid,"STATE/MASTER RIGHTS","UPDATE",$ip,$link1,"");
}
############# if form 2 is submitted #################
if($_POST['submitTab1']){
	// Update Function Rights
	mysqli_query($link1,"update access_function set status='' where uid='$req_userid' ")or die(mysqli_error($link1));
	for($s=1;$s<$_REQUEST['count_repTab1'];$s++){
		$rrr="report1".$s;
		$rep1=$_REQUEST[$rrr];
		$count=count($_REQUEST[$rrr]);
		$j=0;
		while($j < $count){
			 if($rep1[$j]==''){
				$status1='';
			 }else{
				$status1='Y';
			 }


			 /// fisrt check usertype, if user type not equal to admin then further action will taken /////////////////// updated on 26 december 2019
			/* $data_check = mysqli_fetch_array(mysqli_query($link1 ," select utype from admin_users where username = '".$req_userid."' "));
			 if($data_check['utype'] != '1') {
			  $data_check = mysqli_query($link1 ," select username from admin_users where utype = '".$data_check['utype']."'  ");
			  while($row = mysqli_fetch_array($data_check)){			 
			 // alrady exist
			 if(mysqli_num_rows(mysqli_query($link1,"select function_id from access_function where uid='".$row['username']."' and function_id='$rep1[$j]'"))>0){
			   	mysqli_query($link1,"update access_function set status='$status1' where uid='".$row['username']."' and function_id='$rep1[$j]'")or die(mysqli_error($link1));
			 }else{
				mysqli_query($link1,"insert into access_function set uid='".$row['username']."',function_id='$rep1[$j]',status='$status1'")or die(mysqli_error($link1));
			    }
			   } ///// end of while /////////////////////////////////////////////////////////////////
			 } //// end of for /////////////////////////////
			 else {*/
			      if(mysqli_num_rows(mysqli_query($link1,"select function_id from access_function where uid='$req_userid' and function_id='$rep1[$j]'"))>0){
				   mysqli_query($link1,"update access_function set status='$status1' where uid='$req_userid' and function_id='$rep1[$j]'")or die(mysqli_error($link1));
			      }else{
				mysqli_query($link1,"insert into access_function set uid='$req_userid',function_id='$rep1[$j]',status='$status1'")or die(mysqli_error($link1));
			        }			 
			   //}




		   $j++;
		}
		$count=0;
	}
	// end Function Rights
	dailyActivity($_SESSION['userid'],$req_userid,"FUNCTION RIGHTS","UPDATE",$ip,$link1,"");
}
############# if form 3 is submitted #################
/*if($_POST['submitTab2']){
	// Updatign Excel Export Rights
	$report2=$_REQUEST[report2];
	$count2=count($report2);
	$l=0;
	mysqli_query($link1,"update excel_export_right set status='' where user_id='$req_userid' ")or die(mysqli_error($link1));
	while($l < $count2){
	   if($report2[$l]==''){
	      $status2='';
	   }else{
	      $status2='Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select process_id from excel_export_right where user_id='$req_userid' and process_id='$report2[$l]'"))>0){
	      mysqli_query($link1,"update excel_export_right set status='$status2' where user_id='$req_userid' and process_id='$report2[$l]'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into excel_export_right set user_id='$req_userid',process_id='$report2[$l]',status='$status2'")or die(mysqli_error($link1));
	   }
	   $l++;
	}
	// end Excel Export Rights
}*/
############# if form 4 is submitted #################
/*if($_POST['submitTab3']){
	// Updatign Cancellation Rights
	$report3=$_REQUEST[report3];
	$count3=count($report3);
	$l=0;
	mysqli_query($link1,"update access_cancel_rights set status='' where uid='$req_userid' ")or die(mysqli_error($link1));
	while($l < $count3){
	   if($report3[$l]==''){
	      $status3='';
	   }else{
	      $status3='Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select cancel_type from access_cancel_rights where uid='$req_userid' and cancel_type='$report3[$l]'"))>0){
	      mysqli_query($link1,"update access_cancel_rights set status='$status3' where uid='$req_userid' and cancel_type='$report3[$l]'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into access_cancel_rights set uid='$req_userid',cancel_type='$report3[$l]',status='$status3'")or die(mysqli_error($link1));
	   }
	   $l++;
	}
	// end Cancellation Rights
}*/
############# if form 5 is submitted #################
if($_POST['submitTab4']){
	// Updatign Location Type Rights
	$report41=$_REQUEST['location_type'];
	//$count41=count($report41);
	$l=0;
	if($report41!=""){
	   mysqli_query($link1,"update access_role set status='' WHERE uid='".$req_userid."' AND role_id='".$report41."'")or die(mysqli_error($link1));
	   if($report41==''){
	      $status41 = '';
	   }else{
	      $status41 = 'Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select role_id from access_role where uid='$req_userid' and role_id='$report41'"))>0){
	      mysqli_query($link1,"update access_role set status='$status41' where uid='$req_userid' and role_id='$report41'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into access_role set uid='$req_userid',role_id='$report41',status='$status41'")or die(mysqli_error($link1));
	   }
	   $l++;
	}
	// end Location Type Rights
	// Updatign Location Rights
	$report4=$_REQUEST['report4'];
	$state_nm=$_REQUEST['state_name'];
	$count4=count($report4);
	$k=0;
	mysqli_query($link1,"update access_location set status='' where uid='$req_userid' AND state='".$state_nm."' AND id_type='".$report41."'")or die(mysqli_error($link1));
	while($k < $count4){
	   if($report4[$k]==''){
	      $status4='';
	   }else{
	      $status4='Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select location_id from access_location where uid='$req_userid' and location_id='$report4[$k]' AND state='".$state_nm."' AND id_type='".$report41."'"))>0){
	      mysqli_query($link1,"update access_location set status='$status4' where uid='$req_userid' and location_id='$report4[$k]' AND state='".$state_nm."' AND id_type='".$report41."'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into access_location set uid='$req_userid',location_id='$report4[$k]',status='$status4', state='".$state_nm."', id_type='".$report41."'")or die(mysqli_error($link1));
	   }
	   $k++;
	}
	// end Location Rights
	dailyActivity($_SESSION['userid'],$req_userid,"LOCATION RIGHTS","UPDATE",$ip,$link1,"");
}
############# if form 6 is submitted #################
/*if($_POST['submitTab5']){
	// Updating Users Rights
	$mapped_code=$_REQUEST[mapped_code];
	$count=count($mapped_code);
	$j=0;
	mysqli_query($link1,"update mapped_user set status='' where uid='$req_userid' ")or die(mysqli_error($link1));
	while($j < $count){
      if($mapped_code[$j]==''){
         $status='';
	  }else{
		 $status='Y';
	  }
	  // alrady exist
	  if(mysqli_num_rows(mysqli_query($link1,"select mapped_code from mapped_user where uid='$req_userid' and mapped_code='$report[$j]'"))>0){
	     mysqli_query($link1,"update mapped_user set mapped_code='$mapped_code[$j]',status='$status' , update_date='$today' where uid='$req_userid'")or die(mysqli_error($link1));
	  }else{
	     mysqli_query($link1,"insert into mapped_user set uid='$req_userid',mapped_code='$mapped_code[$j]',status='$status', update_date='$today'")or die(mysqli_error($link1));
	  }
	  $j++;
	}
	// end Users Rights
}*/
if(isset($_POST['submitTab6'])){
	mysqli_query($link1,"update mapped_productcat set status='' where userid='$req_userid' ")or die(mysqli_error($link1));
	for($s=0;$s<$_REQUEST['count_repTab6'];$s++){
		$rrr="report14".$s;
		$rep1=$_REQUEST[$rrr];
		//$rep2=$_REQUEST[$rrr2];
		//$product_cat=$_REQUEST[$prdcat];
		$count=count($_REQUEST[$rrr]);
		$j=0;
		  while($j < $count){
			 if($rep1[$j]==''){
				$status1='';
			 }else{
				$status1='Y';
			 }
			 // alrady exist
			  $prodsbct=mysqli_fetch_assoc(mysqli_query($link1,"select prod_sub_cat,product_category from product_sub_category where psubcatid='$rep1[$j]'"));
			  
			  //$serflag=mysqli_fetch_assoc(mysqli_query($link1,"select id,service_flag from mapped_productcat where userid='$req_userid' and prod_subcatid='$rep1[$j]' and product_cat='$prodsbct[prod_cat_type]'"));
             if(mysqli_num_rows(mysqli_query($link1,"select id from mapped_productcat where userid='$req_userid' and prod_subcatid='$rep1[$j]' and product_cat='$prodsbct[product_category]'"))>0){
				mysqli_query($link1,"update mapped_productcat set status='$status1' where userid='$req_userid' and prod_subcatid='$rep1[$j]' and product_cat='$prodsbct[product_category]'")or die(mysqli_error($link1));
			 }else{
				mysqli_query($link1,"insert into mapped_productcat set userid='$req_userid',product_subcat='$prodsbct[prod_sub_cat]',prod_subcatid='$rep1[$j]',product_cat='$prodsbct[product_category]',status='$status1'")or die(mysqli_error($link1));
			 }
		   $j++;
		  }
		  $count=0;
	}
	dailyActivity($_SESSION['userid'],$req_userid,"PRODCAT RIGHTS","UPDATE",$ip,$link1,"");
}
if(isset($_POST['submitTab7'])){
	$post_brand = $_REQUEST["brand"];
	$count_brand = count($post_brand);
	$r = 0;
	mysqli_query($link1,"UPDATE mapped_brand SET status='' where userid = '".$req_userid."'")or die(mysqli_error($link1));
	while($r < $count_brand){
		if($post_brand[$r]==''){
			$status_brand = '';
		}else{
			$status_brand = 'Y';
		}
		// alrady exist
		if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM mapped_brand WHERE userid = '".$req_userid."' and brand = '".$post_brand[$r]."'"))>0){
			mysqli_query($link1,"UPDATE mapped_brand SET status='".$status_brand."' WHERE userid = '".$req_userid."' and brand = '".$post_brand[$r]."'")or die(mysqli_error($link1));
		}else{
			mysqli_query($link1,"INSERT INTO mapped_brand SET userid = '".$req_userid."',brand = '".$post_brand[$r]."',status = '".$status_brand."'")or die(mysqli_error($link1));
		}
		$r++;
	}
	dailyActivity($_SESSION['userid'],$req_userid,"BRAND RIGHTS","UPDATE",$ip,$link1,"");
}


////////// update cancel right written on 27 feb 2023 by shekhar
if($_POST['submitTab8']){
	// Update Function Rights
	mysqli_query($link1,"UPDATE access_ops_rights SET status='' WHERE uid='".$req_userid."' ")or die(mysqli_error($link1));
	for($s=1;$s<$_REQUEST['count_repTab8'];$s++){
		$rrr="report9".$s;
		$rep1=$_REQUEST[$rrr];
		$count=count($_REQUEST[$rrr]);
		$j=0;
		while($j < $count){
			 if($rep1[$j]==''){
				$status1='';
			 }else{
				$status1='Y';
			 }
			 if(mysqli_num_rows(mysqli_query($link1,"SELECT ops_id from access_ops_rights WHERE uid='".$req_userid."' AND ops_id='".$rep1[$j]."'"))>0){
				mysqli_query($link1,"UPDATE access_ops_rights SET status='".$status1."' WHERE uid='".$req_userid."' AND ops_id='".$rep1[$j]."'")or die(mysqli_error($link1));
			 }else{
				mysqli_query($link1,"INSERT INTO access_ops_rights SET uid='".$req_userid."',ops_name='CANCEL', ops_id='".$rep1[$j]."',status='".$status1."'")or die(mysqli_error($link1));
			 }
		   $j++;
		}
		$count=0;
	}
	// end Function Rights
	dailyActivity($_SESSION['userid'],$req_userid,"CANCEL RIGHTS","UPDATE",$ip,$link1,"");
}
///// update mobile app left nav control
if($_POST['submitTab9']){
	$apptabarr=$_REQUEST['apptab'];
	$count = count($apptabarr);
    $j=0;
    mysqli_query($link1,"UPDATE access_app_tab SET status='0' WHERE userid = '".$req_userid."'")or die("ER APPTAB".mysqli_error($link1));
    while($j < $count){
		if($apptabarr[$j]==''){
        	$status='0';
        }else{
            $status='1';
		}
        // alrady exist
        if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM access_app_tab WHERE userid = '".$req_userid."' AND tabid = '".$apptabarr[$j]."'"))>0){
        	mysqli_query($link1,"UPDATE access_app_tab SET status='".$status."', updateby='".$_SESSION['userid']."', updatedate='".$datetime."' WHERE userid = '".$req_userid."' AND tabid = '".$apptabarr[$j]."'")or die(mysqli_error($link1));
        }else{
            mysqli_query($link1,"INSERT INTO access_app_tab SET userid = '".$req_userid."', tabid = '".$apptabarr[$j]."', status='".$status."', updateby='".$_SESSION['userid']."', updatedate='".$datetime."'")or die(mysqli_error($link1));
		}
        $j++;
	}
	dailyActivity($_SESSION['userid'],$req_userid,"APPNAV RIGHTS","UPDATE",$ip,$link1,"");
}	
if($_POST['submitTab10']){
	// Update approval steps Rights
	$rrr="appstep";
	mysqli_query($link1,"UPDATE admin_users SET app_steps_ids='".implode(',',$_REQUEST[$rrr])."' WHERE username='".$req_userid."'")or die(mysqli_error($link1));
	dailyActivity($_SESSION['userid'], $req_userid, "APP STEPS RIGHT", "UPDATE", $ip, $link1, "");
}
///// get user basic details
$res_user = mysqli_query($link1,"SELECT app_steps_ids FROM admin_users WHERE username='".$req_userid."'");
$row_user = mysqli_fetch_assoc($res_user);
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
 <script language="javascript" src="../js/ajax.js"></script>
 <script>
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 ///// multiple check all function
 function checkFunc(field,ind,val){
	var chk=document.getElementById(val+""+ind).checked;
	if(chk==true){ checkAll(field); }
	else{ uncheckAll(field);}
 }
 //////######## Get State ###############//////
 function checkUncheck(val1){
	var arr="";
	var check=document.getElementById(val1).checked;
    if (check==true){
		document.getElementById("region_str").value=(document.getElementById("region_str").value)+","+(document.getElementById(val1).value);
		arr=document.getElementById("region_str").value;
		//alert('checked');
    } 
	if (check==false){
	    //alert('unchecked');
	    document.getElementById("region_str").value=(document.getElementById("region_str").value).replace(","+(document.getElementById(val1).value), "");
		var arr=document.getElementById("region_str").value;
    }
	checkForUC(arr);
   getState(arr);
}
function selfRun(){
	<?php $sql_check=mysqli_query($link1,"select region from access_region where status='Y' and uid='$req_userid'")or die(mysqli_error($link1));
       $num_check=mysqli_num_rows($sql_check);
	   if($num_check > 0){
		   while($row_check=mysqli_fetch_array($sql_check)){
			   $str.=",".$row_check['region'];
		   }
	?>
	var arr="<?=$str?>";
	document.getElementById("region_str").value=arr;
	checkForUC(arr);
	getState(arr);	   
	<?php }
	   else{
	   }
    ?>
}
function checkForUC(val){
	var str=val.split(",");
	var  s=0;
	for(var i=1;i<str.length;i++){
		var chk=document.getElementById(str[i]).checked;
		if(chk==true){
			//alert("checked");
			s=s+1;
		}
	}
	//alert(s);
	if(s==0){
	   document.getElementById("state_dis").style.display="none"; 
	}
	else{
		document.getElementById("state_dis").style.display=""; 
	}
}
///////Get State/////////
function getState(val1){
var strSubmit = "action=getState&value="+val1+"&userid=<?=$req_userid?>";
var strURL = "../includes/getField.php";
var strResultFunc="displayState";
xmlhttpPost(strURL,strSubmit,strResultFunc);
return false;	
}
function displayState(result){
    if(result!="" && result!=0){
	document.getElementById("state_dis").innerHTML=result;
    }
}
//////######## Get State close ###############//////


//////######## Get Location ###############//////
function getLoc(){
	var statename = document.getElementById("state_name").value;
	var locationtyp = document.getElementById("location_type").value;
	  $.ajax({
	    type:'post',
		url:'../includes/getField.php',
		data:{action:"getLocationName",permission_state:statename,permission_loc:locationtyp, usrid:'<?php if(isset($req_userid)){ echo $req_userid;}?>'},
		success:function(data){
	    	$('#location_dis').html(data);
		}
	  });
}
//////######## Get Location close ###############//////
</script>
<script>
$(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
	if(location.hash=="#menu1"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
		//document.getElementById("menu5").style.display="none";
	}
	/*else if(location.hash=="#menu2"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu3"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}*/
	else if(location.hash=="#menu4"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		
		document.getElementById("menu4").style.display="";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
		//document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu6"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu7"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu8"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu9"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu10"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
});
function tabDisplay(){
$(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
if(location.hash=="#menu1"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	/*else if(location.hash=="#menu2"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu3"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}*/
	else if(location.hash=="#menu4"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu6"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";		
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu7"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";		
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu8"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";		
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu9"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";		
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="";
		document.getElementById("menu10").style.display="none";
	}
	else if(location.hash=="#menu10"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";		
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu6").style.display="none";
		document.getElementById("menu7").style.display="none";
		document.getElementById("menu8").style.display="none";
		document.getElementById("menu9").style.display="none";
		document.getElementById("menu10").style.display="none";
	}
}
</script>
</head>
<body onLoad="selfRun();tabDisplay();">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-users"></i> Update User Permission</h2>
      <h4 align="center"><?=$req_username."  (".$req_userid.")";?>
      <?php if($_POST['submitTab']=='Save' || $_POST['submitTab1']=='Save' || $_POST['submitTab2']=='Save' || $_POST['submitTab7']=='Save' || $_POST['submitTab4']=='Save' || $_POST['submitTab6']=='Save' || $_POST['submitTab9']=='Save'  || $_POST['submitTab10']=='Save'){ ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <span style="color:#FF0000"><?php if($_POST['submitTab']=="Save"){ echo "Master/Region";}elseif($_POST['submitTab1']=="Save"){ echo "Processes";}elseif($_POST['submitTab2']=="Save"){ echo "Excel Export";}elseif($_POST['submitTab7']=="Save"){ echo "Brand";}elseif($_POST['submitTab4']=="Save"){ echo "Locations";}elseif($_POST['submitTab6']=="Save"){ echo "Product Category";}elseif($_POST['submitTab9']=="Save"){ echo "App Control";}elseif($_POST['submitTab10']=="Save"){ echo "Approval Steps";}else{} ?> permissions are updated.</span>
   <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
         <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-database fa-lg"></i>&nbsp;&nbsp;Master / Region</a></li>
          <li><a data-toggle="tab" href="#menu1"><i class="fa fa-cogs fa-lg"></i>&nbsp;&nbsp;Processes</a></li>
          <!--<li><a data-toggle="tab" href="#menu2">Excel Export</a></li>
          <li><a data-toggle="tab" href="#menu3">Cancellation</a></li>-->
          <li><a data-toggle="tab" href="#menu4"><i class="fa fa-university fa-lg"></i>&nbsp;&nbsp;Locations</a></li>
          <!--<li><a data-toggle="tab" href="#menu5">Users</a></li>-->
          <li><a data-toggle="tab" href="#menu6"><i class="fa fa-suitcase fa-lg"></i>&nbsp;&nbsp;Product Category</a></li>
          <li><a data-toggle="tab" href="#menu7"><i class="fa fa-tag fa-lg"></i>&nbsp;&nbsp;Brand</a></li>
          <li><a data-toggle="tab" href="#menu8"><i class="fa fa-remove fa-lg"></i>&nbsp;&nbsp;Cancellation</a></li>
          <li><a data-toggle="tab" href="#menu9"><i class="fa fa-mobile fa-lg"></i>&nbsp;&nbsp;App Control</a></li>
          <li><a data-toggle="tab" href="#menu10"><i class="fa fa-gavel fa-lg"></i>&nbsp;&nbsp;Approval Steps</a></li>
         </ul>
         <!-- Tab 1 Master / Region Rights-->
           <div id="home" class="tab-pane fade in active">
          <form id="frm" name="frm" class="form-horizontal" action="" method="post">
          <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm.report)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm.report)" value="Uncheck All" /></div>
                 <table id="myTable" class="table table-hover">
                <?php
				$rs=mysqli_query($link1,"select header_id,header from report_master where status='Y' AND (".$check_module_str.") group by header_id ORDER by header_id");
				$num=mysqli_num_rows($rs);
				if ($num > 0) {
				?>
                 <?php $j=1;
						while($row=mysqli_fetch_array($rs)){
						$report="Select * from report_master where status='Y' and header_id='$row[header_id]' AND (".$check_module_str.") order by header";
						$rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
						?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['header']?></th>
                  </tr>
                </thead>
                <tbody>
                <?php $i=1;
						while($row_report=mysqli_fetch_array($rs_report)){
						if($i%4==1){
						?>
                  <tr>
                  <?php 				}
						$state_acc=mysqli_query($link1,"select report_id from access_report where status='Y' and report_id='$row_report[id]' and uid='$req_userid'")or die(mysqli_error($link1));
						$num=mysqli_num_rows($state_acc);
						?>
                    <td><input style="width:20px" type="checkbox" id="report" name="report[]" value="<?=$row_report['id']?>" <?php if($num > 0) echo "checked";?> />
                <?=$row_report['name']?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 				
				  }
				  $i++;
						}
						} 
                   $j++;
				?>
                </tbody>
              <?php
				}
			  ?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;Region<input type="hidden" name="region_str" id="region_str"/></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input style="width:20px" type='checkbox' name='EAST' id='EAST' value="EAST" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='EAST' and uid='$req_userid'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;EAST</td>
                    <td><input style="width:20px" type='checkbox' name='WEST' id='WEST' value="WEST" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='WEST' and uid='$req_userid'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;WEST</td>
            <td><input style="width:20px" type='checkbox' name='NORTH' id='NORTH' value="NORTH" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='NORTH' and uid='$req_userid'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;NORTH</td>
            <td><input style="width:20px" type='checkbox' name='SOUTH' id='SOUTH' value="SOUTH" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='SOUTH' and uid='$req_userid'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;SOUTH</td>
                  </tr>
                </tbody>
              </table>
              </div>
              <div id="state_dis"></div>
              <div class="form-buttons" align="center">
              <input type="submit" class="btn btn-primary" name="submitTab" id="submitTab" value="Save"> 
              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Next</button>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
              </div>
          </form>
          </div>
          <!-- Tab 2 Processes Rights-->
          <div id="menu1" class="tab-pane fade">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="table-responsive"> 
                <table id="myTable" class="table table-hover">
                <?php 
				$rs=mysqli_query($link1,"select * from function_master where status='Active' AND (".$check_module_str.")");
                $num=mysqli_num_rows($rs);
                if($num > 0){
                   $j=1;
                   while($row=mysqli_fetch_array($rs)){
                   $report="select * from sub_function_master where function_id='$row[function_id]' and status='Y' AND (".$check_module_str.") ORDER by sub_name";
                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
                ?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['function_name']?>&nbsp; <?php if(mysqli_num_rows($rs_report)>1){ ?> <input style="width:20px"  type="checkbox" id="funcTB1<?=$j?>" name="funcTB1<?=$j?>[]" onClick="checkFunc(document.frm1.report1<?=$j?>,'<?=$j?>','funcTB1');"/> <?php } ?> </th>
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
                    $state_acc=mysqli_query($link1,"select function_id from access_function where status='Y' and function_id='$row_report[id]' and uid='$req_userid'")or die(mysqli_error($link1));
                    $num1=mysqli_num_rows($state_acc);?>
                    <td><input style="width:20px"  type="checkbox" id="report1<?=$j?>" name="report1<?=$j?>[]" value="<?=$row_report['id']?>" <?php if($num1 > 0) echo "checked";?> /><?=$row_report['sub_name']?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 
                        }
						$i++;
                    }////// Close 2nd While Loop of TAB 2
                    $j++;
				   }  
				}?>
                </tbody>
                </table>
                </div>
            <div class="form-buttons" align="center"><input name="count_repTab1" id="count_repTab1" type="hidden" value="<?=$j?>"/>
              <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#home'">Previous</button>
              <input type="submit" class="btn btn-primary" name="submitTab1" id="submitTab1" value="Save"> 
              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu4'">Next</button>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
            </div>
          </form>
          </div>
          <!-- Tab 5 Location Rights-->
          <div id="menu4" class="tab-pane fade">
          <form id="frm4" name="frm4" class="form-horizontal" action="" method="post">
          <div class="table-responsive">
                <table id="myTable2" class="table table-hover">
                  <thead>
                    <tr>
                      <td style="border:none"><strong>Location Type:</strong>
                      	<select name="location_type" id="location_type" class="form-control custom-select" style="width:250px;" onChange="getLoc();">
                          <option value="">--Select State--</option>
                          <?php 
							$res_loctype=mysqli_query($link1,"select * from location_type where status='A'"); 
				   			while($row_loctype=mysqli_fetch_assoc($res_loctype)){
                			?>
                          <option value="<?=$row_loctype['locationtype']?>"><?=$row_loctype['locationname']?></option>
                          <?php
							}
							?>
                        </select>
                      </td>
                      <td style="border:none"><strong>State:</strong>
                        <select name="state_name" id="state_name" class="form-control custom-select" style="width:250px;" onChange="getLoc();">
                          <option value="">--Select State--</option>
                          <?php 
							$rs2=mysqli_query($link1,"SELECT * FROM state_master ORDER BY state");
                			while($row=mysqli_fetch_array($rs2)){
                			?>
                          <option value="<?=$row['state']?>"><?=$row['state']?></option>
                          <?php
							}
							?>
                        </select>
                        </td>
                    </tr>
                  </thead>
                </table>
              <span id="location_dis"></span> 
              </div>
             <div class="form-buttons" align="center">
             <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Previous</button>
             <input type="submit" class="btn btn-primary" name="submitTab4" id="submitTab4" value="Save"> 
             <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu6'">Next</button>
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
             </div>
          </form>
          </div>
          <!-- Tab 6 Users Rights-->
          <?php /*?><div id="menu5" class="tab-pane fade">
          <form id="frm5" name="frm5" class="form-horizontal" action="" method="post">
          <div class="table-responsive">
           <table id="myTable" class="table table-hover">
            <thead>
                  <tr>
                    <th style="border:none">&nbsp;Admin Users</th>
                  </tr>
                </thead>
                <tbody>
                 <?php
				  $k=1;
				    if($_REQUEST['userlevel']==1){$usrlvl=" utype='1'  and username='$req_userid'";}else{ $usrlvl=" utype < '$_REQUEST[userlevel]'";}
				   $res_adminusr=mysqli_query($link1,"select * from admin_users where status='active' and $usrlvl"); 
				   while($row_adminusr=mysqli_fetch_assoc($res_adminusr)){
				   	if($k%4==1){   
				  ?>
                  <tr>
                  <?php }?>
                    <td><input style="width:20px" type="checkbox" name="mapped_code[]" value="<?=$row_adminusr[username]?>" <?php if(mysqli_num_rows(mysqli_query($link1,"select mapped_code from mapped_user where status='Y' and uid='$req_userid' and mapped_code='$row_adminusr[username]'"))>0){ echo "checked";} ?>/>&nbsp;&nbsp;<?=$row_adminusr[name]." (".$row_adminusr[username].")"?></td>
                    <?php if($k/4==0){?>
                    </tr>
                  <?php 
				          }
						  $k++;
				   }
				  ?>  
                </tbody>
              </table>
              </div>
            <div class="form-buttons" align="center">
             <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu4'">Previous</button>
             <input type="submit" class="btn btn-primary" name="submitTab5" id="submitTab5" value="Save"> 
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
             </div>
          </form>
          </div>  <?php */?>
          
          <!-- Tab 8 product category Rights-->
          <div id="menu6" class="tab-pane fade">
            <form id="frm6" name="frm6" class="form-horizontal" action="" method="post">
              <div class="table-responsive">
                <table id="myTable8" class="table table-hover">
                  <tbody>
                  	<?php 
					  $arr_productcat=array();
					  $arr_product=array();
					  $res_prdcat=mysqli_query($link1,"select distinct(product_category) as prdcat, product_category from product_sub_category where status='1' order by product_category")or die(mysqli_error($link1));
					  while($row_prdcat=mysqli_fetch_array($res_prdcat)){
							$arr_productcat[]=$row_prdcat[0];
							$arr_product[]=$row_prdcat[1];
					  }
					  ?>
                  	<?php
                 	for($j=0;$j<count($arr_productcat);$j++){?>
                  	<tr>
                    	<td style="border:none" class="<?=$tableheadcolor?>"><?=$arr_product[$j]?>&nbsp;<input style="width:20px"  type="checkbox" id="funcTB6<?=$j?>" name="funcTB6<?=$j?>[]" onClick="checkFunc(document.frm6.report14<?=$j?>,'<?=$j?>','funcTB6');"/></td>
                    </tr>
                    <?php
                   $i=1;
                   $report="select * from product_sub_category where product_category='$arr_productcat[$j]' and status='1' ORDER by prod_sub_cat";
                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
                   while($row_report=mysqli_fetch_array($rs_report)){
                     if($i%5==1){?>
                    <tr>
					<?php 
                     }
                   $state_acc=mysqli_query($link1,"select id from mapped_productcat where status='Y' and prod_subcatid='$row_report[psubcatid]' and product_cat='$arr_productcat[$j]' and userid='$req_userid'")or die(mysqli_error($link1));
                   $num1=mysqli_num_rows($state_acc);?>
                      <td><input style="width:20px"  type="checkbox" id="report14<?=$j?>" name="report14<?=$j?>[]" value="<?=$row_report['psubcatid']?>" <?php if($num1 > 0) echo "checked";?> />&nbsp;<?=$row_report['prod_sub_cat']?></td>
                    <?php if($i/5==0){?>
            		</tr>
                    <?php 
					  }
			        $i++;
				   }////// close while loop
				 }////// close for loop
                 ?>
                    <input name="count_repTab6" id="count_repTab6" type="hidden" value="<?=$j?>"/>
                  </tbody>
                </table>
              </div>
              <div class="form-buttons" align="center">
                <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu4'">Previous</button>
             	<input type="submit" class="btn btn-primary" name="submitTab6" id="submitTab6" value="Save">
                <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu7'">Next</button>
             	<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
                </div>
            </form>
           </div>
           <!-- Tab 8 brand Rights-->
          <div id="menu7" class="tab-pane fade">
            <form id="frm7" name="frm7" class="form-horizontal" action="" method="post">
              <div class="table-responsive">
              	<div align="right">
                	<button class='btn<?=$btncolor?>' id="chkall" type="button" name="chkall" onClick="checkAll(document.frm7.brand)"><i class="fa fa-check-square-o fa-lg"></i>&nbsp;&nbsp;Check All</button>
                    <button class='btn<?=$btncolor?>' id="unchkall" type="button" name="unchkall" onClick="uncheckAll(document.frm7.brand)"><i class="fa fa-square-o fa-lg"></i>&nbsp;&nbsp;Uncheck All</button>
                </div>
                <table id="myTable8" class="table table-hover">
                  <tbody>
                  	<?php 
					 $i=1;
					 $res_brand = mysqli_query($link1,"SELECT id,make FROM make_master where status = '1' order by make");
                     while($row_brand=mysqli_fetch_array($res_brand)){
                     if($i%5==1){ ?>
                  	<tr>
                    <?php 
						}
						$brand_acc = mysqli_query($link1,"SELECT id FROM mapped_brand where status='Y' and brand='".$row_brand["id"]."' and userid='".$req_userid."'")or die(mysqli_error($link1));
					$num = mysqli_num_rows($brand_acc);
                    ?>
                  	<td><input style="width:20px" type="checkbox" id="brand" name="brand[]" value="<?=$row_brand['id']?>" <?php if($num > 0) echo "checked";?> />&nbsp;<?=$row_brand['make']?></td>
                    <?php if($i/8==0){?>
                   </tr>
                    <?php 
					 }$i++;
					 }///// close 2nd while loop of TAB 1
					 ?>
                   </tbody>
                </table>
              </div>
              <div class="form-buttons" align="center">
                <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu6'">Previous</button>
             	<input type="submit" class="btn btn-primary" name="submitTab7" id="submitTab7" value="Save">
                <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu8'">Next</button>
             	<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
                </div>
            </form>
           </div>
          <!-- Tab 9 Cancel Rights-->
          <div id="menu8" class="tab-pane fade">
            <form id="frm8" name="frm8" class="form-horizontal" action="" method="post">
              <div class="table-responsive">
                <table id="myTable" class="table table-hover">
                <?php 
				$rs=mysqli_query($link1,"SELECT module_name FROM operation_rights WHERE ops_name='CANCEL' AND status = 'Y' GROUP BY module_name");
                $num=mysqli_num_rows($rs);
                if($num > 0){
                   $j=1;
                   while($row=mysqli_fetch_array($rs)){
                   $report="SELECT id, tab_name FROM operation_rights WHERE ops_name='CANCEL' AND status = 'Y' AND module_name='".$row["module_name"]."' ORDER BY tab_name";
                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
                ?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['module_name']?>&nbsp; <?php if(mysqli_num_rows($rs_report)>1){ ?> <input style="width:20px"  type="checkbox" id="funcTB9<?=$j?>" name="funcTB9<?=$j?>[]" onClick="checkFunc(document.frm8.report9<?=$j?>,'<?=$j?>','funcTB9');"/> <?php } ?> </th>
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
                    $state_acc=mysqli_query($link1,"SELECT ops_id FROM access_ops_rights WHERE status='Y' AND ops_id='".$row_report['id']."' AND uid='".$req_userid."'")or die(mysqli_error($link1));
                    $num1=mysqli_num_rows($state_acc);?>
                    <td><input style="width:20px"  type="checkbox" id="report9<?=$j?>" name="report9<?=$j?>[]" value="<?=$row_report['id']?>" <?php if($num1 > 0) echo "checked";?> /><?=$row_report['tab_name']?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 
                        }
						$i++;
                    }////// Close 2nd While Loop of TAB 2
                    $j++;
				   }  
				}?>
                </tbody>
                </table>
              </div>
              <div class="form-buttons" align="center"><input name="count_repTab8" id="count_repTab8" type="hidden" value="<?=$j?>"/>
                <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu7'">Previous</button>
             	<input type="submit" class="btn btn-primary" name="submitTab8" id="submitTab8" value="Save">
                <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu9'">Next</button>
             	<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
                </div>
            </form>
           </div>
           <!-- Tab 10 App Tab Rights-->
          <div id="menu9" class="tab-pane fade">
            <form id="frm9" name="frm9" class="form-horizontal" action="" method="post">
              <div class="table-responsive">
              	<div align="right">
                	<button class='btn<?=$btncolor?>' id="chkall" type="button" name="chkall" onClick="checkAll(document.frm9.apptab)"><i class="fa fa-check-square-o fa-lg"></i>&nbsp;&nbsp;Check All</button>
                    <button class='btn<?=$btncolor?>' id="unchkall" type="button" name="unchkall" onClick="uncheckAll(document.frm9.apptab)"><i class="fa fa-square-o fa-lg"></i>&nbsp;&nbsp;Uncheck All</button>
                </div>
                <table id="myTable" class="table table-hover">
                <?php 
				$rs=mysqli_query($link1,"SELECT tabid,subtabname FROM app_tab_master WHERE status = '1' ORDER BY subtabseq");
                $num=mysqli_num_rows($rs);
                if($num > 0){
                   $j=1;
                   while($row=mysqli_fetch_array($rs)){
                ?>
                <tbody>
                  <tr>
                  <?php
                    $app_tab=mysqli_query($link1,"SELECT id  FROM access_app_tab WHERE tabid = '".$row['tabid']."' AND userid = '".$req_userid."' AND status = '1'")or die(mysqli_error($link1));
                    $num1=mysqli_num_rows($app_tab);?>
                    <td><input style="width:20px"  type="checkbox" id="apptab" name="apptab[]" value="<?=$row['tabid']?>" <?php if($num1 > 0) echo "checked";?> />&nbsp;&nbsp;<?=$row['subtabname']?></td>
                  </tr>
                  <?php 
                    $j++;
				   }  
				}?>
                </tbody>
                </table>
              </div>
              <div class="form-buttons" align="center"><input name="count_repTab9" id="count_repTab9" type="hidden" value="<?=$j?>"/>
                <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu8'">Previous</button>
             	<input type="submit" class="btn btn-primary" name="submitTab9" id="submitTab9" value="Save">
                <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu10'">Next</button>
             	<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
                </div>
            </form>
           </div>
           
           <!-- Tab 11 Approval steps Rights-->
          <div id="menu10" class="tab-pane fade">
            <form id="frm10" name="frm10" class="form-horizontal" action="" method="post">
              <div class="table-responsive">
                <div class="form-buttons" style="float:right">
                  <input name="CheckAll" type="button" class="btn<?=$btncolor?>" onClick="checkAll(document.frm10.appstep)" value="Check All" />
                  <input name="UnCheckAll" type="button" class="btn<?=$btncolor?>" onClick="uncheckAll(document.frm10.appstep)" value="Uncheck All" />
                </div>
                <table id="myTable4" class="table table-hover">
                    <thead>
                        <tr class="<?= $tableheadcolor ?>">
                            <th width="10%">Select</th>
                            <th width="90%">Approval Steps</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
						$assign_skill = explode(",",$row_user['app_steps_ids']);
                        $j=1;
                        $res_workcat = mysqli_query($link1,"SELECT * FROM approval_step_master WHERE status='1' ORDER BY process_id");
                        while($row_workcat=mysqli_fetch_assoc($res_workcat)){
                            //// check access skill
                            if(in_array($row_workcat['process_id'], $assign_skill)){ $numacc_skill=1;}else{$numacc_skill=0;}
                        ?>
                        <tr>
                            <td align="center"><input style="width:20px" type="checkbox" id="appstep" name="appstep[]" value="<?=$row_workcat['process_id']?>" <?php if($numacc_skill > 0) echo "checked";?>/></td>
                            <td align="left"><?=$row_workcat['process_name']?></td>
                        </tr>
                        <?php  $j++;}?>
                    </tbody>
                </table>
              </div>
              <div class="form-buttons" align="center"><input name="count_repTab10" id="count_repTab10" type="hidden" value="<?=$j?>"/>
                <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu9'">Previous</button>
             	<input type="submit" class="btn btn-primary" name="submitTab10" id="submitTab10" value="Save">
             	<input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo base64_encode($req_userid);?><?=$pagenav?>'">
                </div>
            </form>
           </div>
          
            
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