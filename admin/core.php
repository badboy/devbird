<?php

error_reporting(E_ALL | E_STRICT);

require '../classes/new_user.class.php';

class Devbird
{
	const Version = '0.3.1';

	var $DB = false;
	var $lastresult = false;
	var $settings = array();
	var $rootpath = false;
	var $blogroot = false;
	var $adminrootpath = false;
	var $design = false;
	var $cur_page = false;
	var $max_entries = 0;	
	var $seitenanz = 0;
	var $done = array('error'=> false, 'msg'=> false);
	var $encoding = 'UTF-8';

	var $user;

	public static $db_con = NULL;

	function __construct()
	{
		date_default_timezone_set('Europe/Berlin');
		define('IN_CORE', true);
		require '../config/config.php';

		$dbconfig = array(
			'hostname'=> $mysql_hostname,
			'username'=> $mysql_username,
			'password'=> $mysql_password,
			'database'=> $mysql_database
		);

		 $this->DB = new mysqli($dbconfig['hostname'], $dbconfig['username'], $dbconfig['password'], $dbconfig['database']);
		if(mysqli_connect_errno())
			die("Can't connect to database");
		self::$db_con = $this->DB;

		$res = $this->query('SELECT type, name, value FROM {settings}') or die($this->error());
		while($setting = $res->fetch_array())
		{
			if($setting['type'] == '1' || $setting['type'] == '2' || $setting['type'] == '3') $this->settings[$setting['name']] = $setting['value'];
			else $this->settings[$setting['name']] = NULL;
		}
		$this->rootpath = $this->settings['Bloglink'];
		$this->adminrootpath = $this->rootpath.'/admin';
		$this->design = $this->settings['Design'];
		$this->blogroot = $this->settings['Blogroot'];
		$this->encoding = $this->settings['Zeichensatz'];
	
		session_start();
		$username = $this->visitor_as_user();
		#var_dump($username);
        if($username)
        {
            $this->user = User::find_by_name($username);
            if($this->user)
            {
                $this->user->is_online();
            }
        }
        else
        {
            $this->user = new User();
        }
    }

    function visitor_as_user()
    {
        if(isset($_SESSION['username'])) return $_SESSION['username'];
        if(isset($_COOKIE['devbird_user_name'])) return base64_decode($_COOKIE['devbird_user_name']);
        return false; 
    }

	function error()
	{
		return $this->DB->error;
	}

	function query($query_string, $save_last=true)
	{
		$allowed_tables = array('links', 'news', 'news_comments', 'settings', 'user', 'pages');
		$joined = join('|', $allowed_tables);

		$query_string = preg_replace("/\{({$joined})}/",TABLE_PREFIX . "$1", $query_string);
		#echo $query_string;
		if($save_last)
		{
			$this->lastresult = $this->DB->query($query_string);
			return $this->lastresult;
		}
		else
			return $this->DB->query($query_string);
	}

	public static function oquery($query_string)
	{
		$allowed_tables = array('links', 'news', 'news_comments', 'settings', 'user', 'pages');
		$joined = join('|', $allowed_tables);

		$query_string = preg_replace("/\{({$joined})}/",TABLE_PREFIX . "$1", $query_string);
		#echo $query_string, "\n";
		return Devbird::$db_con->query($query_string);
	}

	function get_tags()
	{
		$sql = 'SELECT tags FROM {news}';
		$res = $this->query($sql, false);
		$tags = array();
		while($return = $res->fetch_object())
		{
			$arr = explode(' ', $return->tags);
			foreach($arr as $entry)
			{
				if(!in_array($entry, $tags))
					$tags[] = $entry;
			}
		}
        sort($tags);
		return $tags;
	}

	function news_num($where='')
	{
		$res = $this->query("SELECT count(id) as count FROM {news} {$where}");
		if($res)
		{
			$data = $res->fetch_object();
			return $data->count;
		}
		else return 0;
	}

	function comments_num($where='')
	{
		$res = $this->query("SELECT count(id) as count FROM {news_comments} {$where}");
		if($res)
		{
			$data = $res->fetch_object();
			return $data->count;
		}
		else return 0;
	}

