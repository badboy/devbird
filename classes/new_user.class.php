<?

#require_once 'core.php';

class User
{
    var $id;
    var $name;
    var $mail;
    var $password_hash;
    var $salt;
    var $last_login;
    var $use_cookies;
    var $cookie_value;
    var $reseted_pw;

    private $rights;
	private $right_strings = array('edit_settings' => 8, 'edit_articles' => 4, 'visit_admin' => 2, 'comment_articles' => 1, 'nothing' => 0);
    
    function __construct($options = array())
    {
        if(is_array($options))
		{
			if(!empty($options))
			{
				$id = htmlspecialchars($options['id']);
				$name = htmlspecialchars($options['name']);
				$this->mail = htmlspecialchars($options['mail']);
				$this->password_hash = htmlspecialchars($options['password']);
				$this->salt = htmlspecialchars($options['salt']);
				$this->last_login = htmlspecialchars($options['last_login']);
				$this->use_cookies = htmlspecialchars($options['use_cookies']) == 1;
				$this->cookie_value = htmlspecialchars($options['cookie_value']);
				$this->reseted_pw = htmlspecialchars($options['reseted_pw']);
				$this->rights = htmlspecialchars($options['rights']);
			}
        }
        elseif(is_object($options))
        {
            $this->id = htmlspecialchars($options->id);
            $this->name = htmlspecialchars($options->name);
            $this->mail = htmlspecialchars($options->mail);
            $this->password_hash = htmlspecialchars($options->password);
            $this->salt = htmlspecialchars($options->salt);
            $this->last_login = htmlspecialchars($options->last_login);
            $this->use_cookies = htmlspecialchars($options->use_cookies) == 1;
            $this->cookie_value = htmlspecialchars($options->cookie_value);
            $this->reseted_pw = htmlspecialchars($options->reseted_pw);
			$this->rights = htmlspecialchars($options->rights);
        }
    }

    function is_online()
    {
        if($this->session_is_set())
		{
            if($this->last_activity_valid())
            {
                $userid = Devbird::$db_con->real_escape_string($_SESSION['userid']);
                $username = Devbird::$db_con->real_escape_string($_SESSION['username']);
                $cookievalue = Devbird::$db_con->real_escape_string($_SESSION['cookievalue']);

                if($userid == $this->id && $username == $this->name && $cookievalue == $this->cookie_value) 
                {
                    $_SESSION['last_activity'] = time();
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
				if($this->cookies_are_set())
				{
					$password = Devbird::$db_con->real_escape_string(base64_decode($_COOKIE['devbird_user_pw']));
					$ret =  $this->login_with_hashed($password);
					return $ret;
				}
				else
                {
                    $this->clear_session();
					return false;
                }
            }

        }
        elseif($this->cookies_are_set())
		{
            $password = Devbird::$db_con->real_escape_string(base64_decode($_COOKIE['devbird_user_pw']));
			$ret =  $this->login_with_hashed($password);
			return $ret;
		}
        return false;
    }

    function session_is_set()
    {
        return (
            isset($_SESSION['logged_in']) && 
            isset($_SESSION['userid']) && 
            isset($_SESSION['username']) && 
            isset($_SESSION['last_activity']) &&
            isset($_SESSION['cookievalue'])
        );
    }

    function cookies_are_set()
    {
        return (
            isset($_COOKIE['devbird_user_name']) &&
            isset($_COOKIE['devbird_user_pw'])
        );
    }

    function last_activity_valid()
    {
        $now = time();
        $last = (int)$_SESSION['last_activity'];
		return (($now - $last) < 3600);
	}

	function set_session_and_cookie()
    {
		$this->cookie_value = md5(time().$this->id);

        $_SESSION['logged_in'] = true;
        $_SESSION['userid'] = $this->id;
		$_SESSION['username'] = $this->name;
        $_SESSION['last_activity'] = time();
		$_SESSION['cookievalue'] = $this->cookie_value;
		
		$sql = "UPDATE {user} SET `cookie_value`='{$this->cookie_value}' WHERE id='{$this->id}'";
		$res = Devbird::oquery($sql);
		if(!$res) return false;

		if($this->use_cookies)
		{
			@setcookie('devbird_user_name', base64_encode($this->name), time()+60*60*24*30, '/');
			@setcookie('devbird_user_pw', base64_encode($this->password), time()+60*60*24*30, '/');
 		}
    }

    function clear_session()
    {
        $_SESSION['logged_in'] = false;
        $_SESSION['userid'] = false;
        $_SESSION['username'] = false;
        $_SESSION['last_activity'] = false;
        $_SESSION['cookievalue'] = false;
    }

	function delete_cookies()
	{
		setcookie('devbird_user_name', '', time()-3600, '/');
		setcookie('devbird_user_pw', '', time()-3600, '/');
	}

    function has_right($right)
	{
		if(empty($this->rights)) return false;
		if(isset($this->right_strings[$right]))
		{
			if($this->rights == 0) return false;
			$no = $this->right_strings[$right];
			return (($this->rights & $no) == $no);
		}
		return false;
    }

    function login($password)
    {
        $hashed = sha1('--' . $this->salt . '--' . $password);
        $logged_in = $hashed == $this->password_hash;
		if($logged_in) $this->set_session_and_cookie();
		else return false;
        return true;
    }

    function login_with_hashed($password)
    {
		$logged_in = $password == $this->password_hash;
		if($logged_in) $this->set_session_and_cookie();
		else return false;
        return true;
    }
	
	function logout()
	{
		if(!$this->session_is_set() || !$this->last_activity_valid())
			return false;

		$now = time();
		$sql = "UPDATE {user} SET `last_login`='{$now}', `cookie_value`=NULL WHERE id='{$this->id}' LIMIT 1";
		$res = Devbird::oquery($sql);

		$this->clear_session();
		$this->delete_cookies();

		return true;
	}

    static function find_by_name($user)
    {
        $user = Devbird::$db_con->real_escape_string($user);
        $res = Devbird::oquery("SELECT * FROM {user} WHERE `name` = '{$user}' LIMIT 1");
        if(!$res) return false;
		if($res->num_rows != 1) return false;
        $userinfo = $res->fetch_object();
        #var_dump($userinfo);
        return new User($userinfo);
    }

    static function find_by_id($id)
    {
        $id = Devbird::$db_con->real_escape_string($id);
        $res = Devbird::oquery("SELECT * FROM {user} WHERE `id` = '{$id}' LIMIT 1");
        if(!$res) return false;
		if($res->num_rows != 1) return false;
        $userinfo = $res->fetch_object();
        #var_dump($userinfo);
        return new User($userinfo);
	}

	static function all()
	{
		$sql = "SELECT * FROM {user}";
		$res = Devbird::oquery($sql);
		if(!$res) return array();

		$users = array();
 		while($user = $res->fetch_object())
		{
			$users[] = new User($user);
		}
		return $users;
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
#	$user = User::find_by_name('admin');
#	var_dump($user);
#	var_dump($user->is_online());

?>
