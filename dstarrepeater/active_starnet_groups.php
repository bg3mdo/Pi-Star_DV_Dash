<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
$configs = array();

if ($configfile = fopen($gatewayConfigPath,'r')) {
        while ($line = fgets($configfile)) {
                list($key,$value) = split("=",$line);
                $value = trim(str_replace('"','',$value));
                if ($key != 'ircddbPassword' && strlen($value) > 0)
                $configs[$key] = $value;
        }

}
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
$rev="20141101";
$MYCALL=strtoupper($callsign);
?>
<b>Active Starnet Groups</b>
<table>
    <tr>
    <th width="120"><a class="tooltip" href="#">Callsign<span><b>Starnet Callsign</b></span></a></th>
    <th width="120"><a class="tooltip" href="#">LogOff<span><b>Starnet Logoff Callsign</b></span></a></th>
    <th><a class="tooltip" href="#">Info<span><b>Infotext</b></span></a></th>
    <th width="73"><a class="tooltip" href="#">UTOT<span><b>User TimeOut (min)</b>inactivity time after which a user will be disconnected</span></a></th>
    <th width="73"><a class="tooltip" href="#">GTOT<span><b>Group TimeOut (min)</b>inactivity time after which the group will be disconnected</span></a></th>
    </tr>
<?php
    $ci = 0;
    $i = 0;
    $stngrp = array();
    for($i = 1;$i < 6; $i++){
	$param="starNetCallsign" . $i;
	if(isset($configs[$param])) {
	    $gname = $configs[$param];
	    $stngrp[$gname] = $i;
	    $ci++;
	    if($ci > 1) { $ci = 0; }
	    print "<tr>";
	    print "<td align=\"left\">".str_replace(' ', '&nbsp;', substr($gname,0,8))."</td>";
	    $param="starNetLogoff" . $i;
	    if(isset($configs[$param])){ $output = str_replace(' ', '&nbsp;', substr($configs[$param],0,8)); print "<td align=\"left\">$output</td>";} else { print"<td>&nbsp;</td>";}
	    $param="starNetInfo" . $i;
	    if(isset($configs[$param])){ print "<td align=\"left\">$configs[$param]</td>";} else { print"<td>&nbsp;</td>";}
	    $param="starNetUserTimeout" . $i;
	    if(isset($configs[$param])){ print "<td align=\"center\">$configs[$param]</td>";} else { print"<td>&nbsp;</td>";}
	    $param="starNetGroupTimeout" . $i;
	    if(isset($configs[$param])){ print "<td align=\"center\">$configs[$param]</td>";} else { print"<td>&nbsp;</td>";}
	    print "</tr>\n";
	}
    }
?>
    </table>

<?php
	$groupsx = array();
	if ($starLog = fopen($starLogPath,'r')) {
		while($logLine = fgets($starLog)) {
		        preg_match_all('/^(.{19}).*(Adding|Removing) (.{8}).*StarNet group (.{8}).*$/',$logLine,$matches);
		        $groupz = substr($matches[4][0],0,8);
		        $member = substr($matches[3][0],0,8);
		        $action = substr($matches[2][0],0,8);
		        $date = $matches[1][0];
		        $guid = $stngrp[$groupz];
		        if ($action == 'Adding'){
		    	    $groupsx[$guid][$groupz][$member] = $date;
			}
		        elseif ($action == 'Removing'){
		            unset($groupsx[$guid][$groupz][$member]);
		        }
		}
		fclose($starLog);
	}

	//Clean the empty arrays from the multidimensional array
	$groupsx = array_map('array_filter', $groupsx);

	$active = 0;
	for ($i = 1;$i < 6; $i++) {
	    if (isset($groupsx[$i])) {
	    $active = $active + count($groupsx[$i]);
	    }
	}

	if ($active >= 1) {

	echo "<br />\n";
	echo "<b>Active Starnet Group Members</b>\n";
	echo "<table>\n";
	echo "<tr>\n";
	echo "<th><a class=tooltip href=\"#\">Date & Time (UTC)<span><b>Time of Login</b></span></a></th>\n";
	echo "<th width=\"153\"><a class=tooltip href=\"#\">Group<span><b>Starnet Callsign</b></span></a></th>\n";
	echo "<th width=\"153\"><a class=tooltip href=\"#\">Member<span><b>Callsign</b></span></a></th>\n";
	echo "</tr>\n";

	$ci = 0;
	$ulist = array();
	$glist = array();
	for($i = 1;$i < 6; $i++){
	    if(isset($groupsx[$i])){
		$glist = $groupsx[$i];
		foreach ($glist as $gcall => $ulist){
		    foreach ($ulist as $ucall => $ulogin){
			$ci++;
			if($ci > 1) { $ci = 0; }
			$ulogin = date("d-M-Y H:i:s", strtotime(substr($ulogin,0,19)));
			$groupz = str_replace(' ', '&nbsp;', substr($gcall,0,8));
			$ucall = str_replace(' ', '', substr($ucall,0,8));
			print "<tr>";
			print "<td align=\"left\">$ulogin</td>";
			print "<td align=\"left\">$groupz</td>";
			print "<td align=\"left\"><a href=\"http://www.qrz.com/db/$ucall\" target=\"_new\" alt=\"Lookup Callsign\">$ucall</a></td>";
			print "</tr>\n";
			}
		    }
		}
	    }
	echo "</table>\n";
	}

?>