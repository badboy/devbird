<?
############################################
############################################
########## DO NOT EDIT THIS FILE! ##########
########## DO NOT EDIT THIS FILE! ##########
########## DO NOT EDIT THIS FILE! ##########
########## DO NOT EDIT THIS FILE! ##########
############################################
############################################

$drop_tables = array();
$sql_querys = array();

$db_prefix = $DB->real_escape_string($db_prefix);

$drop_tables[] = "DROP TABLE IF EXISTS `{$db_prefix}news`";
$sql_querys[] = <<<QUERY
CREATE TABLE IF NOT EXISTS `{$db_prefix}news` (
  `id` bigint(20) NOT NULL auto_increment,
  `created` int(11) NOT NULL,
  `published` int(11) NOT NULL default '0',
  `short_name` varchar(150) collate utf8_unicode_ci default NULL,
  `title` varchar(150) collate utf8_unicode_ci NOT NULL,
  `message` text collate utf8_unicode_ci NOT NULL,
  `writer` varchar(50) collate utf8_unicode_ci NOT NULL,
  `tags` varchar(150) collate utf8_unicode_ci default NULL,
  `bb_code` text collate utf8_unicode_ci NOT NULL,
  `ajax_saved` tinyint(4) NOT NULL default '0',
  `comments` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`,`message`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
QUERY;

$drop_tables[] = "DROP TABLE IF EXISTS `{$db_prefix}news_comments`";
$sql_querys[] = <<<QUERY
CREATE TABLE IF NOT EXISTS `{$db_prefix}news_comments` (
  `id` bigint(20) NOT NULL auto_increment,
  `news_id` bigint(20) NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `website` varchar(80) collate utf8_unicode_ci NOT NULL,
  `email` varchar(50) collate utf8_unicode_ci NOT NULL,
  `msg` text collate utf8_unicode_ci NOT NULL,
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  `public` tinyint(4) NOT NULL default '1',
  `read` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
QUERY;

$drop_tables[] = "DROP TABLE IF EXISTS `{$db_prefix}links`";
$sql_querys[] = <<<QUERY
CREATE TABLE IF NOT EXISTS `{$db_prefix}links` (
  `id` bigint(20) NOT NULL auto_increment,
  `category` int(11) NOT NULL default '1' COMMENT '1=blogroll, 0=menu',
  `sort` int(11) NOT NULL default '1' COMMENT 'only for menu',
  `link` varchar(100) collate utf8_unicode_ci NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `desc` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
QUERY;

$drop_tables[] = "DROP TABLE IF EXISTS `{$db_prefix}settings`";
$sql_querys[] = <<<QUERY
CREATE TABLE IF NOT EXISTS `{$db_prefix}settings` (
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL default '0',
  `value` varchar(150) collate utf8_unicode_ci default NULL,
  `possibilities` varchar(255) collate utf8_unicode_ci default NULL,
  `description` varchar(255) collate utf8_unicode_ci default NULL,
  `level` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
QUERY;

$drop_tables[] = "DROP TABLE IF EXISTS `{$db_prefix}user`";
$sql_querys[] = <<<QUERY
CREATE TABLE IF NOT EXISTS `{$db_prefix}user` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `rights` tinyint(11) NOT NULL default '0',
  `password` varchar(50) collate utf8_unicode_ci NOT NULL,
  `mail` varchar(150) collate utf8_unicode_ci NOT NULL,
  `last_login` int(11) NOT NULL default '0',
  `use_cookies` tinyint(4) NOT NULL default '0',
  `cookie_value` varchar(50) collate utf8_unicode_ci default NULL,
  `reseted_pw` varchar(20) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
QUERY;

$drop_tables[] = "DROP TABLE IF EXISTS `{$db_prefix}pages`";
$sql_querys[] = <<<QUERY
CREATE TABLE IF NOT EXISTS `{$db_prefix}pages` (
  `short_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `title` varchar(100) collate utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  `bb_code` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`short_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
QUERY;

$sql_order = array('news', 'news_comments', 'links', 'settings', 'user', 'pages');

$settings_insert = <<<SETTINGS
INSERT INTO `{$db_prefix}settings` (`name`, `type`, `value`, `possibilities`, `description`, `level`, `sort`) VALUES
('Blogname', 1, '{$all_blogname}', NULL, 'Der Name für diesen Blog', 1, 0),
('Blogbeschreibung', 1, '{$all_blogdesc}', NULL, 'Beschreibung des Blogs', 1, 1),
('Bloglink', 1, '{$path_url}', NULL, 'Link zum Blog', 1, 2),
('Blogroot', 1, '{$path_root}', NULL, 'der Pfad zum Blog', 1, 3),
('News pro Seite', 2, '5', '1-30', 'Anzahl der angezeigten News pro Seite', 1, 4),
('Design', 3, 'default', '\$dir:/design/', 'Das gewählte Design', 1, 5),
('Kommentare', 2, 'an', 'an|aus', 'Kommentare generell erlauben?', 1, 6),
('Zeichensatz', 2, 'UTF-8', 'UTF-8|ISO-8859-15', 'Der auf der Seite verwendete Zeichensatz', 1, 7),
('Extra-BB-Codes', 2, 'aus', 'an|aus', 'z.B. **fett**, __unterstrichen__ oder //italic//', 1, 8),
('AJAX-Autosave', 2, 'an', 'an|aus', 'Soll der Editor mittels AJAX Artikel automatisch zwischenspeichern?', 1, 9),
('Standardcodesprache', 3, 'c', '\$dir:/admin/geshi/geshi/ -.php', 'Standardmäßig verwendete Sprache für GeSHi (Syntaxhighlighter)', 1, 10);
SETTINGS;

$passwd = sha1($user_password);
$user_insert = <<<USERS
INSERT INTO `{$db_prefix}user` (`id`, `name`, `rights`, `password`, `mail`, `last_login`, `use_cookies`, `cookie_value`, `reseted_pw`) VALUES
(1, '{$user_name}', 15, '{$passwd}', '{$user_mail}', 0, 0, NULL, NULL);
USERS;

$date = time(0);
$first_article = <<<F_ARTICLE
INSERT INTO `{$db_prefix}news` (`created`, `published`, `short_name`, `title`, `message`, `writer`, `tags`, `bb_code`) VALUES
({$date}, {$date}, 'erster-artikel', 'Erster Artikel', 'Hey! Scheint, als ob Devbird fertig installiert ist.<br />\r\nDas hier jedenfalls ist der erste Artikel.<br />\r\nIm Admin-Menü kannst du ihn bearbeiten oder löschen.', 'Devbird', 'article main first blog devbird', 'Hey! Scheint, als ob Devbird fertig installiert ist.\r\nDas hier jedenfalls ist der erste Artikel. \r\nIm Admin-Menü kannst du ihn bearbeiten oder löschen.');
F_ARTICLE;

$first_links = <<<F_LINKS
INSERT INTO `{$db_prefix}links` (`category`, `sort`, `link`, `name`, `desc`) VALUES
(0, 1, 'index', 'Home', 'Zur Startseite'),
(0, 2, 'search.php', 'Suche', 'Suche und finde Artikel'),
(1, 0, 'http://badboy.pytalhost.de/', 'BadBoy_', 'Blog des Programmierers hinter Devbird ');
F_LINKS;

$date += 120;
$first_comment = <<<F_COMMENT
INSERT INTO `{$db_prefix}news_comments` (`news_id`, `name`, `website`, `email`, `msg`, `ip`, `date`, `public`, `read`) VALUES
(1, 'Devbird', '{$path_url}', '', 'Hey! Und hier siehst du auch gleich den ersten Kommentar. Wunderbar, nicht?', '0.0.0.0.', {$date}, 1, 0);
F_COMMENT;

?>
