<?php 
require_once("../config/config.php");
@extract($_POST);
############# if form 1 is submitted #################
if($_POST['submitTab']){

//print_r($_POST['brand']);

///// make comma separated string for product category  ///////////////////
	$details=$_POST['product_cat'];
	$count=count($details);
	
    $rtn_str="";
    for($k=0;$k<=$count;$k++){
	if($rtn_str==""){
          $rtn_str.=$details[$k];
	   }
       else{
          $rtn_str.=",".$details[$k];
	   }
	   }
	 $productcategorystr = rtrim($rtn_str,",") ;
	 
	 ////////////////////////////////////////////////////////////////
	///// make comma separted string for product subcategory  
	 
	 $productsubcat = $_POST['prodsubcat'];
	 
	 $countnew=count($productsubcat);
	
     $subcat_str="";
     for($k=0;$k<=$countnew;$k++){
	 if($subcat_str==""){
          $subcat_str.=$productsubcat[$k];
	   }
       else{
          $subcat_str.=",".$productsubcat[$k];
	   }
	   }
	 $productsubcategorystr = rtrim($subcat_str,",") ;

    ////////////////////////////////// make comma separated string for product ////////////////////
	 $product = $_POST['prod'];
	 
	 $count_prod=count($product);
	
     $product_str="";
     for($k=0;$k<=$count_prod;$k++){
	 if($product_str==""){
          $product_str.=$product[$k];
	   }
       else{
          $product_str.=",".$product[$k];
	   }
	   }
	 $productstr = rtrim($product_str,",") ;
	 
	 ///// make comma separeted string for brand //////////////////////
	 $brand = $_POST['brand'];
	 
	 $count_brand=count($brand);
	
     $brand_str="";
     for($k=0;$k<=$count_brand;$k++){
	 if($brand_str==""){
          $brand_str.=$brand[$k];
	   }
       else{
          $brand_str.=",".$brand[$k];
	   }
	   }
	 $brandstr = rtrim($brand_str,",") ;
	 
////////////////////////////////////////////////////////////////
	

               //// if already exist ///////////////////
		 if(mysqli_num_rows(mysqli_query($link1,"select id from coupon_mapping where coupon_code='".$_REQUEST['coupon_code']."' "))>0){
    
                  if(($productstr == '') && ($brandstr == '')  && ($productsubcategorystr == '') && ($productcategorystr == '')) {
		    $status = '';
		   }else {
		    $status = 'Y';
		    }              
                              
            mysqli_query($link1,"update coupon_mapping set brand='".$brandstr."' , productid = '".$productstr."' , prod_subcat = '".$productsubcategorystr."' , prod_cat = '".$productcategorystr."',status='".$status."'  where coupon_code='".$_REQUEST['coupon_code']."' ")or die(mysqli_error($link1));
         }else{
            mysqli_query($link1,"insert into coupon_mapping set coupon_code='".$_REQUEST['coupon_code']."' , brand='".$brandstr."' , productid = '".$productstr."' , prod_subcat = '".$productsubcategorystr."' , prod_cat = '".$productcategorystr."',status='Y'   ")or die(mysqli_error($link1));
		 }
	
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
 

 //////######## Get product category ###############//////
 function checkUncheck(val1){
	
	var chk_arr =  document.getElementsByName("product_cat[]");
    var chklength = chk_arr.length;  
	var arr="";      
	arr_pc = [];
	var i=0;
	//alert(chklength);
   	for(k=0;k< chklength;k++)
    {
		
    	if (chk_arr[k].checked == true){
		//alert(chk_arr[k].value);
				arr_pc[i] = chk_arr[k].value;
			//document.getElementById("prod_cat").value = (document.getElementById("prod_cat").value)+","+(chk_arr[k].value);
			//arr = document.getElementById("prod_cat").value;
		//alert('checked');
			i++;
    	} 
		else{
			//arr_pc.splice( arr_pc.indexOf(chk_arr[k].value));
	    //alert('unchecked');
	    	//document.getElementById("prod_cat").value = (document.getElementById("prod_cat").value).replace(","+(chk_arr[k].value), "");
			//var arr=document.getElementById("prod_cat").value;
		}
	
   	}
	
	//alert(arr_pc);
	document.getElementById("prod_cat").value = arr_pc;
   	getProdSubCat(arr_pc);
	//showProduct();
  }
  
  function selfRun(){
	<?php 
	$sql_check=mysqli_query($link1,"select prod_cat , productid, prod_subcat from coupon_mapping where status='Y' and coupon_code='$_REQUEST[coupon_code]'")or die(mysqli_error($link1));
       $num_check=mysqli_num_rows($sql_check);
	   if($num_check > 0){
	   $strnew = "";
		   while($row_check=mysqli_fetch_array($sql_check)){
			   $str.=$row_check['prod_cat'];
			   $strnew.=$row_check['productid'];
			   $strnew1.=$row_check['prod_subcat'];
		   }
	?>
	var arr="<?=$str?>";
	var arr1="<?=$strnew?>";
	var arr2 = "<?=$strnew1?>";
	document.getElementById("prod_cat").value=arr;
	//document.getElementById("prodsub_cat").value=arr2;
	//document.getElementById("brand_str1").value=arr1;
	
	getProdSubCat(arr);
    getProd(arr2);
	getBrand(arr1);
	
		   
	<?php }
	   else{
	   }
    ?>
  }


//////  get Product Subcategory ////////////////////
 function getProdSubCat(val1){
	var strSubmit = "action=getProdSubCat&value="+val1+"&coupon=<?=$_REQUEST['coupon_code']?>";
	var strURL = "../includes/getField.php";
	var strResultFunc="displayState";
	xmlhttpPost(strURL,strSubmit,strResultFunc);
	return false;	
	}
	function displayState(result){
	//alert(result);
		if(result!="" && result!=0){
		document.getElementById("state_dis").innerHTML=result;
		showProduct();
		
		}				
	}
	

  function showProduct() {
  
   var chk_arr =  document.getElementsByName("prodsubcat[]");
   
    var chklength = chk_arr.length;  

	arr_product = [];           
	var j=0;
   for(k=0;k< chklength;k++)
    {

    if (chk_arr[k].checked == true){
	   arr_product[j] = chk_arr[k].value;
	   
		//document.getElementById("prodsub_cat").value=(document.getElementById("prodsub_cat").value)+","+(chk_arr[k].value);
		//arr_new=document.getElementById("prodsub_cat").value;
		//getProd(arr_new); 
		//alert('checked');
		j++;
    } 
	else {
	  //  document.getElementById("prodsub_cat").value=(document.getElementById("prodsub_cat").value).replace(","+(chk_arr[k].value), "");
		// arr_new=document.getElementById("prodsub_cat").value;
		// getProd(arr_new); 
         }
	
      }		
	
	document.getElementById("prodsub_cat").value =arr_product;
	
	getProd(arr_product);

   }




 //////  get Product Subcategory ////////////////////
 function getProd(val1){
 	var strSubmit = "action=getProd&value="+val1+"&coupon=<?=$_REQUEST[coupon_code]?>";
	var strURL = "../includes/getField.php";
	var strResultFunc="displayState1";
	xmlhttpPost(strURL,strSubmit,strResultFunc);
	return false;	
	}
	function displayState1(result){
		if(result!="" && result!=0){
		 document.getElementById("show_product").innerHTML=result;
		showBrand();
		}

		
	}


   function showBrand() {

    var chk_arr =  document.getElementsByName("prod[]");
    var chklength = chk_arr.length;  
	var arr=""; 
	
	arr_brand = [];          
	var m = 0;
   for(k=0;k<chklength;k++)
    {
    
    if (chk_arr[k].checked == true){
	
	   arr_brand[m] = chk_arr[k].value;
	
		//document.getElementById("brand_str1").value=(document.getElementById("brand_str1").value)+","+(chk_arr[k].value);
		//arr=document.getElementById("brand_str1").value;
		//alert('checked');
		m++;
    } 
	else {
	   // document.getElementById("brand_str1").value=(document.getElementById("brand_str1").value).replace(","+(chk_arr[k].value), "");
		//var arr=document.getElementById("brand_str1").value;
        }
	
     }	
	//checkForUC(arr);
	
	 document.getElementById("brand_str1").value = arr_brand;
     getBrand(arr_brand);
	
   }

   //////  get Product Subcategory ////////////////////
  function getBrand(val1){
  	var strSubmit = "action=getBrand&value="+val1+"&coupon=<?=$_REQUEST[coupon_code]?>";
	var strURL = "../includes/getField.php";
	var strResultFunc="displayState4";
	xmlhttpPost(strURL,strSubmit,strResultFunc);
	return false;	
	}
	function displayState4(result){
		if(result!="" && result!=0){
		 document.getElementById("show_brand").innerHTML=result;
		
		}

		
	}

  



</script>
<script>

</script>
</head>
<body onLoad="selfRun();">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-codiepie"></i> Coupon Mapping</h2>
      <h4 align="center">(<?=$_REQUEST['coupon_code'];?>)
      <?php if($_POST[submitTab]=='Save'){ ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <span style="color:#FF0000"><?php if($_POST[submitTab]=="Save"){ echo "Coupon Mapping";} ?> permissions are updated.</span>
   <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
         <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Mapping</a></li>
         </ul>
         <!-- Tab 1 Master / Region Rights-->
           <div id="home" class="tab-pane fade in active">
          <form id="frm" name="frm" class="form-horizontal" action="" method="post">
          <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm.product_cat)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm.product_cat)" value="Uncheck All" /></div>
                 <table id="myTable" class="table table-hover">
                 <thead>
                   <tr>
                    <th style="border:none">&nbsp;Product Category
						<input type="hidden" name="prod_cat" id="prod_cat"/>
					  	<input type="hidden" name="prodsub_cat" id="prodsub_cat"/> 
						<input type="hidden" name="brand_str1" id="brand_str1" />
					</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
				    ///// fecth  prod_cat from  coupon_mapping ///////
				$prodcutcat= mysqli_fetch_array(mysqli_query($link1 , "select prod_cat from  coupon_mapping where coupon_code='".$_REQUEST['coupon_code']."' "));				
				$product_cat = explode(",",$prodcutcat['prod_cat']);
				
				$i=1;
				    
				   $prod_data  =  mysqli_query($link1 , " select * from product_cat_master where status = '1' ");
						while($prod_cat=mysqli_fetch_array($prod_data)){
						if($i%4==0){
						?>
                  <tr>
                  <?php 				
				  }
				
						?>
                    <td><input style="width:20px" type="checkbox" id="product_cat" name="product_cat[]" value="<?=$prod_cat['catid']?>"  <?php if(in_array($prod_cat['catid'], $product_cat) ) echo "checked";?>  onClick="checkUncheck(this.value);" />
                <?=$prod_cat['cat_name']?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 				
				  }
				  
				 $i++;
				}		
                   
				?>
                </tbody>
             
                
                
              </table>
              </div>
              <div id="state_dis"></div>
			  
			  <div id="show_product"></div>
			  
			  
			  <div id="show_brand"></div>
			  
			  
              <div class="form-buttons" align="center">
              <input type="submit" class="btn btn-primary" name="submitTab" id="submitTab" value="Save"> 
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='coupon_master.php?op=edit&id=<?php echo $_REQUEST[userid];?><?=$pagenav?>'">
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