<?php
require_once("../config/dbconnect.php");
session_start();
switch($_REQUEST["action"]){
////////////////// get location by selecting  Location type on permission page
case 'getLocation':
 $explode=explode(",",$_REQUEST['value']);
 $arr_idtype=array();
 for($k=0;$k<count($explode);$k++){
	   if($explode[$k]){
	   $arr_idtype[]=$explode[$k];
	   }
 }
 $uni_arr=array_unique($arr_idtype);
 $user_type=mysqli_fetch_array(mysqli_query($link1,"select utype from admin_users where username='$_REQUEST[userid]'"));
 if($user_type['utype']<=7){
	 $find_in="state in (select state from access_state where uid='$_REQUEST[userid]' and status='Y')";
 }
 else{
	// $find_in="district in (select district from access_district where uid='$_REQUEST[userid]' and status='Y')";
	 $find_in="state in (select state from access_state where uid='$_REQUEST[userid]' and status='Y')";
 }
 echo "<div class='form-buttons' style='float:right'><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm4.report4)' value='Check All' />
          <input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm4.report4)' value='Uncheck All' /></div>";
 echo "<table id='myTable' class='table table-hover'><thead><tr><th style='border:none'>&nbsp;Locations</th></tr>";
 
 foreach($uni_arr as $key => $value){
 echo "<tr><td colspan='4'><b>".$value."<b></td></tr>";
 $res_loc=mysqli_query($link1,"select asc_code,name,city,state from asc_master where status='Active' and id_type='$value' and $find_in ORDER BY name")or die("Error2".mysqli_error($link1));
 $i=1;
 while($row_loc=mysqli_fetch_array($res_loc)){
 if($i%4==1){
            echo "<tr>";
               }
              $loc_acc=mysqli_query($link1,"select location_id from access_location where status='Y' and location_id='$row_loc[asc_code]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
              $num2=mysqli_num_rows($loc_acc);
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='report4' name='report4[]' value='$row_loc[asc_code]'";
			   if($num2 > 0) echo "checked";
			   echo "/>";
			  echo $row_loc['name'].",".$row_loc['city'].",".$row_loc['state']."[".$row_loc['asc_code']."]</td>";
               if($i/4==0){
            echo "</tr>";
			     }$i++;
            }
 }
 echo "</table>";
break;

////////////////// get location by selecting  Location type on permission page
case 'getLocation_user':
 $i=1;
 $explode=explode(",",$_REQUEST['value']);
 $arr_idtype=array();
 for($k=0;$k<count($explode);$k++){
	   if($explode[$k]){
	   $arr_idtype[]=$explode[$k];
	   }
 }
 $uni_arr=array_unique($arr_idtype);
 
 $user_type=mysqli_fetch_array(mysqli_query($link1,"select utype from admin_users where username='$_REQUEST[userid]'"));
 if($user_type['utype']<=7){
	 $find_in="state in (select state from access_state where uid='$_REQUEST[userid]' and status='Y')";
 }
 else{
	 $find_in="district in (select district from access_district where uid='$_REQUEST[userid]' and status='Y')";
 }
 echo "<div class='form-buttons' style='float:right'><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm4.report4)' value='Check All' />
          <input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm4.report4)' value='Uncheck All' /></div>";
 echo "<table id='myTable' class='table table-hover'><thead><tr><th style='border:none'>&nbsp;Locations</th></tr>";
 foreach($uni_arr as $key => $value){
 $res_loc=mysqli_query($link1,"select asc_code,name,city,state from asc_master where status='Active' and id_type='$value' and $find_in and create_by='$_REQUEST[userid]'")or die("Error2".mysqli_error($link1));
 while($row_loc=mysqli_fetch_array($res_loc)){
 if($i%4==1){
            echo "<tr>";
               }
              $loc_acc=mysqli_query($link1,"select location_id from access_location where status='Y' and location_id='$row_loc[asc_code]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
              $num2=mysqli_num_rows($loc_acc);
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='report4' name='report4[]' value='$row_loc[asc_code]'";
			   if($num2 > 0) echo "checked";
			   echo "/>";
			  echo $row_loc[name].",".$row_loc[city].",".$row_loc[state]."[".$row_loc[asc_code]."]</td>";
               if($i/4==0){
            echo "</tr>";
			     }$i++;
            }
 }
 echo "</table>";
break;
//////////////// get state by selecting circle on permission page
case 'getState':
 $i=1;
 $explode=explode(",",$_REQUEST['value']);
 $arr_zone=array();
 for($k=0;$k<count($explode);$k++){
 $arr_zone[]=$explode[$k];
 }
 $uni_arr=array_unique($arr_zone);
 echo "<div class='form-buttons' style='float:right'><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm.state)' value='Check All' />
          <input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm.state)' value='Uncheck All' /></div>";
 echo "<table id='myTable' class='table table-hover'><thead><tr><th style='border:none'>&nbsp;States/Cities</th></tr>";
 foreach($uni_arr as $key => $value){ 
 $res_loc=mysqli_query($link1,"Select distinct(state) from state_master where zone='$value' order by state")or die("Error2".mysqli_error($link1));
 while($circlearr=mysqli_fetch_array($res_loc)){
 if($i%6==1){
            echo "<tr>";
               }
              $loc_acc=mysqli_query($link1,"Select state from access_state where status='Y' and state='$circlearr[state]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
              $num2=mysqli_num_rows($loc_acc);
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='state' name='state[]' value='$circlearr[state]'";
			   if($num2 > 0) echo "checked";
			   echo "/>";
			  echo $circlearr['state']."</td>";
               if($i/6==0){
            echo "</tr>";
			     }$i++;
            }
 }
 echo "</table>";
break;

//////////////// get state by selecting circle on permission page
case 'getState_loc':
 $i=1;
 $explode=explode(",",$_REQUEST[value]);
 $arr_zone=array();
 for($k=0;$k<count($explode);$k++){
 $arr_zone[]=$explode[$k];
 }
 $uni_arr=array_unique($arr_zone);
 echo "<div class='form-buttons' style='float:right'><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm.state)' value='Check All' />
          <input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm.state)' value='Uncheck All' /></div>";
 echo "<table id='myTable' class='table table-hover'><thead><tr><th style='border:none'>&nbsp;States/Cities</th></tr>";
 foreach($uni_arr as $key => $value){ 
 $res_loc=mysqli_query($link1,"Select distinct(state) from state_master where zone='$value' order by state")or die("Error2".mysqli_error($link1));
 while($circlearr=mysqli_fetch_array($res_loc)){
 if($i%6==1){
            echo "<tr>";
               }
              $loc_acc=mysqli_query($link1,"Select state from access_state where status='Y' and state='$circlearr[state]' and uid='$_REQUEST[userid]'")or die(mysqli_error($link1));
              $num2=mysqli_num_rows($loc_acc);
			   if($num2 > 0) {
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='state' name='state[]' value='$circlearr[state]'";
			   if($num2 > 0) echo "checked";
			   echo "/>";
			  echo $circlearr['state']."</td>";
               }
			   if($i/6==0){
            echo "</tr>";
			     }$i++;
            }
 }
 echo "</table>";
