<?
$cur_site = 'check';
include('header.php');

$check = '<img src="images/16-em-check.png" alt="checked" />';
$cross = '<img src="images/16-em-cross.png" alt="red x" />';

$mysqli_exists = class_exists('mysqli');
$php_version = phpversion();
$php_right = $php_version[0] == '5';

$config_writable = is_writable('../config/config.php');

if($php_right && $mysqli_exists && $config_writable)
	$all_functions = true

?>
<h1>Check</h1>
<p>
Um Devbird zu installieren, sind einige Vorraussetzungen nötig.<br />
In den nachfolgenden Tabellen siehst du was bei dir vorhanden ist.<br />
Sollte in einer Spalte ein rotes X (<?=$cross ?>) erscheinen, kann Devbird nicht installiert werden. Dann muss das Feature erst aktiviert werden.<br />
<br />
<h2>PHP Funktionen und Module</h2>
<table style="width: 30%">
 <thead>
   <th>Name</th>
   <th>Verfügbar</th>
 </thead>
 <tbody>
  <tr>
   <td>PHP 5.x.x</td>
   <td><?=$php_right ? $check : $cross ?> (deine Version: <?=$php_version ?>)</td>
  </tr>
  <tr>
   <td>mysqli</td>
   <td><?=$mysqli_exists ? $check : $cross ?></td>
  </tr>
  <tr>
   <td>mod_rewrite</td>
   <td>Bitte selber überprüfen</td>
  </tr>
 </tbody>
</table>

<br />
<h2>Dateien</h2>
<table>
 <thead>
   <th>Name</th>
   <th style="padding-right:20px;">Verfügbar</th>
   <th>Info</th>
 </thead>
 <tbody>
  <tr>
   <td>config/config.php</td>
   <td align="center"><?=$config_writable ? $check : $cross ?></td>
   <td><?=$config_writable ? '' : 'Der Installer muss die config.php bearbeiten können. Ändere dafür den chmod der Datei.' ?></td>
  </tr>
 </tbody>
</table>

<? if($all_functions) { ?>
<br /><p><a href="settings.php">Weiter >></a></p>
<? } ?>
<? include('footer.php'); ?>
