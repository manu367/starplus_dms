<?php
require_once("config/dbconnect.php");

///// TRUNCATE TABLE
mysqli_query($link1,"TRUNCATE TABLE `relation_data`");

function add_relation($link1, $userid, $child, $level = 0)
{
	$resp = true;
	$sql = "INSERT INTO relation_data
	SET
	user_id = '".$userid."',
	child_id = '".$child."',
	level = $level,
	create_by = 'CRON',
	create_date = '".date("Y-m-d H:i:s")."',
	create_ip = '".$_SERVER['REMOTE_ADDR']."'";
	$res = mysqli_query($link1, $sql);
	if(!$res)
	{
		exit("Error : ".mysqli_error($link1));
	}
	return $resp;
}

function getRMs($link1, $user, $rms = [])
{
	$sql = "SELECT * FROM admin_users WHERE username LIKE '".$user."' AND status='Active'";
	$res = mysqli_query($link1, $sql);
	if($res)
	{
		if(mysqli_num_rows($res) > 0)
		{
			$row = mysqli_fetch_assoc($res);
			if($row["reporting_manager"])
			{
				$rms = getRMs($link1, $row["reporting_manager"], $rms);
				$rms[] = $user;
			}
			else
			{
				$rms[] = $user;
			}			
		}
	}
	return $rms;
}

function map_relation($link1, $user, $c_count)
{
	//echo "<br>User $user relations";
	$rm_upword = getRMs($link1, $user);
	$rm_upword_rv = array_reverse($rm_upword);
	foreach($rm_upword_rv as $key => $rm){
		if($rm != $user)
		{
			//echo "<br>$rm, $user, $key";
			$status = add_relation($link1, $rm, $user, $key);
			$c_count = ($status)?$c_count+1:$c_count;
		}		
	}
	return $c_count;
}

$c_count = 0;
$sql = "SELECT * FROM admin_users WHERE reporting_manager NOT LIKE '' AND status='Active'";
$res = mysqli_query($link1, $sql);
if($res)
{
	if(mysqli_num_rows($res) > 0)
	{
		while($row = mysqli_fetch_assoc($res))
		{
			$c_count = map_relation($link1, $row["username"], $c_count);
		}
	}
}
else
{
	exit("Error : ".mysqli_error($link1));
}
echo "<br>Total $c_count relation(s) created!";