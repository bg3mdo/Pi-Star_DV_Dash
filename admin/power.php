<?php
//Load the Pi-Star Release file
$pistarReleaseConfig = '/etc/pistar-release';
$configPistarRelease = array();
$configPistarRelease = parse_ini_file($pistarReleaseConfig, true);
//Load the Version Info
require_once('config/version.php');

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/power.php") {
  // Sanity Check Passed.
  header('Cache-Control: no-cache');
  session_start();
?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
  <head>
    <meta name="robots" content="index" />
    <meta name="robots" content="follow" />
    <meta name="language" content="English" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="Author" content="Andrew Taylor (MW0MWZ)" />
    <meta name="Description" content="Pi-Star Power" />
    <meta name="KeyWords" content="Pi-Star" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Pi-Star - Digital Voice Dashboard - Power Control</title>
    <link rel="stylesheet" type="text/css" href="css/ircddb.css?version=1.3" />
  </head>
  <body>
  <div class="container">
  <div class="header">
  <div style="font-size: 8px; text-align: right; padding-right: 8px;">Pi-Star:<?php echo $configPistarRelease['Pi-Star']['Version']?> / Dashboard:<?php echo $version; ?></div>
  <h1>Pi-Star Digital Voice - Power Control</h1>
  <p style="padding-right: 5px; text-align: right; color: #ffffff;">
    <a href="/" style="color: #ffffff;">Dashboard</a> |
    <a href="/admin/" style="color: #ffffff;">Admin</a> |
    <a href="/admin/update.php" style="color: #ffffff;">Update</a> |
    <a href="/admin/config_backup.php" style="color: #ffffff;">Backup/Restore</a> |
    <a href="/admin/configure.php" style="color: #ffffff;">Config</a>
  </p>
  </div>
  <div class="contentwide">
<?php if (!empty($_POST)) { ?>
  <table width="100%">
  <tr><th colspan="2">Power Control</th></tr>
  <?php
        if ( escapeshellcmd($_POST["action"]) == "reboot" ) {
                echo '<tr><td colspan="2" style="background: #000000; color: #00ff00;"><br /><br />Reboot command has been sent to your Pi,
                        <br />please wait 40 secs for it to reboot.<br />
                        <br />You will be re-directed back to the
                        <br />dashboard automatically in 40 seconds.<br /><br /><br />
                        <script language="JavaScript" type="text/javascript">
                                setTimeout("location.href = \'/index.php\'",40000);
                        </script>
                        </td></tr>';
                system('sudo mount -o remount,ro / > /dev/null &');
                exec('sleep 5 && sudo shutdown -r now > /dev/null &');
                };
        if ( escapeshellcmd($_POST["action"]) == "shutdown" ) {
                echo '<tr><td colspan="2" style="background: #000000; color: #00ff00;"><br /><br />Shutdown command has been sent to your Pi,
                        <br /> please wait 30 secs for it to fully shutdown<br />before removing the power.<br /><br /><br /></td></tr>';
                system('sudo mount -o remount,ro / > /dev/null &');
                exec('sleep 5 && sudo shutdown -h now > /dev/null &');
                };
  ?>
  </table>
<?php } else { ?>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <table width="100%">
  <tr>
    <th colspan="2">Power Control</th>
  </tr>
  <tr>
    <td align="center">
      Reboot<br />
      <button style="border: none; background: none;" name="action" value="reboot"><img src="/images/reboot.png" border="0" alt="Reboot" /></button>
    </td>
    <td align="center">
      Shutdown<br />
      <button style="border: none; background: none;" name="action" value="shutdown"><img src="/images/shutdown.png" border="0" alt="Shutdown" /></button>
    </td>
  </tr>
  </table>
  </form>
<?php } ?>
  </div>
  <div class="footer">
  Pi-Star web config, &copy; Andy Taylor (MW0MWZ) 2014-<?php echo date("Y"); ?>.<br />
  Need help? Click <a style="color: #ffffff;" href="https://www.facebook.com/groups/pistar/" target="_new">here for the Support Group</a><br />
  Get your copy of Pi-Star from <a style="color: #ffffff;" href="http://www.mw0mwz.co.uk/pi-star/" target="_blank">here</a>.<br />
  <br />
  </div>
  </div>
  </body>
  </html>
<?php
}
?>
