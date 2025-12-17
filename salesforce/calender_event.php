<?php
require_once("../config/config.php");
////// case  if we want to Add new task
@extract($_POST);
if($_POST){
   if ($_POST['addtask']=='ADD'){
   	$docno = date("YmdHis");
   	//////// 
	$sql = "INSERT INTO pjp_data SET document_no = '".$docno."', pjp_name='BEAT ADD', plan_date ='".$caldate."',task ='".$task."',assigned_user ='".$_SESSION["userid"]."',visit_area ='".$visit_area."',entry_date ='".$today."',entry_by='".$_SESSION['userid']."',file_name='FROM CALENDAR'";
	mysqli_query($link1,$sql);
	$msg="You have successfully created a task on ".$caldate;
	header("location:calender_event.php?msg=".$msg."".$pagenav);
	exit;
   }
}
///// get task details ///////
$sql = "SELECT * FROM pjp_data WHERE assigned_user = '".$_SESSION['userid']."'";
$res = mysqli_query($link1,$sql);
$event_str = "";
while($row = mysqli_fetch_assoc($res)){
	if($event_str == ""){
		$event_str .= "{
				id: ".$row['id'].",
				title: '".$row['task']."',
				start: '".$row["plan_date"]."'
				}";
	}else{
		$event_str .= ",{
				id: ".$row['id'].",
				title: '".$row['task']."',
				start: '".$row["plan_date"]."'
				}";
	}
}///end for loop
////// we hit save button for existing dealer visit
 if (isset($_POST['btnSave2']) && $_POST['btnSave2']=='Submit'){
 	///// extract lat long
	$latlong = explode(",",base64_decode($_REQUEST["latlong"]));
	$flag = true;
	mysqli_autocommit($link1, false);
	///// Insert Master Data
	$query1= "INSERT INTO dealer_visit set userid='".$_SESSION['userid']."',party_code='".$partycode."', remark='".$remark."',visit_date='".$today."',visit_city='',dealer_type='Old',address='',latitude='".$latlong[0]."',longitude='".$latlong[1]."',pjp_id='".$_REQUEST['task_id']."',ip='".$ip."'";
	$result = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "ER1: " . mysqli_error($link1) . ".";
    }
	if($_REQUEST['task_id']){
   		mysqli_query($link1,"UPDATE pjp_data SET task_acheive=task_acheive+1 WHERE id='".$_REQUEST['task_id']."'");
		$resultut = mysqli_query($link1,"INSERT INTO user_track SET userid='".$_SESSION['userid']."', task_name='Dealer Visit Old', task_action='Update', ref_no='".$partycode."', latitude='".$latlong[0]."', longitude='".$latlong[1]."', address='',travel_km='', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."'");
		//// check if query is not executed
		if (!$resultut) {
			 $flag = false;
			 $err_msg = "Error details4: " . mysqli_error($link1) . ".";
		}
		$_REQUEST['task_id'] = "";
		unset($_REQUEST);
   }
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Visit Details successfully  updated.";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.".$err_msg;
	} 
	header("location:calender_event.php?msg=".$msg."".$pagenav);
	exit;
 }
