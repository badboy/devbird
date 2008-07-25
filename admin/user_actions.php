<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

 if($_GET['id'] == 0 && $_GET['action'] == 'new')
	$action = 'new';
 elseif($_GET['id'] != 0 && $_GET['action'] == 'edit')
	$action = 'edit';
 else return;

# if(!isset($_POST['user_id']) || !isset($_POST['user_name']) ||

 $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
 $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
 $user_mail = isset($_POST['user_mail']) ? $_POST['user_mail'] : '';

 $user_pw = $_POST['user_pw'];
 $user_pw_again = $_POST['user_pw_again'];
 if(isset($_POST['user_right_disabled'])) $rights_disabled = true;
 $user_right_sett = isset($_POST['user_right_sett']) ? ($_POST['user_right_sett'] == 'on') : false;
 $user_right_art = isset($_POST['user_right_art']) ? ($_POST['user_right_art'] == 'on') : false;
 $user_right_admin = isset($_POST['user_right_admin']) ? ($_POST['user_right_admin'] == 'on') : false;
 $user_right_comm = isset($_POST['user_right_comm']) ? ($_POST['user_right_comm'] == 'on') : false;
 $user_right_disabled = isset($_POST['user_right_disabled']) ? ($_POST['user_right_disabled'] == 'true') : false;

 $use_cookies = isset($_POST['user_cookies']) ? ($_POST['user_cookies'] == 'on') : false;

 if(($action == 'edit' && empty($user_id)) || empty($user_name) || empty($user_mail))
 {
	$error_msg = "Bitte alle Felder ausfüllen!";
	return;
 }

if(!empty($user_pw)) {
 if(strlen($user_pw) < 5)
 {
	$error_msg = "Passwort muss mindestens 5 Zeichen haben!";
	return;
 }

 if($user_pw != $user_pw_again)
 {
	$error_msg = "Passwort bitte zweimal angeben!";
	return;
 }
}

 $rights = 0;
 if($user_right_sett) $rights += 8;
 if($user_right_art) $rights += 4;
 if($user_right_admin) $rights += 2;
 if($user_right_comm) $rights += 1;
 if($user_right_disabled) $rights = -1;

 if($Blog->insert_user($user_id, $user_name, $user_mail, $user_pw, $use_cookies, $rights, $user_right_disabled))
 {
   $done_msg = "Benutzer erfolgreich gespeichert!";
   return;
 }
 else
 {
   $error_msg = "Fehler beim Speichern des Benutzers!<br />\n{$Blog->error()}";
   return;
 }

?>