break;
/////////////////////////////get city/district by selecting circle on permission page////////////////////
case 'getDistrict':
 $i=1;
 $explode=explode(",",$_REQUEST[value]);
 $arr_state=array();
 for($k=0;$k<count($explode);$k++){
 $arr_state[]=$explode[$k];
 }
 $uni_arr=array_unique($arr_state);
 echo "<input name='CheckAll' type='button' class='button' onClick='checkAll(document.form1.district)' value='Check All' />
          <input name='UnCheckAll' type='button' class='button' onClick='uncheckAll(document.form1.district)' value='Uncheck All' />";
 echo "<table cellpadding='2' cellspacing='2' border='0'><tr><td class=Table_body1>District</td></tr>";
 foreach($uni_arr as $key => $value){ 
 $res_loc=mysql_query("Select distinct(city) from district_master where state='$value' order by city")or die("Error2".mysql_error());
 while($circlearr=mysql_fetch_array($res_loc)){
 if($i%4==1){
            echo "<tr>";
               }
              $loc_acc=mysql_query("Select district from access_district where status='Y' and district='$circlearr[city]' and uid='$_REQUEST[userid]'")or die(mysql_error());
              $num2=mysql_num_rows($loc_acc);
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='district' name='district[]' value='$circlearr[city]'";
			   if($num2 > 0) echo "checked";
			   echo "/>";
			  echo $circlearr['city']."</td>";
               if($i/4==0){
            echo "</tr>";
			     }$i++;
            }
 }
 echo "</table>";
