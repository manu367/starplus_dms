<?php

 include "../../config/constant.php"; 
 
 require_once "../../".CONFIG."connect.php";
     //Database setup information 
    /*$dbhost = 'localhost';  // Database Host
    $dbuser = 'root';       // Database Username
    $dbpass = '';           // Database Password
    $dbname = 'adventure';      // Database Name*/

     //Connect to the database and select database 
   /* $con = mysqli_connect($dbhost, $dbuser, $dbpass) or die(mysqli_error());
    mysqli_select_db($dbname);*/


     /*$return_arr = array();
    $param = $_GET["term"];

    $fetch = mysql_query("SELECT * FROM items WHERE itemCode REGEXP '^$param' LIMIT 5");
*/
    /* Retrieve and store in array the results of the query.*/
  /*  while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {

        $row_array['itemCode'] 		    = $row['itemCode'];
        $row_array['itemDesc'] 		    = $row['itemDesc'];
        $row_array['itemPrice']      	= $row['itemPrice'];

        array_push( $return_arr, $row_array );
    }
*/
    /* Free connection resources. */
  /*  mysql_close($conn);
*/
    /* Toss back results as json encoded array. */
    /*echo json_encode($return_arr);
*/
	 $return_arr = array();
	 
    $param = $_GET["term"];
	
 		/*$sql1=mysqli_query($con,"select * from tax");
		   while($row1=mysqli_fetch_array($sql1)){
		      $tax= $row1['tax_name'];
		
		 } $row_array['itemTax']= "<option>".$tax."</option>";*/
	    $sql=mysqli_query($con,"select * from product where name like '$param%' or sku like '$param%'");
	    
	    while($row = mysqli_fetch_array($sql)) {
	       // $return_arr[] =  ucfirst($row['name']);
		   
			$row_array['itemCode'] 	 = $row['name'];
			//$row_array['itemAvailable']    = $row['current_stock'];
			$row_array['itemPrice']   = $row['retail'];
			 $row_array['itemSku']   = $row['sku'];
			  $row_array['itemId']   = $row['id'];
			
		 
    array_push( $return_arr, $row_array );

      
	    }  
    // Toss back results as json encoded array. 
      echo json_encode($return_arr);
	
	
	 
	