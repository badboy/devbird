<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

require "geshi/geshi.php";
#include "newbbcode.php";

function replace_smilies($str, $root) {
$smilys = array(
"]:->" => "{$root}/smilies/icon_evil.gif",
":idea:" => "{$root}/smilies/icon_idea.gif",
":)" => "{$root}/smilies/icon_smile.gif",
";)" => "{$root}/smilies/icon_wink.gif",
":roll:" => "{$root}/smilies/icon_rolleyes.gif",
":\\" => "{$root}/smilies/icon_confused.gif",
":lol:" => "{$root}/smilies/icon_lol.gif",
":O" => "{$root}/smilies/icon_surprised.gif",
":(" => "{$root}/smilies/icon_sad.gif",
"8-)" => "{$root}/smilies/icon_cool.gif",
":>" => "{$root}/smilies/icon_razz.gif",
":shock:" => "{$root}/smilies/icon_eek.gif",
":'(" => "{$root}/smilies/icon_cry.gif",
":mad:" => "{$root}/smilies/icon_mad.gif",
":D" => "{$root}/smilies/icon_biggrin.gif",
":green:" => "{$root}/smilies/icon_mrgreen.gif",
":|" => "{$root}/smilies/icon_neutral.gif",
":!:" => "{$root}/smilies/icon_exclaim.gif",
":?:" => "{$root}/smilies/icon_question.gif",
":>:" => "{$root}/smilies/icon_arrow.gif",
":red:" => "{$root}/smilies/icon_redface.gif",
":evil:" => "{$root}/smilies/icon_twisted.gif",
); 
 foreach($smilys as $sm => $link)
  $str = str_replace($sm, "<img src=\"{$link}\" alt=\"{$sm}\" />", $str);
return $str;
}

