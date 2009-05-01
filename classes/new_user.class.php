<?

require_once 'core.php';

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
	private $devbird;
	private $right_strings = array('edit_settings' => 8, 'edit_articles' => 4, 'visit_admin' => 2, 'comment_articles' => 1, 'nothing' => 0);
    
    function __construct($options = array())
    {
        if(is_array($options))
        {
            $id = htmlspecialchars($options['id']);
            $name = htmlspecialchars($options['name']);
            $this->mail = htmlspecialchars($options['mail']);
            $this->password_hash = htmlspecialchars($options['password']);
            #$this->salt = htmlspecialchars($options['salt']);
            $this->last_login = htmlspecialchars($options['last_login']);
            $this->use_cookies = htmlspecialchars($options['use_cookies']);
            $this->cookie_value = htmlspecialchars($options['cookie_value']);
            $this->reseted_pw = htmlspecialchars($options['reseted_pw']);
        }
        elseif(is_object($options))
        {
            $this->id = htmlspecialchars($options->id);
            $this->name = htmlspecialchars($options->name);
            $this->mail = htmlspecialchars($options->mail);
            $this->password_hash = htmlspecialchars($options->password);
            #$this->salt = htmlspecialchars($options->salt);
            $this->last_login = htmlspecialchars($options->last_login);
            $this->use_cookies = htmlspecialchars($options->use_cookies);
            $this->cookie_value = htmlspecialchars($options->cookie_value);
            $this->reseted_pw = htmlspecialchars($options->reseted_pw);
        }
        else
        {
            die("User dead =/");
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

    function clear_session()
    {
        $_SESSION['logged_in'] = false;
        $_SESSION['userid'] = false;
        $_SESSION['username'] = false;
        $_SESSION['last_activity'] = false;
        $_SESSION['cookievalue'] = false;
    }

    function has_right($right)
	{
		if(empty($this->rights)) return false;
		if(isset($this->right_strings[$right]))
		{
			if($this->rights == 0) return false;
			$no = $this->right_strings[$right];
			#die($this->rights.'&amp;'.$no.'='.($this->rights & $no));
			return (($this->rights & $no) == $no);
		}
		return false;
    }

    function login($password)
    {
        #$hashed = sha1('--' . $this->salt . '--' . $password);
        $hashed = sha1($password);
        $logged_in = $hashed == $this->password_hash;
        return $logged_in;
    }

    function login_with_hashed($password)
    {
        $logged_in = $password == $this->password_hash;
        return $logged_in;
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
}

#$user = User::find_by_name('admin');
#var_dump($user);
#var_dump($user->is_online());

?>
