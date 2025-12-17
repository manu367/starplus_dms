<?php
class DB_Functions{       
	private $db;
	private $link;
	private $dt_format;
	function __construct() { 
		include_once 'dbconnect.php';
		$this->db = new DatabaseService();
		$this->link = $this->db->getConnection();
		///////////////////
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}       
	function __destruct() {
	}
	/////
	 ///// generic function

public function getAnyDetails($keyid,$fields,$lookupname,$tbname){
	///// check no. of column
	$chk_keyword = substr_count($fields, ',');
   	if($chk_keyword > 0){
		$explodee = explode(",",$fields);
   		$tb_details = mysqli_fetch_array(mysqli_query($this->link,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
   		$rtn_str = "";
   		for($k=0;$k < count($explodee);$k++){
       		if($rtn_str==""){
          		$rtn_str.= $tb_details[$k];
	   		}
       		else{
          		$rtn_str.= "~".$tb_details[$k];
			}
		}
	}
	else{
		$tb_details = mysqli_fetch_array(mysqli_query($this->link,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
		$rtn_str = $tb_details[$fields];
	}
   return $rtn_str;
}
	
	//// check user
	public function getUserDetails($uid,$pwd){
		return mysqli_query($this->link,"SELECT * FROM admin_users WHERE phone = '".$uid."' and password='".$pwd."'");
	}
	
	//// get State
	public function getState(){
		return mysqli_query($this->link,"SELECT * FROM state_master WHERE 1");
	}
	
	//// get City
	public function getCity($state){
		//echo "SELECT * FROM district_master WHERE status='A' and state='".$state."'";
		return mysqli_query($this->link,"SELECT * FROM district_master WHERE status='A' and state='".$state."'");
	}
	
	//// get State
	public function getProduct(){
		return mysqli_query($this->link,"SELECT * FROM installation_product WHERE 1");
	}
	
}

