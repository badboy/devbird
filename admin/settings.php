<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

function GetDir($path, $absolute = false)
{
    if ( substr($path, -1, 1) != '/' ) $path .= '/';
 
    $files = array();
    $dir = opendir($path);
 
    if ( $dir === false ) return false;
 
    while ( ( $file = readdir($dir) ) !== false )
    {
        if ( substr($file, 0, 1) != '.' && $file != '..' )
        {
            $files[] = ($absolute === false) ? $file : $path . $file;;
        }
    }
    return $files;
}

if(!$Blog->user->has_right('edit_settings'))
{
 echo "<p class=\"red_center\"><strong>Tut mir leid! Du bist nicht berechtigt die Einstellungen zu bearbeiten!</strong></p>";
 return;
}
if(isset($_GET['action']) && $_GET['action'] == "update")
  include "settings_save.php";

?>
<form action="<?=$Blog->adminrootpath ?>/settings.db/update" method="post">
<?
$settings = $Blog->get_settings();
while($setting = $settings->fetch_object())
{
 echo "<fieldset>\n";
 echo " <legend>{$setting->name}</legend>\n<p>";
 switch($setting->type)
 {
	case 1:
		echo "<input type=\"text\" style=\"width: 300px;\" name=\"{$setting->name}\" value=\"{$setting->value}\"/>";
		break;
	case 2:
		if(preg_match("/^(\d+)-(\d+)$/", $setting->possibilities, $found))
		{
			$start = $found[1];
			$end = $found[2];
			$out = "<select name=\"{$setting->name}\" style=\"width: 200px;\">\n";
			for($i = $start; $i <= $end; $i++)
				$out .= " <option value=\"{$i}\"".($i == $setting->value ? ' selected="selected"' : '').">{$i}</option>\n";
			$out .= "</select>\n";
			echo $out;
		}
		elseif(preg_match("/^(.+)(\|(.+))+$/", $setting->possibilities, $found))
		{
			unset($found[0]);
			$out = "<select name=\"{$setting->name}\" style=\"width: 200px;\">\n";
			foreach($found as $charset)
			{
				if(empty($found) || $charset[0] == '|') continue;
				$out .= " <option value=\"{$charset}\"".($charset == $setting->value ? ' selected="selected"' : '').">{$charset}</option>\n";
			}
			$out .= "</select>\n";
			echo $out;
		}
		else echo "error with setting type {$setting->type}. Possibilites don't match";
		break;
	case 3:
		if(preg_match('/^\$dir:\\/(.+)\\/$/', $setting->possibilities, $found))
		{
			$dirname = $found[1];
			$out = "<select name=\"{$setting->name}\" style=\"width: 200px;\">\n";
			$dir = GetDir($Blog->blogroot."/{$dirname}/", true);
			sort($dir);
			foreach($dir as $file)
			{
				if(is_dir($file))
				{
					preg_match("/.+\/(.+)$/", $file, $result);
					$file = $result[1];
					$out .= " <option value=\"{$file}\"".($file == $setting->value ? ' selected="selected"' : '').">{$file}</option>\n";
				}
			}
			$out .= "</select>\n";
			echo $out;
		}
		elseif(preg_match('/^\$dir:\\/(.+)\\/ -(.+)$/', $setting->possibilities, $found))
		{
			$dirname = $found[1];
			$strip = $found[2];
			$out = "<select name=\"{$setting->name}\" style=\"width: 200px;\">\n";
			$dir = GetDir($Blog->blogroot."/{$dirname}/", true);
			sort($dir);
			foreach($dir as $file)
			{
				if(!is_dir($file))
				{
					preg_match("/.+\/(.+)$/", $file, $result);
					$file = $result[1];
					$file = str_replace($strip, '', $file);
					$out .= " <option value=\"{$file}\"".($file == $setting->value ? ' selected="selected"' : '').">{$file}</option>\n";
				}
			}
			$out .= "</select>\n";
			echo $out;
		}
		else echo "error with setting type {$setting->type}. Possibilites don't match";
		break;
	default:
		echo "unknown setting type {$setting->type}";
		break;
 }
 echo "<p>\n<p class=\"desc\">{$setting->description}</p>\n";
 echo "</fieldset>\n";
}
?>
<br />
<p style="text-align: center;"><input type="submit" value="Speichern" name="save_settings" />
</form>
