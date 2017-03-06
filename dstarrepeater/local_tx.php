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
    <b>Last calls that accessed this Gateway</b>
    <table>
    <tr>
    <th><a class=tooltip href="#">Date & Time (UTC)</a></th>
    <th><a class=tooltip href="#">Call</a></th>
    <th><a class=tooltip href="#">Your Call</a></th>
    <th><a class=tooltip href="#">Repeater 1</a></th>
    <th><a class=tooltip href="#">Repeater 2</a></th>
    </tr>
<?php
// Headers.log sample:
// 0000000001111111111222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122
// 1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901
// 2012-05-29 20:31:53: Repeater header - My: PE1AGO  /HANS  Your: CQCQCQ    Rpt1: PI1DEC B  Rpt2: PI1DEC G  Flags: 00 00 00
//
    exec('(grep "Repeater header" '.$hdrLogPath.'|sort -r -k7,7|sort -u -k7,8|sort -r >/tmp/worked.log) 2>&1 &');
    $ci = 0;
    if ($WorkedLog = fopen("/tmp/worked.log",'r')) {
	while ($linkLine = fgets($WorkedLog)) {
            if(preg_match_all('/^(.{19}).*My: (.*).*Your: (.*).*Rpt1: (.*).*Rpt2: (.*).*Flags: (.*)$/',$linkLine,$linx) > 0){
		$ci++;
		if($ci > 1) { $ci = 0; }
                print "<tr>";
		$QSODate = date("d-M-Y H:i:s", strtotime(substr($linx[1][0],0,19)));
                $MyCall = str_replace(' ', '', substr($linx[2][0],0,8));
                $MyId = str_replace(' ', '', substr($linx[2][0],9,4));
                $YourCall = str_replace(' ', '&nbsp;', substr($linx[3][0],0,8));
                $Rpt1 = str_replace(' ', '&nbsp;', substr($linx[4][0],0,8));
                $Rpt2 = str_replace(' ', '&nbsp;', substr($linx[5][0],0,8));
                print "<td align=left>$QSODate</td>";
                print "<td align=left width=\"180\"><a href=\"http://www.qrz.com/db/$MyCall\" target=\"_blank\" alt=\"Lookup Callsign\">$MyCall</a>";
                if($MyId) { print "/".$MyId."</td>"; } else { print "</td>"; }
                print "<td align=left width=\"100\">$YourCall</td>";
                print "<td align=left width=\"100\">$Rpt1</td>";
                print "<td align=left width=\"100\">$Rpt2</td>";
                print "</tr>\n";
	    }
	}
	fclose($WorkedLog);
    }
?>
</table>