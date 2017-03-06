<?php
include('wifi/phpincs.php');
$output = $return = 0;
$page = $_GET['page'];


echo '<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"xmlns:v="urn:schemas-microsoft-com:vml">
<html>
<head>
<LINK REL="stylesheet" type="text/css" href="css/ircddb.css"></LINK>
<LINK REL="stylesheet" type="text/css" href="wifi/styles.css"></LINK>
<script type="text/Javascript" src="wifi/functions.js"></script>
<title>WiFi Configuration Portal</title>
</head>
<body>';
switch($page) {
	case "wlan0_info":
		//Declare a pile of variables
		$strIPAddress = NULL;
		$strNetMask = NULL;
		$strRxPackets = NULL;
		$strRxBytes = NULL;
		$strTxPackets = NULL;
		$strTxBytes = NULL;
		$strSSID = NULL;
		$strBSSID = NULL;
		$strBitrate = NULL;
		$strTxPower = NULL;
		$strLinkQuality = NULL;
		$strSignalLevel = NULL;

		exec('ifconfig wlan0',$return);
		exec('iwconfig wlan0',$return);
		$strWlan0 = implode(" ",$return);
		$strWlan0 = preg_replace('/\s\s+/', ' ', $strWlan0);
		preg_match('/HWaddr ([0-9a-f:]+)/i',$strWlan0,$result);
		$strHWAddress = $result[1];
		if(strpos($strWlan0, "UP") !== false && strpos($strWlan0, "RUNNING") !== false) {
			$strStatus = '<span style="color:green">Interface is up</span>';
				//Cant get these unless we are connected :)
				preg_match('/inet addr:([0-9.]+)/i',$strWlan0,$result);
				$strIPAddress = $result[1];
				preg_match('/Mask:([0-9.]+)/i',$strWlan0,$result);
				$strNetMask = $result[1];
				preg_match('/RX packets:(\d+)/',$strWlan0,$result);
				$strRxPackets = $result[1];
				preg_match('/TX packets:(\d+)/',$strWlan0,$result);
				$strTxPackets = $result[1];
				preg_match('/RX Bytes:(\d+ \(\d+.\d+ [K|M|G]iB\))/i',$strWlan0,$result);
				$strRxBytes = $result[1];
				preg_match('/TX Bytes:(\d+ \(\d+.\d+ [K|M|G]iB\))/i',$strWlan0,$result);
				$strTxBytes = $result[1];
				preg_match('/Access Point: ([0-9a-f:]+)/i',$strWlan0,$result);
				$strBSSID = $result[1];
				preg_match('/Bit Rate=([0-9]+ Mb\/s)/i',$strWlan0,$result);
				$strBitrate = $result[1];
				preg_match('/Tx-Power=([0-9]+ dBm)/i',$strWlan0,$result);
				$strTxPower = $result[1];
				preg_match('/ESSID:\"([a-zA-Z0-9_\s]+)\"/i',$strWlan0,$result);
				$strSSID = str_replace('"','',$result[1]);
				preg_match('/Link Quality=([0-9]+\/[0-9]+)/i',$strWlan0,$result);
				$strLinkQuality = $result[1];
				preg_match('/Signal Level=(-[0-9]+ dBm)/i',$strWlan0,$result);
				$strSignalLevel = $result[1];
		} else {
			$strStatus = '<span style="color:red">Interface is down</span>';
		}
		if(isset($_POST['ifdown_wlan0'])) {
			exec('ifconfig wlan0 | grep -i running | wc -l',$test);
			if($test[0] == 1) {
				exec('sudo ifdown wlan0',$return);
			} else {
				echo 'Interface already down';
			}
		} elseif(isset($_POST['ifup_wlan0'])) {
			exec('ifconfig wlan0 | grep -i running | wc -l',$test);
			if($test[0] == 0) {
				exec('sudo ifup wlan0',$return);
			} else {
				echo 'Interface already up';
			}
		}
	echo '<div class="infobox">
<form action="'.$_SERVER['PHP_SELF'].'?page=wlan0_info" method="POST">
<input type="submit" value="ifdown wlan0" name="ifdown_wlan0" />
<input type="submit" value="ifup wlan0" name="ifup_wlan0" />
<input type="button" value="Refresh" onclick="document.location.reload(true)" />
<input type="button" value="Configure WiFi" name="wpa_conf" onclick="document.location=\'?page=\'+this.name" />
</form>
<div class="infoheader">Wireless Information and Statistics</div>
<div id="intinfo"><div class="intheader">Interface Information</div>
Interface Name : wlan0<br />
Interface Status : ' . $strStatus . '<br />
IP Address : ' . $strIPAddress . '<br />
Subnet Mask : ' . $strNetMask . '<br />
Mac Address : ' . $strHWAddress . '<br />
<br />
<div class="intheader">Interface Statistics</div>
Received Packets : ' . $strRxPackets . '<br />
Received Bytes : ' . $strRxBytes . '<br />
Transferred Packets : ' . $strTxPackets . '<br />
Transferred Bytes : ' . $strTxBytes . '<br />
<br />
</div>
<div id="wifiinfo">
<div class="intheader">Wireless Information</div>
Connected To : ' . $strSSID . '<br />
AP Mac Address : ' . $strBSSID . '<br />
<br />
Bitrate : ' . $strBitrate . '<br />
Transmit Power : ' . $strTxPower .'<br />
<br />
Link Quality : ' . $strLinkQuality . '<br />
Signal Level : ' . $strSignalLevel . '<br />
<br />
<br />
<br />
<br />
</div>
</div>
<div class="intfooter">Information provided by ifconfig and iwconfig</div>';
	break;

	case "wpa_conf":
		exec('sudo cat /etc/wpa_supplicant/wpa_supplicant.conf',$return);
		$ssid = array();
		$psk = array();
		foreach($return as $a) {
			if(preg_match('/SSID/i',$a)) {
				$arrssid = explode("=",$a);
				$ssid[] = str_replace('"','',$arrssid[1]);
			}
			if(preg_match('/\#psk/i',$a)) {
				$arrpsk = explode("=",$a);
				$psk[] = str_replace('"','',$arrpsk[1]);
			}
		}
		$numSSIDs = count($ssid);
		$output = '<input type="button" value="WiFi Info" name="wlan0_info" onclick="document.location=\'?page=\'+this.name" /><br />
<form method="POST" action="'.$_SERVER['PHP_SELF'].'?page=wpa_conf" id="wpa_conf_form"><input type="hidden" id="Networks" name="Networks" /><div class="network" id="networkbox">';
		for($ssids = 0; $ssids < $numSSIDs; $ssids++) {
			$output .= '<div id="Networkbox'.$ssids.'" class="NetworkBoxes">Network '.$ssids.' <input type="button" value="Delete" onClick="DeleteNetwork('.$ssids.')" /></span><br />
<span class="tableft" id="lssid0">SSID :</span><input type="text" id="ssid0" name="ssid'.$ssids.'" value="'.$ssid[$ssids].'" onkeyup="CheckSSID(this)" /><br />
<span class="tableft" id="lpsk0">PSK :</span><input type="password" id="psk0" name="psk'.$ssids.'" value="'.$psk[$ssids].'" onkeyup="CheckPSK(this)" /><br /><br /></div>';
		}
		$output .= '</div><div class="infobox"><input type="submit" value="Scan for Networks (10 secs)" name="Scan" /><input type="button" value="Add Network" onClick="AddNetwork();" /><input type="submit" value="Save (and connect)" name="SaveWPAPSKSettings" onmouseover="UpdateNetworks(this)" />
</form></div>';

	echo $output;
	echo '<script type="text/Javascript">UpdateNetworks()</script>';

	if(isset($_POST['SaveWPAPSKSettings'])) {
		$config = 'ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev
update_config=1

';
		$networks = $_POST['Networks'];
		for($x = 0; $x < $networks; $x++) {
			$network = '';
			$ssid = escapeshellarg($_POST['ssid'.$x]);
			$psk = escapeshellarg($_POST['psk'.$x]);
			exec('wpa_passphrase '.$ssid.' '.$psk,$network);
			foreach($network as $b) {
				$config .= "$b"."\n";
			}
		}
		if (strpos($config, 'Passphrase must be 8..63 characters') !== false) { echo "Wifi settings failed to be updated due to invalid PSK"; }
		else {
			exec("echo '$config' > /tmp/wifidata",$return);
			system('sudo mount -o remount,rw / && sudo cp /tmp/wifidata /etc/wpa_supplicant/wpa_supplicant.conf',$returnval);
			if($returnval == 0) {
				echo "<br />\n";
				echo "Wifi Settings Updated Successfully\n";
				system('sudo ifdown wlan0 && sleep 3 && sudo ifup wlan0',$returnval);
			} else {
				echo "Wifi settings failed to be updated";
			}
		}

	} elseif(isset($_POST['Scan'])) {
		$return = '';
		exec('ifconfig wlan0 | grep -i running | wc -l',$test);
		sleep(2);
		exec('sudo wpa_cli scan',$return);
		sleep(5);
		exec('sudo wpa_cli scan_results',$return);
		for($shift = 0; $shift < 4; $shift++ ) {
			array_shift($return);
		}
		echo "<br />\n";
		echo "Networks found : <br />\n";
		echo "<table>\n";
		echo "<tr><th>Connect</th><th>SSID</th><th>Channel</th><th>Signal</th><th>Security</th></tr>";
		foreach($return as $network) {
			$arrNetwork = preg_split("/[\t]+/",$network);
			$bssid = $arrNetwork[0];
			$channel = ConvertToChannel($arrNetwork[1]);
			$signal = $arrNetwork[2] . " dBm";
			$security = ConvertToSecurity($arrNetwork[3]);
			$ssid = $arrNetwork[4];

			echo '<tr>';
			echo '<td style="text-align: left;"><input type="button" value="Connect" onClick="AddScanned(\''.$ssid.'\')" /></td>';
			echo '<td style="text-align: left;">'.$ssid.'</td>';
			echo '<td style="text-align: left;">Channel '.$channel.'</td>';
			echo '<td>'.$signal.'</td>';
			echo '<td style="text-align: left;">'.$security.'</td>';
			echo '</tr>'."\n";

		}
		echo "</table>\n";
	}

	break;
}


echo '
</body>
</html>';
?>
