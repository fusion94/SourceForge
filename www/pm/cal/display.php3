<?
	include ("header.php3");
	include ("config.php3");

	$monthname = date('F', mktime(0,0,0,$month,1,$year));

	echo "<font face=Arial size=5><b><center>$day $monthname $year</b><p>";

	$database = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	mysql_select_db($mysql_database, $database);

	$lastseconds = mktime(0,0,0,$month,$day,$year)-(24*60*60);
        $lastday = date('j', $lastseconds);
        $lastmonth = date('m', $lastseconds);
        $lastyear = date('Y', $lastseconds);

        $nextseconds = mktime(0,0,0,$month,$day,$year)+(24*60*60);
        $nextday = date('j', $nextseconds);
        $nextmonth = date('m', $nextseconds);
        $nextyear = date('Y', $nextseconds);

	echo "<center><table><tr>";
        echo "<td><form method=post action=display.php3><input type=submit value='<<'>
        <input type=hidden name=day value=$lastday>
        <input type=hidden name=month value=$lastmonth>
        <input type=hidden name=year value=$lastyear></form></td>";
	
	echo " 
              <td><form method=post action=operate.php3>
                <input type=hidden name=month value=$month>
                <input type=hidden name=year value=$year>
                <input type=hidden name=day value=$day>
                <input type=submit name='action' value='Add item to calendar'>
              </form></td>

              <td><form method=post action=modify.php3>
                <input type=hidden name=month value=$month>
                <input type=hidden name=day value=$day>
                <input type=hidden name=year value=$year>
                <input type=submit value='Delete or Modify'>
              </form></td>

              <td><form method=post action=welcome.php3>
              <input type=submit value='Return to Calendar'>
              <input type=hidden name=day value=$day>
              <input type=hidden name=month value=$month>
              <input type=hidden name=year value=$year>
              </form>
              </td> ";

        echo "<td><form method=post action=display.php3><input type=submit value='>>'>
        <input type=hidden name=day value=$nextday>
        <input type=hidden name=month value=$nextmonth>
        <input type=hidden name=year value=$nextyear></form></td></tr></table>";

	$query = mysql_query("SELECT * FROM $mysql_tablename WHERE stamp >= \"$year-$month-$day 00:00:00\" AND stamp <= \"$year-$month-$day 23:59:59\" ORDER BY stamp", $database);

	while ($row = mysql_fetch_array($query))
	{
		echo "	<table cellpadding=5 cellspacing=5 border=1>
			<tr><td><b>Poster</td><td><b>Time</td><td><b>Subject</td></tr>
			<tr><td>$row[username]</a></td>
			<td>$row[stamp]</td>
			<td>$row[subject]</td></tr>
			<tr><td colspan=3>$row[description]</td></tr></table><p><hr width=50%><p>";
	}
	
	echo "<center><table><tr>";
	echo "<td><form method=post action=display.php3><input type=submit value='<<'>
	<input type=hidden name=day value=$lastday>
	<input type=hidden name=month value=$lastmonth>
	<input type=hidden name=year value=$lastyear></form></td>"; 

	echo "
	      <td><form method=post action=operate.php3>
		<input type=hidden name=month value=$month>
		<input type=hidden name=year value=$year>
		<input type=hidden name=day value=$day>
		<input type=submit name='action' value='Add item to calendar'>
	      </form></td>

	      <td><form method=post action=modify.php3>
	        <input type=hidden name=month value=$month>
		<input type=hidden name=day value=$day>
		<input type=hidden name=year value=$year>
		<input type=submit value='Delete or Modify'>
	      </form></td>

	      <td><form method=post action=welcome.php3>
	      <input type=submit value='Return to Calendar'>
	      <input type=hidden name=day value=$day>
	      <input type=hidden name=month value=$month>
	      <input type=hidden name=year value=$year>
	      </form>
	      </td> ";
	echo "<td><form method=post action=display.php3><input type=submit value='>>'>
	<input type=hidden name=day value=$nextday>
	<input type=hidden name=month value=$nextmonth>
	<input type=hidden name=year value=$nextyear></form></td></tr></table>";

?>