	function pages_num($where='')
	{
		$res = $this->query('SELECT count(short_name) as count FROM {pages} '.(strlen($where) > 0 ? 'WHERE ' : '').$where);
		if($res)
		{
			$data = $res->fetch_object();
			return $data->count;
		}
		else return 0;
	}

	function get_comment($id, $is_read=false)
	{
		$id = $this->DB->real_escape_string($id);
		if($is_read)
		{
			$sql = "UPDATE {news_comments} SET `read` = '1' WHERE id ='{$id}'";
			$this->query($sql);
		}
		$sql = "SELECT * FROM {news_comments} WHERE id = '{$id}'";
		if(!$this->query($sql)) return false;
		return $this->lastresult->fetch_object();
	}

	function get_comments($page=0, $limit=20, $withnewsname=false)
	{
		$page = $this->DB->real_escape_string($page);
		$start = $page * $limit;
		if($withnewsname)
			$sql = "SELECT {news_comments}.*, {news}.title as news_title FROM {news_comments} INNER JOIN {news} ON ({news}.id = {news_comments}.news_id) ORDER BY {news_comments}.date DESC LIMIT {$start}, {$limit}";
		else
			$sql = "SELECT * FROM {news_comments} ORDER BY date DESC LIMIT {$start}, {$limit}";
		return $this->query($sql);
	}

	function get_pages()
	{
		$sql = "SELECT * FROM {pages}";
		return $this->query($sql);
	}


	function get_page($name)
	{
		$name = $this->DB->real_escape_string($name);
		$sql = "SELECT * FROM {pages} WHERE short_name = '{$name}'";
		if(!$this->query($sql)) return false;
		return $this->lastresult->fetch_object();
	}

	function fetch_comments($query = false)
	{
		if($query) $this->lastresult = $query;
		elseif(!$this->lastresult)
			$this->lastresult = $this->get_comments();
		return $this->lastresult->fetch_object();
	}

	function get_article($id)
	{
		$id = $this->DB->real_escape_string($id);
		$sql = "SELECT * FROM {news} WHERE id = '{$id}'";
		if(!$this->query($sql)) return false;
		return $this->lastresult->fetch_object();
	}

	function get_articles($page=0, $limit=20)
	{
		$page = $this->DB->real_escape_string($page);
		$start = $page * $limit;
		$sql = "SELECT * FROM {news} ORDER BY created DESC LIMIT {$start}, {$limit}";
		return $this->query($sql);
	}

	function fetch_articles()
	{
		if(!$this->lastresult)
			$this->lastresult = $this->get_articles();
		return $this->lastresult->fetch_object();
	}

	function get_link($id)
	{
		$id = $this->DB->real_escape_string($id);
		$sql = "SELECT * FROM {links} WHERE id = '{$id}'";
		if(!$this->query($sql)) return false;
		return $this->lastresult->fetch_object();
	}

	function get_links($type=1)
	{
		$type = $this->DB->real_escape_string($type);
		if($type == 0)
			$sql = "SELECT * FROM {links} WHERE category = '{$type}' ORDER BY sort ASC";
		if($type == 1)
			$sql = "SELECT * FROM {links} WHERE category = '{$type}' ORDER BY name ASC";
		if($type == 2)
			$sql = "SELECT * FROM {links}";
		return $this->query($sql);
	}

	function fetch_links()
	{
		if(!$this->lastresult)
			$this->lastresult = $this->get_links();
		return $this->lastresult->fetch_object();
	}

	function get_user($id)
	{
		$id = $this->DB->real_escape_string($id);
		$sql = "SELECT * FROM {user} WHERE `id` = '{$id}'";
		$res = $this->query($sql);
		if(!$res) return false;
		return $res->fetch_object();
	}

	function get_users($without=false)
	{
		if($without)
		{
			$sql = "SELECT * FROM {user} WHERE `id` != '{$without}'";
		}
		else
		{
			$sql = "SELECT * FROM {user}";
		}
		return $this->query($sql);
	}

