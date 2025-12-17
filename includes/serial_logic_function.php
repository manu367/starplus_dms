<?php
///// function to check serial no. logic written by shekhar on 08 feb 2022
function checkSerialNoLogic($serialno,$checklogicfor){
	$a = array();
	############ if we are checking logic for all charged battery serial no.
	if($checklogicfor=="BTR"){
		$prod_code = substr($serialno,0,3);/////// get product code
		$manuf_date = substr($serialno,3,1);/////// get manufacturing date
		$manuf_month = substr($serialno,4,1);/////// get manufacturing month
		$manuf_year = substr($serialno,5,1);/////// get manufacturing year
		$charging_site = substr($serialno,6,1);/////// get charging site
		$segment = substr($serialno,7,1);/////// get segment
		$brand = substr($serialno,8,1);/////// get brand
		$capacity = substr($serialno,9,1);/////// get AH capacity
		$ws_slab = substr($serialno,10,1);/////// get warranty slab
		$battery_layout = substr($serialno,11,1);/////// get battery layout
		$battery_serial = substr($serialno,12,5);/////// get battery serial no.
		$model_code = $brand.$capacity.$ws_slab.$battery_layout; ///// 4 character model code
		$a = array("product_code" => $prod_code, "mf_date" => $manuf_date, "mf_month" => $manuf_month, "mf_year" => $manuf_year, "charging_site" => $charging_site, "segment" => $segment, "brand" => $brand, "capacity" => $capacity, "warranty_slab" => $ws_slab, "battery_layout" => $battery_layout, "battery_serial" => $battery_serial, "model_code" => $model_code);
	}
	else if($checklogicfor=="LTHIBTR"){ ############ if we are checking logic for Lithium ion battery serial no.
		$prod_code = substr($serialno,0,3);/////// get product code
		$manuf_date = substr($serialno,3,1);/////// get manufacturing date
		$manuf_month = substr($serialno,4,1);/////// get manufacturing month
		$manuf_year = substr($serialno,5,1);/////// get manufacturing year
		$can_softw = substr($serialno,6,1);/////// get CAN software
		$segment = substr($serialno,7,1);/////// get segment
		$brand = substr($serialno,8,1);/////// get brand
		$capacity = substr($serialno,9,1);/////// get AH capacity
		$ws_slab = substr($serialno,10,1);/////// get warranty slab
		$battery_gps = substr($serialno,11,1);/////// get GPS/without GPS
		$battery_serial = substr($serialno,12,5);/////// get battery serial no.
		$model_code = $brand.$capacity.$ws_slab.$battery_gps; ///// 4 character model code
		$a = array("product_code" => $prod_code, "mf_date" => $manuf_date, "mf_month" => $manuf_month, "mf_year" => $manuf_year, "can_software" => $can_softw, "segment" => $segment, "brand" => $brand, "capacity" => $capacity, "warranty_slab" => $ws_slab, "battery_gps" => $battery_gps, "battery_serial" => $battery_serial, "model_code" => $model_code);
	}
	else if($checklogicfor=="ERBTRCHR"){ ############ if we are checking logic for E-Rickshaw battery charger serial no.
		$production_linecode = substr($serialno,0,1);/////// get production line code
		$engg_chgcode = substr($serialno,1,2);/////// get engineering change code
		$prod_code = substr($serialno,3,2);/////// get product code
		$vendor_code = substr($serialno,5,1);/////// get vendor code
		$ws_slab = substr($serialno,6,1);/////// get warranty slab
		$segment = substr($serialno,7,1);/////// get segment
		$last_2digitpo = substr($serialno,8,2);/////// get last 2 digits of PO
		$manuf_month = substr($serialno,10,1);/////// get manufacturing month
		$manuf_year = substr($serialno,11,2);/////// get manufacturing year
		$charger_serial = substr($serialno,13,4);/////// get charger serial no.
		$model_code = $prod_code.$vendor_code.$ws_slab;/////// get charger model code
		$a = array("product_code" => $prod_code, "product_linecode" => $production_linecode, "mf_month" => $manuf_month, "mf_year" => $manuf_year, "vendor_code" => $vendor_code, "segment" => $segment, "last_2digitpo" => $last_2digitpo, "engg_chgcode" => $engg_chgcode, "warranty_slab" => $ws_slab, "charger_serial" => $charger_serial, "model_code" => $model_code);
	}
	else if($checklogicfor=="SOL"){ ############ check serial logic for all solar products
		$range_code = substr($serialno,0,3);/////// get product code
		$manuf_date = "";/////// get manufacturing date
		//$manuf_month = substr($serialno,3,1);/////// get manufacturing month
		//$manuf_year = substr($serialno,4,1);/////// get manufacturing year
		$modelc = substr($serialno,3,2);/////// get model code
		$vendor_code = substr($serialno,5,2);/////// get vendor code
		$segment = substr($serialno,7,1);/////// get segment
		$prod_code = substr($serialno,8,2);/////// get last 2 digits of PO
		//$ws_slab = substr($serialno,10,1);/////// get warranty slab
		$manuf_month = substr($serialno,10,1);/////// get warranty slab
		//$capacity = substr($serialno,11,1);/////// get voltage capacity
		$manuf_year = substr($serialno,11,2);/////// get voltage capacity
		$solar_serial = substr($serialno,13,4);/////// get solar serial no.
		$model_code = $modelc;/////// get solar model code
		$a = array("range_code" => $range_code, "mf_date" => "", "mf_month" => $manuf_month, "mf_year" => $manuf_year, "vendor_code" => $vendor_code, "segment" => $segment, "product_code" => $range_code, "capacity" => $capacity, "warranty_slab" => $ws_slab, "solar_serial" => $solar_serial, "model_code" => $model_code);
	}
	else if($checklogicfor=="SOLELC"){ ############ check serial logic for solar product electronics
		$range_code = substr($serialno,0,3);/////// get range code/ sub category
		$manuf_date = "";/////// get manufacturing date
		$prod_code = substr($serialno,3,2);/////// get product model code
		$vendor_code = substr($serialno,5,2);/////// get vendor code
		$segment = substr($serialno,7,1);/////// get segment
		$last_2digitpo = substr($serialno,8,2);/////// get last 2 digits of PO
		$manuf_month = substr($serialno,10,1);/////// get manufacturing month
		$manuf_year = substr($serialno,11,2);/////// get manufacturing year
		$solar_serial = substr($serialno,13,4);/////// get solar serial no.
		$a = array("range_code" => $range_code, "mf_date" => "", "mf_month" => $manuf_month, "mf_year" => $manuf_year, "vendor_code" => $vendor_code, "segment" => $segment, "product_code" => $prod_code, "last_2digitpo" => $last_2digitpo, "solar_serial" => $solar_serial);
	}
	else{
	
	}
	return $a;
}
////// get exact date from date code written by shekhar on 09 feb 2022
function getMfDate($datecode){
	$d = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "A" => "10", "B" => "11", "C" => "12", "D" => "13", "E" => "14", "F" => "15", "G" => "16", "H" => "17", "I" => "18", "J" => "19", "K" => "20", "L" => "21", "M" => "22", "N" => "23", "O" => "24", "P" => "25", "Q" => "26", "R" => "27", "S" => "28", "T" => "29", "U" => "30", "V" => "31");
	return $d[$datecode];
}
////// get exact month from month code written by shekhar on 09 feb 2022
function getMfMonth($monthcode){
	$m = array("A" => "Jan", "B" => "Feb", "C" => "Mar", "D" => "Apr", "E" => "May", "F" => "Jun", "G" => "Jul", "H" => "Aug", "I" => "Sep", "J" => "Oct", "K" => "Nov", "L" => "Dec");
	return $m[$monthcode];
}
////// get exact year from year code written by shekhar on 09 feb 2022
function getMfYear($yearcode){
	$y = array("7" => "2017", "8" => "2018", "9" => "2019", "0" => "2020", "1" => "2021", "2" => "2022", "3" => "2023", "4" => "2024", "5" => "2025", "6" => "2026");
	return $y[$yearcode];
}
////// get exact battery layout from layout code written by shekhar on 10 feb 2022
function getBatteryLayout($layoutcode){
	$ly = array("L" => "Left Hand", "R" => "Right Hand", "S" => "Standard");
	return $ly[$layoutcode];
}
////// get exact warranty slab from slab code written by shekhar on 10 feb 2022
function getWarrantySlab($wslabcode){
	$ws = array("0" => "6 Months", "1" => "12 Months", "2" => "15 Months", "3" => "18 Months", "4" => "24 Months", "5" => "30 Months", "6" => "36 Months", "7" => "42 Months", "8" => "48 Months", "9" => "60 Months", "N" => "No Warranty", "W" => "Warranty Replacement", "A" => "25 Years");
	return $ws[$wslabcode];
}
/////// get segment name from segment code written by shekhar on 09 feb 2022
function getSegment($segmentcode,$link1){
	$res_segm = mysqli_query($link1,"SELECT segment FROM segment_master WHERE segment_code='".$segmentcode."'");
	$row_segm = mysqli_fetch_assoc($res_segm);
	return $row_segm["segment"];
}
/////// get charging site name from charging site code written by shekhar on 09 feb 2022
function getChargingSite($chargingsitecode,$link1){
	$res_chgsite = mysqli_query($link1,"SELECT site_name FROM charging_site_master WHERE site_code='".$chargingsitecode."'");
	$row_chgsite = mysqli_fetch_assoc($res_chgsite);
	return $row_chgsite["site_name"];
}
////// get brand name from brand id written by shekhar on 10 feb 2022
function getBrandName($brandid,$brandcode,$link1){
	if($brandcode){ $str = " ";} else{ $str .= " id = '".$brandid."'";}
	$res_brand = mysqli_query($link1,"SELECT make FROM make_master WHERE ".$str);
	$row_brand = mysqli_fetch_assoc($res_brand);
	/////// brand
	return $row_brand["make"];
}
////// get product cat name from psc id written by shekhar on 10 feb 2022
function getPSCName($pscid,$link1){
	$res_psc = mysqli_query($link1,"SELECT prod_sub_cat,product_category FROM product_sub_category WHERE psubcatid = '".$pscid."'");
	$row_psc = mysqli_fetch_assoc($res_psc);
	/////// product sub cat
	if($row_psc["prod_sub_cat"]){
		return $row_psc["prod_sub_cat"]."~".$row_psc["product_category"];
	}else{
		$psc = array("ESP" => "Eastman Solar PWM", "ESM" => "Eastman Solar MPPT", "ESG" => "Eastman Solar Grid Tie", "EPP" => "Eastman PV Panel", "ESL" => "Eastman Solar Light", "EES" => "Eastman Eco Smart", "EGP" => "Eastman Gold PWM", "EDM" => "Eastman Diamond MPPT", "EPM" => "Eastman Pearl MPPT", "SCC" => "Solar Charge Controller", "SMU" => "Solar Management Unit");
		return $psc[$pscid]."~Solar Product";
	}
}
/////// get model name from model code written by shekhar on 09 feb 2022
function getModelName($modelcode,$checklogicfor,$link1){
	$res_model = mysqli_query($link1,"SELECT model_name,productsubcat,brand,other_specification1 FROM product_master WHERE model_code='".$modelcode."' OR model_code2 LIKE '".$modelcode.",%'");
	$row_model = mysqli_fetch_assoc($res_model);
	if($row_model["model_name"]){
		/////// get brand name
		$brandname = getBrandName($row_model["brand"],"",$link1);
		////// get product sub cat
		$pscname = getPSCName($row_model["productsubcat"],$link1);
		return $row_model["model_name"]."~".$brandname."~".$pscname."~".$row_model["other_specification1"];
	}else{
		$mpcode = explode("~",$modelcode);
		if($checklogicfor=="SOL"){
			$model = array("01" => "ESP650/12", "02" => "ESP850/12", "03" => "ESP1050/12", "04" => "ESP1450/12", "05" => "ESP2K/24", "06" => "ESP3K5/48", "07" => "ESP5K/48", "08" => "ESP7K5/120", "09" => "ESP/10K/120", "10" => "ESM3K/48", "11" => "ESM5K/96", "12" => "ESM7K5/120", "13" => "ESM10K/120", "14" => "ESG1K", "15" => "ESG3K", "16" => "ESG5K", "17" => "ESG10K", "18" => "EPP100W", "19" => "EPP150W", "20" => "EPP265W", "21" => "ESL9W", "22" => "ESL15W", "23" => "EPP40W", "24" => "EPP50W", "25" => "EPP75W", "26" => "EPP160W", "27" => "EPP320W");
		}elseif($checklogicfor=="SOLELC"){
			$model = array("01" => "EES675/12V", "02" => "EES875/12V", "03" => "EES1075/12V", "04" => "EES1475/24V", "05" => "EGP1100/12V", "06" => "EGP1400/12V", "07" => "EGP1800/24V", "08" => "EGP2250/24V", "09" => "EGP2650/24V", "10" => "EDM3000/48V", "11" => "EDM5000/48V", "12" => "EDM5000/96V", "13" => "EDM7500/120V", "14" => "EDM10000/120V", "15" => "EPM1000/12V", "16" => "EPM2000/24V", "17" => "EPM3000/48V", "18" => "EPM5000/48V", "19" => "EPM5000/96V", "20" => "ESCC101224", "21" => "ESCC201224", "22" => "ESMU401224");
		}else{
			$model = array();
		}
		/////// get brand name
		$brandname = "EASTMAN";
		////// get product sub cat
		$pscname = getPSCName($mpcode[1],$link1);
		////// voltage
		$volt = array("A" => "12V", "B" => "24V", "C" => "36V", "D" => "48V", "E" => "72V", "F" => "96V", "G" => "120V", "H" => "NA");
		return $model[$mpcode[0]]."~".$brandname."~".$pscname."~".$volt[$mpcode[2]];
	}
}
////// get vendor name from vendor code written by shekhar on 10 feb 2022
function getVendorName($vendorcode,$link1){
	$vend = array("A1" => "Fujiyama Power", "B1" => "Advance Electronics", "C1" => "Kstar", "D1" => "Premier", "E1" => "Intelizon", "F1" => "Eastman", "G1" => "Insolation", "V" => "VOLTSMAN POWER TECHNOTOGIES PRIVATE TIMITED");
	return $vend[$vendorcode];
}
////// get software name from s/w code written by shekhar on 07 mar 2022
function getSoftwareName($swcode,$link1){
	$sw = array("C" => "CAN");
	return $sw[$swcode];
}
////// get GPS details written by shekhar on 07 mar 2022
function getGPSInfo($gpscode,$link1){
	$gps = array("W" => "Without GPS", "G" => "GPS");
	return $gps[$gpscode];
}
////// get exact warranty slab from slab code for lithium ion battery written by shekhar on 07 mar 2022
function getWarrantySlabForLithIon($wslabcode){
	$ws = array("0" => "12 Months", "1" => "24 Months", "2" => "36 Months", "3" => "42 Months", "4" => "48 Months", "5" => "60 Months");
	return $ws[$wslabcode];
}
////// get production line from line code written by shekhar on 07 mar 2022
function getProductionLine($linecode,$link1){
	$prodline = array("A" => "Production Line - 1", "B" => "Production Line - 2", "C" => "Production Line - 3", "D" => "Production Line - 4", "E" => "Production Line - 5", "F" => "Production Line - 6", "G" => "Production Line - 7", "H" => "Production Line - 8", "I" => "Production Line - 9", "J" => "Production Line - 10", "K" => "Production Line - 11", "L" => "Production Line - 12", "M" => "Production Line - 13", "N" => "Production Line - 14", "O" => "Production Line - 15", "P" => "Production Line - 16", "Q" => "Production Line - 17", "R" => "Production Line - 18", "S" => "Production Line - 19", "T" => "Production Line - 20", "U" => "Production Line - 21", "V" => "Production Line - 22", "W" => "Production Line - 23", "X" => "Production Line - 24", "Y" => "Production Line - 25", "Z" => "Production Line - 26");
	return $prodline[$linecode];
}
////// get engineer change note from engineer change note code written by shekhar on 07 mar 2022
function getEngChangeNote($engchgcode,$link1){
	$engchgnote = array("AA" => "BOM Change Code Note - 1", "AB" => "BOM Change Code Note - 2", "AC" => "BOM Change Code Note - 3", "AD" => "BOM Change Code Note - 4", "AE" => "BOM Change Code Note - 5", "AF" => "BOM Change Code Note - 6", "AG" => "BOM Change Code Note - 7", "AH" => "BOM Change Code Note - 8", "AI" => "BOM Change Code Note - 9", "AJ" => "BOM Change Code Note - 10", "AK" => "BOM Change Code Note - 11", "AL" => "BOM Change Code Note - 12", "AM" => "BOM Change Code Note - 13", "AN" => "BOM Change Code Note - 14", "AO" => "BOM Change Code Note - 15", "AP" => "BOM Change Code Note - 16", "AQ" => "BOM Change Code Note - 17", "AR" => "BOM Change Code Note - 18", "AS" => "BOM Change Code Note - 19", "AT" => "BOM Change Code Note - 20", "AU" => "BOM Change Code Note - 21", "AV" => "BOM Change Code Note - 22", "AW" => "BOM Change Code Note - 23", "AX" => "BOM Change Code Note - 24", "AY" => "BOM Change Code Note - 25", "AZ" => "BOM Change Code Note - 26");
	return $engchgnote[$engchgcode];
}
////// get exact warranty slab from slab code for E-Rickshaw battery charger written by shekhar on 07 mar 2022
function getWarrantySlabForERickshawChg($wslabcode){
	$ws = array("0" => "6 Months", "1" => "12 Months", "2" => "18 Months", "3" => "24 Months", "4" => "30 Months", "5" => "36 Months");
	return $ws[$wslabcode];
}
////// get exact year from year code for E-Rickshaw battery charger and solar products electronics written by shekhar on 07 mar 2022
function getMfYear2($yearcode){
	$y = array("17" => "2017", "18" => "2018", "19" => "2019", "20" => "2020", "21" => "2021", "22" => "2022", "23" => "2023", "24" => "2024", "25" => "2025", "26" => "2026", "27" => "2027", "28" => "2028");
	return $y[$yearcode];
}
/////// get serial no. validate with its partcode written by shekhar on 07 dec 2022
function getValidateSerialPartcode($serial,$partcode,$link1){
	$rtn_msg = "";
	///// check partcode details written by shekhar on 07 dec 2022
	$partdet = explode("~",getAnyDetails($partcode,"id,productcategory,product_code,model_code,model_code2,product_code2","productcode","product_master",$link1));
	if($partdet[0]!=""){
		///// extract 3 digits product code from serial no. start from beginning  
		$serial_prodcode = substr($serial,0,3);
		$findpc = $serial_prodcode.",";
		$pospc = strpos($partdet[5], $findpc);
		//////// check if serial product code and partcode product should be matched (except  product category (2) E-Rickshaw Charger )
		if($serial_prodcode==$partdet[2] || $pospc !== false || $partdet[1]=="2"){
			/*
			////get product cat
			$prod_cat = explode("~",getAnyDetails($partdet[1],"cat_name,short_code","catid","product_cat_master",$link1));
			////// if serial no. search for all charged battery
			if($prod_cat[1]=="BTR"){
				$serialinfo = checkSerialNoLogic(strtoupper($serial),"BTR");
				///// get serial model code
				$serial_modelcode = $serialinfo["model_code"];
			}
			////// if serial no. search for lithium ion battery
			else if($prod_cat[1]=="LI"){ 
				$serialinfo = checkSerialNoLogic(strtoupper($serial),"LTHIBTR");
				///// get serial model code
				$serial_modelcode = $serialinfo["model_code"];
			}
			////// if serial no. search for E-Rickshaw battery charger
			else if($prod_cat[1]=="ERC"){ 
				$serialinfo = checkSerialNoLogic(strtoupper($serial),"ERBTRCHR");
				///// get serial model code
				$serial_modelcode = $serialinfo["model_code"];
			}
			else if($prod_cat[1]=="SLD"){
				$serialinfo = checkSerialNoLogic(strtoupper($serial),"SOL");
				///// get serial model code
				$serial_modelcode = $serialinfo["model_code"];
			}
			//else if($prod_cat[1]=="POE" || $prod_cat[1]=="UPS" || $prod_cat[1]=="EXP"){
			else{
				$serialinfo = checkSerialNoLogic(strtoupper($serial),"SOLELC");
				///// get serial model code
				$serial_modelcode = $serialinfo["product_code"];
			}
			//////// check model code of serial no.
			$findme = $serial_modelcode.",";
			$pos = strpos($partdet[4], $findme);
			if($serial_modelcode == $partdet[3] || $pos !== false){
				$rtn_msg = "Y";
			}else{
				//$rtn_msg = "Model Code is not matched of serial ".$serial;
				$rtn_msg = "ModelCodeNotMatched";
			}
			*/
			$rtn_msg = "Y";
		}else{
			//$rtn_msg = "Product Code is not matched of serial ".$serial;
			$rtn_msg = "ProductCodeNotMatched";
		}
	}else{
		$rtn_msg = "ProductNotFoundInDB";
	}
	return $rtn_msg;
}
?>