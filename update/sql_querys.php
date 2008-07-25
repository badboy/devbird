<?
$add_pages = <<<QUERY
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
?>
