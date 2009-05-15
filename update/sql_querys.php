<?

function generatePassword ($length = 8)
{

  $password = "";
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
  $i = 0; 
    
  while ($i < $length) { 
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }
  }
  return $password;
}


$edit_tables  = array();

$edit_tables[] = <<<QUERY
ALTER TABLE `{$db_prefix}news_comments` DROP `ip`  
QUERY;

$edit_tables[] = <<<QUERY
ALTER TABLE `{$db_prefix}user` ADD `salt` VARCHAR( 16 ) NOT NULL ;
QUERY;

$salt = mt_rand();
$new_password = generatePassword();
$hashed = sha1("--{$salt}--{$new_password}");
$edit_tables[] = <<<QUERY
UPDATE `{$db_prefix}user` SET `password` = '{$hashed}', `salt` = '{$salt}' WHERE `id` = 1 LIMIT 1;
QUERY;

?>