break;
/////////////// get TAX by state wise////////////////////
case 'getStateTax':
$model_query="select * from tax_master where state='$_REQUEST[state]' and product_type='$_REQUEST[type]'";
$check1=mysql_query($model_query);
if(mysql_num_rows($check1)>0){
$br = mysql_fetch_array($check1);
echo $br[per]."~".$br[name];
}
else{
echo "0"."~"."0";
}
break;
################################################################################################################
/////////////// get City////////////////////
case 'getCity':
echo "<select  name='city'  class='form-control selectpicker' required data-live-search='true'><option value='' >Select City</option>";
echo $model_query="SELECT distinct(city) FROM district_master where state='$_REQUEST[value]' order by city";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[city]."'>";
echo $br['city']."</option>";
}
echo "</select>";
break;
################################################################################################################

/////// get product subcategory on coupon mapping page //////////////////////
case 'getProdSubCat':
 $i=1;
 $explode=explode(",",$_REQUEST['value']);
 $arr_zone=array();
 for($k=0;$k<count($explode);$k++){
 $arr_zone[]=$explode[$k];
 }
 $uni_arr=array_unique($arr_zone);
 echo "<div class='form-buttons' style='float:right'><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm.prodsubcat)' value='Check All' />
          <input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm.prodsubcat)' value='Uncheck All' /></div>";
 echo "<table id='myTable' class='table table-hover'><thead><tr><th style='border:none'>&nbsp;Product SubCategory</th></tr>";
 
 //// fetch product subcategory from coupon mapping table /////////////////////////////////
 $prodcutsubcat= mysqli_fetch_array(mysqli_query($link1 , "select prod_subcat from  coupon_mapping where coupon_code='".$_REQUEST['coupon']."' "));			
 $productsub_cat = explode(",",$prodcutsubcat['prod_subcat']);
 
 foreach($uni_arr as $key => $value){
 if($value != ''){
 $res_loc=mysqli_query($link1,"Select psubcatid , prod_sub_cat,productid from product_sub_category where productid='".$value."' order by product_category")or die("Error2".mysqli_error($link1));
 while($circlearr=mysqli_fetch_array($res_loc)){
 if($i%6==1){
            echo "<tr>";
               }
			   
			  
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='prodsubcat' name='prodsubcat[]' value='$circlearr[psubcatid]' onClick='showProduct()' ";
			   if(in_array($circlearr['psubcatid'], $productsub_cat)) echo "checked";
			   echo "/>";
			   echo $circlearr['prod_sub_cat']."</td>";
               if($i/6==0){
               echo "</tr>";
			     }$i++;
            }
		}	
 }
 echo "</table>";
break;

//////////////////////// get Product ////////////////////////////
case 'getProd':
 $i=1; 
 $explode=explode(",",$_REQUEST[value]);
 $arr_zone=array();
 for($k=0;$k<count($explode);$k++){
 $arr_zone[]=$explode[$k];
 }
 $uni_arr=array_unique($arr_zone);
 echo "<div class='form-buttons' style='float:right'><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm.prod)' value='Check All' /> <input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm.prod)' value='Uncheck All' /></div>";
 echo "<table id='myTable' class='table table-hover'><thead><tr><th style='border:none'>&nbsp;Product</th></tr>";
 
 //// fetch product  from coupon mapping table /////////////////////////////////
 $prodcut= mysqli_fetch_array(mysqli_query($link1 , "select productid from  coupon_mapping where coupon_code='".$_REQUEST['coupon']."' "));			
 $productcat = explode(",",$prodcut['productid']);
 
 foreach($uni_arr as $key => $value){
  $res_loc=mysqli_query($link1,"Select id , productname from product_master where productsubcat='$value' order by productname")or die("Error2".mysqli_error($link1));
 if(mysqli_num_rows($res_loc)>0){
 while($circlearr=mysqli_fetch_array($res_loc)){
 if($i%6==1){
            echo "<tr>";
               }
             
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='prod' name='prod[]' value='$circlearr[id]' onClick='showBrand()' ";
			   if(in_array($circlearr[id], $productcat)) echo "checked";
			   echo "/>";
			  echo $circlearr['productname']."</td>";
               if($i/6==0){
            echo "</tr>";
			     }$i++;
            }
		} /// if ends
 }
 echo "</table>";