////////////// for new dealer visit 
if($_POST['btnSave4']=='Submit'){
   ///// first of all we will check document string ///
   if(mysqli_num_rows(mysqli_query($link1,"SELECT id from document_counter where financial_year='".$prefixdocstr."' and doc_code='".$docstr."'"))==0){
   //// get state code from statemaster
   $res_state=mysqli_query($link1,"select code from state_master where state='".$locationstate."'")or die("ER1".mysqli_error($link1));
   $row_state=mysqli_fetch_array($res_state);
   $statecode=$row_state['code'];
   /// explode location type value
   $expld_loctyp=explode("~",$locationtype);
   ////// count max no. of location in selected state
   $query_code="select MAX(code_id) as qa from asc_master where state='".$locationstate."' and id_type='".$expld_loctyp[0]."'";
   $result_code=mysqli_query($link1,$query_code)or die("ER2".mysqli_error($link1));
   $arr_result2=mysqli_fetch_array($result_code);
   $code_id=$arr_result2[0];
   /// make 3 digit padding
   $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
   //// make logic of location code
   $newlocationcode=substr(strtoupper(BRANDNAME),0,2).$expld_loctyp[0].$statecode.$pad;
   ///// check generated id should not be in system
   if(mysqli_num_rows(mysqli_query($link1,"SELECT sno from asc_master where uid='".$newlocationcode."'"))==0){
   // insert all details of location //
  $sql="INSERT INTO asc_master set uid='".$newlocationcode."',pwd='".$newlocationcode."',name='".$locationname."',asc_code='".$newlocationcode."',code_id='".$pad."', user_level='".$expld_loctyp[1]."',shop_name='".$locationname."',id_type='".$expld_loctyp[0]."',contact_person='".$contact_person."',landline='".$landline."',email='".$email."',phone='".$phone."',addrs='".$comm_address."',disp_addrs='".$dd_address."',landmark='".$landmark."',city='".$locationcity."',state='".$locationstate."',pincode='".$pincode."',vat_no='".$tin_no."',pan_no='".$pan_no."',cst_no='".$cst_no."',st_no='".$st_no."',circle='".$circle."',status='Active',login_status='Active',start_date='".$today."',update_date='".$datetime."',remark='".$remark."',proprietor_type='".$proprietor."',tdsper='".$tdsper."',account_holder='".$accountholder."',account_no='".$accountno."',bank_name='".$bankname."',bank_city='".$bankcity."',ifsc_code='".$ifsccode."',gstin_no= '$_POST[gst_no]' ,create_by='".$_SESSION['userid']."' ";

   mysqli_query($link1,$sql)or die("ER3".mysqli_error($link1));
   //insert into credit bal////////////////////////////
   mysqli_query($link1,"insert into current_cr_status set parent_code='".$parentid."', asc_code='".$newlocationcode."',cr_abl='0',cr_limit='0',total_cr_limit='0'")or die("ER4".mysqli_error($link1));
   ///insert into mapping table/////////////////////////
   mysqli_query($link1,"insert into mapped_master set uid='".$parentid."',mapped_code='".$newlocationcode."',status='Y',update_date='".$today."'")or die("ER5".mysqli_error($link1));
   ///insert into document string table/////////////////////////
   $invstr=$docstr."/".$prefixdocstr."/";
   $stnstr="STN/".$prefixdocstr."/".$docstr."/";
   $prnstr="PRN/".$prefixdocstr."/".$docstr."/";
   $srnstr="SRN/".$prefixdocstr."/".$docstr."/";
   $rcvpaystr="RECP/".$prefixdocstr."/".$docstr."/";
   mysqli_query($link1,"insert into document_counter set location_code='".$newlocationcode."',financial_year='".$prefixdocstr."',doc_code='".$docstr."',inv_str='".$invstr."',stn_str='".$stnstr."',prn_str='".$prnstr."',srn_str='".$srnstr."',rcvpay_str='".$rcvpaystr."',create_on='".$today."'")or die("ER6".mysqli_error($link1));
   //////////////////////////////////////////////////////////////
   //// create a user corresponding to this location
	$query_code="select MAX(uid) as qc from admin_users";
    $result_code=mysqli_query($link1,$query_code)or die("error2".mysql_error());
    $arr_result2=mysqli_fetch_array($result_code);
    $code_id=$arr_result2[0];
    $pad=str_pad(++$code_id,3,"0",STR_PAD_LEFT);
    $admiCode=substr(strtoupper(BRANDNAME),0,2)."USR".$expld_loctyp[0].$statecode.$pad;
	$pwd=$admiCode."@321";
	//// insert in user table
	$usr_add="INSERT INTO admin_users set username ='".$admiCode."',password ='".$pwd."',owner_code='".$newlocationcode."',user_level='".$expld_loctyp[1]."',name= '".$locationname."',utype='7',phone='".$phone."',emailid= '".$email."',create_by='".$_SESSION['userid']."' ,status='active',createdate='".date("Y-m-d H:i:s")."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysql_error());
	//// give auto basic permission ////
	mysqli_query($link1,"insert into access_region set uid='".$admiCode."',region='".$circle."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_state set uid='".$admiCode."',state='".$locationstate."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_role set uid='".$admiCode."',role_id='".$expld_loctyp[0]."',status='Y'")or die(mysqli_error($link1));
	mysqli_query($link1,"insert into access_location set uid='".$admiCode."',location_id='".$newlocationcode."',status='Y'")or die(mysqli_error($link1));
	
   ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$newlocationcode,"LOCATION","ADD",$ip,$link1,"");
	dailyActivity($_SESSION['userid'],$admiCode,"ADMIN USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	   $resultut = mysqli_query($link1,"INSERT INTO user_track SET userid='".$_SESSION['userid']."', task_name='New Dealer', task_action='Create', ref_no='".$po_no."', latitude='".$latlong[0]."', longitude='".$latlong[1]."', address='',travel_km='', remote_address='".$_SERVER['REMOTE_ADDR']."',remote_agent='".$_SERVER['HTTP_USER_AGENT']."' , entry_date='".$today."'");
		//// check if query is not executed
		if (!$resultut) {
			 $flag = false;
			 $err_msg = "Error details4: " . mysqli_error($link1) . ".";
		}
	////// return message
	$msg="You have successfully created a new location with ref. no. ".$newlocationcode." and location user id is ".$admiCode;
   }else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
   }else{
	////// return message
	$msg="Something went wrong like document code was already in DB. Please try again.";
   }
	///// move to parent page
    //header("Location:asp_details.php?msg=".$msg."".$pagenav);
	//$pagenavi="&pid=22&hid=2";
	//$urlvrbl=base64_encode("userid=".$admiCode."&userlevel=7&u_name=".$locationname."".$pagenavi);
	header("location:calender_event.php?msg=".$msg."".$pagenav);
	exit;
}
if (isset($_POST['btnSave3']) && $_POST['btnSave3']=='Submit'){
	///// extract lat long
	$latlong = explode(",",base64_decode($_REQUEST["latlong"]));
	$flag = true;
	mysqli_autocommit($link1, false);
	///// Insert Master Data
	 $query1= "INSERT INTO deviation_request set task_type = 'Dealer Visit' , sch_visit='".$sch_visit."',change_visit='".$chng_visit."', remark='".$remark."',entry_by='".$_SESSION['userid']."',entry_date='".$datetime."',entry_ip='".$ip."',app_status='Pending For Approval',pjp_id='".$_REQUEST['task_id']."'";
	$result = mysqli_query($link1,$query1)or die ("ER1".mysqli_error($link1));
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Deviation request is successfully raised.";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 	
	///// move to parent page
  	header("location:calender_event.php?msg=".$msg."".$pagenav);
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<title><?=siteTitle?></title>
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src='../js/jquery.js'></script>
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/moment.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link href='../css/fullcalendar.min.css' rel='stylesheet' />
<link href='../css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<script src='../js/fullcalendar.min.js'></script>
<script>

	$(document).ready(function() {

		$('#calendar').fullCalendar({
			defaultDate: '<?=$today?>',
			selectable: true,
			selectHelper: true,
			select: function(start, end) {
				//var title = prompt('Event Title:');
				var eventData = "";
				//if (title) {
					/*eventData = {
						title: title,
						start: start,
						end: end
					};*/
					////// evaluate today date in YYYY-mm-dd format
					var today = new Date();
					//var todayd = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
					var todayd = today.getFullYear() + '-' + ("0" + (today.getMonth() + 1)).slice(-2) + '-' + ("0" + today.getDate()).slice(-2);
					//////// make calendar date in also in YYYY-mm-dd format
					var pad = "00";
					var d = new Date(start);
					var dyear = d.getFullYear();
					var dmonth = "" + (d.getMonth()+1);
					var dday = "" + d.getDate();
					///// make month with 0 padding
					var makemonth = pad.substring(0, pad.length - dmonth.length) + dmonth;
					///// make day with 0 padding
					var makeday = pad.substring(0, pad.length - dday.length) + dday;
					var calendDate = dyear+'-'+makemonth+'-'+makeday;
					///// make weekend array to check Sunday
					var cal_date = new Date(calendDate);
					var weekday = new Array(7);
						weekday[0] = "Sunday";
						weekday[1] = "Monday";
						weekday[2] = "Tuesday";
						weekday[3] = "Wednesday";
						weekday[4] = "Thursday";
						weekday[5] = "Friday";
						weekday[6] = "Saturday";
					var dayName = weekday[cal_date.getDay()];
					var nationalHoliday = calendDate.substr(5, 5);/// YYYY-01-26 , YYYY-08-15 , YYYY-10-02
					////check if caledar date is equal to more than calendar date and (not on sunday and 3 national holiday like 26 Jan,15 Aug,02 Oct) the modal can be open
					//alert(calendDate+">="+ todayd);
					if(calendDate >= todayd && dayName != "Sunday" && nationalHoliday != "01-26" && nationalHoliday != "08-15" && nationalHoliday != "10-02"){
					//// open task add window
					$('#myTaskAdd').modal({
						show: true,
						backdrop:"static"
					});
					////assign calendar value
					$("#caldate").val(calendDate);
					$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
				//}
				$('#calendar').fullCalendar('unselect');
				}
			},
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			events: [
				<?=$event_str?>
			],
			eventClick: function(calEvent, jsEvent, view) {
				 //alert('Event: ' + calEvent.title);
				//alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
				//alert('View: ' + view.name);
				//ev.preventDefault();
				 //var uid = $(this).data('id');
				 $.get('calender_modal.php?id=' + calEvent.id, function(html){
					 $('#myModal .modal-body').html(html);
					 var showbtn = '';
					 var tasktype = calEvent.title.replaceAll(/\s/g,'');
					 var checkin = '<button type="button" id="check_in" class="btn btn-success" onClick=getLocation("'+tasktype+'","'+calEvent.id+'");>Check-In</button>';
					 /// for deviation approval button
					 if(calEvent.title == "Dealer Visit"){
					 	//var deviationbtn = '<button type="button" id="davbtn" class="btn btn-danger" onClick=window.location.href="../admin/deviation_request.php?pid=105&hid=FN08&task_id='+ calEvent.id+'">Deviation Request</button>';
						var deviationbtn = '<button type="button" id="davbtn" class="btn btn-danger" onClick=openModelView2('+calEvent.id+',"");>Deviation Request</button>';
					 }else{
					 	var deviationbtn = '';
					 }
					 //var checkout = '<button type="button" id="check_out" class="btn btn-danger" onClick="getLocation()">Check-Out</button>';
					 /*if(calEvent.title == "Dealer Visit"){
					 	var showbtn = '<button type="button" id="dealer_visit" class="btn btn-warning" data-dismiss="modal" onClick=window.location.href="../admin/dealerVisit.php?op=add&pid=35&hid=FN05&task_id='+ calEvent.id+'">Dealer Visit</button>';
					 }else if(calEvent.title == "Sale Order"){
					 	var showbtn = '<button type="button" id="salesOrder" class="btn btn-primary" data-dismiss="modal" onClick=window.location.href="../po/addNewPO.php?op=add&pid=10&hid=FN03&task_id='+ calEvent.id+'">Sales Order</button>';
					 }else if(calEvent.title == "Collection"){
					 	var showbtn = '<button type="button" id="paymentReceive" class="btn btn-primary" data-dismiss="modal" onClick=window.location.href="../payment/payment_receive.php?op=add&pid=35&hid=FN05&task_id='+ calEvent.id+'">Collection</button>';
					 }else if(calEvent.title == "Feedback"){
					 	var showbtn = '<button type="button" id="feedback" class="btn btn-info" data-dismiss="modal" onClick=window.location.href="../admin/add_query.php?op=add&pid=39&hid=FN09&task_id='+ calEvent.id+'">Feedback</button>';
					 }else{
					 	var showbtn = '';
					 }*/
					 $('#myModal .modal-footer').html(deviationbtn+'&nbsp;&nbsp;'+checkin+'&nbsp;&nbsp;'+showbtn+'&nbsp;&nbsp; <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>');
					 $('#myModal').modal({
						show: true,
						backdrop:"static"
					});
				 });
				/*$("#myModal .modal-title").html();
				$('#myModal').modal({
					show: true,
					backdrop:"static"
				});*/
			}
		});
		$( ".fc-scroller.fc-day-grid-container" ).removeAttr("style");
	});
$(document).ready(function(){
	 $("#frm1").validate();
});

function showActionButton(id,title,latlongval){
//alert(title+"----"+latlongval);
	 if(title == "DealerVisit"){
		//var showbtn = '<button type="button" id="dealer_visit" class="btn btn-warning" data-dismiss="modal" onClick=window.location.href="../admin/dealerVisit.php?op=add&pid=35&hid=FN05&task_id='+ id+'&latlong='+ latlongval+'">Dealer Visit</button>';
		var showbtn = '<button type="button" id="dealer_visit" class="btn btn-warning" onClick=openModelView('+id+',"'+latlongval+'");>Dealer Visit</button>';
	 }else if(title == "SaleOrder"){
		var showbtn = '<button type="button" id="salesOrder" class="btn btn-primary" data-dismiss="modal" onClick=window.location.href="../po/addNewPO.php?op=add&pid=10&hid=FN03&task_id='+ id+'&latlong='+ latlongval+'">Sales Order</button>';
	 }else if(title == "Collection"){
		var showbtn = '<button type="button" id="paymentReceive" class="btn btn-primary" data-dismiss="modal" onClick=window.location.href="../payment/payment_receive.php?op=add&pid=35&hid=FN05&task_id='+ id+'&latlong='+ latlongval+'">Collection</button>';
	 }else if(title == "Feedback"){
		var showbtn = '<button type="button" id="feedback" class="btn btn-info" data-dismiss="modal" onClick=window.location.href="../admin/add_query.php?op=add&pid=39&hid=FN09&task_id='+ id+'&latlong='+ latlongval+'">Feedback</button>';
	 }else if(title == "Follow-up"){
		var showbtn = '<button type="button" id="follow-up" class="btn btn-success" data-dismiss="modal" onClick=window.location.href="../salesforce/lead_view.php?tab=0&page=lead&status=&pid=46&hid=FN08&task_id='+ id+'&latlong='+ latlongval+'">Follow-up</button>';
	 }else{
		var showbtn = '';
	 }
	 $('#myModal .modal-footer').html(showbtn+'&nbsp;&nbsp; <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>');
}
	
function getLocation(title,evtid) {
  var ltlg = document.getElementById("latlong");
  if (navigator.geolocation) {
	navigator.geolocation.getCurrentPosition(function (position) {
	showPosition(position, title, evtid);
	});
  } else { 
	ltlg.value = "Not Found,Not Found";
  }
}

function showPosition(position, var1, evntid) {
  var ltlg = document.getElementById("latlong");
  ltlg.value = btoa(position.coords.latitude + "," + position.coords.longitude);
  showActionButton(evntid,var1,ltlg.value);
}
function checkLocation2(){
	if(document.getElementById("latlong").value){
		document.getElementById("err_msg").innerHTML ="";
		return true;
	}else{
		document.getElementById("err_msg").innerHTML ="<span class='text-danger'>Please allow location to mark your attendance. It is mandatory</span>";
		return false;
	}
}
////// function to open modal for dealer visit
function openModelView(taskid,latlong){
	$.get('dealer_visit_modal.php?taskid='+taskid+'&latlong='+latlong, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		//makeSelect();
		formValid();
	 });
	 $("#tile_name").html("<i class='fa fa-handshake-o fa-lg'></i> Update Dealer Visit Details");
	 //$(".modal-footer").html("<button type='button' id='btnCancel' class='btn btn-default' data-dismiss='modal'>Close</button>");
}
////// function to open modal deviation
function openModelView2(taskid,latlong){
	$.get('deviation_request_modal.php?taskid='+taskid+'&latlong='+latlong, function(html){
		 $('#viewModal .modal-body').html(html);
		 $('#viewModal').modal({
			show: true,
			backdrop:"static"
		});
		$("#frm3").validate();
	 });
	 $("#tile_name").html("<i class='fa fa-line-chart fa-lg'></i> Deviation Request");
	 //$(".modal-footer").html("<button type='button' id='btnCancel' class='btn btn-default' data-dismiss='modal'>Close</button>");
}
function formValid(){
	$("#frm2").validate();
	$("#frm4").validate();
	$('#circle').change(function(){
	  var name=$('#circle').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{circle:name},
		success:function(data){
	    $('#statediv').html(data);
	    }
	  });
    });
}
function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  });
   
 }
 /////////// function to get Parent Location on the basis of location Type
 function getParentLocation(){
	  var name=$('#locationtype').val();
	  var splitval=name.split("~");
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{loctype:splitval[1]},
		success:function(data){
	    $('#parentdiv').html(data);
	    }
	  });
   
 }
 /////////// function to get city on the basis of state
 function checkDupliDoccode(val){
	  var fyr=$('#prefixdocstr').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{fcyear:fyr,doccode:val},
		success:function(data){
	      //// if string found then alert
		  if(data>0){
			  alert("Duplicate document code");
			  $('#docstr').val('');
		  }
	    }
	  });
   
 }
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/bootstrap-datetimepicker.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<style type="text/css">
	/*.modal-dialog{
		overflow-y: initial !important
	}
	.modal-body{
		max-height: calc(100vh - 212px);
		overflow-y: auto;
	}*/
	#calendar {
		max-width: 900px;
		margin: 0 auto;
	}
	/*.modal {
	  width: 1000px;
	  margin: auto;
	}*/