	function fetch_users()
	{
		if(!$this->lastresult)
			$this->lastresult = $this->get_users();
		return $this->lastresult->fetch_object();
	}

	function insert_user($id, $name, $mail, $password, $cookies, $rights, $rights_disabled)
	{
		$id = $this->DB->real_escape_string($id);
		$name = $this->DB->real_escape_string($name);
		$mail = $this->DB->real_escape_string($mail);
		$password = $this->DB->real_escape_string($password);
		if($rights == -1) $rights_disabled = true;
		else
		{
			$rights = $this->DB->real_escape_string($rights);
			if($rights < 0 || $rights > 15) $rights = 0;
		}
		$cookies = $cookies ? 1 : 0;
		if($id == 0) // neuen Benutzer anlegen
		{
			if(empty($password))
				return false;

			$password = $this->DB->real_escape_string(sha1($password));
			$sql = "INSERT INTO {user} (`name`, `rights`, `password`, `mail`, `use_cookies`) VALUES ('{$name}', '{$rights}', '{$password}', '{$mail}', '{$cookies}')";
		}
		else
		{
			if(empty($password))
			{
				if($rights_disabled)
					$sql = "UPDATE {user} SET `name`='{$name}', `mail`='{$mail}', `use_cookies`='{$cookies}' WHERE `id` = '{$id}'";
				else
					$sql = "UPDATE {user} SET `name`='{$name}', `rights`='{$rights}', `mail`='{$mail}', `use_cookies`='{$cookies}' WHERE `id` = '{$id}'";
			}
			else
			{
				$password = $this->DB->real_escape_string(sha1($password));
				if($rights_disabled)
					$sql = "UPDATE {user} SET `name`='{$name}', `password`='{$password}', `mail`='{$mail}', `use_cookies`='{$cookies}' WHERE `id` = '{$id}'";
				else
					$sql = "UPDATE {user} SET `name`='{$name}', `rights`='{$rights}', `password`='{$password}', `mail`='{$mail}', `use_cookies`='{$cookies}' WHERE `id` = '{$id}'";
			}
		}
		$res = $this->query($sql);
		return ($res ? true : false);
	}

	function delete_user($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("DELETE FROM {user} WHERE id = '{$id}'");
		return ($res ? true : false);
	}

	function close_article($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("UPDATE {news} SET published = '0' WHERE id ='{$id}'");
		return ($res ? true : false);
	}

	function open_article($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("UPDATE {news} SET published = created WHERE id ='{$id}'");
		return ($res ? true : false);
	}

	function delete_article($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("DELETE FROM {news} WHERE id = '{$id}'");
		return ($res ? true : false);
	}

	function close_page($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("UPDATE {pages} SET published = '0' WHERE short_name ='{$id}'");
		return ($res ? true : false);
	}

	function open_page($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("UPDATE {pages} SET published = created WHERE short_name ='{$id}'");
		return ($res ? true : false);
	}

	function delete_page($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("DELETE FROM {pages} WHERE short_name = '{$id}'");
		return ($res ? true : false);
	}

	function delete_link($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("DELETE FROM {links} WHERE id = '{$id}'");
		return ($res ? true : false);
	}

	function update_link($id, $name, $desc, $link, $sort)
	{
		$id = $this->DB->real_escape_string($id);
		$name = $this->DB->real_escape_string($name);
		$desc = $this->DB->real_escape_string($desc);
		$link = $this->DB->real_escape_string($link);
		$sort = $this->DB->real_escape_string($sort);
		$res = $this->query("UPDATE {links} SET `name`='{$name}', `desc`='{$desc}', `link`='{$link}', `sort`='{$sort}' WHERE id ='{$id}'");
		return ($res ? true : false);
	}

	function insert_link($name, $desc, $link, $type)
	{
		$name = $this->DB->real_escape_string($name);
		$desc = $this->DB->real_escape_string($desc);
		$link = $this->DB->real_escape_string($link);
		$type = $this->DB->real_escape_string($type);
		$res = $this->query("INSERT INTO {links} (`category`, `name`, `desc`, `link`) VALUES ('{$type}', '{$name}', '{$desc}', '{$link}')");
		return ($res ? true : false);
	}

