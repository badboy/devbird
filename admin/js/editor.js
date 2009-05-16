var blogurl = "/admin";
var interval = null;

function insert(aTag, eTag) {
  var input = document.getElementById('article_content');
  input.focus();
  /* für Internet Explorer */
  if(typeof document.selection != 'undefined') {
    /* Einfügen des Formatierungscodes */
    var range = document.selection.createRange();
    var insText = range.text;
    range.text = aTag + insText + eTag;
    /* Anpassen der Cursorposition */
    range = document.selection.createRange();
    if (insText.length == 0) {
      range.move('character', -eTag.length);
    } else {
      range.moveStart('character', aTag.length + insText.length + eTag.length);      
    }
    range.select();
  }
  /* für neuere auf Gecko basierende Browser */
  else if(typeof input.selectionStart != 'undefined')
  {
    /* Einfügen des Formatierungscodes */
    var start = input.selectionStart;
    var end = input.selectionEnd;
    var insText = input.value.substring(start, end);
    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
    /* Anpassen der Cursorposition */
    var pos;
    if (insText.length == 0) {
      pos = start + aTag.length;
    } else {
      pos = start + aTag.length + insText.length + eTag.length;
    }
    input.selectionStart = pos;
    input.selectionEnd = pos;
  }
  /* für die übrigen Browser */
  else
  {
    /* Abfrage der Einfügeposition */
    var pos;
    var re = new RegExp('^[0-9]{0,3}$');
    while(!re.test(pos)) {
      pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
    }
    if(pos > input.value.length) {
      pos = input.value.length;
    }
    /* Einfügen des Formatierungscodes */
    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
  }
}

function editorAdd(val)
{
 var _obj = document.getElementById('msg');
 switch(val) {
  case 'b': 
   insert('[b]', '[/b]');
	break;
  case 'u': 
   alert('deprecated!\n<u> existiert in XHTML nicht. Unterstrichene Texte sollen nur für Links verwendet werden!');
   insert('[u]', '[/u]');
	break;
  case 'i': 
   insert('[i]', '[/i]');
	break;
  case 's':
	 insert('[s]', '[/s]');
	 break;
  case 'url': 
   var eingabe = window.prompt("Url: (http:// nicht vergessen!)", "http://");
   var _name = window.prompt("Name: (alternativ)");
   if(eingabe == null || eingabe == "http://") return;
   if(_name == null) 
     insert('[url]'+eingabe+'[/url]', ' ');
   else
     insert('[url='+eingabe+']'+_name+'[/url]', ' ');

	break;
  case 'left': 
   insert('[left]', '[/left]');
	break;
  case 'center': 
   insert('[center]', '[/center]');
	break;
  case 'right': 
   insert('[right]', '[/right]');
	break;
  case 'img': 
   insert('[img]', '[/img]');
	break;
  case 'newbox':
   insert('{{newbox=', '}}\n');
	break;
  case 'code': 
   insert('[code]', '[/code]');
	break;
  case 'flash':
   var _name = window.prompt("Link:");
   if(_name != null) insert('[flash]'+_name+'[/flash]', ' ');
	break;
  default:
    insert(val, ' ');
	break;
 }
}

function addTag(tag)
{
	var curtags = $('#a_tags').val();
    if(curtags.indexOf(tag) < 0)
	{
    	$('#a_tags').val(curtags + tag + ' ');
		tagcount += 1;
	}
}

function SaveArticleHandle(data)
{
 output = $('#status');
 id_output = $('#article_id');
 if(data.status == 'error')
 {
   switch(data.code)
   {
     case 1000: output.value = "Fehler beim Speichern. Daten konnten nicht übermittelt werden!";
		break;
     case 1001: output.value = "Ohne Text wird nichts gespeichert!";
		break;
     case 1002: output.value = "Artikel konnte nicht gespeichert werden: "+data.message;
		break;
     case 1003: output.value = "Artikel konnte nicht gespeichert werden: "+data.message;
		break;
     case 1004: output.value = "Fehler beim Lesen der Datenbank: "+data.message;
		break;
     case 1005: output.value = "Es wurde nichts zurückgeliefert!";
		break;
     case 1006: output.value = "Nicht eingeloggt!";
		break;
   }
 }
 else
 {
   id_output.value = data.id;
   output.value = "Gespeichert: "+data.date;
 }
}

function saveArticle()
{
 var params = 'js_save=true&'+$('#article_editor').serialize();

 $.getJSON(blogurl+'/ajax_save_article.php', params, function(data) {
	 output = $('#status');
	 id_output = $('#article_id');
	 if(data.status == 'error')
	 {
	   switch(data.code)
	   {
		 case 1000: output.val("Fehler beim Speichern. Daten konnten nicht übermittelt werden!");
			break;
		 case 1001: output.val("Ohne Text wird nichts gespeichert!");
			break;
		 case 1002: output.val("Artikel konnte nicht gespeichert werden: "+data.message);
			break;
		 case 1003: output.val("Artikel konnte nicht gespeichert werden: "+data.message);
			break;
		 case 1004: output.val("Fehler beim Lesen der Datenbank: "+data.message);
			break;
		 case 1005: output.val("Es wurde nichts zurückgeliefert!");
			break;
		 case 1006: output.val("Nicht eingeloggt!");
			break;
	   }
	 }
	 else
	 {
	   id_output.val(data.id);
	   output.val("Gespeichert: "+data.date);
	 } 
 });
}

function startSaving()
{
 saveArticle();
 interval = setInterval("saveArticle()", 180000);
}

function editorInit()
{
 if($('#article_id').val()==0)
 {
   window.onbeforeunload = confirmExit
 }
 setTimeout("startSaving()", 90000);
}

function editorClose()
{
 window.onbeforeunload = null;
 return true;
}

function confirmExit()
{
 return "Du bearbeitest gerade einen Artikel. Dieser ist noch nicht gespeichert. Wenn du die Seite jetzt verlässt, verschwinden alle Änderungen.\nBist du sicher?";
}

