<?php
//function getTeamPeformance($country, $match_type){
function getAccumulatedWins(){
	include 'connect.php';
	include 'send_json.php';	
	mysql_selectdb('Cricket',$link) or die('Error connecting to DB');
	
	$type = $_GET['type'];
	
	$sql = "select min(year) as min_year, max(year) as max_year from matches where type like '%". $type ."%'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$min_year = intval($row['min_year']);
	$max_year = intval($row['max_year']);	
	
	$flag = 0;
	$year = $min_year;
	while($year <= $max_year)
	{
		$sql = "select count(*) as total, m.year as year, t.code as team from matches m, teams t";
		$sql = $sql." where t.id = m.winner_id and m.type like '%". $type ."%' and m.winner_id <> -1 and m.year =".$year;
		$sql = $sql." group by m.winner_id"; 
		//$sql = "select ".$x.", sum(".$y.") as total from batting_stats where type like '%".$type."%' and player_id =".$id;
		//echo $sql;
		$result = mysql_query($sql);
		
		if($result){
			$i = 0;
			$inner_json = "";
			while($row = mysql_fetch_assoc($result)){
				//echo intval($row[$x])." , ".intval($row['total']).'<BR>';
				$inner_json[] = array($row['team'] => intval($row['total']));
				$i++;			
			}
			if($i!=0)
			{
				$json[] = array(intval($year) => $inner_json);
				$flag++;
			}
		}
		$year++;
	}
	if($flag)
		echo "{\"data\": ".json_encode($json)." }";
	else
		echo "{\"data\":[]}";

}
getAccumulatedWins();
?>