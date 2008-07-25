<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

#print_r($_POST);
if(isset($_POST['save_settings']))
{
 $error = false;
 foreach($_POST as $name => $value)
 {
	if($name == 'save_settings') continue;
	$name = str_replace('_', ' ', $name);
	$value = $Blog->DB->real_escape_string(htmlspecialchars($value));
	$ret = $Blog->query("UPDATE {settings} SET value = '{$value}' WHERE name = '{$name}'");
	if(!$ret) { $error = $name; break; }
 }
 if($error)
 {
	echo "<p class=\"message error\">Leider ist beim Speichern ein Fehler aufgetreten. '{$error}' konnte nicht gespeichert werden:<br />\n{$Blog->error()}<br />\nBitte versuche es erneut.</p>";
 }
 else
 {
	echo "<p class=\"message\">Einstellungen erfolgreich gespeichert!</p>";
 }
}

?>
