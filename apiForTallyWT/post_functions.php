<?php
include('constant.php');
class PST_Functions{
	private $db;
	private $link;
	private $dt_format;
	function __construct() { 
		include_once './config/dbconnect.php';
		$this->db = new DatabaseService();
		$this->link = $this->db->getConnection();
		///////////////////
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}       
	function __destruct() {
	}
	//// get sale voucher data
	public function postTallyResponse($vchname,$docno,$doctype,$docdate,$remark){
		$todayDate = $this->dt_format->format('Y-m-d');
		$todayTime = $this->dt_format->format('H:i:s');
		$flag = true;
		///// update deviation
		$res1 =	mysqli_query($this->link,"INSERT INTO tally_voucher_response SET voucher_name = '".$vchname."', doc_no = '".$docno."', doc_type = '".$doctype."', doc_date = '".$docdate."', remark='".$remark."', entry_by='TALLY POST API', entry_date='".$todayDate." ".$todayTime."'");
		//// check if query is not executed
		if (!$res1) {
			 $flag = false;
			 $err_msg = "ER1: " . mysqli_error($this->link) . ".";
		}
		//////update document table accordingly
		if($doctype=="Sales" || $doctype=="Delivery Note"){
			$res2 =	mysqli_query($this->link,"UPDATE billing_master SET post_in_tally = 'Y' WHERE challan_no = '".$docno."'");
			//// check if query is not executed
			if (!$res2) {
				 $flag = false;
				 $err_msg = "ER2: " . mysqli_error($this->link) . ".";
			}	
		}
		if($doctype=="Purchase" || $doctype=="Receipt Note" ||  $doctype=="Receipt Note CHN"){
			$res22 =	mysqli_query($this->link,"UPDATE billing_master SET post_in_tally2 = 'Y' WHERE challan_no = '".$docno."'");
			//// check if query is not executed
			if (!$res22) {
				 $flag = false;
				 $err_msg = "ER22: " . mysqli_error($this->link) . ".";
			}	
		}
		else if($doctype=="Credit Note"){
			$res3 =	mysqli_query($this->link,"UPDATE credit_note SET post_in_tally = 'Y' WHERE ref_no = '".$docno."'");
			//// check if query is not executed
			if (!$res3) {
				 $flag = false;
				 $err_msg = "ER3: " . mysqli_error($this->link) . ".";
			}	
		}
		else if($doctype=="Debit Note"){
			$res4 =	mysqli_query($this->link,"UPDATE credit_note SET post_in_tally = 'Y' WHERE ref_no = '".$docno."'");
			//// check if query is not executed
			if (!$res4) {
				 $flag = false;
				 $err_msg = "ER4: " . mysqli_error($this->link) . ".";
			}	
		}
		else if($doctype=="Receipt"){
			$res5 =	mysqli_query($this->link,"UPDATE payment_receive SET post_in_tally = 'Y' WHERE doc_no = '".$docno."'");
			//// check if query is not executed
			if (!$res5) {
				 $flag = false;
				 $err_msg = "ER5: " . mysqli_error($this->link) . ".";
			}	
		}
		else if($doctype=="Payment"){
			$res6 =	mysqli_query($this->link,"UPDATE payment_send SET post_in_tally = 'Y' WHERE doc_no = '".$docno."'");
			//// check if query is not executed
			if (!$res6) {
				 $flag = false;
				 $err_msg = "ER6: " . mysqli_error($this->link) . ".";
			}	
		}
		else{
			
		}
		////// if all executed
		if($flag){
			return "1";
		}else{
			return "0";
		}
	}
}