	function insert_comment($name, $email, $website, $message, $news_id)
	{
		$name = $this->DB->real_escape_string($name);
		$email = $this->DB->real_escape_string($email);
		$website = $this->DB->real_escape_string($website);
		$message = nl2br(htmlspecialchars($message));
		$message = $this->DB->real_escape_string($message);
		$news_id = $this->DB->real_escape_string($news_id);
		$time = time(0);
		$res = $this->query("INSERT INTO {news_comments} (`news_id`, `name`, `website`, `email`, `msg`, `date`, `read`) VALUES ('{$news_id}', '{$name}', '{$website}', '{$email}', '{$message}', '{$time}', '1')");
		return ($res ? true : false);
	}
	function close_comment($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("UPDATE {news_comments} SET public = '0' WHERE id ='{$id}'");
		return ($res ? true : false);
	}

	function open_comment($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("UPDATE {news_comments} SET public = '1' WHERE id ='{$id}'");
		return ($res ? true : false);
	}

	function delete_comment($id)
	{
		$id = $this->DB->real_escape_string($id);
		$res = $this->query("DELETE FROM {news_comments} WHERE id = '{$id}'");
		return ($res ? true : false);
	}

	function get_settings()
	{
		return $this->query("SELECT * FROM {settings} ORDER BY sort");
	}
	
	function insert_article($article_id, $article_title, $article_writer, $article_date, $article_public, $article_content, $article_bb, $article_tags, $article_comments)
	{
		$article_id = $this->DB->real_escape_string($article_id);
		$article_title = $this->DB->real_escape_string($article_title);
		$article_short = $this->DB->real_escape_string($this->shorttext($article_title));
 		$article_writer = $this->DB->real_escape_string($article_writer);
		$article_date = $this->DB->real_escape_string($article_date);
		$article_public = $this->DB->real_escape_string($article_public);
		$article_content = $this->DB->real_escape_string($article_content);
		$article_bb = $this->DB->real_escape_string($article_bb);
		$article_tags = $this->DB->real_escape_string($article_tags);
		$article_comments = $article_comments == 1 ? 1 : 0;
		
		if($article_id == 0) // insert new article
		{
			$sql = <<<SQL_QUERY
INSERT INTO `{news}` (`created`, `published`, `short_name`, `title`, `message`, `writer`, `tags`, `bb_code`, `ajax_saved`, `comments`) VALUES
('{$article_date}', '{$article_public}', '{$article_short}', '{$article_title}', '{$article_content}', '{$article_writer}', '{$article_tags}', '{$article_bb}', '0', '{$article_comments}');
SQL_QUERY;
		}
		else
		{
			$sql = <<<SQL_QUERY
UPDATE `{news}`
SET
 `created` = '{$article_date}',
 `published` = '{$article_public}',
 `short_name` = '{$article_short}',
 `title` = '{$article_title}',
 `message` = '{$article_content}',
 `writer` = '{$article_writer}',
 `tags` = '{$article_tags}',
 `bb_code` = '{$article_bb}',
 `ajax_saved` = '0',
 `comments` = '{$article_comments}'
WHERE
`id` = '{$article_id}'
SQL_QUERY;
		}
#echo $sql;
		$res = $this->query($sql);
		if($res) return true;
		return false;
	}

	function insert_page($action, $page_title, $page_longtitle, $page_date, $page_public, $page_content, $page_bb)
	{
		$page_title = $this->DB->real_escape_string($page_title);
		$page_longtitle = $this->DB->real_escape_string($page_longtitle);
		$page_short = $this->DB->real_escape_string($this->shorttext($page_title));
		$page_date = $this->DB->real_escape_string($page_date);
		$page_public = $this->DB->real_escape_string($page_public);
		$page_content = $this->DB->real_escape_string($page_content);
		$page_bb = $this->DB->real_escape_string($page_bb);

		if($action == 'new')
		{
			$sql = <<<SQL_QUERY
INSERT INTO `{pages}` (`short_name`, `title`, `created`, `published`, `body`, `bb_code`) VALUES
('{$page_short}', '{$page_longtitle}', '{$page_date}', '{$page_public}', '{$page_content}', '{$page_bb}');
SQL_QUERY;
		}
		else
		{
			$sql = <<<SQL_QUERY
UPDATE `{pages}`
SET
 `short_name` = '{$page_short}',
 `title` = '{$page_longtitle}',
 `created` = '{$page_date}',
 `published` = '{$page_public}',
 `body` = '{$page_content}',
 `bb_code` = '{$page_bb}'
WHERE
`short_name` = '{$page_short}'
SQL_QUERY;
		}
		$res = $this->query($sql);
		if($res) return true;
		else return false;
	}