</style>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active">
      <h2 align="center"><i class="fa fa-calendar"></i> My Beat Plan</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
		<div id='calendar'></div>
		<!-- Modal -->
          <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-lg">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Task Details</h4>
                </div>
                <div class="modal-body">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                
            
                </div>
              </div>
            </div>
          </div>
          <!-- Modal  for add task on select event on calendar -->
          <div class="modal fade" id="myTaskAdd" role="dialog">
            <div class="modal-dialog modal-lg">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class="fa fa-plus"></i> Create Task</h4>
                </div>
                <div class="modal-body">
                 <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Task Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <select name='task' id='task' class="form-control required" required>
                             <option value="">--Please Select--</option>
                             <?php
                             $res_task = mysqli_query($link1,"SELECT task_name FROM pjptask_master WHERE status='A' order by task_name");	
                             while($row_task = mysqli_fetch_assoc($res_task)){
                             ?>
                             <option value="<?=$row_task["task_name"]?>" <?php if($row_task["task_name"]=="Dealer Visit"){ echo "selected";}?>><?=$row_task["task_name"]?></option>
                             <?php 
                             }
                             ?>
                          </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Area To Visit<span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="visit_area" id="visit_area" required class="form-control mastername"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                    	<input name="caldate" id="caldate" type="hidden"/>
                        <input type="submit" class="btn btn-success" name="addtask" id="addtask" value="ADD" title="Add New Task" <?php if($_POST['addtask']=='ADD'){?>disabled<?php }?>>
                    </div>
                  </div>
            </form>
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          
<!-- Start Product Details Modal -->
<div class="modal modalTH fade" id="viewModal" role="dialog" style="margin-top:30px">
	<div class="modal-dialog modal-dialogTH modal-lg">
  		<!-- Modal content-->
  		<div class="modal-content">
    		<div class="modal-header btn-success">
      			<button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title" align="center" id="tile_name"></h2>
    		</div>
    		<div class="modal-body modal-bodyTH">
     			<!-- here dynamic task details will show -->
    		</div>
    		<!--<div class="modal-footer">
      			
    		</div>-->
  		</div>
	</div>
</div>
<!--close Product Details modal-->


    </div>
 
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
