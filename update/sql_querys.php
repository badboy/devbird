<?
$add_pages = <<<QUERY
DELETE FROM `{$db_prefix}settings` WHERE CONVERT(`{$db_prefix}settings`.`name` USING utf8) = 'Extra-BB-Codes' LIMIT 1
UPDATE `{$db_prefix}settings` SET `sort` = '8' WHERE CONVERT( `{$db_prefix}settings`.`name` USING utf8 ) = 'AJAX-Autosave' LIMIT 1 ;
UPDATE `{$db_prefix}settings` SET `sort` = '9' WHERE CONVERT( `{$db_prefix}settings`.`name` USING utf8 ) = 'Standardcodesprache' LIMIT 1 ;
QUERY;

?>
