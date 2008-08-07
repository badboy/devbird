<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

if(isset($_GET['action']) && isset($_GET['id']))
{
 $action = $_GET['action'];
 $id = $_GET['id'];
 switch($action)
 {
	case 'close':
		if(!$Blog->close_comment($id))
		{
			$error_msg = "Leider ist beim Sperren des Kommentars ein Fehler aufgetreten. <br />\n{$Blog->error()}<br />\nBitte versuche es erneut.";
		}
		break;
	case 'open':
		if(!$Blog->open_comment($id))
		{
			$error_msg = "Leider ist beim Entsperren des Kommentars ein Fehler aufgetreten. <br />\n{$Blog->error()}<br />\nBitte versuche es erneut.";
		}
		break;
	case 'delete':
		if(!$Blog->delete_comment($id))
		{
			$error_msg = "Leider ist beim Löschen des Kommentars ein Fehler aufgetreten. <br />\n{$Blog->error()}<br />\nBitte versuche es erneut.";
		}
		break;
 }
}

?>