	function shorttext($text)
	{
		$text = html_entity_decode($text);
		$text = preg_replace('/[^a-zA-Z0-9]/', '-', $text);
		$text = preg_replace('/-{2,}/', '-', $text);
		$text = preg_replace('/-+$/', '', $text);
		$text = preg_replace('/^-+/', '', $text);
		return strtolower($text);
	}
	
	function send_trackback($trackback_url, $article_id, $article_title, $article_content)
    {
    	$ret_array = array('error'=>0);
    	$url = $this->rootpath.'/'.$article_id.'/'.$this->shorttext($article_title);

		$blogname = $this->settings['Blogname'];
		$title = $article_title;
		# 255
		
		if(strlen($article_content) > 255)
		{
			$desc = '(...) ' . substr($article_content, 0, 243) . ' (...)';
		}
		else
			$desc = $article_content;
			
		$desc = strip_tags($desc);
    
		$data = "title=" . urlencode($title);
		$data .= "&url=" . urlencode($url);
		$data .= "&blog_name=" . urlencode($blogname);
		$data .= "&excerpt=" . urlencode($desc);


		$tb_url = parse_url($trackback_url);
		$trackback_url = $tb_url['path'];
		if(isset($tb_url['query']) && !empty($tb_url['query']))
			$trackback_url .= '?'.$tb_url['query'];
		$host = $tb_url['host'];



		if(!isset($tb_url['port']) || empty($tb_url['port']))
			$fp = @fsockopen($host, 80);
		else
			$fp = @fsockopen($host, $tb_url['port']);
		if(!$fp) return array('error'=>1, 'message'=>'Konnte nicht zum Server verbinden');
		fwrite($fp, "POST ".$trackback_url." HTTP/1.1\r\n");
		fwrite($fp, "Host: ".$host."\r\n");
		fwrite($fp, "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n");
		fwrite($fp, "Content-length: ".strlen($data)."\r\n");
		fwrite($fp, "Connection: close\r\n\r\n");
		fwrite($fp, $data);
		
		$complete_ret = '';
		
		do
		{
			$retstring = fread($fp, 512);
			if(strlen($retstring) > 0)
				$complete_ret .= $retstring;
		}
		while(strlen($retstring) > 0);
		
		fclose($fp);
		list($header, $body) = explode("\r\n\r\n", $complete_ret);
		$result;
		if(!preg_match("/content-type: (.+)/i", $header, $result))
		{
			$ret_array = array('error'=>0, 'warning'=>1, 'message'=>'Server sendet keinen Content-Type.');
		}

		$cot = trim($result[1]);
		if(!preg_match('/^text\/xml/i', $cot))
		{
			$ret_array = array('error'=>0, 'warning'=>1, 'message'=>'Server sendet falschen Content-Type.');
		}
		
		if(strpos($body, '<error>0</error>'))
			return array('error'=>0);
		else if(strpos($body, '<error>1</error>'))
		{
			if(!preg_match("/<message>(.+)<\/message>/", $body, $result))
				return array('error'=>1, 'message'=>'Server liefert einen Fehler aber keine Nachricht zurück.');
			else
				return array('error'=>1, 'message'=>'"' . htmlspecialchars($result[1]) . '"');
		}
		else
			return array('error'=>1, 'message'=>'Server liefert kein Resultat.');

		
        return $ret_array;
    }
}


$Blog = new Devbird;
global $Blog;
?>
