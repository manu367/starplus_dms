<?php 
//require_once("../config/config.php");
@extract($_POST);

	// Update Masters Rights

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

function selfRunLC(){
	<?php 
	$res_loctype=mysqli_query($link1,"select * from location_type where status='A'  and seq_id >= $_SESSION[user_level]"); 
				   while($row_loctype=mysqli_fetch_assoc($res_loctype)){
				   $sql_check=mysqli_query($link1,"select role_id from access_role where status='Y' and role_id='$row_loctype[locationtype]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
       $num_check=mysqli_num_rows($sql_check);
	   if($num_check > 0){
		   while($row_check=mysqli_fetch_array($sql_check)){
			   $str.=",".$row_loctype['locationtype'];
		   }
	?>
	var arr="<?=$str?>";
	document.getElementById("role_str").value=arr;
	checkForUCLC(arr);
	getLocation(arr);	   
	<?php }
	   else{
	   }
	   }
    ?>
}
function checkForUCLC(val){
	var str=val.split(",");
	var  s=0;
	for(var i=1;i<str.length;i++){
		var chk=document.getElementById(str[i]).checked;
		if(chk==true){
			alert("checked");
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
var strSubmit = "action=getLocation_user&value="+val1+"&userid=<?=$_REQUEST[userid]?>";
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
	if(location.hash=="#menu5"){
	
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="";
	}
	else {
		document.getElementById("menu4").style.display="";
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
if(location.hash=="#menu5"){
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="";
	}
	else {
		
		
		document.getElementById("menu4").style.display="";
		document.getElementById("menu5").style.display="none";
	}
	
}
</script>
</head>
<body onLoad="selfRunLC();tabDisplay();">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-users"></i> Update User Permission</h2>
      <h4 align="center"><?=$_REQUEST[u_name]."  (".$_REQUEST['userid'].")";?>
      <?php if($_POST[submitTab4]=='Save' || $_POST[submitTab5]=='Save'){ ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <span style="color:#FF0000"><?php if($_POST[submitTab4]=="Save"){ echo "Locations";}else{ echo "Users";} ?> permissions are updated.</span>
   <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
         <ul class="nav nav-tabs">
                    <li  class="active"><a data-toggle="tab" href="#menu4">Locations</a></li>
          <li><a data-toggle="tab" href="#menu5">Users</a></li>
         </ul>
         <!-- Tab 1 Master / Region Rights-->
           
          <!-- Tab 2 Processes Rights-->
          
          <!-- Tab 3 Excel Export Rights-->
          
          <!-- Tab 4 Cancellation Rights-->
          
          <!-- Tab 5 Location Rights-->
          <div id="menu4" class="tab-pane fade in active">
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
				   $res_loctype=mysqli_query($link1,"select * from location_type where status='A'  and seq_id >= $_SESSION[user_level]"); 
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
          
             <input type="submit" class="btn btn-primary" name="submitTab4" id="submitTab4" value="Save"> 
             <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu5'">Next</button>
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser_loc.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
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
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser_loc.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
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