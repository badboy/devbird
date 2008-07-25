<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

$hide_form = isset($hide_form) ? $hide_form : '';
$hide_rights = isset($hide_rights) ? $hide_rights : false;

$user_name = isset($user_name) ? $user_name : '';
$user_mail = isset($user_mail) ? $user_mail : '';

$right_sett = isset($right_sett) ? $right_sett : false;
$right_art = isset($right_art) ? $right_art : false;
$right_admin = isset($right_admin) ? $right_admin : false;
$right_comm = isset($right_comm) ? $right_comm : false;
$use_cookies = isset($use_cookie) ? $use_cookie : false;

if(isset($_POST['user_save']))
	include 'user_actions.php';

if($_GET['action'] == 'edit' && isset($_GET['id']))
{
	if($_GET['id'] != $Blog->user->id && !$Blog->user->has_right('edit_settings'))
	{
		echo "<p class=\"red_center\"><strong>";
		echo "Du darfst die Daten dieses Users nicht bearbeiten<br />\n";
		echo "<a href=\"{$Blog->adminrootpath}/user_formular.db/{$Blog->user->id}/edit\">Bearbeite</a> stattdessen doch
 deine.";
		echo "</strong></p>\n";
	}
	else
	{
		define('IN_USER_FORMULAR', true);
		$user = $Blog->get_user($_GET['id']);

		$user_name = htmlspecialchars($user->name);
		$user_mail = htmlspecialchars($user->mail);
		$rights = $user->rights;

		if(!$Blog->user->has_right('edit_settings')) $hide_rights = true;
		$right_sett = has_right($rights, 'edit_settings');
		$right_art = has_right($rights, 'edit_articles');
		$right_admin = has_right($rights, 'visit_admin');
		$right_comm = has_right($rights, 'comment_articles');

		$use_cookies = $user->use_cookies == 1;

		include('user_form.php');
	}
}
elseif($_GET['action'] == 'new' && isset($_GET['id']) && $_GET['id'] == 0)
{
		define('IN_USER_FORMULAR', true);
		include('user_form.php');
}
else
{
 echo <<<EOF
<p>Oh...scheint, als ob du hier falsch bist.<br />
Schau doch mal <a href="{$Blog->adminrootpath}">hier</a> vorbei.</p>
EOF;
}
?>
<br />
<p><a href="<?=$Blog->adminrootpath ?>/user.db">Zurück</a></p>
