<?php

error_reporting(E_ALL | E_STRICT);

require 'classes/MathCaptcha.class.php';
require 'classes/user.class.php';

class Devbird
{
	const Version = '0.3.0';

	var $DB = false;
	var $lastresult = false;
	var $settings = array();
	var $rootpath;
	var $adminrootpath;
	var $design;
	var $cur_page;
	var $max_entries = 0;	
	var $seitenanz = 0;
	var $captcha_result;
	var $captcha_question = '';
	var $done = array('error'=> false, 'msg'=> false);
	var $encoding = 'UTF-8';

	var $newsbox = 'newsbox.php';
	var $commentbox = 'comment.php';

	var $user;

	function __construct()
	{
		date_default_timezone_set('Europe/Berlin');
		define('IN_CORE', true);
		require 'config/config.php';

		$dbconfig = array(
			'hostname'=> $mysql_hostname,
			'username'=> $mysql_username,
			'password'=> $mysql_password,
			'database'=> $mysql_database
		);

		$this->DB = new mysqli($dbconfig['hostname'], $dbconfig['username'], $dbconfig['password'], $dbconfig['database']);
		if(mysqli_connect_errno())
			die("Can't connect to database");

		$res = $this->query('SELECT type, name, value FROM {settings}') or die($this->error());
		while($setting = $res->fetch_array())
		{
			if($setting['type'] == '1' || $setting['type'] == '2' || $setting['type'] == '3') $this->settings[$setting['name']] = $setting['value'];
#			else if($setting['type'] == '2') $this->settings[$setting['name']] = intval($setting['value']);
			else $this->settings[$setting['name']] = NULL;
		}
		$this->rootpath = $this->settings['Bloglink'];
		$this->adminrootpath = $this->rootpath.'/admin';
		$this->design = $this->settings['Design'];
		$this->encoding = $this->settings['Zeichensatz'];

		session_start();
		$this->user = new User($this);
		$this->user->is_online();
	}

	function include_lightbox()
	{
		echo '<script type="text/javascript" src="'.$this->rootpath.'/javascript/prototype.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.$this->rootpath.'/javascript/scriptaculous.js?load=effects,builder"></script>'."\n";
		echo '<script type="text/javascript" src="'.$this->rootpath.'/javascript/lightbox.js"></script>'."\n";
		echo '<link rel="stylesheet" href="'.$this->rootpath.'/css/lightbox.css" type="text/css" media="screen" />'."\n";
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
		if($save_last)
			return ($this->lastresult=$this->DB->query($query_string));
		else
			return $this->DB->query($query_string);
	}

