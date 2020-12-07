<?php
/*
	Redaxo-Addon Blocksettings
	Verwaltung: Hilfe
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/
?>

<style>
.faq { margin: 0px !important; cursor: pointer; }
.faq + div { margin: 0px 0px 15px; }
</style>

<section class="rex-page-section">
	<div class="panel panel-default">

		<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1604_head_help'); ?></div></header>
        
		<div class="panel-body">
			<div class="rex-docs">
				<div class="rex-docs-sidebar">
                	<nav class="rex-nav-toc">
                    	<ul>
                        	<li><a href="#default">Allgemein</a>
                            <li><a href="#json">Definition der Eingabefelder</a>
                            <li><a href="#output">Abruf in der Modulausgabe</a>
                            <li><a href="#extras">Zusatzfunktionen</a>
                        </ul>
                    </nav>
        	    </div>

                
       		  <div class="rex-docs-content">
				<h1>Addon: <?php echo $this->i18n('a1604_title'); ?></h1>

				  <p>Mit dieser Erweiterung definieren Sie wiederkehrende Einstellungsmöglichkeiten für die Content-Blöcke (Slices), welche in der Blockausgabe ausgelesen und genutzt werden können.<br>
			      Eingabefelder können dabei in verschiedene Bereiche (Tabs) und optionale Gruppen eingeordnet werden.</p>
<p>Für die Feld-Definition stehen folgende Typen zur Verfügung:</p>
				<ul>
					  <li>Textfeld</li>
					  <li>Mehrzeiliges Textfeld (Textarea) mit und ohne Wysiwyg-Editor</li>
					  <li>Checkbox</li>
					  <li>Radiobuttons</li>
					  <li>Eingabefeld für Zahlen (Number)</li>
					  <li>Textfeld mit Farbwähler (Color)</li>
					  <li>Auswahl mittels Schieberegler (Range)</li>
					  <li>Klappfeld (Select)</li>
					  <li>Kalenderauswahl (mit und ohne Zeitauswahl)</li>
					  <li>Auswahl Redaxo-Medium</li>
					  <li>Auswahl Redaxo-Medienliste</li>
					  <li>Auswahl Redaxo-Link</li>
					  <li>Auswahl Redaxo-Linkliste<br>
				      </li>
                </ul>


<p>Weiterhin ist eine Zusatzfunktion zur Prüfung des Online-Status eines jeden Blockes integriert, um das zeitgesteuerte Anzeigen/Ausblenden von Blöcken zu ermöglichen.</p>
<p>&nbsp;</p>

<h2>Allgemein</h2>
                    
                    <!-- Allgemein -->
                    <a name="default"></a>
                    <h3>Bereich &quot;<?php echo $this->i18n('a1604_default'); ?>&quot;:</h3>
                    <p>In diesem Bereich definieren und strukturieren Sie Ihre gewünschten Felder im <a href="#json">JSON-Format</a>.</p>
                    <p>                    Weiterhin geben Sie hier an, bei welchen Pflegemodulen die Felder eingebunden werden sollen.<br>
                    Die Einbindung der Felder kann automatisch in alle Pflegemodule erfolgen, jedoch auch per Auswahl nur auf einige Module beschränkt werden.</p>

                <h3>Bereich &quot;<?php echo $this->i18n('a1604_config'); ?>&quot;:</h3>
                    <p>Wählen Sie hier  einen WYSIWYG-Editor aus, wenn Sie Eingabefelder mit Editor nutzen möchten.<br>
                    Über weitere Optionen können Sie die Editoreinbindung beeinflussen</p>
                    





<p>&nbsp;</p>

<h2>Definition der Eingabefelder</h2>
                    
                    <!-- Allgemein -->
                    <a name="json"></a>
                    
                    <p>Die Definition der Eingabefelder erfolgt im JSON-Format, welches in strukturierter Form angelegt wird.<br>
                    Zur Hinterlegung der Struktur stehen 3 Ebenen zur Verfügung, welche sich in Tabs, Gruppen und Felder aufteilen.</p>
                    <p>Das erste Element &quot;settings&quot; bildet dabei den Anfang und muss zwingend vorhanden sein.</p>
                <p>Anschließend definieren Sie die Tabs (&quot;tab&quot;), welche die Navigation abbilden und gut zum strukturieren Ihrer Felder anhand verschiedener Themen genutzt werden kann (z.B. Einstellungen für Design, Inhalte &amp; Sichtbarkeit).<br>
                      Die nächste Ebene bilden die Gruppen oder bereits die eigentlichen Felder. <br>
                  Mittels der Gruppen (&quot;groups&quot;) kann eine weitere, optionale Strukturierungsebene innerhalb eines Tabs erstellt werden. </p>
                    <p>Innerhalb der Definition der Felder (&quot;fields&quot;) legen Sie  Ihre gewünschten Felder mit deren Optionen an.<br>
                      Bei allen Feldern 
                ist der Name (&quot;name&quot;) und der Feldtyp (&quot;type&quot;) zwingend notwendig, um eine Feld erzeugen zu können.</p>
                <h3>JSON-Beispiel:</h3>


<pre style="height: 400px;">
<?php
echo rex_file::get(rex_addon::get('blocksettings')->getPath('data/example.json'));
?>
</pre>

<p>&nbsp;</p>


                <p><strong>                    Strukturebene und deren Optionen</strong></p>


                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <th width="200" scope="col">Strukturebene</th>
                        <th scope="col">Erklärung &amp; Attribute</th>
                    </tr>
                      <tr>
                        <td valign="top"><strong>settings<br>
</strong>(Pflichtangabe)</td>
                        <td valign="top">
                          Dieses Element kommt nur einmal in der Definition vor und dient der Einleitung der weiteren Struktur.
                            <pre>
{ "settings": [
		...
]}
</pre>
                        </td>
                      </tr>
                      <tr>
                        <td valign="top"><strong>Tabs<br>
</strong>(Pflichtangabe)</td>
                        <td valign="top">Erste Strukturierungsebene zur Ausgabe eines Navigationsmenüs.
                        <br>
<pre>
{
    "tab": "Name des 1. Menü-Elementes",
    "fields": [
        ...
    ]
},

{
    "tab": "Name des 2. Menü-Elementes",
    "groups": [
        ...
    ]
}
</pre></td>
                      </tr>
                      <tr>
                        <td valign="top"><strong>Gruppen</strong></td>
                        <td valign="top">Mit zusätzlichen Gruppen können nachfolgende Felder in einer zweiten Ebene gruppiert werden.
                        <br>
<pre>
{
    "tab": "...",
    "groups": [
        {
            "name": "Name der Gruppe",
            "inlinefields": true,
            "fields": [
                ...
            ]
        }
    ]
}
</pre>

<strong>&quot;inlinefields&quot;</strong> = true|false<br>
Mit dieser Option wird für alle nachfolgenden Felder die zeilenweise Ausgabe entfernt, so dass diese als Inline-Blöcke nebeneinander gestellt werden können.<br>
Für eine sinnvolle Nutzung dieser Option sollten die Felder mit dem  &quot;width&quot;-Attribut auf ein passende Breite eingestellt werden.<br> &nbsp;</td>
                      </tr>
                      <tr>
                        <td valign="top"><strong>Felder</strong></td>
                        <td valign="top">Angabe der gewünschten Eingabefelder, welche entsprechend der Definition nacheinander ausgegeben werden.
                        <br>
<pre>
{
    "tab": "...",
    "fields": [
        {
            "name": "blockAlign",
            "type": "select",
            "label": "Ausrichtung",
            "value": {
                "left": "links",
                "center": "mittig",
                "right": "rechts"
            },
            "multiple": false,
            "default": "0"
        },
        ...
    ]
},

{
    "tab": "...",
    "groups": [
        {
            "name": "...",
            "fields": [
                {
                    "name": "cssClass",
                    "type": "text",
                    "label": "CSS-Klasse",
                    "maxlength": 30
                },
                ...
            ]
        },
        ...
    ]
}
</pre></td>
                      </tr>
                  </table>
                  
                  
                  
                  <p>&nbsp;</p>

                <p><strong>                    Feldtypen und deren Optionen</strong></p>
                <p>Für alle Felder können verschiedene Optionen definiert werden, um die Generierung der Ausgabe  beeinflussen zu können.<br>
                  Um jedoch eine sinnvolle Generierung der Ausgabe gewährleisten zu können, müssen <strong>mindestens die Attribute &quot;name&quot;, &quot;type&quot; und &quot;label&quot;</strong> angegeben werden.<br>
                Alle anderen Attribute (z.B. &quot;width&quot; oder &quot;suffix&quot;) sind optional und können bei Bedarf weggelassen werden.</p>
                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f001"><span class="caret"></span> Textfeld (type = text)</p>
                <div id="f001" class="collapse">Erstellt ein einfaches Text-Eingabefeld.<br>
<pre>
{
    "name": "cssClass",
    "type": "text",
    "label": "CSS-Klasse",
    "value": "",
    "placeholder": "Angabe einer optionalen CSS-Klasse",
    "maxlength": 30
    "width": &quot;&quot;,
    "prefix": "",
    "suffix": ""
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes .</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top">Vorbelegung des Feldes mit diesem Wert.</td>
  </tr>
  <tr>
    <td valign="top"><strong>placeholder</strong></td>
    <td valign="top">Angabe eines Platzhaltertextes für weitere Erklärungen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>maxlength</strong></td>
    <td valign="top">Angabe einer maximalen Länge für Werteeingaben.</td>
  </tr>
  <tr>
    <td valign="top"><strong>width</strong></td>
    <td valign="top">Einstellung des Feldes auf eine bestimmte Zielbreite (Pixel).<br>
      Mit diese Option, in Verbindung mit Gruppen und Attribut &quot;
        inlinefields&quot;), lassen sich mehrere Felder nebeneinander stellen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>prefix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt vor dem Feld aus.<br>
      Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>suffix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt nach dem Feld aus.<br>
Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>
       
       
                    


                <p class="faq text-danger" data-toggle="collapse" data-target="#f002"><span class="caret"></span> Mehrzeiliges Textfeld (type = textarea)</p>
                <div id="f002" class="collapse">Erstellt ein mehrzeiliges Text-Eingabefeld (mit oder ohne Wysiwyg-Editor). <br>
<pre>
{
    "name": "infoText",
    "type": "textarea",
    "label": "Zusatzinformation",
    "value": "",
    "placeholder": "Hinterlegen Sie hier eine zusätzliche Information",
    "editor": true
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top">Vorbelegung des Feldes mit diesem Wert.</td>
  </tr>
  <tr>
    <td valign="top"><strong>placeholder</strong></td>
    <td valign="top">Angabe eines Platzhaltertextes für weitere Erklärungen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>width</strong></td>
    <td valign="top">Einstellung des Feldes auf eine bestimmte Zielbreite (Pixel).<br>
      Mit diese Option, in Verbindung mit Gruppen und Attribut &quot;
      inlinefields&quot;), lassen sich mehrere Felder nebeneinander stellen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>prefix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt vor dem Feld aus.<br>
      Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>suffix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt nach dem Feld aus.<br>
      Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>editor</strong></td>
    <td valign="top">Bindet den Wysiwyg-Editor beim entsprechenden Textfeld ein.<br>
      Mögliche Werte: true|false
      <br>
      <br>
      Hinweis:<br>
      Um einen Wysiwyg-Editor nutzen zu können, muss der TinyMCE oder CKEditor als Addon installiert sein.&lt;br&gt;<br>
      Wählen Sie anschließend im Einstellungsbereich den gewünschten Editor aus und geben bei Bedarf weitere Optionen an.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>



                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f003"><span class="caret"></span> Checkbox (type = checkbox)</p>
                <div id="f003" class="collapse">Erstellt eine Checkbox
                        zur Aktivierung einer Option.<br>
<pre>
{
    "name": "hideBlock",
    "type": "checkbox",
    "label": "Block verstecken",
    "value": "checked",
    "checked": false,
    "placeholder": "Ja, diesen Block unsichtbar schalten"
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top">Zu speichernder Wert der Checkbox.</td>
  </tr>
  <tr>
    <td valign="top"><strong>checked</strong></td>
    <td valign="top">Setzt die Checkbox bereits auf aktiviert, sofern die Checkbox noch nicht gespeichert wurde. <br>
      Mögliche Werte: true|false </td>
  </tr>
  <tr>
    <td valign="top"><strong>placeholder</strong></td>
    <td valign="top">Angabe eines anklickbaren Textes zum Aktivieren der Checkbox (Label).</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f004"><span class="caret"></span> Radiobuttons (type = radio)</p>
                <div id="f004" class="collapse">Erstellt eine Optionsauswahl als Radiobuttons.
                        <br>
<pre>
{
    "name": "cssClass",
    "type": "radio",
    "label": "Ausgabe-Stil",
    "value": {
        &quot;theme1&quot;: &quot;Gestaltung 1&quot;,
        &quot;theme2&quot;: &quot;Gestaltung 2&quot;,
        &quot;theme3&quot;: &quot;Gestaltung 3&quot;
    }
    "default": &quot;theme1&quot;
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top"> Angabe der möglichen Werte als Struktur (&quot;Wert&quot;: &quot;Titel&quot;).</td>
  </tr>
  <tr>
    <td valign="top"><strong>default</strong></td>
    <td valign="top">Angabe des vor auszuwählenden Wertes aus der Liste der möglichen Werte.
      <br>
      Als Angabe wird der &quot;Wert&quot; erwartet.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f005"><span class="caret"></span> Eingabefeld für Zahlen  (type = number)</p>
                <div id="f005" class="collapse">Erstellt ein Eingabefeld
                        für Zahlen mit Erhöhen/Vermindern-Schaltflächen.<br>
<pre>
{
    "name": "paddingTop",
    "type": "number",
    "label": "oben",
    "value": "0"
    "maxlength": 3
    &quot;min&quot;: -100,
    &quot;max&quot;: 100,
    "width": &quot;75&quot;,
    "prefix": "",
    "suffix": "px"
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top">Vorbelegung des Feldes mit diesem Wert.</td>
  </tr>
  <tr>
    <td valign="top"><strong>maxlength</strong></td>
    <td valign="top">Angabe einer maximalen Länge für Werteeingaben.</td>
  </tr>
  <tr>
    <td valign="top"><strong>min</strong></td>
    <td valign="top">Hinterlegung des minimal gültigen Wertes für die Eingabe.
  </td>
  </tr>
  <tr>
    <td valign="top"><strong>max</strong></td>
    <td valign="top">Hinterlegung des maximal gültigen Wertes für die Eingabe. </td>
  </tr>  <tr>
    <td valign="top"><strong>width</strong></td>
    <td valign="top">Einstellung des Feldes auf eine bestimmte Zielbreite (Pixel).<br>
      Mit diese Option, in Verbindung mit Gruppen und Attribut &quot;
        inlinefields&quot;), lassen sich mehrere Felder nebeneinander stellen.</td>
  </tr>

  <tr>
    <td valign="top"><strong>prefix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt vor dem Feld aus.<br>
      Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>suffix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt nach dem Feld aus.<br>
Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f006"><span class="caret"></span> Textfeld mit Farbwähler (type = color)</p>
                <div id="f006" class="collapse">Erstellt ein  Text-Eingabefeld
                        mit zusätzlichem HTML5-Farbwähler.<br>
<pre>
{
    "name": "bgColor",
    "type": "color",
    "label": "Hintergrundfarbe",
    "value": "#FFFFFF",
    "placeholder": "Bsp: #11AA99"
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top">Vorbelegung des Feldes mit diesem Wert.</td>
  </tr>
  <tr>
    <td valign="top"><strong>placeholder</strong></td>
    <td valign="top">Angabe eines Platzhaltertextes für weitere Erklärungen.</td>
  </tr>
  </table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f007"><span class="caret"></span> Auswahl mittels Schieberegler (type = range)</p>
                <div id="f007" class="collapse">Erstellt einen Schieberegler als Range-Auswahlfeld.<br>
<pre>
{
    "name": "quality",
    "type": "range",
    "label": "Qualität",
    "value": 1
    "min": 1,
    "max": 100,
    "step": 1,
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes .</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top">Vorbelegung des Feldes mit diesem Wert.</td>
  </tr>
  <tr>
    <td valign="top"><strong>min</strong></td>
    <td valign="top">Hinterlegung des minimal gültigen Wertes für die Eingabe.
  </td>
  </tr>
  <tr>
    <td valign="top"><strong>max</strong></td>
    <td valign="top">Hinterlegung des maximal gültigen Wertes für die Eingabe. </td>
  </tr>
  <tr>
    <td valign="top"><strong>step</strong></td>
    <td valign="top">Hinterlegung der Schrittweise beim Verschieben des Reglers.
</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f008"><span class="caret"></span> Klappfeld (type = select)</p>
                <div id="f008" class="collapse">Erstellt ein Auswahlklappfeld (Select).<br>
<pre>
{
    "name": "blockAlign",
    "type": "select",
    "label": "Ausrichtung",
    "value": {
        &quot;left&quot;: &quot;links&quot;,
        &quot;center&quot;: &quot;mittig&quot;,
        &quot;right&quot;: &quot;rechts&quot;
    },
    "multiple": false,
    "default": "left",
    "width": &quot;&quot;,
    "prefix": "",
    "suffix": ""
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes .</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>value</strong></td>
    <td valign="top">Angabe der möglichen Werte als Struktur (&quot;Wert&quot;: &quot;Titel&quot;).</td>
  </tr>
  <tr>
    <td valign="top"><strong>width</strong></td>
    <td valign="top">Einstellung des Feldes auf eine bestimmte Zielbreite (Pixel).<br>
      Mit diese Option, in Verbindung mit Gruppen und Attribut &quot;
      inlinefields&quot;), lassen sich mehrere Felder nebeneinander stellen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>prefix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt vor dem Feld aus.<br>
      Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>suffix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt nach dem Feld aus.<br>
Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>multiple</strong></td>
    <td valign="top">Aktivierung der Option zur Mehrfachauswahl von Werten<br>
    Mögliche Werte: true | false </td>
  </tr>
  <tr>
    <td valign="top"><strong>default</strong></td>
    <td valign="top">Angabe des vor auszuwählenden Wertes aus der Liste der möglichen Werte. <br>
Als Angabe wird der &quot;Wert&quot; erwartet.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f009"><span class="caret"></span> Kalenderauswahl (type = date|datetime)</p>
                <div id="f009" class="collapse">Erstellt ein Eingabefeld
                        mit einer Kalenderauswahl für ein Datum in deutschem Format (&quot;dd.mm.jjjj&quot; / &quot;dd.mm.jjjj hh:ss&quot;).<br>
<pre>
{
    "name": "onlineFrom",
    "type": "date",
    "label": "Sichtbar vom"
    "prefix": "",
    "suffix": ""
}

{
    "name": "onlineTo",
    "type": "datetime",
    "label": "Sichtbar bis"
    "prefix": "",
    "suffix": ""
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.<br>
      Mögliche Werte: date = Auswahl Datum | datetime = Auswahl Datum &amp; Zeit</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>prefix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt vor dem Feld aus.<br>
      Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>suffix</strong></td>
    <td valign="top">Gibt eine zusätzliche Information direkt nach dem Feld aus.<br>
Tipp: Die Information sollte nur wenige Zeichen umfassen.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f010"><span class="caret"></span> Redaxo Media-Widget (type = rexmedia)</p>
                <div id="f010" class="collapse">Bindet das Redaxo-Widget zur Auswahl eines Mediums (Mediapool) ein.<br>
<pre>
{
    "name": "bgImg",
    "type": "rexmedia",
    "label": "Hintergrundbild",
    "mediatypes": "gif,jpg,jpeg,png"
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>mediatypes</strong></td>
    <td valign="top">Angabe der gültigen Mediatypen für die Auswahl innerhalb des Mediapools.
      <br>
      Die Angabe erfolgt analog der Angabe in den üblichen REX_MEDIA-Platzhaltern.</td>
  </tr>
  </table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f011"><span class="caret"></span> Redaxo Medialist-Widget (type = rexmedialist)</p>
                <div id="f011" class="collapse">Bindet das Redaxo-Widget zur Auswahl von multiplen Medien (Mediapool) ein.<br>
<pre>
{
    "name": "sliderImages",
    "type": "rexmedialist",
    "label": "Slider-Bilder",
    "mediatypes": "gif,jpg,jpeg,png"
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>mediatypes</strong></td>
    <td valign="top">Angabe der gültigen Mediatypen für die Auswahl innerhalb des Mediapools.
      <br>
      Die Angabe erfolgt analog der Angabe in den üblichen REX_MEDIA-Platzhaltern.</td>
  </tr>
  </table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f012"><span class="caret"></span> Redaxo Link-Widget (type = rexlink)</p>
                <div id="f012" class="collapse">Bindet das Redaxo-Widget zur Auswahl eines Links (Linkmap) ein.<br>
<pre>
{
    "name": "cmsSite",
    "type": "rexlink",
    "label": "Auswahl CMS-Seite"
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>


                
                
<p class="faq text-danger" data-toggle="collapse" data-target="#f013"><span class="caret"></span> Redaxo Linklist-Widget (type = rexlinklist)</p>
                <div id="f013" class="collapse">Bindet das Redaxo-Widget zur Auswahl von multiplen Links (Linkmap) ein.<br>
<pre>
{
    "name": "articles",
    "type": "rexlinklist",
    "label": "Auswahl CMS-Artikel"
}
</pre>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Attribut</th>
    <th scope="col">Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>name<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
      Angabe eines <u>eindeutigen</u> Feldnames, welcher gleichzeitig zum Abruf der gespeicherten Werte genutzt wird. </td>
  </tr>
  <tr>
    <td valign="top"><strong>type<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">Angabe des auszugebenden Feldtypes.</td>
  </tr>
  <tr>
    <td valign="top"><strong>label</strong></td>
    <td valign="top">Hinterlegung der Feldbezeichnung, welche vor dem Feld als Text ausgegeben wird.</td>
  </tr>
</table>

<p>&nbsp;</p>

</div>




<p>&nbsp;</p>

<h2>Abruf in der Modulausgabe</h2>
                    
                    <!-- Allgemein -->
                    <a name="output"></a>
                    <p>Mit dem folgenden PHP-Code können alle vom  Redakteur im jeweiligen Block gespeicherten Werte abgerufen und individuell weiterverarbeitet werden.<br>
                      Die Rückgabe der Werte erfolgt dabei als Array.
                    </p>

<pre>
<code>$s = new blockSettings();
print_r( $s->getSettings(REX_SLICE_ID) );</code>
</pre>


<p>&nbsp;</p>

<p>Es kann natürlich auch ein einzelner Wert direkt ausgelesen werden.<br>
Mit dem folgenden Code rufen Sie gezielt einen Wert ab, welcher mit dem Feldnamen angesprochen wird.
</p>

<pre>
<code>$s = new blockSettings();
echo $s->getSettings(REX_SLICE_ID, 'blockWidth');</code>
</pre>

Beim gezielten Abruf von Werten steht noch ein weiterer Parameter zur  Konvertierung der Werterückgabe zur Verfügung.<br>
Fügen Sie als dritten Parameter ein 'int' oder 'time' hinzu, um die Rückgabe als Zahl oder Timestamp (Quellformat vorausgesetzt) zu erhalten:

<pre>
<code>$s = new blockSettings();
echo $s->getSettings(REX_SLICE_ID, 'blockWidth', 'int');        //intval() wird auf den Wert angewendet
echo $s->getSettings(REX_SLICE_ID, 'onlineFrom', 'time');       //Datumsformat wird in einen Timestamp gewandelt</code>
</pre>







<p>&nbsp;</p>

<h2>Zusatzfunktionen</h2>
                    
                    <!-- Allgemein -->
                    <a name="extras"></a>
                    <h3>Online-Status eines Blockes  realisieren:</h3>
                <p>Über eine  Zusatzfunktion im Addon kann ein  zeitgesteuerter Online-Status eines jeden Blockes realisiert werden.<br>
                Definieren Sie dazu 2 Kalenderfelder (type = date|datetime) und hinterlegen die Feldnamen in den Einstellungen dieses Addons.<br>
                  Anschließend wird der Online-Status automatisch bei der Block-Ausgabe mit den im Block hinterlegten Kalenderwerten geprüft und  die Ausgabe ggf. blockiert.                </p>
                <p>                  Hinweis: Soll keine automatische Prüfung durchgeführt werden, dann lassen Sie die beiden Felder in den Einstellungen einfach leer.                </p>
<p>Zusätzlich kann über den folgenden Funktionsaufruf innerhalb der Modulausgabe eine eigene Abfrage nach dem Online-Status durchgeführt werden:</p>
                    
<pre>
<code>$s = new blockSettings();
if ($s->getOnlinestatus(REX_SLICE_ID, 'onlineFrom', 'onlineTo')) {
    //Block ist sichtbar
} else {
    //Block ist nicht sichtbar
}</code>
</pre>
                    
                    <p>                    Für &quot;onlineFrom&quot; und &quot;onlineTo&quot; geben Sie dabei die Feldnamen Ihrer beiden definierten Kalenderfelder an.                    </p>
                    <p>&nbsp;</p>
                    
                    <h3>Fragen, Wünsche, Probleme?</h3>
                    Du hast einen Fehler gefunden oder ein nettes Feature parat?<br>
				Lege ein Issue unter <a href="<?php echo $this->getProperty('supportpage'); ?>" target="_blank"><?php echo $this->getProperty('supportpage'); ?></a> an. 
                    
</div>
			</div>

	  </div>
	</div>
</section>