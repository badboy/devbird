<?
$cur_site = 'database';
include('header.php');
function die_save($msg)
{
 echo "<p>{$msg}</p>\n";
 echo "<p><a href=\"settings.php\">Zurück</a></p>\n";
 include 'footer.php';
 exit(1);
}

?>
<h1>Installation</h1>
<?
if(!isset($_POST['save_settings']))
{
?>
<p>Bitte benutzte doch das Formular. ;)</p>
<p><p><a href="index.php">Zum Anfang</a></p>
<?
}
else {#if(isset($_POST['save_settings'])) {
 $all_blogname = htmlspecialchars($_POST['all_blogname']);
 $all_blogdesc = htmlspecialchars($_POST['all_blogdesc']);
 $path_url = htmlspecialchars($_POST['path_url']);
 $path_root = htmlspecialchars($_POST['path_root']);
 $user_name = htmlspecialchars($_POST['user_name']);
 $user_mail = htmlspecialchars($_POST['user_mail']);
 $user_password = htmlspecialchars($_POST['user_password']);
 $user_again = htmlspecialchars($_POST['user_again']);
 $db_host = htmlspecialchars($_POST['db_host']);
 $db_db = htmlspecialchars($_POST['db_db']);
 $db_user = htmlspecialchars($_POST['db_user']);
 $db_password = htmlspecialchars($_POST['db_password']);
 $db_prefix = htmlspecialchars($_POST['db_prefix']);

if(empty($all_blogname) || empty($all_blogdesc) || empty($path_url) || empty($path_root) || empty($user_name) || empty($user_mail) || empty($user_password) || empty($user_again) || empty($db_host) || empty($db_db) || empty($db_user) || empty($db_prefix))
{
 echo "<p>Bitte alle Felder ausfüllen</p><p><a href=\"settings.php\">Zurück</a></p>";
}
else
{
 $config_content = <<<CONFIG_CONTENT
<?
if(!defined('IN_CORE')) die('Direct Access is not allowed!');

define('TABLE_PREFIX', '{$db_prefix}');
##mysql data
\$mysql_hostname = '{$db_host}';
\$mysql_username = '{$db_user}';
\$mysql_password = '{$db_password}';
\$mysql_database = '{$db_db}';

$password_salt = mt_rand();
?>
CONFIG_CONTENT;

 $DB = @new mysqli($db_host, $db_user, $db_password, $db_db);
 if (mysqli_connect_errno() != 0)
 {
  die_save("<p>Konnte nicht zur Datenbank verbinden. Sind die Daten vielleicht falsch?</p><p><a href=\"settings.php\">Zurück</a></p>");
 }

 // wenn Verbindung OK => schreibe Config-Datei
 if(!@file_put_contents('../config/config.php', $config_content)) die_save('Konfigurationsdatei konnte nicht geschrieben werden.');
 echo '<p style="color:green;">Konfigurationsdatei erfolgreich gespeichert!</p>', "\n";

 // erstelle Tabellen
 include 'sql_querys.php';
 if(count($sql_querys) != 6) die_save('Achtung! Die Datei \'sql_querys.php\' wurde scheinbar verändert! Bitte benutze nur die Originaldatei!');
 $i = 0;
 $error = false;
 foreach($drop_tables as $single_drop)
 {
   if(!$DB->query($single_drop))
   {
     echo '<p>Konnte Tabelle ', $db_prefix, $sql_order[$i], ' nicht löschen!</p>';
   }
 }
 foreach($sql_querys as $single_query)
 {
   if($DB->query($single_query))
   {
     echo '<p style="color:green;">Tabelle \'', $db_prefix, $sql_order[$i], '\' erfolgreich erstellt!</p>', "\n";
   }
   else
   {
     echo '<p style="color:red;">Erstellung der Tabelle \'', $db_prefix, $sql_order[$i], '\' ist fehlgeschlagen! ('.$DB->error.')</p>', "\n";
     $error = true;
   }
   ++$i;
 }
 if($error) die_save('Beim Erstellen der Tabellen in der Datenbank scheint ein Fehler aufgetreten zu sein. So kann Devbird aber nicht installiert werden.');

 echo "<br />\n";
 echo "<p>Füge Daten in die Tabellen ein...</p>";

 if(!$DB->query($settings_insert))
 {
   die_save('Grundeinstellungen konnten nicht in die Datenbank geschrieben werden! ('.$DB->error.')');
 }
 echo '<p style="color:green;">Grundeinstellungen erfolgreich in die Datenbank gespeichert!</p>', "\n";

 if(!ereg("^.+@.+\\..+$", $user_mail))
 {
   die_save('eMail-Adresse des Admin-Accounts ist ungültig.');
 }

 if(strlen($user_password) <  5 || strlen($user_password) < 0 || strlen($user_password) > 100)
 {
   die_save('Passwort muss mindestens 5 Zeichen haben!');
 }
 if($user_password != $user_again)
 {
   die_save('Passwörter stimmen nicht überein!');
 }
 if($user_password == $user_name)
 {
   die_save('Passwort darf nicht gleich dem Namen sein!');
 }
 
 $user_name = $DB->real_escape_string($user_name);
 $user_mail = $DB->real_escape_string($user_mail);
 
 if(!$DB->query($user_insert))
 {
   die_save('Admin-Account konnte nicht in die Datenbank geschrieben werden! ('.$DB->error.')');
 }
 echo '<p style="color:green;">Admin-Account erfolgreich in die Datenbank gespeichert!</p>', "\n"; 

 if(!$DB->query($first_article))
 {
   die_save("Fehler beim Eintragen des Testartikels.<br />\n{$DB->error}");
 }
 if(!$DB->query($first_comment))
 {
   die_save("Fehler beim Eintragen des Testkommentars.<br />\n{$DB->error}");
 }
 if(!$DB->query($first_links))
 {
   die_save("Fehler beim Eintragen der ersten Links.<br />\n{$DB->error}");
 }

 if(!@chmod('../config/config.php', 0664))
 {
   echo '<p style="color:red;">Die Zugriffsrechte der Konfiguarionsdatei konnten nicht angepasst werden. Bitte erledige das manuell. Devbird muss die Datei nur lesen können.</p>', "\n";
 }

?>
<br /><p><a href="ready.php">Weiter >></a></p>
<?
} }
include('footer.php'); 
?>
