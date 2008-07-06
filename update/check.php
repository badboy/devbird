<?
$cur_site = 'check';
include('header.php');
$check = '<img src="images/16-em-check.png" alt="checked" />';
$cross = '<img src="images/16-em-cross.png" alt="red x" />';
?>
<h1>Check</h1>
<p>
Um Devbird zu aktualisieren, brauchst du nicht viel tun.<br />
Du musst nur sichergehen, dass die Konfiguration stimmt und für den Updater lesbar ist.<br />
</p>
<br />
<?
if(!is_readable('../config/config.php'))
{
?>
<p><?=$cross ?> Die Konfigurationsdatei kann nicht gelesen werden. Bist du sicher, das Devbird hier installiert ist?
<? } else {?>
<p><?=$check ?> Die Konfigurationsdatei kann ohne Probleme gelesen werden. Gehe nun sicher, dass die Angaben stimmen.</p>
<? 
define('IN_CORE', true);
include('../config/config.php');
?>
Hostname: <strong><?=$mysql_hostname; ?></strong><br />
Username: <strong><?=$mysql_username; ?></strong><br />
Password: <strong><?=$mysql_password; ?></strong><br />
Datenbank: <strong><?=$mysql_database; ?></strong><br />
Prefix: <strong><?=TABLE_PREFIX ?></strong>
<br /><br /><p><a href="install.php">Stimmt alles? Dann geht's hier weiter >></a></p>
<? } ?>
<? include('footer.php'); ?>
