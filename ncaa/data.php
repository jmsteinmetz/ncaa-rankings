<?php
$limit = $_GET['limit'];
$type = $_GET['type'];
$team = $_GET['team'];
$subtype = $_GET['sub'];
$conference = $_GET['conference'];

$server = "localhost";
$username = "root";
$password = "root";
$database = "ncaaranking";

 
$conn =  mysql_connect($server, $username, $password) or die("Couldn't connect to MySQL" . mysql_error());
mysql_select_db($database, $conn) or die ("Couldn't open $test: " . mysql_error());

if ($type == 'players') {
	// http://localhost/ncaa/data.php?type=players&team=5&limit=1000
	$result = mysql_query("SELECT idplayers AS id, playercode, firstname, lastname, uniformnumber, class, position, height, weight, hometown, homestate, homecount AS homecountry, lastschool, name AS school 
		FROM players, teams
		WHERE (players.teamcode = teams.teamcode) 
		AND teams.teamcode = " . $team);
};

if ($type == 'teams') {
	// http://localhost/ncaa/data.php?type=teams
	$result = mysql_query("SELECT teamcode, teams.name AS team, conferencecode, code, conferences.name AS conference, subdivision 
		FROM teams, conferences 
		WHERE (teams.conferencecode = conferences.code) 
		ORDER BY conferencecode ASC");
}

if ($type == 'results') {
	// http://localhost/ncaa/data.php?type=results
	// Need to combine & group and make each record the game record.
	$result = mysql_query("SELECT teamgamestats.gamecode, teamgamestats.teamcode, teams.name, game.date, points 
		FROM teamgamestats, game, teams
		WHERE (teamgamestats.teamcode =  teams.teamcode)
		GROUP BY teamgamestats.gamecode, teamgamestats.teamcode ASC");
}

if ($type == 'stats') {
	// http://localhost/ncaa/data.php?type=stats&sub=rushing&team=5
	if ($subtype == 'rushing') {
		$result = mysql_query("SELECT playergamestatistics.playercode, players.firstname, players.lastname, 
			sum(rushattempts) AS attempts, 
			sum(rushyards) AS yards,
			sum(rushtd) AS td
			FROM playergamestatistics, players 
			WHERE (playergamestatistics.playercode = players.playercode)
			AND (rushattempts > 0)
			AND teamcode = " . $team . " GROUP BY playergamestatistics.playercode");
	}

	
	if ($subtype == 'passing') {
		// http://localhost/ncaa/data.php?type=stats&sub=passing&team=5
		$result = mysql_query("SELECT playergamestatistics.playercode, players.firstname, players.lastname, 
			sum(passattempts) AS attempts, 
			sum(passcompletions) AS completions, 
			sum(passyards) AS yards,
			sum(passtd) AS td,
			sum(passint) AS interceptions,
			sum(passconv) AS conversions
			FROM playergamestatistics, players 
			WHERE (playergamestatistics.playercode = players.playercode)
			AND (passattempts > 0)
			AND teamcode = " . $team . " GROUP BY playergamestatistics.playercode");
	}

	
	if ($subtype == 'receiving') {
		// http://localhost/ncaa/data.php?type=stats&sub=receiving&team=5
		$result = mysql_query("SELECT playergamestatistics.playercode, players.firstname, players.lastname, 
			sum(receptions) AS receptions, 
			sum(receivingyards) AS yards, 
			sum(receivingtd) AS td
			FROM playergamestatistics, players 
			WHERE (playergamestatistics.playercode = players.playercode)
			AND (receptions > 0)
			AND teamcode = " . $team . " GROUP BY playergamestatistics.playercode");
	}

	
	if ($subtype == 'team') {
		// http://localhost/ncaa/data.php?type=stats&sub=team&team=5
		$result = mysql_query("SELECT * FROM teamgamestats, teams 
			WHERE (teamgamestats.teamcode = teams.teamcode) 
			ORDER BY teamgamestats.teamcode");
	}
};

$rows = array();

while($r = mysql_fetch_assoc($result)) {
    $rows["data"][] = $r;
}

print json_encode($rows);
 
mysql_close($conn);
?>