	function get_tags()
	{
		$sql = 'SELECT tags FROM {news} WHERE published > 0';
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

	function getnewsbytag($tag, $start=0,$limit=10)
	{
		$sql = "SELECT * FROM {news} WHERE published > 0 AND tags LIKE '%{$tag}%'ORDER BY created DESC";
		return $this->query($sql);
	}



	function getnews($start=0,$limit=10)
	{
		$sql = "SELECT * FROM {news} WHERE published > 0 ORDER BY created DESC LIMIT $start, $limit";
		return $this->query($sql);
	}

	function getnewsbyid($id, $only=false)
	{
		if(empty($id)) return false;
		$id = $this->DB->real_escape_string($id);
		if($only)
		{
			$only = $this->DB->real_escape_string($only);
			$sql= "SELECT {$only} FROM {news} WHERE published > 0 AND id = '".$id."' LIMIT 1";
		}
		else
			$sql= "SELECT * FROM {news} WHERE published > 0 AND id = '".$id."' LIMIT 1";
		return $this->query($sql);
	}

	function getcomment($id)
	{
		if(empty($id)) return false;
		$id = $this->DB->real_escape_string($id);
		$sql= "SELECT * FROM {news_comments} WHERE news_id = '{$id}' AND public = '1'";
		return ($this->commentsres=$this->query($sql));
	}

	function getnextcomment($asobject=true)
	{
		return $this->fetch($this->commentsres, $asobject);
	}	

	function fetch($result=false, $asobject=true)
	{
		if(!$result) $result = $this->lastresult;
		if($asobject)
			return $result->fetch_object();
		else
			return $result->fetch_array();
	}

	function nextnews($result=false, $asobject=true)
	{
		return $this->fetch($result, $asobject);
	}

	function commentsno($id, $hidden=false)
	{
		if(empty($id)) return false;
		$id = $this->DB->real_escape_string($id);
		if($hidden)
			$sql = "SELECT COUNT(*) as count FROM {news_comments} WHERE news_id = '$id' GROUP BY news_id";
		else
			$sql = "SELECT COUNT(*) as count FROM {news_comments} WHERE news_id = '$id' AND public = '1' GROUP BY news_id";
		$res = $this->query($sql);
		if(!$res) return $this->error();
		$fetched = $res->fetch_object();
		return $fetched ? (is_numeric($fetched->count)?$fetched->count:0) : 0;
	}

	function getblogroll()
	{
		return $this->getlinks(1);
	}

	function getlinks($cat = 0)
	{
		$cat = $this->DB->real_escape_string($cat);
		if($cat == 0)
			$sql = "SELECT * FROM {links} WHERE category = '{$cat}' ORDER BY sort";
		if($cat == 1)
			$sql = "SELECT * FROM {links} WHERE category = '{$cat}' ORDER BY name";
		return ($this->linkres=$this->query($sql));
	}

	function getpagelinks($exclude)
	{
		$sql = "SELECT short_name, title FROM {pages} WHERE published != 0";
		if($exclude && is_array($exclude))
		{
			foreach($exclude as $to_ex)
			{
				$to_ex = $this->DB->real_escape_string($to_ex);
				$sql .= " AND title != '{$to_ex}'";
			}
		}
		return ($this->pagelinkres=$this->query($sql));
	}

	function nextblogroll()
	{
		if(!isset($this->blogrollres) || !$this->blogrollres)
			$this->blogrollres = $this->getblogroll();
		if(!$this->blogrollres) return false;
		return $this->blogrollres->fetch_object();
	}

	function nextpagelink($exclude=false)
	{
		if(!isset($this->pagelinkres) || !$this->pagelinkres)
			$this->pagelinkres = $this->getpagelinks($exclude);
		if(!$this->pagelinkres) return false;
		return $this->pagelinkres->fetch_object();
	}

	function nextlink()
	{
		if(!isset($this->linkres) || !$this->linkres)
			$this->linkres = $this->getlinks();
		if(!$this->linkres) return false;
		return $this->linkres->fetch_object();
	}

	function design($root=false)
	{
		if($root)
			return $this->rootpath.'/design/'.$this->design;
		else
			return 'design/'.$this->design;
	}
	function getheader()
	{
		return $this->design().'/'.$this->settings['Header'];
	}

	function set_newsbox($file)
	{
		$path = $this->design().'/'.$file;
		if(!file_exists($path)) return false;
		$this->newsbox = $file;
	}

	function set_commentbox($file)
	{
		$path = $this->design().'/'.$file;
		if(!file_exists($path)) return false;
		$this->commentbox = $file;
	}


	function fetch_links()
	{
		while($roll = $this->nextlink())
		{
			$roll_link = stripslashes($roll->link);
			$roll_desc = stripslashes($roll->desc);
			$roll_name = stripslashes($roll->name);
			$out = " <li>";
			$out .= "<a href=\"{$this->rootpath}/{$roll_link}\" title=\"{$roll_desc}\">{$roll_name}</a>";
			$out .= "</li>\n";
			echo $out;
		}
		$this->linkres = false;
	}

	function fetch_pagelinks($exclude=false)
	{
		while($link = $this->nextpagelink($exclude))
		{
			$out = " <li>";
			$out .= "<a href=\"{$this->rootpath}/site/{$link->short_name}\" title=\"{$link->title}\">{$link->title}</a>";
			$out .= "</li>\n";
			echo $out;
		}
		$this->pagelinkres = false;
	}

	function fetch_blogroll()
	{
		while($roll = $this->nextblogroll())
		{
			$roll_link = stripslashes($roll->link);
			$roll_desc = stripslashes($roll->desc);
			$roll_name = stripslashes($roll->name);
			$out = " <li>";
			$out .= "<a href=\"{$roll_link}\" title=\"{$roll_desc}\">{$roll_name}</a>";
			$out .= "</li>\n";
			echo $out;
		}
		$this->blogrollres = false;
	}

	function get_np_page()
	{
		$next = false; $prev = false;
		if($this->cur_page !== false)
		{
			if($this->cur_page-1 >= 0)
				$next = $this->cur_page;
			if($this->cur_page < $this->seitenanz-1)
				$prev = $this->cur_page+2;
			return array('next'=>$next, 'previous'=>$prev);
		}
		return array('next'=>false, 'previous'=>false);
	}

	function datestring($timestamp)
	{
		$now = time(0);
		$exist = $now - $timestamp;
		$day = $exist / 60 / 60 / 24 % 7;
		$hour = $exist / 60 / 60 % 24;
		$min = $exist / 60 % 60;
		$sec = $exist % 60;

		if($day > 1 || $exist > (86400)) # (60*60*24)
			return date("d.m.Y - H:i", $timestamp);
		else if($day == 1) return "vor einem Tag";
		else if($hour > 0) return "vor etwa {$hour} Stunde".($hour==1?'':'n');
		else if($min > 1) return "vor {$min} Minuten";
		else if($sec > 0)
		{
			if($sec < 60) return "vor weniger als einer Minute";
			if($sec < 50) return "vor weniger als 50 Sekunden";
			if($sec < 40) return "vor weniger als 40 Sekunden";
			if($sec < 30) return "vor weniger als 30 Sekunden";
			if($sec < 20) return "vor weniger als 20 Sekunden";
			if($sec < 10) return "vor weniger als 10 Sekunden";
			if($sec < 5) return "vor weniger als 5 Sekunden";
		}
		else
			return date("d.m.Y - H:i", $timestamp);
	}

	function newsfetch($news, $path)
	{
		$id = $news->id;
		$title = htmlspecialchars(stripslashes($news->title));
		$writer = $news->writer;
		$date = $this->datestring($news->published); #date("d.m.Y - H:i",$news->published);
		$content = stripslashes($news->message);
		$id = $news->id;
		if(!$news->comments) $disable_comments = true;
		else if($this->settings['Kommentare'] == 'aus') $disable_comments = true;
		else $disable_comments = false;
		$com_count = $this->commentsno($id);
		$url = $this->rootpath.'/'.$id.'/'.$this->shorttext($title);
		$trackback_url = $url . '/trackback';
		$tags = explode(' ', $news->tags);
		$rootpath = $this->rootpath;
		$Blog = $this;
		include $path;
	}

	function get_page($name)
	{
		$name = $this->DB->real_escape_string($name);
		$sql = "SELECT * FROM {pages} WHERE short_name = '{$name}' AND published > 0";
		if(!$this->query($sql)) return false;
		return $this->lastresult->fetch_object();
	}

	function fetch_page($_page)
	{
		if(empty($_page))
		{
			$this->news_error(0, 1);
			return false;
		}
		$page = $this->get_page($_page);
		if($page == false)
		{
			$this->news_error(0, 1);
			return false;
		}
		$path = $this->design().'/'.$this->newsbox;
		if(!file_exists($path))
		{
			$this->news_error(0, 1);
			return false;
		}

		#print_r($page->body);
		$found = preg_match_all("/{{newbox=(.+?)}}/i", $page->body, $splitted);
		$date = $this->datestring($page->published);

		$titles = array();
		$con = $page->body;
		if($found > 0)
		{
			for($i=0;$i<$found;$i++)
			{
				$titles[] = $splitted[1][$i];
			}
		}

		for($i=0;$i<$found;$i++)
		{
			if($found-1 != $i)
			{
				$len = strlen("{{newbox={$titles[$i]}}}");
				$pos = strpos($con, "{{newbox={$titles[$i+1]}}}")-$len;
				$content = substr($con, $len, $pos);
				$con = substr($con, $pos+$len);
			}
			else
			{
				$content = substr($con, strlen("{{newbox={$titles[$i]}}}"));
			}

				$title = stripslashes($titles[$i]);
				$content = stripslashes($content);

				$writer = '&lt;nil&gt;';
				$disable_comments = true;
				$com_count = 0;
				$url = $this->rootpath.'/site/'.$page->short_name;
				$tags = false;
				$rootpath = $this->rootpath;
				$Blog = $this;
				include $path;
		}


	}

	function fetch_news_by_keywords($keyword, $type)
	{
		$keyword = $this->DB->real_escape_string($keyword);

		$path = $this->design().'/'.$this->newsbox;
		if(!file_exists($path)) return false;

		$news_per_site = $this->settings['News pro Seite'];

		if($type == "fulltext")
		{
			$query = "SELECT *, MATCH(titel,message) AGAINST ('{$keyword}') as score FROM {news} WHERE ";
			$query .= "MATCH (title,message) AGAINST ('{$keyword}')";
		}
		else
		{
			$search_array = split(' ', $keyword);
			$query1 = "SELECT * FROM {news} WHERE ";
			$query = "";
			foreach($search_array as $word)
			{
				$word = $this->DB->real_escape_string($word);
				if(strlen($query) == 0)
					$query .= " title LIKE '%{$word}%' OR message LIKE '%{$word}%'";
				else
					$query .= " OR title LIKE '%{$word}%' OR message LIKE '%{$word}%'";
			}
			$query = $query1 . $query;
			$order = "ORDER BY created DESC";
		}

		$res = $this->query($query);
		if(!$res) return false;
		$this->max_entries = $res->num_rows;

		if($this->max_entries == 0)
			return false;

		while($news = $res->fetch_object())
		{
			--$news_per_site;
			$this->newsfetch($news, $path);
		}
		return true;
	}

	function fetch_news_by_tag($tag, $page)
	{
		$tag = $this->DB->real_escape_string($tag);
		$this->cur_page = $page;

		$path = $this->design().'/'.$this->newsbox;
		if(!file_exists($path)) return false;

		$this->max_entries = $this->get_max_entries_for_tag($tag);
		$news_per_site = $this->settings['News pro Seite'];

  		$this->seitenanz = ceil($this->max_entries/$news_per_site);
		if(empty($page) || $page < 0 || $page > $this->seitenanz) $page=0;

		$result = $this->getnewsbytag($tag, $page * $news_per_site, $news_per_site);
		if(!$result) {
			$this->news_error(0,0);
			return false;
		}
		if($result->num_rows == 0) { 
			$this->news_error(0,1); 
			return false;
		}

		while(($news = $this->nextnews($result)))
		{
			--$news_per_site;
			$this->newsfetch($news, $path);
		}
		return true;
	}

        function fetch_news($page=0)
        {
		$this->cur_page = empty($page) ? 0: $page;

		$path = $this->design().'/'.$this->newsbox;
		if(!file_exists($path)) return false;

		$this->max_entries = $this->get_max_entries();
		$news_per_site = $this->settings['News pro Seite'];

  		$this->seitenanz = ceil($this->max_entries/$news_per_site);
		if(empty($page) || $page < 0 || $page > $this->seitenanz) $page=0;

		$result = $this->getnews($page * $news_per_site, $news_per_site);
		if(!$result) {
			$this->news_error(0,0);
			return false;
		}
		if($result->num_rows == 0) { 
			$this->news_error(0,1); 
			return false;
		}
		while($news = $this->nextnews($result))
		{
			--$news_per_site;
			$this->newsfetch($news, $path);
		}
		return true;
	}
	
	function fetch_single_news($id, $commenthead, $nocommenttext, $nocomments=false)
	{
		$path = $this->design().'/'.$this->newsbox;
		$comment_path = $this->design().'/'.$this->commentbox;
		if(!file_exists($path) || !file_exists($comment_path)) return false;

		$result = $this->getnewsbyid($id);

		if($result->num_rows == 0) {
			$this->news_error($id, 1);
			return false;
		}

		$news = $this->nextnews();
		$this->newsfetch($news, $path);

		if(!$news->comments) $disable_comments = true;
		else if($this->settings['Kommentare'] == 'aus') $disable_comments = true;
		else $disable_comments = false;

		if(!$disable_comments)
		{
			if($commenthead) echo $commenthead;
			else
			{
				echo "<p><a id=\"comments\"><u>Kommentare:</u></a></p><br />\n";
				echo '<div style="margin-left:30px;margin-right:20px;">', "\n";
			}
			$result = $this->getcomment($id);
			if($result->num_rows == 0) echo ($nocommenttext ? $nocommenttext : "<i>Keine Kommentare vorhanden</i>");
			else
			{
				while($comment = $this->getnextcomment())
				{
					$name = stripslashes($comment->name);
					$date = date("d.m.Y - H:i", $comment->date);
					$msg = stripslashes($comment->msg);
					$website = stripslashes($comment->website);
					include $comment_path;
				}
			}
			if(!$commenthead || strstr($commenthead, '<div')) echo "</div>\n";
			return true;
		}
		else
		{
			if($nocomments)
				echo '<p>',$nocomments, '</p>';
			else
				echo "<p>Die Kommentarfunktion ist abgeschaltet.</p>\n";
		}
	}

        // types: 0 => error, 1 => not found, else 404? or other
	function news_error($newsid, $type=0)
	{
		$Blog = $this;
		$nouser = true;
		$file = $this->design().'/'.$this->newsbox;
		if(!file_exists($file)) die("Fatal Error! Please contact admin!");

		$url = $this->rootpath.'/';
		$disable_comments = true;
		$writer = 'devbird';
		$date = date('d.m.Y - H:i',time());
		$id = $newsid;
		$com_count = '0';
		if($type==0) # error
		{
			$title = 'Ein Fehler ist aufgetreten';
			$er_file = $this->design().'/error.txt.php';
			if(is_readable($er_file))
			{
				$content = file_get_contents($er_file);
				if(preg_match("/^==\s(.+)\s==\n(.+)/s", $content, $found))
				{
					$title = $found[1];	
					$content = $found[2];
				}
			}
			else
				$content = "Leider ist beim Aufruf ein Fehler aufgetreten.<br /><br />\nBitte versuche es später noch einmal.<br />\nSollte es dann noch immer nicht funktioniern, kontaktiere mich doch einfach über das <a href=\"{$this->rootpath}/contact\">Kontaktformular</a>.<br />\nEine kleine Info, wie du zu dem Fehler gekommen bist, könnte mir helfen das Problem zu beseitigen.";
		}
		elseif($type==1) # not found
		{
			$title = 'Kein Eintrag gefunden';
			$er_file = ($this->design().'/noentry.txt.php');
			if(is_readable($er_file))
			{
				$content = file_get_contents($er_file);
				if(preg_match("/^==\s(.+)\s==\n(.+)/s", $content, $found))
				{
					$title = $found[1];	
					$content = $found[2];
				}
			}
			else
				$content = "Leider wurde kein Eintrag gefunden.<br /><br />\nBist du vielleicht einem fehlerhaften Link gefolgt?<br />\nDann kontaktiere mich doch bitte.<br />\nEine kleine Info, wie du zu dem Fehler gekommen bist, könnte mir helfen das Problem zu beseitigen.";
		}
		else {
			$title = "$type Error";
			$er_file = $this->design().'/http_error.txt.php';
			if(is_readable($er_file))
			{
				$content = file_get_contents($er_file);
				if(preg_match("/^==\s(.+)\s==\n(.+)/s", $content, $found))
				{
					$title = $found[1];	
					$title = str_replace("{type}", $type, $title);
					$content = $found[2];
				}
			}
			else
				$content = "Leider wurde die aufgerufene Seite nicht gefunden.<br /><br />\nBist du vielleicht einem fehlerhaften Link gefolgt?<br />\nDann kontaktiere mich doch bitte.<br />\nEine kleine Info, wie du zu dem Fehler gekommen bist, könnte mir helfen das Problem zu beseitigen.";
		}
		$tags = false;
		include $file;
	}

	function session()
	{
		$this->captcha = new MathCaptcha;

		$name = isset($_POST['nickname']) ? htmlspecialchars($_POST['nickname']) : '';
		$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
		$website = isset($_POST['webseite']) ? htmlspecialchars($_POST['webseite']) : '';
		$comment = isset($_POST['msg']) ? $_POST['msg'] : '';
		$newsid = isset($_POST['newsid']) ? $_POST['newsid'] : '';

		if($this->user->is_online())
		{
			$this->done['name'] = $this->user->name;
			$this->done['email'] = $this->user->email;
			$this->done['website'] = $this->rootpath;
		}

		if(isset($_COOKIE['devbird_comment_user_name']) || isset($_COOKIE['devbird_comment_user_mail']) || isset($_COOKIE['devbird_comment_user_site']))
		{
			$this->done['name'] = htmlspecialchars($_COOKIE['devbird_comment_user_name']);
			$this->done['email'] = htmlspecialchars($_COOKIE['devbird_comment_user_mail']);
			$this->done['website'] = htmlspecialchars($_COOKIE['devbird_comment_user_site']);
		}

		if(isset($_POST['comment_submit']))
		{
			$this->done['name'] = $name;
			$this->done['email'] = $email;
			$this->done['website'] = $website;
			$this->done['comment'] = $comment;

			$this->done['msg'] = false;
			$this->done['error'] = false;

			if(empty($_POST['nickname']) || empty($_POST['email']) || empty($_POST['solution']) || empty($_POST['msg']) || empty($_POST['human']))
 			{
				$this->generate_question();
				$msg = 'Bitte alle Pflichtfelder ausfüllen';

				$this->done['error'] = "<p style=\"color:red;\">$msg</p>\n";
				return;
			}
			elseif(md5($_POST['solution']) != $_POST['human'])#!$this->captcha->Check($_POST['solution']))
			{
				$this->generate_question();
				$msg = 'Captcha war falsch';

				$this->done['error'] = "<p style=\"color:red;\">$msg</p>\n";
				return;
			}
			elseif(!ereg("^.+@.+\\..+$", $email))
			{
				$this->generate_question();
				$msg = 'Ungültige eMail-Adresse';

				$this->done['error'] = "<p style=\"color:red;\">$msg</p>\n";
				return;
			}
			else {
	  			$comment = nl2br(htmlspecialchars($comment));

				if(isset($_POST['cookie_save']) && $_POST['cookie_save'] == 'on')
				{
					setcookie('devbird_comment_user_name', $name, time()+60*60*24*30, '/');
					setcookie('devbird_comment_user_mail', $email, time()+60*60*24*30, '/');
					setcookie('devbird_comment_user_site', $website, time()+60*60*24*30, '/');
				}

				if($this->save_comment($newsid, $name, $email, $website, $comment))
				{
					$this->done['msg'] = "<p style=\"color:green\">Der Kommentar wurde gespeichert</p>\n";
					$this->generate_question();
				}
				else
				{
					$this->done['error'] = "<p style=\"color:red\">Fehler beim Speichern des Kommentars.</p>\n";
					$this->generate_question();
				}
				return;
			}
		}
		else
		{
			$this->done['msg'] = false;
			$this->done['error'] = false;
			$this->generate_question();
		}
	}

	function atomfeed()
	{
		session_cache_limiter('private');

#		header("Pragma: public");
#		header("Expires: 0");
#		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: application/atom+xml");
#		header("Content-Transfer-Encoding: binary");

		$feed_title = $this->settings['Blogname'];
		$feed_link = $this->settings['Bloglink'];
		$feed_desc = $this->settings['Blogbeschreibung'];

		$user = 'devbird';
		$userq = $this->query("SELECT name FROM {user} WHERE id = '1'");
		if($userq)
		{
			if($userr = $userq->fetch_object())
				$user = $userr->name;
		}

		echo "<?xml version=\"1.0\" encoding=\"{$this->encoding}\" ?>\n";
		echo "<feed xmlns=\"http://www.w3.org/2005/Atom\">\n";
		echo " <title>{$feed_title}</title>\n";
		echo " <link rel=\"alternate\" type=\"text/html\" href=\"{$feed_link}\"/>\n";
		echo " <link rel=\"self\" type=\"application/atom+xml\" href=\"{$feed_link}/feed/atom\" />\n";
		echo " <author>\n";
		echo "  <name>{$user}</name>\n";
		echo " </author>\n";
		echo " <id>{$feed_link}</id>\n";
		echo " <generator uri=\"http://www.badboy.pytalhost.de/\" version=\"".Devbird::Version."\">Devbird</generator>\n";
		$result = $this->getnews(0, 1);
		$rr;
		$date = $this->date3339();
		if($result && $result->num_rows > 0 && ($rr = $result->fetch_object()) )
		{
			$date = $this->date3339($rr->published);
		}
		echo " <updated>{$date}</updated>\n";
		echo "\n";

		$result = $this->getnews(0, 10);
		if(!$result) {
			$this->atomfeed_error(0);
		}
		elseif($result->num_rows == 0) {
			$this->atomfeed_error(1);
		}
		else {

			while(($news = $this->nextnews()))
			{
				$title = stripslashes($news->title);
				$date = $this->date3339($news->published);
				$content = stripslashes($news->message);
				$id = $news->id;
				$url = $this->rootpath.'/'.$id.'/'.$this->shorttext($title);

				echo " <entry>\n";
				echo "  <title>{$title}</title>\n";
				echo "  <link href=\"{$url}\"/>\n";
				echo "  <id>{$url}</id>\n";
				echo "  <updated>{$date}</updated>\n";
				echo "  <content type=\"html\">\n<![CDATA[\n{$content}\n]]>\n</content>\n";
				echo " </entry>\n";
			}
		}
		echo "</feed>";
	}

	function rssfeed()
	{
		session_cache_limiter('private');

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: application/xml");
		header("Content-Transfer-Encoding: binary");
		

		$feed_title = $this->settings['Blogname'];
		$feed_link = $this->settings['Bloglink'];
		$feed_desc = $this->settings['Blogbeschreibung'];

		echo "<?xml version=\"1.0\" encoding=\"{$this->encoding}\" ?>\n";
		echo "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
		echo "<channel>\n";
		echo " <atom:link href=\"{$feed_link}/feed.php\" rel=\"self\" type=\"application/rss+xml\" />\n";
		echo " <title>{$feed_title}</title>\n";
		echo " <link>{$feed_link}</link>\n";
		echo " <description>{$feed_desc}</description>\n";
		echo " <generator>Devbird v". Devbird::Version ." - http://www.badboy.pytalhost.de/</generator>\n";

		echo "\n";

		$result = $this->getnews(0, 10);
		if(!$result) {
			$this->rssfeed_error(0);
		}
		elseif($result->num_rows == 0) { 
			$this->rssfeed_error(1);
		}
		else {

			while(($news = $this->nextnews()))
			{
				$title = stripslashes($news->title);
				$date = date("r",$news->published);
				$content = stripslashes($news->message);
				$id = $news->id;
				$url = $this->rootpath.'/'.$id.'/'.$this->shorttext($title);

				echo " <item>\n";
				echo "  <title>{$title}</title>\n";
				echo "  <description>\n<![CDATA[\n{$content}\n]]>\n</description>\n";
				echo "  <link>{$url}</link>\n";
				echo "  <guid>{$url}</guid>\n";
				echo "  <pubDate>{$date}</pubDate>\n"; 
				echo " </item>\n";
			}
		}
		echo "</channel>\n";
		echo "</rss>";
	}

	function buildtitle()
	{
		if(!$_GET) return 'Home';#
		if(isset($_GET['tag'])) return 'Tagsuche: '.htmlspecialchars($_GET['tag']);
		if(isset($_GET['q'])) return 'Suche: '.htmlspecialchars($_GET['q']);
		if(isset($_GET['id'])) 
		{
			$res = $this->getnewsbyid($_GET['id'], 'title');
			if($res)
			{
				$news = $res->fetch_object();
				return stripslashes($news->title);
			}
			else
				return 'Fehler!';
		}
		if(isset($_GET['site'])) 
		{
			$page = $this->get_page($_GET['site']);
			if($page)
				return stripslashes($page->title);
			else
				return 'Fehler!';
		}
	}

private
	function date3339($timestamp=0)
	{
		if(!$timestamp)
		{
			$timestamp = time();
		}
		$date = date('Y-m-d\TH:i:s', $timestamp);
		$tz = date('O', $timestamp);

		return $date.substr($tz, 0, 3).':'.substr($tz, 3);
	}

	function rssfeed_error($type)
	{
		$date = date("d.m.Y - H:i");

		if($type != 1) # error
		{
			$title = 'Ein Fehler ist aufgetreten';
			$content = "Leider ist beim Aufruf ein Fehler aufgetreten.<br /><br />\nBitte versuche es später noch einmal.<br />\nSollte es dann noch immer nicht funktioniern, kontaktiere mich doch einfach über das <a href=\"{$this->rootpath}/contact\">Kontaktformular</a>.<br />\nEine kleine Info, wie du zu dem Fehler gekommen bist, könnte mir helfen das Problem zu beseitigen.";
		}
		else # type = 1
		{
			$title = 'Kein Eintrag gefunden';
			$content = "Leider wurde kein Eintrag gefunden.<br /><br />\nBist du vielleicht einem fehlerhaften Link gefolgt?<br />\nDann kontaktiere mich doch gleich über das <a href=\"{$this->rootpath}/contact\">Kontaktformular</a>.<br />\nEine kleine Info, wie du zu dem Fehler gekommen bist, könnte mir helfen das Problem zu beseitigen.";
		}

		echo " <item>\n";
		echo "  <title>{$title}</title>\n";
		echo "  <description>\n<![CDATA[\n{$content}\n]]>\n</description>\n";
		echo "  <link>{$this->rootpath}</link>\n";
		echo "  <guid>{$this->rootpath}</guid>\n";
		echo "  <pubDate>{$date}</pubDate>\n";
		echo " </item>\n";
	}

	function atomfeed_error($type)
	{
		$date = date("d.m.Y - H:i");

		if($type != 1) # error
		{
			$title = 'Ein Fehler ist aufgetreten';
			$content = "Leider ist beim Aufruf ein Fehler aufgetreten.<br /><br />\nBitte versuche es später noch einmal.<br />\nSollte es dann noch immer nicht funktioniern, kontaktiere mich doch einfach über das <a href=\"{$this->rootpath}/contact\">Kontaktformular</a>.<br />\nEine kleine Info, wie du zu dem Fehler gekommen bist, könnte mir helfen das Problem zu beseitigen.";
		}
		else # type = 1
		{
			$title = 'Kein Eintrag gefunden';
			$content = "Leider wurde kein Eintrag gefunden.<br /><br />\nBist du vielleicht einem fehlerhaften Link gefolgt?<br />\nDann kontaktiere mich doch gleich über das <a href=\"{$this->rootpath}/contact\">Kontaktformular</a>.<br />\nEine kleine Info, wie du zu dem Fehler gekommen bist, könnte mir helfen das Problem zu beseitigen.";
		}

		echo " <item>\n";
		echo "  <title>{$title}</title>\n";
		echo "  <description>\n<![CDATA[\n{$content}\n]]>\n</description>\n";
		echo "  <link>{$this->rootpath}</link>\n";
		echo "  <guid>{$this->rootpath}</guid>\n";
		echo "  <pubDate>{$date}</pubDate>\n"; 
		echo " </item>\n";
	}

	function save_comment($id, $name, $email, $website, $comment)
	{
		if(!empty($website)) {
   			if(substr($website, 0,7) != "http://") $website = "http://".$website;
		}

		$id = $this->DB->real_escape_string($id);
		$name = $this->DB->real_escape_string($name);
		$email = $this->DB->real_escape_string($email);
		$website = $this->DB->real_escape_string($website);
		$comment = $this->DB->real_escape_string($comment);
		
		$p = $this->getnewsbyid($id);
		if(!$p) return false;
		$p = $p->fetch_object();
		if(!$p) return false;

		$ip = $this->DB->real_escape_string($_SERVER['REMOTE_ADDR']);
		$date = time();

		$sql = "INSERT INTO {news_comments} (news_id, name, email, website, msg, date, ip) VALUES ('$id', '$name', '$email', '$website', '$comment', '{$date}', '".$ip."')";
		$ret = $this->query($sql);
#		echo $this->error();
		return ($ret ? true : false);
	}

	function generate_question()
	{
			$res = $this->captcha->Generate('de');
			$this->captcha_question = $res[0];
			$this->captcha_result = $res[1];
	}

	function shorttext($text)
	{
		$text = preg_replace('/[^a-zA-Z0-9]/', '-', $text);
		$text = preg_replace('/-{2,}/', '-', $text);
		$text = preg_replace('/-+$/', '', $text);
		return strtolower($text);
	}

	function get_max_entries()
	{
		$sql = "SELECT count(*) as count FROM {news} WHERE published > 0";
		$res = $this->query($sql);
		if(!$res) return 0;
		$ch = $res->fetch_object();
		return $ch->count;
	}

	function get_max_entries_for_tag($tag)
	{
		$sql = "SELECT COUNT(*) as count FROM {news} WHERE published > 0 AND tags LIKE '%{$tag}%'";
		$res = $this->query($sql);
		if(!$res) return 0;
		$ch = $res->fetch_object();
		return $ch->count;
	}
}


$Blog = new Devbird;
global $Blog;
?>
