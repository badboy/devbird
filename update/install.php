<?
$cur_site = 'database';
include('header.php');
function die_save($msg)
{
 echo "<p>{$msg}</p>\n";
 echo "<p><a href=\"check.php\">Zurück</a></p>\n";
 include 'footer.php';
 exit(1);
}

define('IN_CORE', true);
include('../config/config.php');
?>
<h1>Datenbank anpassen</h1>

<?
 $DB = @new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);
 $db_prefix = TABLE_PREFIX;
 if (mysqli_connect_errno() != 0)
 {
  die_save("<p>Konnte nicht zur Datenbank verbinden. Sind die Daten vielleicht falsch?</p><p><a href=\"settings.php\">Zurück</a></p>");
 }

 // ändere Tabellen
 include 'sql_querys.php';
 if($DB->query($add_pages))
 {
   echo '<p style="color:green;">Tabelle \'', $db_prefix, 'pages\' erfolgreich erfolgreich angelegt!</p>', "\n";
 }
 else
 {
   echo '<p style="color:red;">Ändern der Tabelle \'', $db_prefix, 'news\' ist fehlgeschlagen! ('.$DB->error.')</p>', "\n";
   $error = true;
 }

 if($error) die_save('Beim Ändern der Tabellen in der Datenbank scheint ein Fehler aufgetreten zu sein. So kann Devbird aber nicht aktualisiert werden.');

?>
<br /><p><a href="ready.php">Weiter >></a></p>
<?
include('footer.php'); 
?>
