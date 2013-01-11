<?
	include("header.php3");
	include("config.php3");

	$tablename = date('Fy', mktime(0,0,0,$month,1,$year));
	$monthname = date('F', mktime(0,0,0,$month,1,$year));

	echo "<font face=Arial size=5><b><center>$day $monthname $year</center></b><p>";
	echo "<font size=3>";

	$database = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	mysql_select_db($mysql_database, $database);

	$query = mysql_query("SELECT * FROM $mysql_tablename WHERE stamp >= \"$year-$month-$day 00:00:00\" AND stamp <= \"$year-$month-$day 23:59:59\" ORDER BY stamp", $database);

	echo "<form method=post action=operate.php3>
		<table cellpadding=5 cellspacing=5 border=1 width=80%>
		<tr><td><b>Select</td><td><b>Username</td><td><b>Time</td><td><b>Subject</td>
		<td><b>Description</td></tr>";
	while ($row = mysql_fetch_array($query))
	{
		$i++;
		echo "<tr><td><input type=radio name=id value=$row[id]></td>
			<td>$row[username]</td><td>$row[stamp]</td>
			<td>$row[subject]</td><td>$row[description]</td></tr>";
	}
	echo "</table><p>
		<input type=hidden name=day value=$day>
		<input type=hidden name=month value=$month>
		<input type=hidden name=year value=$year>
		<input type=submit name=action value=\"Delete marked\">
		<input type=submit name=action value=\"Modify marked\">
		</form>";
?>
