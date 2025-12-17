<?php 
require_once("config/dbconnect.php"); 
$today = date("Y-m-d");
$currtime = date("His");
function SaveFile($filename, $records)
{
	$master = '';
	$heading = false;
	$col = [
		"S.No." => "seq",
		"Id" => "sno",
		"Location Code" => "asc_code",
		"Godown Id" => "sub_location",
		"partcode" => "partcode",
		"OK" => "okqty",
		"DAMAGE" => "broken",
		"MISSING" => "missing",
		"Updated On" => "updatedate"
		
	];
	if(!empty($records))
	{
		$i = 1;
		foreach($records as $row)
		{
			if(!$heading)
			{
				$master .= implode("\t", array_keys($col))."\n";
				$heading = true;
			}
			$selection = [];
			
			foreach($col as $col_name)
			{
				if($col_name=="seq"){
					$selection[$col_name] = $i;
				}else{
					$selection[$col_name] = $row[$col_name];
				}
				
			}
			$master .= implode("\t", array_values($selection))."\n";
			$i++;
		}
	}
	$myfile = fopen($filename, "w") or die("0");
	fwrite($myfile, $master);
	fclose($myfile);
	//exit("1");
}
////////// fetch data from table //////////
$res_loc = mysqli_query($link1,"SELECT asc_code FROM asc_master WHERE id_type IN ('HO','BR') AND status='Active'");
while($row_loc = mysqli_fetch_assoc($res_loc)){
	$payload = [];
	$sql = "SELECT * FROM stock_status WHERE asc_code='".$row_loc["asc_code"]."'";
	$res = mysqli_query($link1, $sql);
	if($res){
		if(mysqli_num_rows($res) > 0)
		{
			while($row = mysqli_fetch_assoc($res))
			{
				$payload[] = $row;
			}		
		}
	}
	if(count($payload)>0){
		$dirct = "daily_inventory_loc/".$row_loc["asc_code"];
		if (!is_dir($dirct)) {
			mkdir($dirct, 0777, 'R');
		}
		$filename = $dirct."/Inventory_Report_".$today."_".$currtime.".xls";
		SaveFile($filename, $payload);
	}
}
exit("1");
?>