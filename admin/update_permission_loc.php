<?php 
//require_once("../config/config.php");
@extract($_POST);
############# if form 1 is submitted #################
if($_POST['submitTab']){
	// Update Masters Rights
	$report=$_REQUEST[report];
	$count=count($report);
    $j=0;
    mysqli_query($link1,"update access_report set status='' where uid='$_REQUEST[userid]' ")or die("er3".mysqli_error($link1));
    while($j < $count){
         if($report[$j]==''){
            $status='';
         }else{
            $status='Y';
		 }
         // alrady exist
         if(mysqli_num_rows(mysqli_query($link1,"select report_id from access_report where uid='$_REQUEST[userid]' and report_id='$report[$j]'"))>0){
            mysqli_query($link1,"update access_report set status='$status' where uid='$_REQUEST[userid]' and report_id='$report[$j]'")or die(mysqli_error($link1));
         }else{
            mysqli_query($link1,"insert into access_report set uid='$_REQUEST[userid]',report_id='$report[$j]',status='$status'")or die(mysqli_error($link1));
		 }
         $j++;
	}
	// end Master Rights
	// Update Region Rights
    if($_REQUEST[EAST]==''){ $status=''; }else{ $status='Y'; }
    if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$_REQUEST[userid]' and region='EAST'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$_REQUEST[userid]' and region='EAST'")or die(mysqli_error($link1));
    }else{
      mysqli_query($link1,"insert into access_region set uid='$_REQUEST[userid]',region='EAST',status='$status'")or die(mysqli_error($link1));
	}
   if($_REQUEST[WEST]==''){ $status=''; }else{ $status='Y'; }
   if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$_REQUEST[userid]' and region='WEST'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$_REQUEST[userid]' and region='WEST'")or die(mysqli_error($link1));
   }else{
      mysqli_query($link1,"insert into access_region set uid='$_REQUEST[userid]',region='WEST',status='$status'")or die(mysqli_error($link1));
   }
   if($_REQUEST[NORTH]==''){ $status=''; }else{ $status='Y'; }
   if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$_REQUEST[userid]' and region='NORTH'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$_REQUEST[userid]' and region='NORTH'")or die(mysqli_error($link1));
   }else{
      mysqli_query($link1,"insert into access_region set uid='$_REQUEST[userid]',region='NORTH',status='$status'")or die(mysqli_error($link1));
   }
   if($_REQUEST[SOUTH]==''){ $status=''; }else{ $status='Y'; }
   if(mysqli_num_rows(mysqli_query($link1,"select region from access_region where uid='$_REQUEST[userid]' and region='SOUTH'"))>0){
      mysqli_query($link1,"update access_region set status='$status' where uid='$_REQUEST[userid]' and region='SOUTH'")or die(mysqli_error($link1));
   }else{
      mysqli_query($link1,"insert into access_region set uid='$_REQUEST[userid]',region='SOUTH',status='$status'")or die(mysqli_error($link1));
   }
   // end Region Rights
   // Update State Rights
   $state=$_REQUEST[state];
   $count=count($state);
   $i=0;
   mysqli_query($link1,"update access_state set status='' where uid='$_REQUEST[userid]' ")or die(mysqli_error($link1));
   while($i < $count){
      if($state[$i]==''){ $status=''; }else{ $status='Y'; }
      if(mysqli_num_rows(mysqli_query($link1,"select state from access_state where uid='$_REQUEST[userid]' and state='$state[$i]'"))>0){
         mysqli_query($link1,"update access_state set status='$status' where uid='$_REQUEST[userid]' and state='$state[$i]'")or die(mysqli_error($link1));
      }else{
         mysqli_query($link1,"insert into access_state set uid='$_REQUEST[userid]',state='$state[$i]',status='$status'")or die(mysqli_error($link1));
	  }
      $i++;	
   }
   // end State Rights
}
############# if form 2 is submitted #################
if($_POST['submitTab1']){
	// Update Function Rights
	mysqli_query($link1,"update access_function set status='' where uid='$_REQUEST[userid]' ")or die(mysqli_error($link1));
	for($s=1;$s<$_REQUEST[count_repTab1];$s++){
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
			 // alrady exist
			 if(mysqli_num_rows(mysqli_query($link1,"select function_id from access_function where uid='$_REQUEST[userid]' and function_id='$rep1[$j]'"))>0){
				mysqli_query($link1,"update access_function set status='$status1' where uid='$_REQUEST[userid]' and function_id='$rep1[$j]'")or die(mysqli_error($link1));
			 }else{
				mysqli_query($link1,"insert into access_function set uid='$_REQUEST[userid]',function_id='$rep1[$j]',status='$status1'")or die(mysqli_error($link1));
			 }
		   $j++;
		}
		$count=0;
	}
	// end Function Rights
}
############# if form 3 is submitted #################
if($_POST['submitTab2']){
	// Updatign Excel Export Rights
	$report2=$_REQUEST[report2];
	$count2=count($report2);
	$l=0;
	mysqli_query($link1,"update excel_export_right set status='' where user_id='$_REQUEST[userid]' ")or die(mysqli_error($link1));
	while($l < $count2){
	   if($report2[$l]==''){
	      $status2='';
	   }else{
	      $status2='Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select process_id from excel_export_right where user_id='$_REQUEST[userid]' and process_id='$report2[$l]'"))>0){
	      mysqli_query($link1,"update excel_export_right set status='$status2' where user_id='$_REQUEST[userid]' and process_id='$report2[$l]'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into excel_export_right set user_id='$_REQUEST[userid]',process_id='$report2[$l]',status='$status2'")or die(mysqli_error($link1));
	   }
	   $l++;
	}
	// end Excel Export Rights
}
############# if form 4 is submitted #################
if($_POST['submitTab3']){
	// Updatign Cancellation Rights
	$report3=$_REQUEST[report3];
	$count3=count($report3);
	$l=0;
	mysqli_query($link1,"update access_cancel_rights set status='' where uid='$_REQUEST[userid]' ")or die(mysqli_error($link1));
	while($l < $count3){
	   if($report3[$l]==''){
	      $status3='';
	   }else{
	      $status3='Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select cancel_type from access_cancel_rights where uid='$_REQUEST[userid]' and cancel_type='$report3[$l]'"))>0){
	      mysqli_query($link1,"update access_cancel_rights set status='$status3' where uid='$_REQUEST[userid]' and cancel_type='$report3[$l]'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into access_cancel_rights set uid='$_REQUEST[userid]',cancel_type='$report3[$l]',status='$status3'")or die(mysqli_error($link1));
	   }
	   $l++;
	}
	// end Cancellation Rights
}
############# if form 5 is submitted #################
if($_POST['submitTab4']){
	// Updatign Location Type Rights
	$report41=$_REQUEST[report41];
	$count41=count($report41);
	$l=0;
	mysqli_query($link1,"update access_role set status='' where uid='$_REQUEST[userid]' ")or die(mysqli_error($link1));
	while($l < $count41){
	   if($report41[$l]==''){
	      $status41='';
	   }else{
	      $status41='Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select role_id from access_role where uid='$_REQUEST[userid]' and role_id='$report41[$l]'"))>0){
	      mysqli_query($link1,"update access_role set status='$status41' where uid='$_REQUEST[userid]' and role_id='$report41[$l]'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into access_role set uid='$_REQUEST[userid]',role_id='$report41[$l]',status='$status41'")or die(mysqli_error($link1));
	   }
	   $l++;
	}
	// end Location Type Rights
	// Updatign Location Rights
	$report4=$_REQUEST[report4];
	$count4=count($report4);
	$k=0;
	mysqli_query($link1,"update access_location set status='' where uid='$_REQUEST[userid]' ")or die(mysqli_error($link1));
	while($k < $count4){
	   if($report4[$k]==''){
	      $status4='';
	   }else{
	      $status4='Y';
	   }
	   // alrady exist
	   if(mysqli_num_rows(mysqli_query($link1,"select location_id from access_location where uid='$_REQUEST[userid]' and location_id='$report4[$k]'"))>0){
	      mysqli_query($link1,"update access_location set status='$status4' where uid='$_REQUEST[userid]' and location_id='$report4[$k]'")or die(mysqli_error($link1));
	   }else{
	      mysqli_query($link1,"insert into access_location set uid='$_REQUEST[userid]',location_id='$report4[$k]',status='$status4'")or die(mysqli_error($link1));
	   }
	   $k++;
	}
	// end Location Rights
}
############# if form 6 is submitted #################
if($_POST['submitTab5']){
	// Updating Users Rights
	$mapped_code=$_REQUEST[mapped_code];
	$count=count($mapped_code);
	$j=0;
	mysqli_query($link1,"update mapped_user set status='' where uid='$_REQUEST[userid]' ")or die(mysqli_error($link1));
	while($j < $count){
      if($mapped_code[$j]==''){
         $status='';
	  }else{
		 $status='Y';
	  }
	  // alrady exist
	  if(mysqli_num_rows(mysqli_query($link1,"select mapped_code from mapped_user where uid='$_REQUEST[userid]' and mapped_code='$report[$j]'"))>0){
	     mysqli_query($link1,"update mapped_user set mapped_code='$mapped_code[$j]',status='$status' , update_date='$today' where uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
	  }else{
	     mysqli_query($link1,"insert into mapped_user set uid='$_REQUEST[userid]',mapped_code='$mapped_code[$j]',status='$status', update_date='$today'")or die(mysqli_error($link1));
	  }
	  $j++;
	}
	// end Users Rights
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
	<?php $sql_check=mysqli_query($link1,"select region from access_region where status='Y' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
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
var strSubmit = "action=getState&value="+val1+"&userid=<?=$_REQUEST[userid]?>";
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
function checkUncheckLC(val1){
	var arr="";
	var check=document.getElementById(val1).checked;
    if (check==true){
		document.getElementById("role_str").value=(document.getElementById("role_str").value)+","+(document.getElementById(val1).value);
		arr=document.getElementById("role_str").value;
		//alert('checked');
    } 
	if (check==false){
	    //alert('unchecked');
	    document.getElementById("role_str").value=(document.getElementById("role_str").value).replace(","+(document.getElementById(val1).value), "");
		var arr=document.getElementById("role_str").value;
    }
	checkForUCLC(arr);
    getLocation(arr);
}
function getLocation(val1){
//if(val1!="")
//{
var strSubmit = "action=getLocation&value="+val1+"&userid=<?=$_REQUEST[userid]?>";
var strURL = "../includes/getField.php";
var strResultFunc="displayLocation";
xmlhttpPost(strURL,strSubmit,strResultFunc);
return false;	
//}
}
function displayLocation(result){
	//alert(result);
    if(result!="" && result!=0){
	document.getElementById("location_dis").innerHTML=result;
    }
}
function selfRunLC(){
	<?php $sql_check=mysqli_query($link1,"select role_id from access_role where status='Y' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
       $num_check=mysqli_num_rows($sql_check);
	   if($num_check > 0){
		   while($row_check=mysqli_fetch_array($sql_check)){
			   $str.=",".$row_check['role_id'];
		   }
	?>
	var arr="<?=$str?>";
	document.getElementById("role_str").value=arr;
	checkForUCLC(arr);
	getLocation(arr);	   
	<?php }
	   else{
	   }
    ?>
}
function checkForUCLC(val){
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
	   document.getElementById("location_dis").style.display="none"; 
	}
	else{
		document.getElementById("location_dis").style.display=""; 
	}
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
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu2"){
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
	}
	else if(location.hash=="#menu4"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu5"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
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
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu2"){
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
	}
	else if(location.hash=="#menu4"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu5"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
}
</script>
</head>
<body onLoad="selfRun();selfRunLC();tabDisplay();">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-users"></i> Update User Permission</h2>
      <h4 align="center"><?=$_REQUEST[u_name]."  (".$_REQUEST['userid'].")";?>
      <?php if($_POST[submitTab]=='Save' || $_POST[submitTab1]=='Save' || $_POST[submitTab2]=='Save' || $_POST[submitTab3]=='Save' || $_POST[submitTab4]=='Save' || $_POST[submitTab5]=='Save'){ ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <span style="color:#FF0000"><?php if($_POST[submitTab]=="Save"){ echo "Master/Region";}elseif($_POST[submitTab1]=="Save"){ echo "Processes";}elseif($_POST[submitTab2]=="Save"){ echo "Excel Export";}elseif($_POST[submitTab3]=="Save"){ echo "Cancellation";}elseif($_POST[submitTab4]=="Save"){ echo "Locations";}elseif($_POST[submitTab5]=="Save"){ echo "Users";} ?> permissions are updated.</span>
   <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
         <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Master / Region</a></li>
          <li><a data-toggle="tab" href="#menu1">Processes</a></li>
          <li><a data-toggle="tab" href="#menu2">Excel Export</a></li>
          <li><a data-toggle="tab" href="#menu3">Cancellation</a></li>
          <li><a data-toggle="tab" href="#menu4">Locations</a></li>
          <li><a data-toggle="tab" href="#menu5">Users</a></li>
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
				$rs=mysqli_query($link1,"select header_id,header from report_master where status='Y'  and utype = '2' group by header_id ORDER by header_id");
				$num=mysqli_num_rows($rs);
				if ($num > 0) {
				?>
                 <?php $j=1;
						while($row=mysqli_fetch_array($rs)){
						$report="Select * from report_master where status='Y'  and utype = '2' and header_id='$row[header_id]' order by header";
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
						$state_acc=mysqli_query($link1,"select report_id from access_report where status='Y' and report_id='$row_report[id]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
						$num=mysqli_num_rows($state_acc);
						?>
                    <td><input style="width:20px" type="checkbox" id="report" name="report[]" value="<?=$row_report[id]?>" <?php if($num > 0) echo "checked";?> />
                <?=$row_report[name]?></td>
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
                    <td><input style="width:20px" type='checkbox' name='EAST' id='EAST' value="EAST" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='EAST' and uid='$_REQUEST[userid]'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;EAST</td>
                    <td><input style="width:20px" type='checkbox' name='WEST' id='WEST' value="WEST" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='WEST' and uid='$_REQUEST[userid]'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;WEST</td>
            <td><input style="width:20px" type='checkbox' name='NORTH' id='NORTH' value="NORTH" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='NORTH' and uid='$_REQUEST[userid]'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;NORTH</td>
            <td><input style="width:20px" type='checkbox' name='SOUTH' id='SOUTH' value="SOUTH" <?php if(mysqli_num_rows(mysqli_query($link1,"Select region from access_region where status='Y' and region='SOUTH' and uid='$_REQUEST[userid]'"))>0){ echo "checked";} ?> onClick="checkUncheck(this.value);"/>&nbsp;&nbsp;SOUTH</td>
                  </tr>
                </tbody>
              </table>
              </div>
              <div id="state_dis"></div>
              <div class="form-buttons" align="center">
              <input type="submit" class="btn btn-primary" name="submitTab" id="submitTab" value="Save"> 
              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Next</button>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
              </div>
          </form>
          </div>
          <!-- Tab 2 Processes Rights-->
          <div id="menu1" class="tab-pane fade">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="table-responsive"> 
                <table id="myTable" class="table table-hover">
                <?php 
				$rs=mysqli_query($link1,"select * from function_master where status='Active' and utype = '2' ");
                $num=mysqli_num_rows($rs);
                if($num > 0){
                   $j=1;
                   while($row=mysqli_fetch_array($rs)){
                   $report="select * from sub_function_master where function_id='$row[function_id]' and status='Y' and utype = '2' ORDER by sub_name";
                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
                ?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row[function_name]?>&nbsp;<input style="width:20px"  type="checkbox" id="funcTB1<?=$j?>" name="funcTB1<?=$j?>[]" onClick="checkFunc(document.frm1.report1<?=$j?>,'<?=$j?>','funcTB1');"/> </th>
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
                    $state_acc=mysqli_query($link1,"select function_id from access_function where status='Y' and function_id='$row_report[id]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
                    $num1=mysqli_num_rows($state_acc);?>
                    <td><input style="width:20px"  type="checkbox" id="report1<?=$j?>" name="report1<?=$j?>[]" value="<?=$row_report[id]?>" <?php if($num1 > 0) echo "checked";?> /><?=$row_report[sub_name]?></td>
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
              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Next</button>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
            </div>
          </form>
          </div>
          <!-- Tab 3 Excel Export Rights-->
          <div id="menu2" class="tab-pane fade">
          <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
            <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm2.report2)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm2.report2)" value="Uncheck All" /></div>
                 <table id="myTable" class="table table-hover">
                <?php
				$rs=mysqli_query($link1,"select * from excel_cancel_rights where status='A' group by tab_name");
				$num=mysqli_num_rows($rs);
				if ($num > 0) {
				?>
                 <?php $j=1;
						while($row=mysqli_fetch_array($rs)){
						$report="Select * from excel_cancel_rights where status='A' and tab_name='$row[tab_name]' and tab_type='EXCEL' order by transaction_type";
						$rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
						?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['tab_name']?></th>
                  </tr>
                </thead>
                <tbody>
                <?php $i=1;
						while($row_report=mysqli_fetch_array($rs_report)){
						if($i%4==1){
						?>
                  <tr>
                  <?php 				}
						$state_acc=mysqli_query($link1,"select process_id from excel_export_right where status='Y' and process_id='$row_report[id]' and user_id='$_REQUEST[userid]'")or die(mysqli_error($link1));
						$num=mysqli_num_rows($state_acc);
						?>
                    <td><input style="width:20px" type="checkbox" id="report2" name="report2[]" value="<?=$row_report[id]?>" <?php if($num > 0) echo "checked";?> />
                <?=$row_report[transaction_type]?></td>
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
              </table>
              </div>
            <div class="form-buttons" align="center">
             <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Previous</button>
             <input type="submit" class="btn btn-primary" name="submitTab2" id="submitTab2" value="Save"> 
             <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu3'">Next</button>
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
             </div>
          </form>
          </div>
          <!-- Tab 4 Cancellation Rights-->
          <div id="menu3" class="tab-pane fade">
          <form id="frm3" name="frm3" class="form-horizontal" action="" method="post">
            <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm3.report3)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm3.report3)" value="Uncheck All" /></div>
                 <table id="myTable" class="table table-hover">
                <?php
				$rs=mysqli_query($link1,"select * from excel_cancel_rights where status='A' and utype = '2' group by tab_name");
				$num=mysqli_num_rows($rs);
				if ($num > 0) {
				?>

                 <?php $j=1;
						while($row=mysqli_fetch_array($rs)){
						$report="Select * from excel_cancel_rights where status='A' and tab_name='$row[tab_name]' and tab_type='CANCEL' and utype = '2' order by transaction_type";
						$rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
						?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['tab_name']?></th>
                  </tr>
                </thead>
                <tbody>
                <?php $i=1;
						while($row_report=mysqli_fetch_array($rs_report)){
						if($i%4==1){
						?>
                  <tr>
                  <?php 				}
						$state_acc=mysqli_query($link1,"select cancel_type from access_cancel_rights where status='Y' and cancel_type='$row_report[id]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
						$num=mysqli_num_rows($state_acc);
						?>
                    <td><input style="width:20px" type="checkbox" id="report3" name="report3[]" value="<?=$row_report[id]?>" <?php if($num > 0) echo "checked";?> />
                <?=$row_report[transaction_type]?></td>
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
              </table>
              </div>
            <div class="form-buttons" align="center">
             <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Previous</button>
             <input type="submit" class="btn btn-primary" name="submitTab3" id="submitTab3" value="Save"> 
             <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu4'">Next</button>
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
             </div>
          </form>
          </div>
          <!-- Tab 5 Location Rights-->
          <div id="menu4" class="tab-pane fade">
          <form id="frm4" name="frm4" class="form-horizontal" action="" method="post">
          <div class="table-responsive"> 
          <table id="myTable" class="table table-hover">
            <thead>
                  <tr>
                    <th style="border:none">&nbsp;Location Type<input type="hidden" name="role_str" id="role_str"/></th>
                  </tr>
                </thead>
                <tbody>
                 <?php
				  $k=1;
				   $res_loctype=mysqli_query($link1,"select * from location_type where status='A'"); 
				   while($row_loctype=mysqli_fetch_assoc($res_loctype)){
				   	if($k%6==1){   
				  ?>
                  <tr>
                  <?php }?>
                    <td><input style="width:20px" type='checkbox' name="report41[]" id='<?=$row_loctype[locationtype]?>' value="<?=$row_loctype[locationtype]?>" <?php if(mysqli_num_rows(mysqli_query($link1,"select role_id from access_role where status='Y' and role_id='$row_loctype[locationtype]' and uid='$_REQUEST[userid]'"))>0){ echo "checked";} ?> onClick="checkUncheckLC(this.value);"/>&nbsp;&nbsp;<?=$row_loctype[locationname]?></td>
                    <?php if($k/6==0){?>
                    </tr>
                  <?php 
				          }
						  $k++;
				   }
				  ?>  
                  
                </tbody>
              </table>
              </div>
              <div id="location_dis"></div>
             <div class="form-buttons" align="center">
             <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu3'">Previous</button>
             <input type="submit" class="btn btn-primary" name="submitTab4" id="submitTab4" value="Save"> 
             <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu5'">Next</button>
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
             </div>
          </form>
          </div>
          <!-- Tab 6 Users Rights-->
          <div id="menu5" class="tab-pane fade">
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
				    if($_REQUEST['userlevel']==1){$usrlvl=" utype='1'  and username='$_REQUEST[userid]'";}else{ $usrlvl=" utype < '$_REQUEST[userlevel]'";}
				   $res_adminusr=mysqli_query($link1,"select * from admin_users where status='active'  and tab_type = '1' and $usrlvl"); 
				   while($row_adminusr=mysqli_fetch_assoc($res_adminusr)){
				   	if($k%4==1){   
				  ?>
                  <tr>
                  <?php }?>
                    <td><input style="width:20px" type="checkbox" name="mapped_code[]" value="<?=$row_adminusr[username]?>" <?php if(mysqli_num_rows(mysqli_query($link1,"select mapped_code from mapped_user where status='Y' and uid='$_REQUEST[userid]' and mapped_code='$row_adminusr[username]'"))>0){ echo "checked";} ?>/>&nbsp;&nbsp;<?=$row_adminusr[name]." (".$row_adminusr[username].")"?></td>
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
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
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