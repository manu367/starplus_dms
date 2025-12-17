<?php
//require_once("config/dbconnect.php");
$s = 0;
$today = date("Y-m-d");
$datetime = date("Y-m-d H:i:s");
//$a = array("1277" => "Bihar","2127" => "Bihar","1655" => "Bihar","1923" => "Bihar","2069" => "Bihar","2151" => "Bihar","2231" => "Bihar","2293" => "Bihar","2306" => "Bihar","2313" => "Bihar","2337" => "Bihar","2375" => "Bihar","2183" => "Uttar Pradesh","2272" => "Uttar Pradesh","2303" => "Uttar Pradesh","2182" => "Uttar Pradesh","2222" => "Uttar Pradesh","2193" => "Uttar Pradesh","2188" => "Uttar Pradesh","1838" => "Uttar Pradesh","2052" => "Uttar Pradesh","1182" => "Uttar Pradesh","1986" => "Uttar Pradesh","1827" => "Uttar Pradesh","1985" => "Uttar Pradesh","1197" => "Uttar Pradesh","1712" => "Uttar Pradesh","EAPL0111" => "Uttar Pradesh","1981" => "Uttar Pradesh","2203" => "Uttar Pradesh","2187" => "Uttar Pradesh","2110" => "Uttar Pradesh","2164" => "Uttar Pradesh","2302" => "Uttar Pradesh","2234" => "Uttar Pradesh","2343" => "Uttar Pradesh","1919" => "Rajasthan","1569" => "Rajasthan","1882" => "Rajasthan","1766" => "Rajasthan","1939" => "Rajasthan","EAPL0258" => "Rajasthan","1883" => "Rajasthan","1940" => "Rajasthan","1961" => "Rajasthan","1819" => "Rajasthan","2168" => "Rajasthan","2266" => "Rajasthan","1820" => "Rajasthan","1762" => "Odisha","2068" => "Odisha","2284" => "Odisha","1910" => "Uttar Pradesh","1862" => "Uttar Pradesh","1866" => "Uttar Pradesh","1283" => "Uttar Pradesh","EAPL0143" => "Uttar Pradesh","1897" => "Uttar Pradesh","1980" => "Uttar Pradesh","1912" => "Uttar Pradesh","2015" => "Uttar Pradesh","1926" => "Uttar Pradesh","2154" => "Uttar Pradesh","2339" => "Uttar Pradesh","1788" => "Uttar Pradesh","1850" => "Uttar Pradesh","2021" => "Uttar Pradesh","1994" => "Uttar Pradesh","2076" => "Uttar Pradesh","2074" => "Uttar Pradesh","2124" => "Uttar Pradesh","2185" => "Uttar Pradesh","2269" => "Uttar Pradesh","1663" => "Jharkhand","1816" => "Jharkhand","2101" => "Jharkhand","1904" => "Punjab","2053" => "Punjab","2057" => "Punjab","1810" => "Punjab","2126" => "Punjab","1936" => "Punjab","1988" => "Punjab","2365" => "Punjab","2100" => "Jammu & Kashmir","1131" => "Haryana","1924" => "Haryana","1958" => "Haryana","1176" => "Haryana","2000" => "Haryana","1957" => "Haryana","2221" => "Haryana","2285" => "Haryana","2347" => "Haryana","2321" => "Jammu & Kashmir","2298" => "Punjab","2254" => "Bihar","2391" => "Jharkhand","2395" => "Uttar Pradesh","2382" => "Uttar Pradesh","1758" => "Tamilnadu","1769" => "Tamilnadu","EAPL0852" => "Tamilnadu","1329" => "Tamilnadu","2098" => "Tamilnadu","1488" => "Tamilnadu","1579" => "Karnataka","1856" => "Karnataka","1247" => "Karnataka","1553" => "Karnataka","2389" => "Karnataka","1977" => "Karnataka","1787" => "Andhra Pradesh","2140" => "Andhra Pradesh","2051" => "Andhra Pradesh","2141" => "Andhra Pradesh","2239" => "Andhra Pradesh","1989" => "Madhya Pradesh","2277" => "Madhya Pradesh","2133" => "Madhya Pradesh","2370" => "Madhya Pradesh","1784" => "Madhya Pradesh","2038" => "Maharastra","2334" => "Maharastra","2367" => "Maharastra","2372" => "Maharastra","1842" => "Maharastra","2138" => "Uttar Pradesh");
$a = array("2100" => "Punjab",
"2321" => "Punjab",
"1758" => "Kerala",
"1769" => "Kerala",
"EAPL0852" => "Kerala",
"1329" => "Kerala",
"2098" => "Kerala",
"1488" => "Kerala",
"1787" => "Telangana","2140" => "Telangana","2051" => "Telangana","2141" => "Telangana","2239" => "Telangana","1989" => "Chhattisgarh","2277" => "Chhattisgarh","2133" => "Chhattisgarh","2370" => "Chhattisgarh","1784" => "Chhattisgarh","2038" => "Gujrat","2334" => "Gujrat","2367" => "Gujrat","2372" => "Gujrat","1842" => "Gujrat");
//$res_adm = mysqli_query($link1,"SELECT username,oth_empid FROM admin_users WHERE oth_empid IN ('1277','2127','1655','1923','2069','2151','2231','2293','2306','2313','2337','2375','2183','2272','2303','2182','2222','2193','2188','1838','2052','1182','1986','1827','1985','1197','1712','EAPL0111','1981','2203','2187','2110','2164','2302','2234','2343','1919','1569','1882','1766','1939','EAPL0258','1883','1940','1961','1819','2168','2266','1820','1762','2068','2284','1910','1862','1866','1283','EAPL0143','1897','1980','1912','2015','1926','2154','2339','1788','1850','2021','1994','2076','2074','2124','2185','2269','1663','1816','2101','1904','2053','2057','1810','2126','1936','1988','2365','2100','1131','1924','1958','1176','2000','1957','2221','2285','2347','2321','2298','2254','2391','2395','2382','1758','1769','EAPL0852','1329','2098','1488','1579','1856','1247','1553','2389','1977','1787','2140','2051','2141','2239','1989','2277','2133','2370','1784','2038','2334','2367','2372','1842','2138')");
$res_adm = mysqli_query($link1,"SELECT username,oth_empid FROM admin_users WHERE oth_empid IN ('2100','2321','1758','1769','EAPL0852','1329','2098','1488','1787','2140','2051','2141','2239','1989','2277','2133','2370','1784','2038','2334','2367','2372','1842')");
while($row = mysqli_fetch_assoc($res_adm)){
	////// update access state
	//mysqli_query($link1,"DELETE FROM access_state WHERE uid='".$row["username"]."'");
	////// update access location 
	//mysqli_query($link1,"DELETE FROM access_location WHERE uid='".$row["username"]."'");
	////// update access role 
	//mysqli_query($link1,"DELETE FROM access_role WHERE uid='".$row["username"]."'");
	/////// give role
	//mysqli_query($link1,"insert into access_role set uid='".$row["username"]."',role_id='DL',status='Y'")or die(mysqli_error($link1));
	//mysqli_query($link1,"insert into access_role set uid='".$row["username"]."',role_id='DS',status='Y'")or die(mysqli_error($link1));
	///// give state right
	mysqli_query($link1,"INSERT INTO access_state set uid='".$row["username"]."',state='".$a[$row["oth_empid"]]."',status='Y'")or die(mysqli_error($link1));
	///// now give dealer distributor rights
	$res_asp = mysqli_query($link1,"SELECT asc_code,state,id_type FROM asc_master WHERE id_type IN ('DL','DS') AND state = '".$a[$row["oth_empid"]]."' AND status='Active'");
	while($row_asp = mysqli_fetch_assoc($res_asp)){
		mysqli_query($link1,"insert into access_location set uid='".$row["username"]."',location_id='".$row_asp["asc_code"]."',state='".$a[$row["oth_empid"]]."',id_type='".$row_asp["id_type"]."',status='Y'")or die(mysqli_error($link1));
	}
	$s++;
}
echo $s."  ".$err_msg;