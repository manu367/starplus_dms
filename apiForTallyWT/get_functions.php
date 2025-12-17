<?php
include('constant.php');
class GET_Functions{   
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
	///// get product details
	function getAnyDetails($keyid,$fields,$lookupname,$tbname){
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
	////// date format
	function date_format($dt_sel){
		return substr($dt_sel,8,2)."-".substr($dt_sel,5,2)."-".substr($dt_sel,0,4);
	}
	//// get sale voucher data
	public function getSaleVoucher($invno,$from_date,$to_date){
		if($from_date){ $str = " AND sale_date >='".$from_date."' AND sale_date <='".$to_date."'";}
		if($invno){ $str .= " AND challan_no='".$invno."'";} 
		$invm_arr = array();
		/////// get master data of billing
		$i=0;
		$res_billm = mysqli_query($this->link,"SELECT * FROM billing_master WHERE type NOT IN ('GRN','LP','DIRECT SALE RETURN','SALE RETURN') AND status!='Cancelled' AND post_in_tally='' AND billing_type!='COMBO' ".$str);
		while($row_billm = mysqli_fetch_assoc($res_billm)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_billm["from_location"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			$from_statecode = substr($row_billm["from_gst_no"],0,2);
			$to_statecode = substr($row_billm["to_gst_no"],0,2);
			$inv_date = str_replace("-","/",$this->date_format($row_billm["sale_date"]));
			$dc_date = str_replace("-","/",$this->date_format($row_billm["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_billm["from_addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_billm["from_addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_billm["from_addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_billm["to_addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_billm["to_addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_billm["to_addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_billm["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_billm["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_billm["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_billm["deliv_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_billm["deliv_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_billm["deliv_addrs"];
				$deliv_pty_addrs2 = "";
			}

			//////////document details
			if($row_billm["document_type"]=="Delivery Challan"){
				$invm_arr[$i]["DocInfo"]["DocType"] = "DC";
			}else{
				$invm_arr[$i]["DocInfo"]["DocType"] = "INV";
			}
			$invm_arr[$i]["DocInfo"]["DocNo"] = $row_billm["challan_no"];
			$invm_arr[$i]["DocInfo"]["DocDate"] = $inv_date;
			////////// seller details
			$invm_arr[$i]["DocInfo"]["SellerGstin"] = $row_billm["from_gst_no"];
			$invm_arr[$i]["DocInfo"]["SellerLegalName"] = $row_billm["from_partyname"];
			$invm_arr[$i]["DocInfo"]["SellerAddr1"] = $from_pty_addrs;
			$invm_arr[$i]["DocInfo"]["SellerAddr2"] = $from_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["SellerCity"] = $row_billm["from_city"];
			$invm_arr[$i]["DocInfo"]["SellerState"] = $row_billm["from_state"];
			$invm_arr[$i]["DocInfo"]["SellerPincode"] = $row_billm["from_pincode"];
			$invm_arr[$i]["DocInfo"]["SellerStatecode"] = $from_statecode;
			$invm_arr[$i]["DocInfo"]["SellerPhone"] = $row_billm["from_phone"];
			$invm_arr[$i]["DocInfo"]["SellerEmail"] = $row_billm["from_email"];
			/////// buyer details
			$invm_arr[$i]["DocInfo"]["BuyerGstin"] = $row_billm["to_gst_no"];
			$invm_arr[$i]["DocInfo"]["BuyerLegalName"] = $row_billm["party_name"];
			$invm_arr[$i]["DocInfo"]["BuyerAddr1"] = $to_pty_addrs;
			$invm_arr[$i]["DocInfo"]["BuyerAddr2"] = $to_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["BuyerCity"] = $row_billm["to_city"];
			$invm_arr[$i]["DocInfo"]["BuyerState"] = $row_billm["to_state"];
			$invm_arr[$i]["DocInfo"]["BuyerPincode"] = $row_billm["to_pincode"];
			$invm_arr[$i]["DocInfo"]["BuyerStatecode"] = $to_statecode;
			$invm_arr[$i]["DocInfo"]["BuyerPhone"] = $row_billm["to_phone"];
			$invm_arr[$i]["DocInfo"]["BuyerEmail"] = $row_billm["to_email"];
			/////// Dispatch details
			$invm_arr[$i]["DocInfo"]["DispatchFrom"] = $row_billm["from_partyname"];
			$invm_arr[$i]["DocInfo"]["DispAddr1"] = $disp_pty_addrs;
			$invm_arr[$i]["DocInfo"]["DispAddr2"] = $disp_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["DispCity"] = $row_billm["from_city"];
			$invm_arr[$i]["DocInfo"]["DispPincode"] = $row_billm["from_pincode"];
			$invm_arr[$i]["DocInfo"]["DispStatecode"] = $from_statecode;
			////// Shipping details
			$invm_arr[$i]["DocInfo"]["ShipGstin"] = $row_billm["to_gst_no"];
			$invm_arr[$i]["DocInfo"]["ShipTo"] = $row_billm["party_name"];
			$invm_arr[$i]["DocInfo"]["ShipAddr1"] = $to_pty_addrs;
			$invm_arr[$i]["DocInfo"]["ShipAddr2"] = $to_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["ShipCity"] = $row_billm["to_city"];
			$invm_arr[$i]["DocInfo"]["ShipPincode"] = $row_billm["to_pincode"];
			$invm_arr[$i]["DocInfo"]["ShipStatecode"] = $to_statecode;
			$invm_arr[$i]["DocInfo"]["ShipStateName"] = $row_billm["to_state"];
			/////// value details
			$invm_arr[$i]["DocInfo"]["AssessedVal"] = $row_billm['basic_cost'];
			//$invm_arr[$i]["DocInfo"]["CgstVal"] = $row_billm['total_cgst_amt'];
			//$invm_arr[$i]["DocInfo"]["SgstVal"] = $row_billm['total_sgst_amt'];
			//$invm_arr[$i]["DocInfo"]["IgstVal"] = $row_billm['total_igst_amt'];
			$invm_arr[$i]["DocInfo"]["DiscVal"] = $row_billm['discount_amt'];
			//$invm_arr[$i]["DocInfo"]["RoundOffVal"] = $row_billm['round_off'];
			$invm_arr[$i]["DocInfo"]["TotalVal"] = $row_billm['total_cost'];
			
			////// ledger details
			$vchtype = "";
			$invm_arr[$i]["DocInfo"]["AccountLedger"] = "";
			$invm_arr[$i]["DocInfo"]["IgstName"] = "";
			$invm_arr[$i]["DocInfo"]["IgstValue"] = "";
			$invm_arr[$i]["DocInfo"]["CgstName"] = "";
			$invm_arr[$i]["DocInfo"]["CgstValue"] = "";
			$invm_arr[$i]["DocInfo"]["SgstName"] = "";
			$invm_arr[$i]["DocInfo"]["SgstValue"] = "";
			$invm_arr[$i]["DocInfo"]["TcsName"] = "";
			$invm_arr[$i]["DocInfo"]["TcsValue"] = "";
			$invm_arr[$i]["DocInfo"]["RoundOffName"] = "";
			$invm_arr[$i]["DocInfo"]["RoundOffvalue"] = "";
			$gst_val = 0;	
			$tcs_val =0;
			$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_billm["challan_no"]."' AND location_code='".$row_billm["from_location"]."'");
			while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){				
				if($row_ac_lg["ledger_type"]=="ACCOUNT"  || $row_ac_lg["ledger_type"]==""){
					$invm_arr[$i]["DocInfo"]["AccountLedger"] = $row_ac_lg["ledger_name"];
				}
				if($row_ac_lg["ledger_type"]=="IGST"){
					$invm_arr[$i]["DocInfo"]["IgstName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["IgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="CGST"){
					$invm_arr[$i]["DocInfo"]["CgstName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["CgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="SGST"){
					$invm_arr[$i]["DocInfo"]["SgstName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["SgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="TCS"){
					$invm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
					$tcs_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="ROUND OFF"){
					$invm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
					//$invm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
				}
				$vchtype = $row_ac_lg["voucher_name"];
			}
			$invm_arr[$i]["DocInfo"]["VoucherTypeName"] = $vchtype;
			///// branch name
			$branchname = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code ='".$row_billm["from_location"]."' AND ledger_voucher='Voucher' AND extension_for='1' AND status='Active'"));
			//$invm_arr[$i]["DocInfo"]["BranchCode"] = $row_billm["from_location"];
			//$invm_arr[$i]["DocInfo"]["BranchCode"] = $branchname["extension_name"];
			$invm_arr[$i]["DocInfo"]["BranchCode"] = $tallySink[1];
            ////////////////get  cost centre name
			$billfrom = $this->getAnyDetails($row_billm["sub_location"],"name","asc_code","asc_master");
			$explodevalf = explode("~",$billfrom);
			if($explodevalf[0]){ $costcentre=$billfrom; $godown=$billfrom;}else{ $costc =explode("~",$this->getAnyDetails($row_billm["sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			
			$invm_arr[$i]["DocInfo"]["CostCentre"] = $costcentre;
			$invm_arr[$i]["DocInfo"]["PaymentTerm"] = $row_billm["payment_term"];
			$invm_arr[$i]["DocInfo"]["Remark"] = $row_billm["billing_rmk"];
			$invm_arr[$i]["DocInfo"]["ItemScheme"] = "";
			///////// get item details
			$j = 1;
			$invd_arr = array();
			$bill_itemlist = array();
			$brand_arr = array();
			$psc_arr = array();
			$item_cost = 0;	
			$res_billd = mysqli_query($this->link,"SELECT * FROM billing_model_data WHERE challan_no='".$row_billm["challan_no"]."'");
			while($row_billd = mysqli_fetch_assoc($res_billd)){
				$tax_per = $row_billd['cgst_per']+$row_billd['sgst_per']+$row_billd['igst_per'];
				if($row_billd['prod_code']=='39' || $row_billd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
				/// get part details
				$part_det = explode("~",$this->getAnyDetails($row_billd['prod_code'],"productname,hsn_code,productsubcat,brand","productcode","product_master"));
				//////
				$brand_arr[] = $part_det[3];
				$psc_arr[] = $part_det[2];
				$bill_itemlist["Sno"] = $j;
				$bill_itemlist["ProductDesc"] = $part_det[0];
				$bill_itemlist["IsServiceChg"] = $service_flag;
				$bill_itemlist["HsnCode"] = $part_det[1];
				$bill_itemlist["Qty"] = $row_billd["qty"];
				$bill_itemlist["Unit"] = "NOS";
				$bill_itemlist["UnitPrice"] = $row_billd["price"];
				$bill_itemlist["SubTotal"] = $row_billd["value"];
				$bill_itemlist["Discount"] = ($row_billd["discount"]);
				$bill_itemlist["PreTaxVal"] = $row_billd["value"];
				$bill_itemlist["AssessedVal"] = $row_billd["value"];
				$bill_itemlist["GstRate"] = $tax_per;
				$bill_itemlist["IgstAmt"] = $row_billd["igst_amt"];
				$bill_itemlist["CgstAmt"] = $row_billd["cgst_amt"];
				$bill_itemlist["SgstAmt"] = $row_billd["sgst_amt"];
				$bill_itemlist["TotalItemVal"] = $row_billd["totalvalue"];
				$bill_itemlist["GodownName"] = $godown;
				array_push($invd_arr,$bill_itemlist);
				$item_cost += $row_billd["value"]-$row_billd["discount"];
				$j++;
			}//// close 2nd while loop
			$grand_total = number_format($item_cost,'2','.','')+number_format($gst_val,'2','.','')+number_format($tcs_val,'2','.','');
			$invm_arr[$i]["DocInfo"]["InvAmt"] = number_format($grand_total,'2','.','');
			if(strpos($grand_total, ".") !== false){
				$expd_gt = explode(".",$grand_total);
				$checkval = ".".$expd_gt[1];
				if($checkval>=.50){
					$ro = 1-$checkval;
					$roundoff = "".number_format($ro,'2','.','');
				}else{
					$roundoff = "-".number_format($checkval,'2','.','');
				}
			}else{
				$roundoff = 0.00;
			}
			if($roundoff!=0 && $roundoff!=0.00){
				$invm_arr[$i]["DocInfo"]["RoundOffName"] = "Rounded Off";
			}	
			$invm_arr[$i]["DocInfo"]["RoundOffvalue"] = $roundoff;	
				
			$invm_arr[$i]["DocInfo"]["ItemsInfo"] = $invd_arr;
			
			/////// check which brand is more in this bill
			$counted_brand = array_count_values($brand_arr);
			arsort($counted_brand); //sort descending maintain keys
			$most_brand = key($counted_brand); //get the key, as we are rewound it's the first key
			/////// check which product sub category is more in this bill
			$counted_psc = array_count_values($psc_arr);
			arsort($counted_psc); //sort descending maintain keys
			$most_psc = key($counted_psc); //get the key, as we are rewound it's the first key
			$invm_arr[$i]["DocInfo"]["Brand"] = $this->getAnyDetails($most_brand,"make","id","make_master");
			$invm_arr[$i]["DocInfo"]["Segment"] = $this->getAnyDetails($most_psc,"prod_sub_cat","psubcatid","product_sub_category");
			
			
			$i++;
		}///// close first while loop
		}
		return $invm_arr;
	}
	//// get purchase voucher data
	public function getPurchaseVoucher($grnno,$from_date,$to_date){
		if($from_date){ $str = " AND ((sale_date >='".$from_date."' AND sale_date <='".$to_date."') or (receive_date >='".$from_date."' AND receive_date <='".$to_date."'))";}
		if($grnno){ $str .= " AND challan_no='".$grnno."'";} 
		$purm_arr = array();
		/////// get master data of billing
		$i=0;
		$res_purm = mysqli_query($this->link,"SELECT * FROM billing_master WHERE type IN ('GRN','LP','RETAIL','STN','CORPORATE') AND billing_type!='COMBO' AND post_in_tally2='' AND status='Received' ".$str);
		while($row_purm = mysqli_fetch_assoc($res_purm)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_purm["to_location"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			$from_statecode = substr($row_purm["from_gst_no"],0,2);
			$to_statecode = substr($row_purm["to_gst_no"],0,2);
			
			### If Receive date is available then inv date will receive else sale date
			if($row_purm["receive_date"]!='0000-00-00'){
				$inv_date = str_replace("-","/",$this->date_format($row_purm["receive_date"]));
			}
			else{
			$inv_date = str_replace("-","/",$this->date_format($row_purm["sale_date"]));
			}
			$dc_date = str_replace("-","/",$this->date_format($row_purm["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_purm["from_addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_purm["from_addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_purm["from_addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_purm["to_addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_purm["to_addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_purm["to_addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_purm["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_purm["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_purm["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_purm["deliv_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_purm["deliv_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_purm["deliv_addrs"];
				$deliv_pty_addrs2 = "";
			}
			if($row_purm["document_type"]=="Delivery Challan"){
				$purm_arr[$i]["DocInfo"]["DocType"] = "DC";
			}else{
				$purm_arr[$i]["DocInfo"]["DocType"] = "INV";
			}
			//////////document details
			//$purm_arr[$i]["DocInfo"]["DocType"] = "INV";
			$purm_arr[$i]["DocInfo"]["DocNo"] = $row_purm["challan_no"];
			$purm_arr[$i]["DocInfo"]["DocDate"] = $inv_date;
			$purm_arr[$i]["DocInfo"]["DocRecDate"] = str_replace("-","/",$this->date_format($row_purm["receive_date"]));
			$purm_arr[$i]["DocInfo"]["RefInvNo"] = $row_purm["inv_ref_no"];
			$purm_arr[$i]["DocInfo"]["RefInvDate"] = str_replace("-","/",$this->date_format($row_purm["po_inv_date"]));
			////////// seller details
			$purm_arr[$i]["DocInfo"]["SellerGstin"] = $row_purm["from_gst_no"];
			$purm_arr[$i]["DocInfo"]["SellerLegalName"] = $row_purm["from_partyname"];
			$purm_arr[$i]["DocInfo"]["SellerAddr1"] = $from_pty_addrs;
			$purm_arr[$i]["DocInfo"]["SellerAddr2"] = $from_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["SellerCity"] = $row_purm["from_city"];
			$purm_arr[$i]["DocInfo"]["SellerState"] = $row_purm["from_state"];
			$purm_arr[$i]["DocInfo"]["SellerPincode"] = $row_purm["from_pincode"];
			$purm_arr[$i]["DocInfo"]["SellerStatecode"] = $from_statecode;
			$purm_arr[$i]["DocInfo"]["SellerPhone"] = $row_purm["from_phone"];
			$purm_arr[$i]["DocInfo"]["SellerEmail"] = $row_purm["from_email"];
			/////// buyer details
			$purm_arr[$i]["DocInfo"]["BuyerGstin"] = $row_purm["to_gst_no"];
			$purm_arr[$i]["DocInfo"]["BuyerLegalName"] = $row_purm["party_name"];
			$purm_arr[$i]["DocInfo"]["BuyerAddr1"] = $to_pty_addrs;
			$purm_arr[$i]["DocInfo"]["BuyerAddr2"] = $to_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["BuyerCity"] = $row_purm["to_city"];
			$purm_arr[$i]["DocInfo"]["BuyerState"] = $row_purm["to_state"];
			$purm_arr[$i]["DocInfo"]["BuyerPincode"] = $row_purm["to_pincode"];
			$purm_arr[$i]["DocInfo"]["BuyerStatecode"] = $to_statecode;
			$purm_arr[$i]["DocInfo"]["BuyerPhone"] = $row_purm["to_phone"];
			$purm_arr[$i]["DocInfo"]["BuyerEmail"] = $row_purm["to_email"];
			/////// Dispatch details
			$purm_arr[$i]["DocInfo"]["DispatchFrom"] = $row_purm["from_partyname"];
			$purm_arr[$i]["DocInfo"]["DispAddr1"] = $disp_pty_addrs;
			$purm_arr[$i]["DocInfo"]["DispAddr2"] = $disp_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["DispCity"] = $row_purm["from_city"];
			$purm_arr[$i]["DocInfo"]["DispPincode"] = $row_purm["from_pincode"];
			$purm_arr[$i]["DocInfo"]["DispStatecode"] = $from_statecode;
			////// Shipping details
			$purm_arr[$i]["DocInfo"]["ShipGstin"] = $row_purm["to_gst_no"];
			$purm_arr[$i]["DocInfo"]["ShipTo"] = $row_purm["party_name"];
			$purm_arr[$i]["DocInfo"]["ShipAddr1"] = $to_pty_addrs;
			$purm_arr[$i]["DocInfo"]["ShipAddr2"] = $to_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["ShipCity"] = $row_purm["to_city"];
			$purm_arr[$i]["DocInfo"]["ShipPincode"] = $row_purm["to_pincode"];
			$purm_arr[$i]["DocInfo"]["ShipStatecode"] = $to_statecode;
			$invm_arr[$i]["DocInfo"]["ShipStateName"] = $row_purm["to_state"];
			/////// value details
			$purm_arr[$i]["DocInfo"]["AssessedVal"] = $row_purm['basic_cost'];
			//$purm_arr[$i]["DocInfo"]["CgstVal"] = $row_purm['cgst_amt'];
			//$purm_arr[$i]["DocInfo"]["SgstVal"] = $row_purm['sgst_amt'];
			//$purm_arr[$i]["DocInfo"]["IgstVal"] = $row_purm['igst_amt'];
			$purm_arr[$i]["DocInfo"]["DiscVal"] = 0.00;
			$purm_arr[$i]["DocInfo"]["RoundOffVal"] = $row_purm['round_off'];
			$purm_arr[$i]["DocInfo"]["TotalVal"] = $row_purm['total_cost'];
			////// ledger details
			$vchtype = "";
			$purm_arr[$i]["DocInfo"]["TcsName"] = "";
			$purm_arr[$i]["DocInfo"]["TcsValue"] = "";
			$purm_arr[$i]["DocInfo"]["RoundOffName"] = "";
			$purm_arr[$i]["DocInfo"]["RoundOffvalue"] = "";
			
			/////check ledger entry 
			$check_ldg = mysqli_num_rows(mysqli_query($this->link,"SELECT id FROM location_ledger WHERE transaction_no ='".$row_purm["challan_no"]."' AND location_code='".$row_purm["to_location"]."' AND ledger_type='ACCOUNT'"));
			if($check_ldg>1){
				$purm_arr[$i]["DocInfo"]["AccountLedger"] = "";
				$purm_arr[$i]["DocInfo"]["IgstName"] = "";
				$purm_arr[$i]["DocInfo"]["CgstName"] = "";
				$purm_arr[$i]["DocInfo"]["SgstName"] = "";

				$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_purm["challan_no"]."' AND location_code='".$row_purm["to_location"]."'");		
				$aclg = array();
				$iglg = array();
				$cglg = array();
				$sglg = array();
				$ac = array();
				$ig = array();
				$sg = array();
				$cg = array();
				while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){	
					if($row_ac_lg["ledger_type"]=="ACCOUNT" || $row_ac_lg["ledger_type"]==""){
						$ac = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($aclg,$ac);
					}
					if($row_ac_lg["ledger_type"]=="IGST"){
						$ig = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($iglg,$ig);
					}
					if($row_ac_lg["ledger_type"]=="CGST"){
						$cg = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($cglg,$cg);
					}
					if($row_ac_lg["ledger_type"]=="SGST"){
						$sg = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($sglg,$sg);
					}
					if($row_ac_lg["ledger_type"]=="TCS"){
						$purm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="ROUND OFF"){
						$purm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
					}
					$vchtype = $row_ac_lg["voucher_name"];
				}
				$purm_arr[$i]["DocInfo"]["AccountLedger"] = $aclg;
				$purm_arr[$i]["DocInfo"]["IgstName"] = $iglg;
				$purm_arr[$i]["DocInfo"]["CgstName"] = $cglg;
				$purm_arr[$i]["DocInfo"]["SgstName"] = $sglg;
			}else{
				$purm_arr[$i]["DocInfo"]["AccountLedger"] = "";
				$purm_arr[$i]["DocInfo"]["IgstName"] = "";
				$purm_arr[$i]["DocInfo"]["IgstValue"] = "";
				$purm_arr[$i]["DocInfo"]["CgstName"] = "";
				$purm_arr[$i]["DocInfo"]["CgstValue"] = "";
				$purm_arr[$i]["DocInfo"]["SgstName"] = "";
				$purm_arr[$i]["DocInfo"]["SgstValue"] = "";
				$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_purm["challan_no"]."' AND location_code='".$row_purm["to_location"]."'");		
				while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){				
					if($row_ac_lg["ledger_type"]=="ACCOUNT" || $row_ac_lg["ledger_type"]==""){
						$purm_arr[$i]["DocInfo"]["AccountLedger"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["AccountValue"] = $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="IGST"){
						$purm_arr[$i]["DocInfo"]["IgstName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["IgstValue"] = $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="CGST"){
						$purm_arr[$i]["DocInfo"]["CgstName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["CgstValue"] = $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="SGST"){
						$purm_arr[$i]["DocInfo"]["SgstName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["SgstValue"] = $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="TCS"){
						$purm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="ROUND OFF"){
						$purm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
					}
					$vchtype = $row_ac_lg["voucher_name"];
				}
			}
			$purm_arr[$i]["DocInfo"]["VoucherTypeName"] = $vchtype;
			///// branch name
			$branchname = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code ='".$row_purm["to_location"]."' AND ledger_voucher='Voucher' AND extension_for='1' AND status='Active'"));
			//$purm_arr[$i]["DocInfo"]["BranchCode"] = $row_purm["to_location"];
			//$purm_arr[$i]["DocInfo"]["BranchCode"] = $branchname["extension_name"];
			$purm_arr[$i]["DocInfo"]["BranchCode"] = $tallySink[1];
			////////////////get  cost centre name
			if($row_purm["type"]=="RETAIL" || $row_purm["type"]=="STN"){
				$receivein = $this->getAnyDetails($row_purm["receive_sub_location"],"name","asc_code","asc_master");
				$explodevalf = explode("~",$receivein);
				//if($explodevalf[0]){ $costcentre=$receivein; }else{ $costcentre=$this->getAnyDetails($row_purm["sub_location"],"cost_center","sub_location","sub_location_master");}
				if($explodevalf[0]){ $costcentre=$receivein; $godown=$receivein;}else{ $costc =explode("~",$this->getAnyDetails($row_purm["receive_sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			}else{
				$receivein = $this->getAnyDetails($row_purm["sub_location"],"name","asc_code","asc_master");
				$explodevalf = explode("~",$receivein);
				//if($explodevalf[0]){ $costcentre=$receivein; }else{ $costcentre=$this->getAnyDetails($row_purm["sub_location"],"cost_center","sub_location","sub_location_master");}
				if($explodevalf[0]){ $costcentre=$receivein; $godown=$receivein;}else{ $costc =explode("~",$this->getAnyDetails($row_purm["sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			}
			
			$purm_arr[$i]["DocInfo"]["CostCentre"] = $costcentre;
			//$purm_arr[$i]["DocInfo"]["freight"] = $row_purm["freight"];
			$purm_arr[$i]["DocInfo"]["tds194q"] = $row_purm["tds"];
			$purm_arr[$i]["DocInfo"]["Remark"] = $row_purm["receive_remark"];	
			///////// get item details
			$j = 1;
			$freight_val = 0.00;
			$purd_arr = array();
			$pur_itemlist = array();
			$res_purd = mysqli_query($this->link,"SELECT * FROM billing_model_data WHERE challan_no='".$row_purm["challan_no"]."'");
			while($row_pucd = mysqli_fetch_assoc($res_purd)){
				$tax_per = $row_pucd['cgst_per']+$row_pucd['sgst_per']+$row_pucd['igst_per'];
				if($row_pucd['prod_code']=='39' || $row_pucd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
				/// get part details
				$part_det = explode("~",$this->getAnyDetails($row_pucd['prod_code'],"productname,hsn_code,is_service","productcode","product_master"));
				//////
				if($part_det[2]=="Y"){
					$freight_val += $row_pucd["value"];
				}else{
					$pur_itemlist["Sno"] = $j;
					$pur_itemlist["ProductDesc"] = $part_det[0];
					$pur_itemlist["IsServiceChg"] = $service_flag;
					$pur_itemlist["HsnCode"] = $part_det[1];
					$pur_itemlist["Qty"] = $row_pucd["qty"];
					$pur_itemlist["Unit"] = "NOS";
					$pur_itemlist["UnitPrice"] = $row_pucd["price"];
					$pur_itemlist["SubTotal"] = $row_pucd["value"];
					$pur_itemlist["Discount"] = 0.00;
					$pur_itemlist["PreTaxVal"] = $row_pucd["value"];
					$pur_itemlist["AssessedVal"] = $row_pucd["value"];
					$pur_itemlist["GstRate"] = $tax_per;
					$pur_itemlist["IgstAmt"] = $row_pucd["igst_amt"];
					$pur_itemlist["CgstAmt"] = $row_pucd["cgst_amt"];
					$pur_itemlist["SgstAmt"] = $row_pucd["sgst_amt"];
					$pur_itemlist["TotalItemVal"] = $row_pucd["totalvalue"];
					$pur_itemlist["GodownName"] = $godown;
					array_push($purd_arr,$pur_itemlist);
					$j++;
				}
			}//// close 2nd while loop
			$purm_arr[$i]["DocInfo"]["ItemsInfo"] = $purd_arr;
			$purm_arr[$i]["DocInfo"]["freight"] = $freight_val;
			$i++;
		}///// close first while loop
		}
		return $purm_arr;
	}
	//// get CN voucher data
	public function getCNVoucher($crno,$from_date,$to_date){
		//if($from_date){ $str = " AND create_date >='".$from_date."' AND create_date <='".$to_date."'";}
		//// change date filter on 31 jan 23 on behalf of jagat
		if($from_date){ $str = " AND app_date >='".$from_date."' AND app_date <='".$to_date."'";}
		if($crno){ $str .= " AND ref_no='".$crno."'";} 
		$cnm_arr = array();
		/////// get master data of credit
		$i=0;
		$res_cnm = mysqli_query($this->link,"SELECT * FROM credit_note WHERE status IN ('Approved') AND billing_type!='COMBO' AND post_in_tally='' ".$str);
		while($row_cnm = mysqli_fetch_assoc($res_cnm)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_cnm["location_id"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			///// get billing details
			if($row_cnm["description"]=="DIRECT SALE RETURN"){
				$res_inv = mysqli_query($this->link,"SELECT 
				from_gst_no AS to_gst_no,
				to_gst_no AS from_gst_no,
				from_addrs AS to_addrs,
				to_addrs AS from_addrs,
				disp_addrs AS deliv_addrs,
				deliv_addrs AS disp_addrs,
				from_partyname AS party_name,
				party_name AS from_partyname,
				from_city AS to_city,
				from_pincode AS to_pincode,
				from_phone AS to_phone,
				from_email AS to_email,
				to_city AS from_city,
				to_pincode AS from_pincode,
				to_phone AS from_phone,
				to_email AS from_email FROM billing_master WHERE challan_no='".$row_cnm["challan_no"]."'")or die(mysqli_error($this->link));
			}else if($row_cnm["description"]=="SALE RETURN"){
				$res_inv = mysqli_query($this->link,"SELECT from_gst_no,to_gst_no,from_addrs,to_addrs,disp_addrs,deliv_addrs,from_partyname,party_name,from_city,from_pincode,from_phone,from_email,to_city,to_pincode,to_phone,to_email FROM billing_master WHERE challan_no='".$row_cnm["entered_ref_no"]."'")or die(mysqli_error($this->link));
			}else{
				$res_inv = mysqli_query($this->link,"SELECT from_gst_no,to_gst_no,from_addrs,to_addrs,disp_addrs,deliv_addrs,from_partyname,party_name,from_city,from_pincode,from_phone,from_email,to_city,to_pincode,to_phone,to_email FROM billing_master WHERE challan_no='".$row_cnm["challan_no"]."'")or die(mysqli_error($this->link));
			}
			$row_inv = mysqli_fetch_array($res_inv);
			//////
			$from_statecode = substr($row_inv["from_gst_no"],0,2);
			$to_statecode = substr($row_inv["to_gst_no"],0,2);
			//$cn_date = str_replace("-","/",$this->date_format($row_cnm["create_date"]));
			$cn_date = str_replace("-","/",$this->date_format($row_cnm["app_date"]));
			//$dc_date = str_replace("-","/",$this->date_format($row_cnm["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_inv["from_addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_inv["from_addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_inv["from_addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_inv["to_addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_inv["to_addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_inv["to_addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_inv["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_inv["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_inv["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_inv["deliv_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_inv["deliv_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_inv["deliv_addrs"];
				$deliv_pty_addrs2 = "";
			}
			//////////document details
			$cnm_arr[$i]["DocInfo"]["DocType"] = "CN";
			$cnm_arr[$i]["DocInfo"]["DocNo"] = $row_cnm["ref_no"];
			$cnm_arr[$i]["DocInfo"]["DocDate"] = $cn_date;
			////////// seller details
			$cnm_arr[$i]["DocInfo"]["SellerGstin"] = $row_inv["from_gst_no"];
			$cnm_arr[$i]["DocInfo"]["SellerLegalName"] = $row_inv["from_partyname"];
			$cnm_arr[$i]["DocInfo"]["SellerAddr1"] = $from_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["SellerAddr2"] = $from_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["SellerCity"] = $row_inv["from_city"];
			$cnm_arr[$i]["DocInfo"]["SellerPincode"] = $row_inv["from_pincode"];
			$cnm_arr[$i]["DocInfo"]["SellerStatecode"] = $from_statecode;
			$cnm_arr[$i]["DocInfo"]["SellerPhone"] = $row_inv["from_phone"];
			$cnm_arr[$i]["DocInfo"]["SellerEmail"] = $row_inv["from_email"];
			/////// buyer details
			$cnm_arr[$i]["DocInfo"]["BuyerGstin"] = $row_inv["to_gst_no"];
			$cnm_arr[$i]["DocInfo"]["BuyerLegalName"] = $row_inv["party_name"];
			$cnm_arr[$i]["DocInfo"]["BuyerAddr1"] = $to_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["BuyerAddr2"] = $to_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["BuyerCity"] = $row_inv["to_city"];
			$cnm_arr[$i]["DocInfo"]["BuyerPincode"] = $row_inv["to_pincode"];
			$cnm_arr[$i]["DocInfo"]["BuyerStatecode"] = $to_statecode;
			$cnm_arr[$i]["DocInfo"]["BuyerPhone"] = $row_inv["to_phone"];
			$cnm_arr[$i]["DocInfo"]["BuyerEmail"] = $row_inv["to_email"];
			/////// Dispatch details
			$cnm_arr[$i]["DocInfo"]["DispatchFrom"] = $row_inv["from_partyname"];
			$cnm_arr[$i]["DocInfo"]["DispAddr1"] = $disp_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["DispAddr2"] = $disp_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["DispCity"] = $row_inv["from_city"];
			$cnm_arr[$i]["DocInfo"]["DispPincode"] = $row_inv["from_pincode"];
			$cnm_arr[$i]["DocInfo"]["DispStatecode"] = $from_statecode;
			////// Shipping details
			$cnm_arr[$i]["DocInfo"]["ShipGstin"] = $row_inv["to_gst_no"];
			$cnm_arr[$i]["DocInfo"]["ShipTo"] = $row_inv["party_name"];
			$cnm_arr[$i]["DocInfo"]["ShipAddr1"] = $to_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["ShipAddr2"] = $to_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["ShipCity"] = $row_inv["to_city"];
			$cnm_arr[$i]["DocInfo"]["ShipPincode"] = $row_inv["to_pincode"];
			$cnm_arr[$i]["DocInfo"]["ShipStatecode"] = $to_statecode;
			/////// value details
			$cnm_arr[$i]["DocInfo"]["AssessedVal"] = $row_cnm['basic_amt'];
			$cnm_arr[$i]["DocInfo"]["CgstVal"] = $row_cnm['cgst_amt'];
			$cnm_arr[$i]["DocInfo"]["SgstVal"] = $row_cnm['sgst_amt'];
			$cnm_arr[$i]["DocInfo"]["IgstVal"] = $row_cnm['igst_amt'];
			$cnm_arr[$i]["DocInfo"]["DiscVal"] = $row_cnm["discount"];
			//$cnm_arr[$i]["DocInfo"]["RoundOffVal"] = $row_cnm['round_off'];
			$cnm_arr[$i]["DocInfo"]["TotalVal"] = $row_cnm['amount'];
			////// ledger details
			$vchtype = "";
			$cnm_arr[$i]["DocInfo"]["AccountLedger"] = "";
			$cnm_arr[$i]["DocInfo"]["IgstName"] = "";
			$cnm_arr[$i]["DocInfo"]["IgstValue"] = "";
			$cnm_arr[$i]["DocInfo"]["CgstName"] = "";
			$cnm_arr[$i]["DocInfo"]["CgstValue"] = "";
			$cnm_arr[$i]["DocInfo"]["SgstName"] = "";
			$cnm_arr[$i]["DocInfo"]["SgstValue"] = "";
			$cnm_arr[$i]["DocInfo"]["TcsName"] = "";
			$cnm_arr[$i]["DocInfo"]["TcsValue"] = "";
			$cnm_arr[$i]["DocInfo"]["RoundOffName"] = "";
			$cnm_arr[$i]["DocInfo"]["RoundOffvalue"] = "";
			$gst_val = 0;	
			$tcs_val =0;
			$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_cnm["ref_no"]."' AND location_code='".$row_cnm["location_id"]."'");
			while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){				
				if($row_ac_lg["ledger_type"]=="ACCOUNT"){
					$cnm_arr[$i]["DocInfo"]["AccountLedger"] = $row_ac_lg["ledger_name"];
				}
				if($row_ac_lg["ledger_type"]=="IGST"){
					$cnm_arr[$i]["DocInfo"]["IgstName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["IgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="CGST"){
					$cnm_arr[$i]["DocInfo"]["CgstName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["CgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="SGST"){
					$cnm_arr[$i]["DocInfo"]["SgstName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["SgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="TCS"){
					$cnm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
					$tcs_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="ROUND OFF"){
					$cnm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
				}
				$vchtype = $row_ac_lg["voucher_name"];
			}
			$cnm_arr[$i]["DocInfo"]["VoucherTypeName"] = $vchtype;
			///// branch name
			$branchname = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code ='".$row_cnm["location_id"]."' AND ledger_voucher='Voucher' AND extension_for='3' AND status='Active'"));
			//$cnm_arr[$i]["DocInfo"]["BranchCode"] = $row_billm["from_location"];
			$cnm_arr[$i]["DocInfo"]["BranchCode"] = $tallySink[1];
            ////////////////get  cost centre name
			$billfrom = $this->getAnyDetails($row_cnm["sub_location"],"name","asc_code","asc_master");
			$explodevalf = explode("~",$billfrom);
			//if($explodevalf[0]){ $costcentre=$billfrom; }else{ $costcentre=$this->getAnyDetails($row_cnm["sub_location"],"cost_center","sub_location","sub_location_master");}
			if($explodevalf[0]){ $costcentre=$billfrom; $godown=$billfrom;}else{ $costc =explode("~",$this->getAnyDetails($row_cnm["sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			
			$cnm_arr[$i]["DocInfo"]["CostCentre"] = $costcentre;
			$cnm_arr[$i]["DocInfo"]["Remark"] = $row_cnm["remark"];	
			///////// get item details
			$j = 1;
			$cnd_arr = array();
			$cn_itemlist = array();
			$item_cost = 0;
			$res_cnd = mysqli_query($this->link,"SELECT * FROM credit_note_data WHERE ref_no='".$row_cnm["ref_no"]."'");
			while($row_cnd = mysqli_fetch_assoc($res_cnd)){
				$tax_per = $row_cnd['cgst_per']+$row_cnd['sgst_per']+$row_cnd['igst_per'];
				if($row_cnd['prod_code']=='39' || $row_cnd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
				/// get part details
				$part_det = explode("~",$this->getAnyDetails($row_cnd['prod_code'],"productname,hsn_code,productsubcat,brand","productcode","product_master"));
				//////
				$brand_arr[] = $part_det[3];
				$psc_arr[] = $part_det[2];
				$cn_itemlist["Sno"] = $j;
				$cn_itemlist["ProductDesc"] = $part_det[0];
				$cn_itemlist["IsServiceChg"] = $service_flag;
				$cn_itemlist["HsnCode"] = $part_det[1];
				$cn_itemlist["Qty"] = $row_cnd["req_qty"];
				$cn_itemlist["Unit"] = "NOS";
				$cn_itemlist["UnitPrice"] = $row_cnd["price"];
				$cn_itemlist["SubTotal"] = $row_cnd["value"];
				$cn_itemlist["Discount"] = $row_cnd["discount"];
				$cn_itemlist["PreTaxVal"] = $row_cnd["value"];
				$cn_itemlist["AssessedVal"] = $row_cnd["value"];
				$cn_itemlist["GstRate"] = $tax_per;
				$cn_itemlist["IgstAmt"] = $row_cnd["igst_amt"];
				$cn_itemlist["CgstAmt"] = $row_cnd["cgst_amt"];
				$cn_itemlist["SgstAmt"] = $row_cnd["sgst_amt"];
				$cn_itemlist["TotalItemVal"] = $row_cnd["totalvalue"];
				$cn_itemlist["GodownName"] = $godown;
				array_push($cnd_arr,$cn_itemlist);
				$item_cost += $row_cnd["value"]-$row_cnd["discount"];
				$j++;
			}//// close 2nd while loop
			$grand_total = number_format($item_cost,'2','.','')+number_format($gst_val,'2','.','')+number_format($tcs_val,'2','.','');
			$cnm_arr[$i]["DocInfo"]["InvAmt"] = number_format($grand_total,'2','.','');
			if(strpos($grand_total, ".") !== false){
				$expd_gt = explode(".",$grand_total);
				$checkval = ".".$expd_gt[1];
				if($checkval>=.50){
					$ro = 1-$checkval;
					$roundoff = "".number_format($ro,'2','.','');
				}else{
					$roundoff = "-".number_format($checkval,'2','.','');
				}
			}else{
				$roundoff = 0.00;
			}	
			$cnm_arr[$i]["DocInfo"]["RoundOffvalue"] = $roundoff;
			
			$cnm_arr[$i]["DocInfo"]["ItemsInfo"] = $cnd_arr;
			
			/////// check which brand is more in this bill
			$counted_brand = array_count_values($brand_arr);
			arsort($counted_brand); //sort descending maintain keys
			$most_brand = key($counted_brand); //get the key, as we are rewound it's the first key
			/////// check which product sub category is more in this bill
			$counted_psc = array_count_values($psc_arr);
			arsort($counted_psc); //sort descending maintain keys
			$most_psc = key($counted_psc); //get the key, as we are rewound it's the first key
			$cnm_arr[$i]["DocInfo"]["Brand"] = $this->getAnyDetails($most_brand,"make","id","make_master");
			$cnm_arr[$i]["DocInfo"]["Segment"] = $this->getAnyDetails($most_psc,"prod_sub_cat","psubcatid","product_sub_category");
			
			$i++;
		}
		}///// close first while loop
		return $cnm_arr;
	}
	//// get DN voucher data
	public function getDNVoucher($drno,$from_date,$to_date){
		if($from_date){ $str = " AND create_date >='".$from_date."' AND create_date <='".$to_date."'";}
		if($drno){ $str .= " AND ref_no='".$drno."'";} 
		$dnm_arr = array();
		$i=0;
		/////// get master data of debit
		$res_dnm = mysqli_query($this->link,"SELECT * FROM debit_note WHERE status IN ('Approved') AND post_in_tally='' ".$str);
		while($row_dnm = mysqli_fetch_assoc($res_dnm)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_dnm["location_id"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			///// get billing details
			$res_inv = mysqli_query($this->link,"SELECT from_gst_no,to_gst_no,from_addrs,to_addrs,disp_addrs,deliv_addrs,from_partyname,party_name,from_city,from_pincode,from_phone,from_email,to_city,to_pincode,to_phone,to_email FROM billing_master WHERE challan_no='".$row_dnm["entered_ref_no"]."'")or die(mysqli_error($this->link));
			$row_inv = mysqli_fetch_array($res_inv);
			//////
			$from_statecode = substr($row_inv["from_gst_no"],0,2);
			$to_statecode = substr($row_inv["to_gst_no"],0,2);
			$dn_date = str_replace("-","/",$this->date_format($row_dnm["create_date"]));
			//$dc_date = str_replace("-","/",$this->date_format($row_dnm["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_inv["from_addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_inv["from_addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_inv["from_addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_inv["to_addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_inv["to_addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_inv["to_addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_inv["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_inv["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_inv["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_inv["deliv_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_inv["deliv_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_inv["deliv_addrs"];
				$deliv_pty_addrs2 = "";
			}
			//////////document details
			$dnm_arr[$i]["DocInfo"]["DocType"] = "DN";
			$dnm_arr[$i]["DocInfo"]["DocNo"] = $row_dnm["ref_no"];
			$dnm_arr[$i]["DocInfo"]["DocDate"] = $dn_date;
			////////// seller details
			$dnm_arr[$i]["DocInfo"]["SellerGstin"] = $row_inv["from_gst_no"];
			$dnm_arr[$i]["DocInfo"]["SellerLegalName"] = $row_inv["from_partyname"];
			$dnm_arr[$i]["DocInfo"]["SellerAddr1"] = $from_pty_addrs;
			$dnm_arr[$i]["DocInfo"]["SellerAddr2"] = $from_pty_addrs2;
			$dnm_arr[$i]["DocInfo"]["SellerCity"] = $row_inv["from_city"];
			$dnm_arr[$i]["DocInfo"]["SellerPincode"] = $row_inv["from_pincode"];
			$dnm_arr[$i]["DocInfo"]["SellerStatecode"] = $from_statecode;
			$dnm_arr[$i]["DocInfo"]["SellerPhone"] = $row_inv["from_phone"];
			$dnm_arr[$i]["DocInfo"]["SellerEmail"] = $row_inv["from_email"];
			/////// buyer details
			$dnm_arr[$i]["DocInfo"]["BuyerGstin"] = $row_inv["to_gst_no"];
			$dnm_arr[$i]["DocInfo"]["BuyerLegalName"] = $row_inv["party_name"];
			$dnm_arr[$i]["DocInfo"]["BuyerAddr1"] = $to_pty_addrs;
			$dnm_arr[$i]["DocInfo"]["BuyerAddr2"] = $to_pty_addrs2;
			$dnm_arr[$i]["DocInfo"]["BuyerCity"] = $row_inv["to_city"];
			$dnm_arr[$i]["DocInfo"]["BuyerPincode"] = $row_inv["to_pincode"];
			$dnm_arr[$i]["DocInfo"]["BuyerStatecode"] = $to_statecode;
			$dnm_arr[$i]["DocInfo"]["BuyerPhone"] = $row_inv["to_phone"];
			$dnm_arr[$i]["DocInfo"]["BuyerEmail"] = $row_inv["to_email"];
			/////// Dispatch details
			$dnm_arr[$i]["DocInfo"]["DispatchFrom"] = $row_inv["from_partyname"];
			$dnm_arr[$i]["DocInfo"]["DispAddr1"] = $disp_pty_addrs;
			$dnm_arr[$i]["DocInfo"]["DispAddr2"] = $disp_pty_addrs2;
			$dnm_arr[$i]["DocInfo"]["DispCity"] = $row_inv["from_city"];
			$dnm_arr[$i]["DocInfo"]["DispPincode"] = $row_inv["from_pincode"];
			$dnm_arr[$i]["DocInfo"]["DispStatecode"] = $from_statecode;
			////// Shipping details
			$dnm_arr[$i]["DocInfo"]["ShipGstin"] = $row_inv["to_gst_no"];
			$dnm_arr[$i]["DocInfo"]["ShipTo"] = $row_inv["party_name"];
			$dnm_arr[$i]["DocInfo"]["ShipAddr1"] = $to_pty_addrs;
			$dnm_arr[$i]["DocInfo"]["ShipAddr2"] = $to_pty_addrs2;
			$dnm_arr[$i]["DocInfo"]["ShipCity"] = $row_inv["to_city"];
			$dnm_arr[$i]["DocInfo"]["ShipPincode"] = $row_inv["to_pincode"];
			$dnm_arr[$i]["DocInfo"]["ShipStatecode"] = $to_statecode;
			/////// value details
			$dnm_arr[$i]["DocInfo"]["AssessedVal"] = $row_dnm['basic_amt'];
			$dnm_arr[$i]["DocInfo"]["CgstVal"] = $row_dnm['cgst_amt'];
			$dnm_arr[$i]["DocInfo"]["SgstVal"] = $row_dnm['sgst_amt'];
			$dnm_arr[$i]["DocInfo"]["IgstVal"] = $row_dnm['igst_amt'];
			$dnm_arr[$i]["DocInfo"]["DiscVal"] = $row_dnm["discount"];
			$dnm_arr[$i]["DocInfo"]["RoundOffVal"] = $row_dnm['round_off'];
			$dnm_arr[$i]["DocInfo"]["TotalVal"] = $row_dnm['amount'];
			////// ledger details
			$vchtype = "";
			$dnm_arr[$i]["DocInfo"]["AccountLedger"] = "";
			$dnm_arr[$i]["DocInfo"]["IgstName"] = "";
			$dnm_arr[$i]["DocInfo"]["IgstValue"] = "";
			$dnm_arr[$i]["DocInfo"]["CgstName"] = "";
			$dnm_arr[$i]["DocInfo"]["CgstValue"] = "";
			$dnm_arr[$i]["DocInfo"]["SgstName"] = "";
			$dnm_arr[$i]["DocInfo"]["SgstValue"] = "";
			$dnm_arr[$i]["DocInfo"]["TcsName"] = "";
			$dnm_arr[$i]["DocInfo"]["TcsValue"] = "";
			$dnm_arr[$i]["DocInfo"]["RoundOffName"] = "";
			$dnm_arr[$i]["DocInfo"]["RoundOffvalue"] = "";
			$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_dnm["ref_no"]."' AND location_code='".$row_dnm["location_id"]."'");
			while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){				
				if($row_ac_lg["ledger_type"]=="ACCOUNT"){
					$dnm_arr[$i]["DocInfo"]["AccountLedger"] = $row_ac_lg["ledger_name"];
				}
				if($row_ac_lg["ledger_type"]=="IGST"){
					$dnm_arr[$i]["DocInfo"]["IgstName"] = $row_ac_lg["ledger_name"];
					$dnm_arr[$i]["DocInfo"]["IgstValue"] = $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="CGST"){
					$dnm_arr[$i]["DocInfo"]["CgstName"] = $row_ac_lg["ledger_name"];
					$dnm_arr[$i]["DocInfo"]["CgstValue"] = $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="SGST"){
					$dnm_arr[$i]["DocInfo"]["SgstName"] = $row_ac_lg["ledger_name"];
					$dnm_arr[$i]["DocInfo"]["SgstValue"] = $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="TCS"){
					$dnm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
					$dnm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="ROUND OFF"){
					$dnm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
					$dnm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
				}
				$vchtype = $row_ac_lg["voucher_name"];
			}
			$dnm_arr[$i]["DocInfo"]["VoucherTypeName"] = $vchtype;
			///// branch name
			$branchname = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code ='".$row_dnm["location_id"]."' AND ledger_voucher='Voucher' AND extension_for='4' AND status='Active'"));
			//$dnm_arr[$i]["DocInfo"]["BranchCode"] = $row_billm["from_location"];
			$dnm_arr[$i]["DocInfo"]["BranchCode"] = $tallySink[1];
            ////////////////get  cost centre name
			$billfrom = $this->getAnyDetails($row_dnm["sub_location"],"name","asc_code","asc_master");
			$explodevalf = explode("~",$billfrom);
			//if($explodevalf[0]){ $costcentre=$billfrom; }else{ $costcentre=$this->getAnyDetails($row_dnm["sub_location"],"cost_center","sub_location","sub_location_master");}
			if($explodevalf[0]){ $costcentre=$billfrom; $godown=$billfrom;}else{ $costc =explode("~",$this->getAnyDetails($row_dnm["sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			
			$dnm_arr[$i]["DocInfo"]["CostCentre"] = $costcentre;
			$dnm_arr[$i]["DocInfo"]["Remark"] = $row_dnm["remark"];	
			///////// get item details
			$j = 1;
			$dnd_arr = array();
			$dn_itemlist = array();
			$res_dnd = mysqli_query($this->link,"SELECT * FROM debit_note_data WHERE ref_no='".$row_dnm["ref_no"]."'");
			while($row_dnd = mysqli_fetch_assoc($res_dnd)){
				$tax_per = $row_dnd['cgst_per']+$row_dnd['sgst_per']+$row_dnd['igst_per'];
				if($row_dnd['prod_code']=='39' || $row_dnd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
				/// get part details
				$part_det = explode("~",$this->getAnyDetails($row_dnd['prod_code'],"productname,hsn_code,productsubcat,brand","productcode","product_master"));
				//////
				$brand_arr[] = $part_det[3];
				$psc_arr[] = $part_det[2];
				$dn_itemlist["Sno"] = $j;
				$dn_itemlist["ProductDesc"] = $part_det[0];
				$dn_itemlist["IsServiceChg"] = $service_flag;
				$dn_itemlist["HsnCode"] = $part_det[1];
				$dn_itemlist["Qty"] = $row_dnd["req_qty"];
				$dn_itemlist["Unit"] = "NOS";
				$dn_itemlist["UnitPrice"] = $row_dnd["price"];
				$dn_itemlist["SubTotal"] = $row_dnd["value"];
				$dn_itemlist["Discount"] = 0.00;
				$dn_itemlist["PreTaxVal"] = $row_dnd["value"];
				$dn_itemlist["AssessedVal"] = $row_dnd["value"];
				$dn_itemlist["GstRate"] = $tax_per;
				$dn_itemlist["IgstAmt"] = $row_dnd["igst_amt"];
				$dn_itemlist["CgstAmt"] = $row_dnd["cgst_amt"];
				$dn_itemlist["SgstAmt"] = $row_dnd["sgst_amt"];
				$dn_itemlist["TotalItemVal"] = $row_dnd["totalvalue"];
				$dn_itemlist["GodownName"] = $godown;
				array_push($dnd_arr,$dn_itemlist);
				$j++;
			}//// close 2nd while loop
			$dnm_arr[$i]["DocInfo"]["ItemsInfo"] = $dnd_arr;
			
			/////// check which brand is more in this bill
			$counted_brand = array_count_values($brand_arr);
			arsort($counted_brand); //sort descending maintain keys
			$most_brand = key($counted_brand); //get the key, as we are rewound it's the first key
			/////// check which product sub category is more in this bill
			$counted_psc = array_count_values($psc_arr);
			arsort($counted_psc); //sort descending maintain keys
			$most_psc = key($counted_psc); //get the key, as we are rewound it's the first key
			$dnm_arr[$i]["DocInfo"]["Brand"] = $this->getAnyDetails($most_brand,"make","id","make_master");
			$dnm_arr[$i]["DocInfo"]["Segment"] = $this->getAnyDetails($most_psc,"prod_sub_cat","psubcatid","product_sub_category");
			
			$i++;
		}
		}///// close first while loop
		return $dnm_arr;
	}
	//// get Receipt voucher data
	public function getReceiptVoucher($receiptno,$from_date,$to_date){
		if($from_date){ $str = " AND payment_date >='".$from_date."' AND payment_date <='".$to_date."'";}
		if($receiptno){ $str .= " AND doc_no='".$receiptno."'";} 
		$recpt_arr = array();
		$i=0;
		/////// get master data of payment
		$res_recpt = mysqli_query($this->link,"SELECT * FROM payment_receive WHERE status IN ('Approve') AND post_in_tally='' ".$str);
		while($row_recpt = mysqli_fetch_assoc($res_recpt)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_recpt["to_location"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			///// get from party details
			$res_frompty = mysqli_query($this->link,"SELECT name,contact_person,landline,email,phone,addrs,disp_addrs,city,state,pincode,pan_no,gstin_no FROM asc_master WHERE asc_code='".$row_recpt["from_location"]."'")or die(mysqli_error($this->link));
			$row_frompty = mysqli_fetch_array($res_frompty);
			///// get to party details
			//$res_topty = mysqli_query($this->link,"SELECT name,contact_person,landline,email,phone,addrs,disp_addrs,city,state,pincode,pan_no,gstin_no FROM asc_master WHERE asc_code='".$row_recpt["to_location"]."'")or die(mysqli_error($this->link));
			//$row_topty = mysqli_fetch_array($res_topty);
			//////
			$from_statecode = substr($row_frompty["gstin_no"],0,2);
			$to_statecode = substr($row_topty["gstin_no"],0,2);
			$recpt_date = str_replace("-","/",$this->date_format($row_recpt["payment_date"]));
			//$dc_date = str_replace("-","/",$this->date_format($row_recpt["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_frompty["addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_frompty["addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_frompty["addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_topty["addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_topty["addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_topty["addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_frompty["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_frompty["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_frompty["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_topty["disp_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_topty["disp_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_topty["disp_addrs"];
				$deliv_pty_addrs2 = "";
			}
			//////////document details
			$recpt_arr[$i]["DocInfo"]["DocType"] = "Recipt";
			$recpt_arr[$i]["DocInfo"]["DocNo"] = $row_recpt["doc_no"];
			$recpt_arr[$i]["DocInfo"]["DocDate"] = $recpt_date;
			////////// seller details
			$recpt_arr[$i]["DocInfo"]["Gstin"] = $row_frompty["gstin_no"];
			$recpt_arr[$i]["DocInfo"]["PartyName"] = $row_frompty["name"];
			$recpt_arr[$i]["DocInfo"]["Addr1"] = $from_pty_addrs;
			$recpt_arr[$i]["DocInfo"]["Addr2"] = $from_pty_addrs2;
			$recpt_arr[$i]["DocInfo"]["City"] = $row_frompty["city"];
			$recpt_arr[$i]["DocInfo"]["Pincode"] = $row_frompty["pincode"];
			$recpt_arr[$i]["DocInfo"]["Statecode"] = $from_statecode;
			$recpt_arr[$i]["DocInfo"]["Phone"] = $row_frompty["phone"];
			$recpt_arr[$i]["DocInfo"]["Email"] = $row_frompty["email"];
			/////// value details
			$recpt_arr[$i]["DocInfo"]["Mode"] = $this->getAnyDetails($row_recpt['payment_mode'],"mode","id","payment_mode");
			$recpt_arr[$i]["DocInfo"]["TransactionID"] = $row_recpt['transaction_id'];
			$recpt_arr[$i]["DocInfo"]["BillNo"] = $row_recpt['against_ref_no'];
			$recpt_arr[$i]["DocInfo"]["TotalVal"] = $row_recpt['amount'];
			$i++;
		}
		}///// close first while loop
		return $recpt_arr;
	}
	//// get Payment voucher data
	public function getPaymentVoucher($payrefno,$from_date,$to_date){
		if($from_date){ $str = " AND payment_date >='".$from_date."' AND payment_date <='".$to_date."'";}
		if($payrefno){ $str .= " AND doc_no='".$payrefno."'";} 
		$payment_arr = array();
		$i = 0;
		/////// get master data of payment
		$res_payment = mysqli_query($this->link,"SELECT * FROM payment_send WHERE status IN ('Approve') AND post_in_tally='' ".$str);
		while($row_payment = mysqli_fetch_assoc($res_payment)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_payment["from_location"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			///// get from party details
			$res_frompty = mysqli_query($this->link,"SELECT name,contact_person,landline,email,phone,addrs,disp_addrs,city,state,pincode,pan_no,gstin_no FROM asc_master WHERE asc_code='".$row_payment["from_location"]."'")or die(mysqli_error($this->link));
			$row_frompty = mysqli_fetch_array($res_frompty);
			///// get to party details
			//$res_topty = mysqli_query($this->link,"SELECT name,contact_person,landline,email,phone,addrs,disp_addrs,city,state,pincode,pan_no,gstin_no FROM asc_master WHERE asc_code='".$row_payment["to_location"]."'")or die(mysqli_error($this->link));
			//$row_topty = mysqli_fetch_array($res_topty);
			//////
			$from_statecode = substr($row_frompty["gstin_no"],0,2);
			$to_statecode = substr($row_topty["gstin_no"],0,2);
			$recpt_date = str_replace("-","/",$this->date_format($row_payment["payment_date"]));
			//$dc_date = str_replace("-","/",$this->date_format($row_payment["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_frompty["addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_frompty["addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_frompty["addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_topty["addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_topty["addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_topty["addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_frompty["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_frompty["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_frompty["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_topty["disp_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_topty["disp_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_topty["disp_addrs"];
				$deliv_pty_addrs2 = "";
			}
			//////////document details
			$payment_arr[$i]["DocInfo"]["DocType"] = "Payment";
			$payment_arr[$i]["DocInfo"]["DocNo"] = $row_payment["doc_no"];
			$payment_arr[$i]["DocInfo"]["DocDate"] = $recpt_date;
			////////// seller details
			$payment_arr[$i]["DocInfo"]["Gstin"] = $row_frompty["gstin_no"];
			$payment_arr[$i]["DocInfo"]["VendorName"] = $row_frompty["name"];
			$payment_arr[$i]["DocInfo"]["Addr1"] = $from_pty_addrs;
			$payment_arr[$i]["DocInfo"]["Addr2"] = $from_pty_addrs2;
			$payment_arr[$i]["DocInfo"]["City"] = $row_frompty["city"];
			$payment_arr[$i]["DocInfo"]["Pincode"] = $row_frompty["pincode"];
			$payment_arr[$i]["DocInfo"]["Statecode"] = $from_statecode;
			$payment_arr[$i]["DocInfo"]["Phone"] = $row_frompty["phone"];
			$payment_arr[$i]["DocInfo"]["Email"] = $row_frompty["email"];
			/////// value details
			$payment_arr[$i]["DocInfo"]["Mode"] = $this->getAnyDetails($row_payment['payment_mode'],"mode","id","payment_mode");
			$payment_arr[$i]["DocInfo"]["TransactionID"] = $row_payment['transaction_id'];
			$payment_arr[$i]["DocInfo"]["BillNo"] = $row_payment['against_ref_no'];
			$payment_arr[$i]["DocInfo"]["TotalVal"] = $row_payment['amount'];
			$i++;
		}
		}///// close first while loop
		return $payment_arr;
	}
	//// get Combo voucher data
	public function getComboVoucher($invno,$from_date,$to_date){
		if($from_date){ $str = " AND sale_date >='".$from_date."' AND sale_date <='".$to_date."'";}
		if($invno){ $str .= " AND challan_no='".$invno."'";} 
		$invm_arr = array();
		/////// get master data of billing
		$i=0;
		$res_billm = mysqli_query($this->link,"SELECT * FROM billing_master WHERE type NOT IN ('GRN','LP','DIRECT SALE RETURN','SALE RETURN') AND post_in_tally='' AND status!='Cancelled' AND billing_type='COMBO' ".$str);
		while($row_billm = mysqli_fetch_assoc($res_billm)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_billm["from_location"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			$from_statecode = substr($row_billm["from_gst_no"],0,2);
			$to_statecode = substr($row_billm["to_gst_no"],0,2);
			$inv_date = str_replace("-","/",$this->date_format($row_billm["sale_date"]));
			$dc_date = str_replace("-","/",$this->date_format($row_billm["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_billm["from_addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_billm["from_addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_billm["from_addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_billm["to_addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_billm["to_addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_billm["to_addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_billm["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_billm["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_billm["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_billm["deliv_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_billm["deliv_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_billm["deliv_addrs"];
				$deliv_pty_addrs2 = "";
			}

			//////////document details
			if($row_billm["document_type"]=="Delivery Challan"){
				$invm_arr[$i]["DocInfo"]["DocType"] = "DC";
			}else{
				$invm_arr[$i]["DocInfo"]["DocType"] = "INV";
			}
			$invm_arr[$i]["DocInfo"]["DocNo"] = $row_billm["challan_no"];
			$invm_arr[$i]["DocInfo"]["DocDate"] = $inv_date;
			////////// seller details
			$invm_arr[$i]["DocInfo"]["SellerGstin"] = $row_billm["from_gst_no"];
			$invm_arr[$i]["DocInfo"]["SellerLegalName"] = $row_billm["from_partyname"];
			$invm_arr[$i]["DocInfo"]["SellerAddr1"] = $from_pty_addrs;
			$invm_arr[$i]["DocInfo"]["SellerAddr2"] = $from_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["SellerCity"] = $row_billm["from_city"];
			$invm_arr[$i]["DocInfo"]["SellerState"] = $row_billm["from_state"];
			$invm_arr[$i]["DocInfo"]["SellerPincode"] = $row_billm["from_pincode"];
			$invm_arr[$i]["DocInfo"]["SellerStatecode"] = $from_statecode;
			$invm_arr[$i]["DocInfo"]["SellerPhone"] = $row_billm["from_phone"];
			$invm_arr[$i]["DocInfo"]["SellerEmail"] = $row_billm["from_email"];
			/////// buyer details
			$invm_arr[$i]["DocInfo"]["BuyerGstin"] = $row_billm["to_gst_no"];
			$invm_arr[$i]["DocInfo"]["BuyerLegalName"] = $row_billm["party_name"];
			$invm_arr[$i]["DocInfo"]["BuyerAddr1"] = $to_pty_addrs;
			$invm_arr[$i]["DocInfo"]["BuyerAddr2"] = $to_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["BuyerCity"] = $row_billm["to_city"];
			$invm_arr[$i]["DocInfo"]["BuyerState"] = $row_billm["to_state"];
			$invm_arr[$i]["DocInfo"]["BuyerPincode"] = $row_billm["to_pincode"];
			$invm_arr[$i]["DocInfo"]["BuyerStatecode"] = $to_statecode;
			$invm_arr[$i]["DocInfo"]["BuyerPhone"] = $row_billm["to_phone"];
			$invm_arr[$i]["DocInfo"]["BuyerEmail"] = $row_billm["to_email"];
			/////// Dispatch details
			$invm_arr[$i]["DocInfo"]["DispatchFrom"] = $row_billm["from_partyname"];
			$invm_arr[$i]["DocInfo"]["DispAddr1"] = $disp_pty_addrs;
			$invm_arr[$i]["DocInfo"]["DispAddr2"] = $disp_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["DispCity"] = $row_billm["from_city"];
			$invm_arr[$i]["DocInfo"]["DispPincode"] = $row_billm["from_pincode"];
			$invm_arr[$i]["DocInfo"]["DispStatecode"] = $from_statecode;
			////// Shipping details
			$invm_arr[$i]["DocInfo"]["ShipGstin"] = $row_billm["to_gst_no"];
			$invm_arr[$i]["DocInfo"]["ShipTo"] = $row_billm["party_name"];
			$invm_arr[$i]["DocInfo"]["ShipAddr1"] = $to_pty_addrs;
			$invm_arr[$i]["DocInfo"]["ShipAddr2"] = $to_pty_addrs2;
			$invm_arr[$i]["DocInfo"]["ShipCity"] = $row_billm["to_city"];
			$invm_arr[$i]["DocInfo"]["ShipPincode"] = $row_billm["to_pincode"];
			$invm_arr[$i]["DocInfo"]["ShipStatecode"] = $to_statecode;
			$invm_arr[$i]["DocInfo"]["ShipStateName"] = $row_billm["to_state"];
			/////// value details
			$assval =	$row_billm['basic_cost']+$row_billm['discount_amt'];
			$invm_arr[$i]["DocInfo"]["AssessedVal"] = "$assval";
			//$invm_arr[$i]["DocInfo"]["CgstVal"] = $row_billm['total_cgst_amt'];
			//$invm_arr[$i]["DocInfo"]["SgstVal"] = $row_billm['total_sgst_amt'];
			//$invm_arr[$i]["DocInfo"]["IgstVal"] = $row_billm['total_igst_amt'];
			$invm_arr[$i]["DocInfo"]["DiscVal"] = $row_billm['discount_amt'];
			//$invm_arr[$i]["DocInfo"]["RoundOffVal"] = $row_billm['round_off'];
			$invm_arr[$i]["DocInfo"]["TotalVal"] = $row_billm['total_cost'];
			////// ledger details
			$vchtype = "";
			$invm_arr[$i]["DocInfo"]["AccountLedger"] = "";
			$invm_arr[$i]["DocInfo"]["IgstName"] = "";
			$invm_arr[$i]["DocInfo"]["IgstValue"] = "";
			$invm_arr[$i]["DocInfo"]["CgstName"] = "";
			$invm_arr[$i]["DocInfo"]["CgstValue"] = "";
			$invm_arr[$i]["DocInfo"]["SgstName"] = "";
			$invm_arr[$i]["DocInfo"]["SgstValue"] = "";
			$invm_arr[$i]["DocInfo"]["TcsName"] = "";
			$invm_arr[$i]["DocInfo"]["TcsValue"] = "";
			$invm_arr[$i]["DocInfo"]["RoundOffName"] = "";
			$invm_arr[$i]["DocInfo"]["RoundOffvalue"] = "";
			$gst_val = 0;	
			$tcs_val =0;
			$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_billm["challan_no"]."' AND location_code='".$row_billm["from_location"]."'");
			while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){				
				if($row_ac_lg["ledger_type"]=="ACCOUNT"  || $row_ac_lg["ledger_type"]==""){
					$invm_arr[$i]["DocInfo"]["AccountLedger"] = $row_ac_lg["ledger_name"];
				}
				if($row_ac_lg["ledger_type"]=="IGST"){
					$invm_arr[$i]["DocInfo"]["IgstName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["IgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="CGST"){
					$invm_arr[$i]["DocInfo"]["CgstName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["CgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="SGST"){
					$invm_arr[$i]["DocInfo"]["SgstName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["SgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="TCS"){
					$invm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
					$tcs_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="ROUND OFF"){
					$invm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
					$invm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
				}
				$vchtype = $row_ac_lg["voucher_name"];
			}
			$invm_arr[$i]["DocInfo"]["VoucherTypeName"] = $vchtype;
			///// branch name
			$branchname = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code ='".$row_billm["from_location"]."' AND ledger_voucher='Voucher' AND extension_for='1' AND status='Active'"));
			//$invm_arr[$i]["DocInfo"]["BranchCode"] = $row_billm["from_location"];
			//$invm_arr[$i]["DocInfo"]["BranchCode"] = $branchname["extension_name"];
			$invm_arr[$i]["DocInfo"]["BranchCode"] = $tallySink[1];
            ////////////////get  cost centre name
			$billfrom = $this->getAnyDetails($row_billm["sub_location"],"name","asc_code","asc_master");
			$explodevalf = explode("~",$billfrom);
			if($explodevalf[0]){ $costcentre=$billfrom; $godown=$billfrom;}else{ $costc =explode("~",$this->getAnyDetails($row_billm["sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			
			$invm_arr[$i]["DocInfo"]["CostCentre"] = $costcentre;
			$invm_arr[$i]["DocInfo"]["PaymentTerm"] = $row_billm["payment_term"];
			$invm_arr[$i]["DocInfo"]["Remark"] = $row_billm["billing_rmk"];
			$invm_arr[$i]["DocInfo"]["ItemScheme"] = "";
			///////// get item details
			$j = 1;
			$invd_arr = array();
			$bill_itemlist = array();
			$comboitems_arr = array();
			$bill_comboitems = array();
			$brand_arr = array();
			$psc_arr = array();
			$item_cost = 0;
			$res_billd = mysqli_query($this->link,"SELECT * FROM billing_model_data WHERE challan_no='".$row_billm["challan_no"]."'");
			while($row_billd = mysqli_fetch_assoc($res_billd)){
				$tax_per = $row_billd['cgst_per']+$row_billd['sgst_per']+$row_billd['igst_per'];
				if($row_billd['prod_code']=='39' || $row_billd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
				/// get part details
				$part_det = explode("~",$this->getAnyDetails($row_billd['prod_code'],"productname,hsn_code,productsubcat,brand","productcode","product_master"));
				//////
				$brand_arr[] = $part_det[3];
				$psc_arr[] = $part_det[2];
				///// check combo model
				if($row_billd["prod_cat"]=="C"){
					$combo_det = explode("~",$this->getAnyDetails($row_billd['combo_code'],"bom_modelname,bom_hsn","bom_modelcode","combo_master"));
					$bill_itemlist["Sno"] = $j;
					$bill_itemlist["ProductDesc"] = $combo_det[0];
					$bill_itemlist["IsServiceChg"] = $service_flag;
					$bill_itemlist["HsnCode"] = $combo_det[1];
					$bill_itemlist["Qty"] = $row_billd["qty"];
					$bill_itemlist["Unit"] = "NOS";
					$bill_itemlist["UnitPrice"] = $row_billd["price"];
					$bill_itemlist["SubTotal"] = $row_billd["value"];
					$bill_itemlist["Discount"] = ($row_billd["discount"]);
					$bill_itemlist["PreTaxVal"] = $row_billd["value"];
					$bill_itemlist["AssessedVal"] = $row_billd["value"];
					$bill_itemlist["GstRate"] = $tax_per;
					$bill_itemlist["IgstAmt"] = $row_billd["igst_amt"];
					$bill_itemlist["CgstAmt"] = $row_billd["cgst_amt"];
					$bill_itemlist["SgstAmt"] = $row_billd["sgst_amt"];
					$bill_itemlist["TotalItemVal"] = $row_billd["totalvalue"];
					$bill_itemlist["GodownName"] = $godown;
					array_push($invd_arr,$bill_itemlist);
					$item_cost += $row_billd["value"]-$row_billd["discount"];
					$j++;
				}else{
					$bill_comboitems["ProductDesc"] = $part_det[0];
					$bill_comboitems["Qty"] = $row_billd["qty"];
					$bill_comboitems["Rate"] = $row_billd["price"];
					$bill_comboitems["Amt"] = $row_billd["value"];
					$bill_comboitems["GodownName"] = $godown;
					array_push($comboitems_arr,$bill_comboitems);
				}
				
			}//// close 2nd while loop
			$grand_total = number_format($item_cost,'2','.','')+number_format($gst_val,'2','.','')+number_format($tcs_val,'2','.','');
			$invm_arr[$i]["DocInfo"]["InvAmt"] = number_format($grand_total,'2','.','');
			if(strpos($grand_total, ".") !== false){
				$expd_gt = explode(".",$grand_total);
				$checkval = ".".$expd_gt[1];
				if($checkval>=.50){
					$ro = 1-$checkval;
					$roundoff = "".number_format($ro,'2','.','');
				}else{
					$roundoff = "-".number_format($checkval,'2','.','');
				}
			}else{
				$roundoff = 0.00;
			}	
			$invm_arr[$i]["DocInfo"]["RoundOffvalue"] = $roundoff;
			
			$invm_arr[$i]["DocInfo"]["ItemsInfo"] = $invd_arr;
			$invm_arr[$i]["DocInfo"]["ComboItemsInfo"] = $comboitems_arr;
			
			/////// check which brand is more in this bill
			$counted_brand = array_count_values($brand_arr);
			arsort($counted_brand); //sort descending maintain keys
			$most_brand = key($counted_brand); //get the key, as we are rewound it's the first key
			/////// check which product sub category is more in this bill
			$counted_psc = array_count_values($psc_arr);
			arsort($counted_psc); //sort descending maintain keys
			$most_psc = key($counted_psc); //get the key, as we are rewound it's the first key
			$invm_arr[$i]["DocInfo"]["Brand"] = $this->getAnyDetails($most_brand,"make","id","make_master");
			$invm_arr[$i]["DocInfo"]["Segment"] = $this->getAnyDetails($most_psc,"prod_sub_cat","psubcatid","product_sub_category");
			
			
			$i++;
		}
		}///// close first while loop
		return $invm_arr;
	}
		//// get combo purchase voucher data
	public function getComboPurchaseVoucher($grnno,$from_date,$to_date){
		//if($from_date){ $str = " AND sale_date >='".$from_date."' AND sale_date <='".$to_date."'";}
		if($from_date){ $str = " AND ((sale_date >='".$from_date."' AND sale_date <='".$to_date."') or (receive_date >='".$from_date."' AND receive_date <='".$to_date."'))";}
		if($grnno){ $str .= " AND challan_no='".$grnno."'";} 
		$purm_arr = array();
		/////// get master data of billing
		$i=0;
		$res_purm = mysqli_query($this->link,"SELECT * FROM billing_master WHERE type IN ('CLP','RETAIL') AND billing_type='COMBO' AND post_in_tally2='' AND status='Received' ".$str);
		while($row_purm = mysqli_fetch_assoc($res_purm)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_purm["to_location"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			$from_statecode = substr($row_purm["from_gst_no"],0,2);
			$to_statecode = substr($row_purm["to_gst_no"],0,2);
			//$inv_date = str_replace("-","/",$this->date_format($row_purm["sale_date"]));
			### If Receive date is available then inv date will receive else sale date
			if($row_purm["receive_date"]!='0000-00-00'){
				$inv_date = str_replace("-","/",$this->date_format($row_purm["receive_date"]));
			}
			else{
				$inv_date = str_replace("-","/",$this->date_format($row_purm["sale_date"]));
			}
			$dc_date = str_replace("-","/",$this->date_format($row_purm["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_purm["from_addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_purm["from_addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_purm["from_addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_purm["to_addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_purm["to_addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_purm["to_addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_purm["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_purm["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_purm["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_purm["deliv_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_purm["deliv_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_purm["deliv_addrs"];
				$deliv_pty_addrs2 = "";
			}
			if($row_purm["document_type"]=="Delivery Challan"){
				$purm_arr[$i]["DocInfo"]["DocType"] = "DC";
			}else{
				$purm_arr[$i]["DocInfo"]["DocType"] = "INV";
			}
			//////////document details
			//$purm_arr[$i]["DocInfo"]["DocType"] = "INV";
			$purm_arr[$i]["DocInfo"]["DocNo"] = $row_purm["challan_no"];
			$purm_arr[$i]["DocInfo"]["DocDate"] = $inv_date;
			$purm_arr[$i]["DocInfo"]["DocRecDate"] = str_replace("-","/",$this->date_format($row_purm["receive_date"]));
			$purm_arr[$i]["DocInfo"]["RefInvNo"] = $row_purm["inv_ref_no"];
			$purm_arr[$i]["DocInfo"]["RefInvDate"] = str_replace("-","/",$this->date_format($row_purm["po_inv_date"]));
			////////// seller details
			$purm_arr[$i]["DocInfo"]["SellerGstin"] = $row_purm["from_gst_no"];
			$purm_arr[$i]["DocInfo"]["SellerLegalName"] = $row_purm["from_partyname"];
			$purm_arr[$i]["DocInfo"]["SellerAddr1"] = $from_pty_addrs;
			$purm_arr[$i]["DocInfo"]["SellerAddr2"] = $from_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["SellerCity"] = $row_purm["from_city"];
			$purm_arr[$i]["DocInfo"]["SellerState"] = $row_purm["from_state"];
			$purm_arr[$i]["DocInfo"]["SellerPincode"] = $row_purm["from_pincode"];
			$purm_arr[$i]["DocInfo"]["SellerStatecode"] = $from_statecode;
			$purm_arr[$i]["DocInfo"]["SellerPhone"] = $row_purm["from_phone"];
			$purm_arr[$i]["DocInfo"]["SellerEmail"] = $row_purm["from_email"];
			/////// buyer details
			$purm_arr[$i]["DocInfo"]["BuyerGstin"] = $row_purm["to_gst_no"];
			$purm_arr[$i]["DocInfo"]["BuyerLegalName"] = $row_purm["party_name"];
			$purm_arr[$i]["DocInfo"]["BuyerAddr1"] = $to_pty_addrs;
			$purm_arr[$i]["DocInfo"]["BuyerAddr2"] = $to_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["BuyerCity"] = $row_purm["to_city"];
			$purm_arr[$i]["DocInfo"]["BuyerState"] = $row_purm["to_state"];
			$purm_arr[$i]["DocInfo"]["BuyerPincode"] = $row_purm["to_pincode"];
			$purm_arr[$i]["DocInfo"]["BuyerStatecode"] = $to_statecode;
			$purm_arr[$i]["DocInfo"]["BuyerPhone"] = $row_purm["to_phone"];
			$purm_arr[$i]["DocInfo"]["BuyerEmail"] = $row_purm["to_email"];
			/////// Dispatch details
			$purm_arr[$i]["DocInfo"]["DispatchFrom"] = $row_purm["from_partyname"];
			$purm_arr[$i]["DocInfo"]["DispAddr1"] = $disp_pty_addrs;
			$purm_arr[$i]["DocInfo"]["DispAddr2"] = $disp_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["DispCity"] = $row_purm["from_city"];
			$purm_arr[$i]["DocInfo"]["DispPincode"] = $row_purm["from_pincode"];
			$purm_arr[$i]["DocInfo"]["DispStatecode"] = $from_statecode;
			////// Shipping details
			$purm_arr[$i]["DocInfo"]["ShipGstin"] = $row_purm["to_gst_no"];
			$purm_arr[$i]["DocInfo"]["ShipTo"] = $row_purm["party_name"];
			$purm_arr[$i]["DocInfo"]["ShipAddr1"] = $to_pty_addrs;
			$purm_arr[$i]["DocInfo"]["ShipAddr2"] = $to_pty_addrs2;
			$purm_arr[$i]["DocInfo"]["ShipCity"] = $row_purm["to_city"];
			$purm_arr[$i]["DocInfo"]["ShipPincode"] = $row_purm["to_pincode"];
			$purm_arr[$i]["DocInfo"]["ShipStatecode"] = $to_statecode;
			$invm_arr[$i]["DocInfo"]["ShipStateName"] = $row_purm["to_state"];
			/////// value details
			$purm_arr[$i]["DocInfo"]["AssessedVal"] = $row_purm['basic_cost'];
			$purm_arr[$i]["DocInfo"]["CgstVal"] = $row_purm['cgst_amt'];
			$purm_arr[$i]["DocInfo"]["SgstVal"] = $row_purm['sgst_amt'];
			$purm_arr[$i]["DocInfo"]["IgstVal"] = $row_purm['igst_amt'];
			$purm_arr[$i]["DocInfo"]["DiscVal"] = $row_purm['discount_amt'];
			//$purm_arr[$i]["DocInfo"]["RoundOffVal"] = $row_purm['round_off'];
			$purm_arr[$i]["DocInfo"]["TotalVal"] = $row_purm['total_cost'];
			////// ledger details
			$vchtype = "";
			$purm_arr[$i]["DocInfo"]["TcsName"] = "";
			$purm_arr[$i]["DocInfo"]["TcsValue"] = "";
			$purm_arr[$i]["DocInfo"]["RoundOffName"] = "";
			$purm_arr[$i]["DocInfo"]["RoundOffvalue"] = "";
			$gst_val = 0;	
			$tcs_val =0;
			/////check ledger entry 
			$check_ldg = mysqli_num_rows(mysqli_query($this->link,"SELECT id FROM location_ledger WHERE transaction_no ='".$row_purm["challan_no"]."' AND location_code='".$row_purm["to_location"]."' AND ledger_type='ACCOUNT'"));
			if($check_ldg>1){
				$purm_arr[$i]["DocInfo"]["AccountLedger"] = "";
				$purm_arr[$i]["DocInfo"]["IgstName"] = "";
				$purm_arr[$i]["DocInfo"]["CgstName"] = "";
				$purm_arr[$i]["DocInfo"]["SgstName"] = "";

				$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_purm["challan_no"]."' AND location_code='".$row_purm["to_location"]."'");		
				$aclg = array();
				$iglg = array();
				$cglg = array();
				$sglg = array();
				$ac = array();
				$ig = array();
				$sg = array();
				$cg = array();
				while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){	
					if($row_ac_lg["ledger_type"]=="ACCOUNT" || $row_ac_lg["ledger_type"]==""){
						$ac = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($aclg,$ac);
					}
					if($row_ac_lg["ledger_type"]=="IGST"){
						$ig = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($iglg,$ig);
						$gst_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="CGST"){
						$cg = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($cglg,$cg);
						$gst_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="SGST"){
						$sg = array("name"=>$row_ac_lg["ledger_name"],"value"=>$row_ac_lg["ledger_value"]);
						array_push($sglg,$sg);
						$gst_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="TCS"){
						$purm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
						$tcs_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="ROUND OFF"){
						$purm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
					}
					$vchtype = $row_ac_lg["voucher_name"];
				}
				$purm_arr[$i]["DocInfo"]["AccountLedger"] = $aclg;
				$purm_arr[$i]["DocInfo"]["IgstName"] = $iglg;
				$purm_arr[$i]["DocInfo"]["CgstName"] = $cglg;
				$purm_arr[$i]["DocInfo"]["SgstName"] = $sglg;
			}else{
				$purm_arr[$i]["DocInfo"]["AccountLedger"] = "";
				$purm_arr[$i]["DocInfo"]["IgstName"] = "";
				$purm_arr[$i]["DocInfo"]["IgstValue"] = "";
				$purm_arr[$i]["DocInfo"]["CgstName"] = "";
				$purm_arr[$i]["DocInfo"]["CgstValue"] = "";
				$purm_arr[$i]["DocInfo"]["SgstName"] = "";
				$purm_arr[$i]["DocInfo"]["SgstValue"] = "";
				$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_purm["challan_no"]."' AND location_code='".$row_purm["to_location"]."'");		
				while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){				
					if($row_ac_lg["ledger_type"]=="ACCOUNT" || $row_ac_lg["ledger_type"]==""){
						$purm_arr[$i]["DocInfo"]["AccountLedger"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["AccountValue"] = $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="IGST"){
						$purm_arr[$i]["DocInfo"]["IgstName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["IgstValue"] = $row_ac_lg["ledger_value"];
						$gst_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="CGST"){
						$purm_arr[$i]["DocInfo"]["CgstName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["CgstValue"] = $row_ac_lg["ledger_value"];
						$gst_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="SGST"){
						$purm_arr[$i]["DocInfo"]["SgstName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["SgstValue"] = $row_ac_lg["ledger_value"];
						$gst_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="TCS"){
						$purm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
						$tcs_val += $row_ac_lg["ledger_value"];
					}
					if($row_ac_lg["ledger_type"]=="ROUND OFF"){
						$purm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
						$purm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
					}
					$vchtype = $row_ac_lg["voucher_name"];
				}
			}
			$purm_arr[$i]["DocInfo"]["VoucherTypeName"] = $vchtype;
			///// branch name
			$branchname = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code ='".$row_purm["to_location"]."' AND ledger_voucher='Voucher' AND extension_for='1' AND status='Active'"));
			//$purm_arr[$i]["DocInfo"]["BranchCode"] = $row_purm["to_location"];
			//$purm_arr[$i]["DocInfo"]["BranchCode"] = $branchname["extension_name"];
			$purm_arr[$i]["DocInfo"]["BranchCode"] = $tallySink[1];
			////////////////get  cost centre name
			if($row_purm["type"]=="RETAIL" || $row_purm["type"]=="STN"){
				$receivein = $this->getAnyDetails($row_purm["receive_sub_location"],"name","asc_code","asc_master");
				$explodevalf = explode("~",$receivein);
				//if($explodevalf[0]){ $costcentre=$receivein; }else{ $costcentre=$this->getAnyDetails($row_purm["sub_location"],"cost_center","sub_location","sub_location_master");}
				if($explodevalf[0]){ $costcentre=$receivein; $godown=$receivein;}else{ $costc =explode("~",$this->getAnyDetails($row_purm["receive_sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			}else{
				$receivein = $this->getAnyDetails($row_purm["sub_location"],"name","asc_code","asc_master");
				$explodevalf = explode("~",$receivein);
				//if($explodevalf[0]){ $costcentre=$receivein; }else{ $costcentre=$this->getAnyDetails($row_purm["sub_location"],"cost_center","sub_location","sub_location_master");}
				if($explodevalf[0]){ $costcentre=$receivein; $godown=$receivein;}else{ $costc =explode("~",$this->getAnyDetails($row_purm["sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			}
			
			$purm_arr[$i]["DocInfo"]["CostCentre"] = $costcentre;
			//$purm_arr[$i]["DocInfo"]["freight"] = $row_purm["freight"];
			$purm_arr[$i]["DocInfo"]["tds194q"] = $row_purm["tds"];
			$purm_arr[$i]["DocInfo"]["Remark"] = $row_purm["receive_remark"];
			///////// get item details
			$j = 1;
			$freight_val = 0.00;
			$item_cost = 0.00;
			$comboitems_arr = array();
			$pur_comboitems = array();
			$purd_arr = array();
			$pur_itemlist = array();
			$res_purd = mysqli_query($this->link,"SELECT * FROM billing_model_data WHERE challan_no='".$row_purm["challan_no"]."'");
			while($row_pucd = mysqli_fetch_assoc($res_purd)){
				$tax_per = $row_pucd['cgst_per']+$row_pucd['sgst_per']+$row_pucd['igst_per'];
				if($row_pucd['prod_code']=='39' || $row_pucd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
				/// get part details
				$part_det = explode("~",$this->getAnyDetails($row_pucd['prod_code'],"productname,hsn_code,is_service","productcode","product_master"));
				//////
				if($part_det[2]=="Y"){
					$freight_val += $row_pucd["value"];
				}else{
					///// check combo model
					if($row_pucd["prod_cat"]=="C"){
						$combo_det = explode("~",$this->getAnyDetails($row_pucd['combo_code'],"bom_modelname,bom_hsn","bom_modelcode","combo_master"));
						$pur_itemlist["Sno"] = $j;
						$pur_itemlist["ProductDesc"] = $combo_det[0];
						$pur_itemlist["IsServiceChg"] = $service_flag;
						$pur_itemlist["HsnCode"] = $combo_det[1];
						$pur_itemlist["Qty"] = $row_pucd["qty"];
						$pur_itemlist["Unit"] = "NOS";
						$pur_itemlist["UnitPrice"] = $row_pucd["price"];
						$pur_itemlist["SubTotal"] = $row_pucd["value"];
						$pur_itemlist["Discount"] = $row_pucd["discount"];
						$pur_itemlist["PreTaxVal"] = $row_pucd["value"];
						$pur_itemlist["AssessedVal"] = $row_pucd["value"];
						$pur_itemlist["GstRate"] = $tax_per;
						$pur_itemlist["IgstAmt"] = $row_pucd["igst_amt"];
						$pur_itemlist["CgstAmt"] = $row_pucd["cgst_amt"];
						$pur_itemlist["SgstAmt"] = $row_pucd["sgst_amt"];
						$pur_itemlist["TotalItemVal"] = $row_pucd["totalvalue"];
						$pur_itemlist["GodownName"] = $godown;
						array_push($purd_arr,$pur_itemlist);
						$item_cost += $row_pucd["value"]-$row_pucd["discount"];
						$j++;
					}else{
						$pur_comboitems["ProductDesc"] = $part_det[0];
						$pur_comboitems["Qty"] = $row_pucd["qty"];
						$pur_comboitems["Rate"] = $row_pucd["price"];
						$pur_comboitems["Amt"] = $row_pucd["value"];
						$pur_comboitems["GodownName"] = $godown;
						array_push($comboitems_arr,$pur_comboitems);
					}
				}
			}//// close 2nd while loop
			$grand_total = number_format($item_cost,'2','.','')+number_format($gst_val,'2','.','')+number_format($tcs_val,'2','.','');
			$purm_arr[$i]["DocInfo"]["InvAmt"] = number_format($grand_total,'2','.','');
			if(strpos($grand_total, ".") !== false){
				$expd_gt = explode(".",$grand_total);
				$checkval = ".".$expd_gt[1];
				if($checkval>=.50){
					$ro = 1-$checkval;
					$roundoff = "".number_format($ro,'2','.','');
				}else{
					$roundoff = "-".number_format($checkval,'2','.','');
				}
			}else{
				$roundoff = 0.00;
			}	
			$purm_arr[$i]["DocInfo"]["RoundOffvalue"] = $roundoff;
			$purm_arr[$i]["DocInfo"]["ItemsInfo"] = $purd_arr;
			$purm_arr[$i]["DocInfo"]["ComboItemsInfo"] = $comboitems_arr;
			$purm_arr[$i]["DocInfo"]["freight"] = $freight_val;
			$i++;
		}///// close first while loop
		}
		return $purm_arr;
	}
	///// get combo CN voucher data on 06 feb 2023 by shekhar
	public function getComboCNVoucher($crno,$from_date,$to_date){
		//if($from_date){ $str = " AND create_date >='".$from_date."' AND create_date <='".$to_date."'";}
		//// change date filter on 31 jan 23 on behalf of jagat
		if($from_date){ $str = " AND app_date >='".$from_date."' AND app_date <='".$to_date."'";}
		if($crno){ $str .= " AND ref_no='".$crno."'";} 
		$cnm_arr = array();
		/////// get master data of credit
		$i=0;
		$res_cnm = mysqli_query($this->link,"SELECT * FROM credit_note WHERE status IN ('Approved') AND billing_type='COMBO' AND post_in_tally='' ".$str);
		while($row_cnm = mysqli_fetch_assoc($res_cnm)){
			////// check tally sink enable or not
			$tallySink = explode("~",$this->getAnyDetails($row_cnm["location_id"],"tally_sink,tally_branch_code","asc_code","asc_master"));
			if($tallySink[0]=="Y"){
			///// get billing details
			if($row_cnm["description"]=="DIRECT SALE RETURN"){
				$res_inv = mysqli_query($this->link,"SELECT 
				from_gst_no AS to_gst_no,
				to_gst_no AS from_gst_no,
				from_addrs AS to_addrs,
				to_addrs AS from_addrs,
				disp_addrs AS deliv_addrs,
				deliv_addrs AS disp_addrs,
				from_partyname AS party_name,
				party_name AS from_partyname,
				from_city AS to_city,
				from_pincode AS to_pincode,
				from_phone AS to_phone,
				from_email AS to_email,
				to_city AS from_city,
				to_pincode AS from_pincode,
				to_phone AS from_phone,
				to_email AS from_email FROM billing_master WHERE challan_no='".$row_cnm["challan_no"]."'")or die(mysqli_error($this->link));
			}else if($row_cnm["description"]=="SALE RETURN"){
				$res_inv = mysqli_query($this->link,"SELECT from_gst_no,to_gst_no,from_addrs,to_addrs,disp_addrs,deliv_addrs,from_partyname,party_name,from_city,from_pincode,from_phone,from_email,to_city,to_pincode,to_phone,to_email FROM billing_master WHERE challan_no='".$row_cnm["entered_ref_no"]."'")or die(mysqli_error($this->link));
			}else{
				$res_inv = mysqli_query($this->link,"SELECT from_gst_no,to_gst_no,from_addrs,to_addrs,disp_addrs,deliv_addrs,from_partyname,party_name,from_city,from_pincode,from_phone,from_email,to_city,to_pincode,to_phone,to_email FROM billing_master WHERE challan_no='".$row_cnm["challan_no"]."'")or die(mysqli_error($this->link));
			}
			$row_inv = mysqli_fetch_array($res_inv);
			//////
			$from_statecode = substr($row_inv["from_gst_no"],0,2);
			$to_statecode = substr($row_inv["to_gst_no"],0,2);
			//$cn_date = str_replace("-","/",$this->date_format($row_cnm["create_date"]));
			$cn_date = str_replace("-","/",$this->date_format($row_cnm["app_date"]));
			//$dc_date = str_replace("-","/",$this->date_format($row_cnm["dc_date"]));
			////// check from address character count not more than 100
			$from_addrs_cnt = strlen($row_inv["from_addrs"]);
			if($from_addrs_cnt > 99){
				$from_addrs_splt = str_split($row_inv["from_addrs"],90);
				$from_pty_addrs = $from_addrs_splt[0];
				$from_pty_addrs2 = $from_addrs_splt[1];
			}else{
				$from_pty_addrs = $row_inv["from_addrs"];
				$from_pty_addrs2 = "";
			}
			////// check to address character count not more than 100
			$to_addrs_cnt = strlen($row_inv["to_addrs"]);
			if($to_addrs_cnt > 99){
				$to_addrs_splt = str_split($row_inv["to_addrs"],90);
				$to_pty_addrs = $to_addrs_splt[0];
				$to_pty_addrs2 = $to_addrs_splt[1];
			}else{
				$to_pty_addrs = $row_inv["to_addrs"];
				$to_pty_addrs2 = "";
			}
			////// check disp address character count not more than 100
			$disp_addrs_cnt = strlen($row_inv["disp_addrs"]);
			if($disp_addrs_cnt > 99){
				$disp_addrs_splt = str_split($row_inv["disp_addrs"],90);
				$disp_pty_addrs = $disp_addrs_splt[0];
				$disp_pty_addrs2 = $disp_addrs_splt[1];
			}else{
				$disp_pty_addrs = $row_inv["disp_addrs"];
				$disp_pty_addrs2 = "";
			}
			////// check delivery address character count not more than 100
			$deliv_addrs_cnt = strlen($row_inv["deliv_addrs"]);
			if($deliv_addrs_cnt > 99){
				$deliv_addrs_splt = str_split($row_inv["deliv_addrs"],90);
				$deliv_pty_addrs = $deliv_addrs_splt[0];
				$deliv_pty_addrs2 = $deliv_addrs_splt[1];
			}else{
				$deliv_pty_addrs = $row_inv["deliv_addrs"];
				$deliv_pty_addrs2 = "";
			}
			//////////document details
			$cnm_arr[$i]["DocInfo"]["DocType"] = "CN";
			$cnm_arr[$i]["DocInfo"]["DocNo"] = $row_cnm["ref_no"];
			$cnm_arr[$i]["DocInfo"]["DocDate"] = $cn_date;
			////////// seller details
			$cnm_arr[$i]["DocInfo"]["SellerGstin"] = $row_inv["from_gst_no"];
			$cnm_arr[$i]["DocInfo"]["SellerLegalName"] = $row_inv["from_partyname"];
			$cnm_arr[$i]["DocInfo"]["SellerAddr1"] = $from_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["SellerAddr2"] = $from_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["SellerCity"] = $row_inv["from_city"];
			$cnm_arr[$i]["DocInfo"]["SellerPincode"] = $row_inv["from_pincode"];
			$cnm_arr[$i]["DocInfo"]["SellerStatecode"] = $from_statecode;
			$cnm_arr[$i]["DocInfo"]["SellerPhone"] = $row_inv["from_phone"];
			$cnm_arr[$i]["DocInfo"]["SellerEmail"] = $row_inv["from_email"];
			/////// buyer details
			$cnm_arr[$i]["DocInfo"]["BuyerGstin"] = $row_inv["to_gst_no"];
			$cnm_arr[$i]["DocInfo"]["BuyerLegalName"] = $row_inv["party_name"];
			$cnm_arr[$i]["DocInfo"]["BuyerAddr1"] = $to_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["BuyerAddr2"] = $to_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["BuyerCity"] = $row_inv["to_city"];
			$cnm_arr[$i]["DocInfo"]["BuyerPincode"] = $row_inv["to_pincode"];
			$cnm_arr[$i]["DocInfo"]["BuyerStatecode"] = $to_statecode;
			$cnm_arr[$i]["DocInfo"]["BuyerPhone"] = $row_inv["to_phone"];
			$cnm_arr[$i]["DocInfo"]["BuyerEmail"] = $row_inv["to_email"];
			/////// Dispatch details
			$cnm_arr[$i]["DocInfo"]["DispatchFrom"] = $row_inv["from_partyname"];
			$cnm_arr[$i]["DocInfo"]["DispAddr1"] = $disp_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["DispAddr2"] = $disp_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["DispCity"] = $row_inv["from_city"];
			$cnm_arr[$i]["DocInfo"]["DispPincode"] = $row_inv["from_pincode"];
			$cnm_arr[$i]["DocInfo"]["DispStatecode"] = $from_statecode;
			////// Shipping details
			$cnm_arr[$i]["DocInfo"]["ShipGstin"] = $row_inv["to_gst_no"];
			$cnm_arr[$i]["DocInfo"]["ShipTo"] = $row_inv["party_name"];
			$cnm_arr[$i]["DocInfo"]["ShipAddr1"] = $to_pty_addrs;
			$cnm_arr[$i]["DocInfo"]["ShipAddr2"] = $to_pty_addrs2;
			$cnm_arr[$i]["DocInfo"]["ShipCity"] = $row_inv["to_city"];
			$cnm_arr[$i]["DocInfo"]["ShipPincode"] = $row_inv["to_pincode"];
			$cnm_arr[$i]["DocInfo"]["ShipStatecode"] = $to_statecode;
			/////// value details
			$cnm_arr[$i]["DocInfo"]["AssessedVal"] = $row_cnm['basic_amt'];
			//$cnm_arr[$i]["DocInfo"]["CgstVal"] = $row_cnm['cgst_amt'];
			//$cnm_arr[$i]["DocInfo"]["SgstVal"] = $row_cnm['sgst_amt'];
			//$cnm_arr[$i]["DocInfo"]["IgstVal"] = $row_cnm['igst_amt'];
			$cnm_arr[$i]["DocInfo"]["DiscVal"] = $row_cnm["discount"];
			//$cnm_arr[$i]["DocInfo"]["RoundOffVal"] = $row_cnm['round_off'];
			$cnm_arr[$i]["DocInfo"]["TotalVal"] = $row_cnm['amount'];
			////// ledger details
			$vchtype = "";
			$cnm_arr[$i]["DocInfo"]["AccountLedger"] = "";
			$cnm_arr[$i]["DocInfo"]["IgstName"] = "";
			$cnm_arr[$i]["DocInfo"]["IgstValue"] = "";
			$cnm_arr[$i]["DocInfo"]["CgstName"] = "";
			$cnm_arr[$i]["DocInfo"]["CgstValue"] = "";
			$cnm_arr[$i]["DocInfo"]["SgstName"] = "";
			$cnm_arr[$i]["DocInfo"]["SgstValue"] = "";
			$cnm_arr[$i]["DocInfo"]["TcsName"] = "";
			$cnm_arr[$i]["DocInfo"]["TcsValue"] = "";
			$cnm_arr[$i]["DocInfo"]["RoundOffName"] = "";
			$cnm_arr[$i]["DocInfo"]["RoundOffvalue"] = "";
			$gst_val = 0;	
			$tcs_val =0;
			$res_ac_lg = mysqli_query($this->link,"SELECT * FROM location_ledger WHERE transaction_no ='".$row_cnm["ref_no"]."' AND location_code='".$row_cnm["location_id"]."'");
			while($row_ac_lg = mysqli_fetch_assoc($res_ac_lg)){				
				if($row_ac_lg["ledger_type"]=="ACCOUNT"){
					$cnm_arr[$i]["DocInfo"]["AccountLedger"] = $row_ac_lg["ledger_name"];
				}
				if($row_ac_lg["ledger_type"]=="IGST"){
					$cnm_arr[$i]["DocInfo"]["IgstName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["IgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="CGST"){
					$cnm_arr[$i]["DocInfo"]["CgstName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["CgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="SGST"){
					$cnm_arr[$i]["DocInfo"]["SgstName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["SgstValue"] = $row_ac_lg["ledger_value"];
					$gst_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="TCS"){
					$cnm_arr[$i]["DocInfo"]["TcsName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["TcsValue"] = $row_ac_lg["ledger_value"];
					$tcs_val += $row_ac_lg["ledger_value"];
				}
				if($row_ac_lg["ledger_type"]=="ROUND OFF"){
					$cnm_arr[$i]["DocInfo"]["RoundOffName"] = $row_ac_lg["ledger_name"];
					$cnm_arr[$i]["DocInfo"]["RoundOffvalue"] = $row_ac_lg["ledger_value"];
				}
				$vchtype = $row_ac_lg["voucher_name"];
			}
			$cnm_arr[$i]["DocInfo"]["VoucherTypeName"] = $vchtype;
			///// branch name
			$branchname = mysqli_fetch_assoc(mysqli_query($this->link,"SELECT extension_name FROM ledger_voucher_extension WHERE location_code ='".$row_cnm["location_id"]."' AND ledger_voucher='Voucher' AND extension_for='3' AND status='Active'"));
			//$cnm_arr[$i]["DocInfo"]["BranchCode"] = $row_billm["from_location"];
			$cnm_arr[$i]["DocInfo"]["BranchCode"] = $tallySink[1];
            ////////////////get  cost centre name
			$billfrom = $this->getAnyDetails($row_cnm["sub_location"],"name","asc_code","asc_master");
			$explodevalf = explode("~",$billfrom);
			//if($explodevalf[0]){ $costcentre=$billfrom; }else{ $costcentre=$this->getAnyDetails($row_cnm["sub_location"],"cost_center","sub_location","sub_location_master");}
			if($explodevalf[0]){ $costcentre=$billfrom; $godown=$billfrom;}else{ $costc =explode("~",$this->getAnyDetails($row_cnm["sub_location"],"cost_center,sub_location_name","sub_location","sub_location_master"));$costcentre=$costc[0];$godown=$costc[1];}
			
			$cnm_arr[$i]["DocInfo"]["CostCentre"] = $costcentre;
			$cnm_arr[$i]["DocInfo"]["Remark"] = $row_cnm["remark"];
			///////// get item details
			$j = 1;
			$comboitems_arr = array();
			$cn_comboitems = array();
			$cnd_arr = array();
			$cn_itemlist = array();
			$item_cost = 0;
			$res_cnd = mysqli_query($this->link,"SELECT * FROM credit_note_data WHERE ref_no='".$row_cnm["ref_no"]."'");
			while($row_cnd = mysqli_fetch_assoc($res_cnd)){
				$tax_per = $row_cnd['cgst_per']+$row_cnd['sgst_per']+$row_cnd['igst_per'];
				if($row_cnd['prod_code']=='39' || $row_cnd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
				/// get part details
				$part_det = explode("~",$this->getAnyDetails($row_cnd['prod_code'],"productname,hsn_code,productsubcat,brand","productcode","product_master"));						
				///// check combo model
				if($row_cnd["prod_cat"]=="C"){
					$combo_det = explode("~",$this->getAnyDetails($row_cnd['combo_code'],"bom_modelname,bom_hsn","bom_modelcode","combo_master"));
					$cn_itemlist["Sno"] = $j;
					$cn_itemlist["ProductDesc"] = $combo_det[0];
					$cn_itemlist["IsServiceChg"] = $service_flag;
					$cn_itemlist["HsnCode"] = $combo_det[1];
					$cn_itemlist["Qty"] = $row_cnd["req_qty"];
					$cn_itemlist["Unit"] = "NOS";
					$cn_itemlist["UnitPrice"] = $row_cnd["price"];
					$cn_itemlist["SubTotal"] = $row_cnd["value"];
					$cn_itemlist["Discount"] = $row_cnd["discount"];
					$cn_itemlist["PreTaxVal"] = $row_cnd["value"];
					$cn_itemlist["AssessedVal"] = $row_cnd["value"];
					$cn_itemlist["GstRate"] = $tax_per;
					$cn_itemlist["IgstAmt"] = $row_cnd["igst_amt"];
					$cn_itemlist["CgstAmt"] = $row_cnd["cgst_amt"];
					$cn_itemlist["SgstAmt"] = $row_cnd["sgst_amt"];
					$cn_itemlist["TotalItemVal"] = $row_cnd["totalvalue"];
					$cn_itemlist["GodownName"] = $godown;
					array_push($cnd_arr,$cn_itemlist);
					$item_cost += $row_cnd["value"]-$row_cnd["discount"];
					$j++;
				}else{
					$brand_arr[] = $part_det[3];
					$psc_arr[] = $part_det[2];
					$cn_comboitems["ProductDesc"] = $part_det[0];
					$cn_comboitems["Qty"] = $row_cnd["req_qty"];
					$cn_comboitems["Rate"] = $row_cnd["price"];
					$cn_comboitems["Amt"] = $row_cnd["value"];
					$cn_comboitems["GodownName"] = $godown;
					array_push($comboitems_arr,$cn_comboitems);
				}
			}//// close 2nd while loop
			$grand_total = number_format($item_cost,'2','.','')+number_format($gst_val,'2','.','')+number_format($tcs_val,'2','.','');
			$cnm_arr[$i]["DocInfo"]["InvAmt"] = number_format($grand_total,'2','.','');
			if(strpos($grand_total, ".") !== false){
				$expd_gt = explode(".",$grand_total);
				$checkval = ".".$expd_gt[1];
				if($checkval>=.50){
					$ro = 1-$checkval;
					$roundoff = "".number_format($ro,'2','.','');
				}else{
					$roundoff = "-".number_format($checkval,'2','.','');
				}
			}else{
				$roundoff = 0.00;
			}	
			$cnm_arr[$i]["DocInfo"]["RoundOffvalue"] = $roundoff;
			$cnm_arr[$i]["DocInfo"]["ItemsInfo"] = $cnd_arr;
			$cnm_arr[$i]["DocInfo"]["ComboItemsInfo"] = $comboitems_arr;			
			
			/////// check which brand is more in this bill
			$counted_brand = array_count_values($brand_arr);
			arsort($counted_brand); //sort descending maintain keys
			$most_brand = key($counted_brand); //get the key, as we are rewound it's the first key
			/////// check which product sub category is more in this bill
			$counted_psc = array_count_values($psc_arr);
			arsort($counted_psc); //sort descending maintain keys
			$most_psc = key($counted_psc); //get the key, as we are rewound it's the first key
			$cnm_arr[$i]["DocInfo"]["Brand"] = $this->getAnyDetails($most_brand,"make","id","make_master");
			$cnm_arr[$i]["DocInfo"]["Segment"] = $this->getAnyDetails($most_psc,"prod_sub_cat","psubcatid","product_sub_category");
			
			$i++;
		}
		}///// close first while loop
		return $cnm_arr;
	}
}