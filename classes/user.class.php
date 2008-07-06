<?
class User
{
/**
 4. User darf Einstellungen bearbeiten			2 ^ 3 = 8	edit_settings
 3. User darf Beiträge schreiben/löschen/bearbeiten	2 ^ 2 = 4	edit_articles
 2. User darf Adminmenü betreten			2 ^ 1 = 2	visit_admin
 1. User darf kommentieren				2 ^ 0 = 1	comment_articles
 0. User darf garnichts (ausgeschaltet|geblockt)	0

 alle zusammen: 8+4+2+1 = 15
*/
	var $name;
	var $email;
	var $id;

	private $rights;
	private $devbird;
	private $right_strings = array('edit_settings' => 8, 'edit_articles' => 4, 'visit_admin' => 2, 'comment_articles' => 1, 'nothing' => 0);

	function __construct($devbird)
	{
		$this->devbird = $devbird;
	}

	function is_online()
	{
#		print_r($_COOKIE);
		if(isset($_SESSION['logged_in']) && isset($_SESSION['userid']) && isset($_SESSION['username']) && isset($_SESSION['last_activity']) && isset($_SESSION['cookievalue']))
		{
			$now = time();
			$last = $_SESSION['last_activity'];
			if(($now - $last) >= 3600)
			{
				if(isset($_COOKIE['devbird_user_name']) && isset($_COOKIE['devbird_user_pw']))
				{
					$username = $this->devbird->DB->real_escape_string(base64_decode($_COOKIE['devbird_user_name']));
					$password = $this->devbird->DB->real_escape_string(base64_decode($_COOKIE['devbird_user_pw']));
					$ret =  $this->login($username, $password, true);
					return $ret;
				}
				else
				{
					$_SESSION['logged_in'] = false;
					$_SESSION['userid'] = false;
					$_SESSION['username'] = false;
					$_SESSION['last_activity'] = false;
					$_SESSION['cookievalue'] = false;
					return false;
				}
			}
			$userid = $this->devbird->DB->real_escape_string($_SESSION['userid']);
			$username = $this->devbird->DB->real_escape_string($_SESSION['username']);
			$cookievalue = $this->devbird->DB->real_escape_string($_SESSION['cookievalue']);

			$sql = "SELECT * FROM {user} WHERE `id` = '{$userid}' AND `name` = '{$username}' AND `cookie_value` = '{$cookievalue}'";
			$res = $this->devbird->query($sql);
			if(!$res) return false;
			if($res->num_rows != 1) return false;
			$userinfo = $res->fetch_object();

			$_SESSION['last_activity'] = time();
			$this->name = htmlspecialchars($username);
			$this->rights = $userinfo->rights;
			$this->email = $userinfo->mail;
			$this->id = $userinfo->id;
			return true;
		}
		elseif(isset($_COOKIE['devbird_user_name']) && isset($_COOKIE['devbird_user_pw']))
		{
			$username = $this->devbird->DB->real_escape_string(base64_decode($_COOKIE['devbird_user_name']));
			$password = $this->devbird->DB->real_escape_string(base64_decode($_COOKIE['devbird_user_pw']));
			$ret =  $this->login($username, $password, true);
			return $ret;
		}

		return false;
	}

	function login($name, $password, $nosha=false)
	{
		$name = $this->devbird->DB->real_escape_string($name);
		$password = $this->devbird->DB->real_escape_string($password);
		if(!$nosha)
			$password = sha1($password);
		#echo $password;
		$sql = "SELECT * FROM {user} WHERE `name`='{$name}' AND `password` = '{$password}' LIMIT 1";
		$res = $this->devbird->query($sql);
		if(!$res) return false;
		if($res->num_rows == 0) return false;
		$userinfo = $res->fetch_object();
		if($userinfo->rights == 0) return false;

		$new_cookie = md5(time().$userinfo->id);
		$sql = "UPDATE {user} SET `cookie_value`='{$new_cookie}' WHERE id='{$userinfo->id}'";
		$res = $this->devbird->query($sql);
		if(!$res) return false;

		if($userinfo->use_cookies == 1)
		{
			@setcookie('devbird_user_name', base64_encode($name), time()+60*60*24*30, '/');
			@setcookie('devbird_user_pw', base64_encode($password), time()+60*60*24*30, '/');
 		}


		$this->name = htmlspecialchars($name);
		$this->rights = $userinfo->rights;
		$this->email = htmlspecialchars($userinfo->mail);
		$this->id = $userinfo->id;

		$_SESSION['userid'] = $userinfo->id;
		$_SESSION['username'] = $userinfo->name;
		$_SESSION['last_activity'] = time();
		$_SESSION['cookievalue'] = $new_cookie;
		$_SESSION['logged_in'] = true;

		return true;
	}

	function logout()
	{
		if(!isset($_SESSION['userid']) || !isset($_SESSION['logged_in']) || !isset($_SESSION['username']) || !isset($_SESSION['last_activity']) || !isset($_SESSION['cookievalue']))
			return false;
		$id = $_SESSION['userid'];
		$now = time();
		$sql = "UPDATE {user} SET `last_login`='{$now}', `cookie_value`=NULL WHERE id='{$id}' LIMIT 1";
		$res = $this->devbird->query($sql);

		$_SESSION['logged_in'] = false;
		$_SESSION['userid'] = false;
		$_SESSION['username'] = false;
		$_SESSION['last_activity'] = false;
		$_SESSION['cookievalue'] = false;
		setcookie('devbird_user_name', '', time()-3600, '/');
		setcookie('devbird_user_pw', '', time()-3600, '/');

		$this->rights = 0;
		$this->email = '';
		$this->name = '';
		$this->id = 0;
		return true;
	}

	function has_right($right)
	{
		if(empty($this->rights)) return false;
		if(isset($this->right_strings[$right]))
		{
			if($this->rights == 0) return false;
			$no = $this->right_strings[$right];
#			die($this->rights.'&amp;'.$no.'='.($this->rights & $no));
			return (($this->rights & $no) == $no);
		}
		return false;
	}

}

function has_right($rights, $right)
{
	$right_strings = array('edit_settings' => 8, 'edit_articles' => 4, 'visit_admin' => 2, 'comment_articles' => 1, 'nothing' => 0);
	if(empty($rights)) return false;
	if(isset($right_strings[$right]))
	{
		if($rights == 0) return false;
		$no = $right_strings[$right];
		return (($rights & $no) == $no);
	}
	return false;
}
?>
