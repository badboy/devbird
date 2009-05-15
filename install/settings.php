<?
$cur_site = 'settings';
include('header.php');

$dir = getcwd();
chdir("..");
$basedir = dirname(dirname($_SERVER['PHP_SELF']));
$uri = 'http://'.$_SERVER['HTTP_HOST'].$basedir;
$path_dir = getcwd();
?>
<h1>Einstellungen</h1>
<form action="install.php" method="post">
<h2 style="text-align: left;">Allgemeine Informationen</h2>
 <p><input class="input" type="text" name="all_blogname" /> Name des Blogs</p>
 <p><input class="input" type="text" name="all_blogdesc" /> Kurzbeschreibung des Blogs</p>

<br />
<h2 style="text-align: left;">Pfade</h2>
 <p><input class="input" type="text" name="path_url" value="<?=$uri ?>" /> URL zum Blog</p>
 <p><input class="input" type="text" name="path_root" value="<?=$path_dir ?>" /> Pfad zum Blogverzeichnis</p>

<br />
<h2 style="text-align: left;">Admin-Account anlegen</h2>
 <p><input class="input" type="text" name="user_name" /> Name</p>
 <p><input class="input" type="text" name="user_mail" /> eMail</p>
 <p><input class="input" type="password" name="user_password" /> Passwort (min. 5 Zeichen)</p>
 <p><input class="input" type="password" name="user_again" /> Passwort wiederholen</p>

<br />
<h2 style="text-align: left;">Datenbank</h2>
 <p><input class="input" type="text" name="db_host" value="localhost" /> Host (meist localhost)</p>
 <p><input class="input" type="text" name="db_db" /> Datenbank</p>
 <p><input class="input" type="text" name="db_user" /> User</p>
 <p><input class="input" type="password" name="db_password" /> Passwort</p>
 <p><input class="input" type="text" name="db_prefix" value="db_" /> Tabellenprefix</p>

<br />
<p>Es müssen alle Felder ausgefüllt werden.</p>
<br />
<input style="clear: both;" type="submit" name="save_settings" value="Weiter >>" />
</form>
<? include('footer.php'); ?>