function replace_ubbcode($str, $extendedbbcode=false, $stdlang=false, $rootpath) {
// code
 $code_ids = array();
 while(($ereg= preg_match("/\[code(=(.+?))*\](.+?)\[\/code\]/s", $str,$reg)))
 {
  $code_id = 0;
  do
  {
      $code_id = mt_rand(100, 10000);
  }
  while(array_key_exists($code_id, $code_ids));

  $lang = $reg[2];
  $str2 = $reg[3];
  if($lang === null || $lang == "")
  {
    if($stdlang) $lang = $stdlang;
    else $lang = 'c';
  }

  $humanreadable_lang = lang_name($lang);
  if($humanreadable_lang == " (unknown language)")
    $humanreadable_lang = $lang.$humanreadable_lang;

  $str2 = stripslashes($str2);

  $str2 = geshi_highlight($str2, $lang, null, true);
  $str2 = eregi_replace("\\\\([ntr])", '\\\\\\\\1', $str2);
  $str2 = eregi_replace("<br />", "", $str2);
  $str2 = preg_replace("/\n<\/span><\/code>$/", '</span></code>', $str2);

  $code_ids[$code_id] = "</p>\n<pre class=\"code\">\n{$str2}\n</pre>\n<p>";
  $str = preg_replace("/\[code(=({$lang}))*\](.+?)\[\/code\]/s", "[code_id={$code_id}/]", $str, 1);
 }

 $str = htmlspecialchars($str);
 $str = nl2br($str);

 $str = eregi_replace("\\\\\\\\", "\\", $str);
 $str = eregi_replace("\\\\&quot;", "&quot;", $str);

# $str = "<p>".$str."</p>";
# $str = eregi_replace("\n.\n", "</p><p>", $str);
# $str = eregi_replace("\n", "<br />\n", $str);
# $str = eregi_replace("</p><p>", "</p>\n<p>", $str);

  $str = eregi_replace("=== ([^=]+) ===(<br />)?", "</p>\n<h4>\\1</h4>\n<p>", $str);
  $str = eregi_replace("== ([^=]+) ==(<br />)?", "</p>\n<h3>\\1</h3>\n<p>", $str);


# $str = eregi_replace("_{([^}]+)}", "<sub>\\1</sub>", $str);
# $str = eregi_replace("\^{([^}]+)}", "<sup>\\1</sup>", $str);
// formatting
 $str = eregi_replace("\\[b]([^\\[]*)\\[/b\\]","<strong>\\1</strong>", $str); // bold|strong
 $str = eregi_replace("\\[i]([^\\[]*)\\[/i\\]","<i>\\1</i>", $str); // kursiv
 $str = eregi_replace("\\[u]([^\\[]*)\\[/u\\]","<u>\\1</u>", $str); // underline
 $str = preg_replace('/\[bq]([^\[]*)\[\/bq\]/i', "</p><blockquote><p>\\1</p></blockquote><p>", $str);
 if($extendedbbcode)
 {
   $str = eregi_replace("//([^/]+)//", "<i>\\1</i>", $str); // kursiv
   $str = eregi_replace("\\*\\*([^\\*]+)\\*\\*", "<strong>\\1</strong>", $str); // b|strong
   $str = eregi_replace("__([^_]+)__", "<u>\\1</u>", $str); // underline
 }
 $str = eregi_replace("\\[s]([^\\[]*)\\[/s\\]","<s>\\1</s>", $str); // strike
 $str = eregi_replace("\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]","<span style=\"color:\\1;\">\\2</span>", $str); // color

// flash
 $str = eregi_replace("\\[flash]([^\\[]*)\\[/flash\\]","<object data=\"\\1\" type=\"application/x-shockwave-flash\" style=\"width: 425px; height: 355px\">\n <param name=\"movie\" value=\"\\1\" />\n <param name=\"wmode\" value=\"transparent\" />\n</object>", $str);

// pic
 $str = eregi_replace("\\[img(=([^\\[]*))*\\]([^\\[]*)\\[/img\\]","<a href=\"\\3\" rel=\"lightbox[\\2]\"><img src=\"\\3\" alt=\"\\3\" /></a>", $str); // bild
 $str = eregi_replace("\\[img(=([^\\[]*))*\\]([^\\[]*)\\[/img,([0-9]+),([0-9]+)\\]","<a href=\"\\3\" rel=\"lightbox[\\2]\"><img src=\"\\3\" style=\"width:\\4px;height:\\5px\" alt=\"\\3\" /></a>", $str); // bild mit größe

// links
 $str = preg_replace("/\[\[(.+?)\|(.+?)\]\]/", "<a href=\"\\1\">\\2</a>", $str); // link with title
 $str = preg_replace("/\[\[(.+)\]\]/", "<a href=\"\\1\">\\1</a>", $str); // link without title
// $str = eregi_replace("\[\[(.+?)\|(.+?)\]\]", "<a href=\"\\1\">\\2</a>", $str); // link with title
// $str = eregi_replace("\[\[(.+)\]\]", "<a href=\"\\1\">\\1</a>", $str); // link without title

$str = eregi_replace("\\[center]([^\\[]*)\\[/center\\]", "</p>\n<p style=\"text-align:center\">\\1</p>\n<p>", $str);
$str = eregi_replace("\\[left]([^\\[]*)\\[/left\\]", "</p>\n<p style=\"text-align:left\">\\1</p>\n<p>", $str);
$str = eregi_replace("\\[right]([^\\[]*)\\[/right\\]","</p>\n<p style=\"text-align:right\">\\1</p>\n<p>", $str);

$str = replace_smilies($str, $rootpath);

$preg = array(
//lists
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\*(?::\w+)?\](.*?)(?=(?:\s*<br\s*\/?>\s*)?\[\*|(?:\s*<br\s*\/?>\s*)?\[\/?list)/si' => "\n<li class=\"bb-listitem\">\\1</li>",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list(:(?!u|o)\w+)?\](?:<br\s*\/?>)?/si'    => "\n</ul>",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list:u(:\w+)?\](?:<br\s*\/?>)?/si'         => "\n</ul>",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list:o(:\w+)?\](?:<br\s*\/?>)?/si'         => "\n</ol>",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(:(?!u|o)\w+)?\]\s*(?:<br\s*\/?>)?/si'   => "\n<ul class=\"bb-list-unordered\">",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list:u(:\w+)?\]\s*(?:<br\s*\/?>)?/si'        => "\n<ul class=\"bb-list-unordered\">",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list:o(:\w+)?\]\s*(?:<br\s*\/?>)?/si'        => "\n<ol class=\"bb-list-ordered\">",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=1\]\s*(?:<br\s*\/?>)?/si' => "\n<ol class=\"bb-list-ordered bb-list-ordered-d\">",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=i\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered bb-list-ordered-lr\">",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=I\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered bb-list-ordered-ur\">",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=a\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered bb-list-ordered-la\">",
          '/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=A\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered bb-list-ordered-ua\">",

          // [url]
          '/(?<!\\\\)\[url(?::\w+)?\]www\.(.*?)\[\/url(?::\w+)?\]/si'        => "<a href=\"http://www.\\1\">www.\\1</a>",
          '/(?<!\\\\)\[url(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'             => "<a href=\"\\1\">\\1</a>",
          '/(?<!\\\\)\[url(?::\w+)?=(.*?)?\](.*?)\[\/url(?::\w+)?\]/si'      => "<a href=\"\\1\">\\2</a>",
);

 $str = preg_replace(array_keys($preg), array_values($preg), $str);


// code
/*$i=0;
 while(($ereg= preg_match("/\[code(=(.+?))*\](.+?)\[\/code\]/s", $str,$reg)) && $i < 10)
 {
  $i++;
  $lang = $reg[2];
  $str2 = $reg[3];
  if($lang === null || $lang == "")
  {
    if($stdlang) $lang = $stdlang;
    else $lang = 'c';
  }

  $humanreadable_lang = lang_name($lang);
  if($humanreadable_lang == " (unknown language)")
    $humanreadable_lang = $lang.$humanreadable_lang;

  $str2 = eregi_replace("&quot;", "\"", $str2);
  $str2 = eregi_replace("&gt;", ">", $str2);
  $str2 = eregi_replace("&lt;", "<", $str2);
  $str2 = eregi_replace("<br />", "", $str2);

  $str2 = geshi_highlight($str2, $lang, null, true);
  $str2 = eregi_replace("\\\\n", '\\\\\\n', $str2);
  $str2 = eregi_replace("<br />", "", $str2);
  $str2 = preg_replace("/\n<\/span><\/code>$/", '</span></code>', $str2);

  $str = preg_replace("/\[code(=({$lang}))*\](.+?)\[\/code\]/s", "</p>\n<pre class=\"code\">\n{$str2}\n</pre>\n<p>", $str, 1);
 }*/
 #var_dump($code_ids);
  foreach($code_ids as $c_id => $code_str)
  {
      $out = 0;
      $str = preg_replace("/\[code_id={$c_id}\/\]/", $code_str, $str, 1, $out);
  }

return $str;
}

?>
