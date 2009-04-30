<?

require_once '../core.php';

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
            $id = $options['id'];
            $name = $options['name'];
            $this->mail = $options['mail'];
            $this->password_hash = $options['password'];
            #$this->salt = $options['salt'];
            $this->last_login = $options['last_login'];
            $this->use_cookies = $options['use_cookies'];
            $this->cookie_value = $options['cookie_value'];
            $this->reseted_pw = $options['reseted_pw'];
        }
        elseif(is_object($options))
        {
            $this->id = $options->id;
            $this->name = $options->name;
            $this->mail = $options->mail;
            $this->password_hash = $options->password;
            #$this->salt = $options->salt;
            $this->last_login = $options->last_login;
            $this->use_cookies = $options->use_cookies;
            $this->cookie_value = $options->cookie_value;
            $this->reseted_pw = $options->reseted_pw;
        }
        else
        {
            die("User dead =/");
        }
    }

    function is_online()
    {
        return true;
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
        $hashed = sha1('--' . $this->salt . '--' . $password);
        $logged_in = $hashed == $this->password_hash;
        if(!$logged_in) return false;
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

var_dump(User::find_by_name('admin'));

?>
