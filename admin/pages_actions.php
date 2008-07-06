<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

if(isset($_GET['action']) && isset($_GET['id']))
{
 $action = $_GET['action'];
 $id = $_GET['id'];
 switch($action)
 {
	case 'close':
		$Blog->close_page($id);
		break;
	case 'open':
		$Blog->open_page($id);
		break;
	case 'delete':
		if(!$Blog->delete_page($_GET['id']))
		{
			$error_msg = "Leider ist beim Löschen ein Fehler aufgetreten. Die Unterseite konnte nicht gelöscht werden:<br />\n{$Blog->error()}<br />\nBitte versuche es erneut.";
		}
		break;
 }
}

?>
