<?
$cur_site = 'ready';
include('header.php');
?>
<h1>Fertig</h1>
<p>Herzlichen Glückwunsch! Wenn du hier angelangt bist, hast du Devbird erfolgreich installiert.</p>
<br />
<p>Da Devbird nunmal aber nicht das professionellste Blogscript ist, musst du auch selbst noch ein bisschen Hand anlegen.</p>
<p>Aber auch das ist nicht schwer. Im folgenden werden alle Schritte beschrieben.</p>
<ol>
 <li>In der Datei javascript/lightbox.js müssen in Zeile 67 und 68 die Pfade zu den Bilddateien angepasst werden.</li>
 <li>In der Datei admin/js/editor.js in der Zeile 141 muss der Pfad zum Adminbereich angepasst werden</li>
 <li>Sowohl in der .htacces im Hauptordner als auch im Unterordner admin/ sollte bei Bedarf in der zweiten Zeile (RewriteBase) und in der letzten Zeile (ErrorDocument 404) der Pfad angepasst werden.</li>
 <li>Setze den chmod der Konfigurationsdatei config.php in config/ auf 644</li>
 <li>Lösche den Ordner install/ (oder bennene ihn wenigstens um)</li>
</ol>
<br />
<p>Schritte 1 und 2 kannst du aber auch automatisiert erledigen lassen.</p>
<p>Dazu einfach folgenden Link anklicken: <a href="changeit.php">Dateien anpassen</a></p>
<p>Es gibt keine Garantie, dass es klappt. Sollten die Dateien von PHP les- und schreibbar sein, sollte alles aber einwandfrei funktionieren <br />(das Script gibt Auskunft, ob es geklappt hat).</p>
<br />
<p>Nun bist du fertig und kannst deinen Blog benutzen -&gt; <a href="../">ab zu deinem neuen Blog</a></p>
<? include('footer.php'); ?>