break;

/////////////////////////////////////////////////////////////////////////

//////////////////////// get Product ////////////////////////////
case 'getBrand':
 $i=1;
 $explode=explode(",",$_REQUEST[value]);
 $arr_zone=array();
 for($k=0;$k<count($explode);$k++){
 $arr_zone[]=$explode[$k];
 }
 $uni_arr=array_unique($arr_zone);
 echo "<div class='form-buttons' style='float:right'><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm.brand)' value='Check All' />
          <input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm.brand)' value='Uncheck All' /></div>";
 echo "<table id='myTable' class='table table-hover'><thead><tr><th style='border:none'>&nbsp;Brand</th></tr>";
 
 //// fetch product  from coupon mapping table /////////////////////////////////
 $brand= mysqli_fetch_array(mysqli_query($link1 , "select brand from  coupon_mapping where coupon_code='".$_REQUEST['coupon']."' "));			
 $brandcat = explode(",",$brand['brand']);
 
 foreach($uni_arr as $key => $value){ 
 $res_loc=mysqli_query($link1,"Select distinct(brand) from product_master where id='$value' order by productname")or die("Error2".mysqli_error($link1));
 while($circlearr=mysqli_fetch_array($res_loc)){
  $brandname = mysqli_fetch_array(mysqli_query($link1 , "select make from make_master where id = '$circlearr[brand]'  "));
 if($i%6==1){
            echo "<tr>";
               }
              echo "<td class=Table_body><input style='width:20px' type='checkbox' id='brand' name='brand[]' value='$circlearr[brand]'  ";
			   if(in_array($circlearr[brand], $brandcat)) echo "checked";
			   echo "/>";
			  echo $brandname['make']."</td>";
               if($i/6==0){
            echo "</tr>";
			     }$i++;
            }
 }
 echo "</table>";
break;
/////// get location id on basis of state and location type for given rights to user written by shekhar on 10 oct 2022
case 'getLocationName':
if(isset($_POST['permission_loc'])){
	$report="SELECT asc_code, name, city, state FROM asc_master WHERE state='".$_POST['permission_state']."' AND id_type='".$_POST['permission_loc']."' ORDER BY name";
 	$rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
 	$i=1;
 	if(mysqli_num_rows($rs_report)>0){
 		echo "<table id='myTable4' class='table table-hover'><tbody><tr><td><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm4.report4)' value='Check All'/>&nbsp;<input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm4.report4)' value='Uncheck All' /></td></tr>";
    	while($row_report=mysqli_fetch_array($rs_report)){
     		if($i%4==1){
        		echo "<tr>";
			}          
  			$state_acc=mysqli_query($link1,"SELECT id FROM access_location WHERE uid='".$_POST['usrid']."' AND location_id='".$row_report['asc_code']."' AND status='Y'")or die(mysqli_error($link1));
        	$num1=mysqli_num_rows($state_acc);
  			echo "<td><input style='width:20px'  type='checkbox' id='report4' name='report4[]' value='".$row_report['asc_code']."'";
  			if($num1 > 0) echo "checked";
			echo "/>".$row_report['name'].", ".$row_report['city'].", ".$row_report['state'].", ".$row_report['asc_code']."</td>";
			if($i/4==0){
				echo "</tr>";
			}
			$i++;
		}
		echo "</tbody></table>";
	}
}
break;
/////////////////////////////////////////////////////////////////////////
/////// 
/*case getProdDropDown:
echo "<script src='../js/bootstrap-select.min.js'></script>";
echo "<link rel='stylesheet' href='../css/bootstrap-select.min.css'>";
$indx=$_REQUEST['value1'];
echo "<select class='form-control' data-live-search='true' name='prod_code[".$indx."]' id='prod_code[".$indx."]' required><option value=''>--None--</option>";
      $model_query="select model from model_master where status='Active'";
      $check1=mysql_query($model_query);
	  while($br = mysql_fetch_array($check1)){
		  echo "<option data-tokens='".$br['model']."' value='".$br['model']."'>".$br['model']."</option>";
	  }
echo "</select>~".$indx;
break;*/
}
?>