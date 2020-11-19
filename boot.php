<?php
/*
	Redaxo-Addon Blocksettings
	Boot (weitere Konfigurationen)
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
	
	Info:
	Basisdaten wie Autor, Version, Subpages etc. werden in der package.yml notiert.
	Klassen und lang-Dateien werden automatisch gefunden (Ordnernamen beachten).
	Dateibasierte Konfigurationswerte nicht hier vornehmen !!! -> rex_config dafür nutzen (siehe install.php) !!!
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');



//Funktionen einladen/definieren
//Backend-Anpassungen
require_once(rex_path::addon($mypage)."/functions/functions.inc.php");
rex_view::addCssFile($this->getAssetsUrl('style.css'));
rex_view::addJsFile($this->getAssetsUrl('script.js'));


//SettingForm bei Slice-anzeigen einbinden
rex_extension::register( 'SLICE_SHOW', function($ep){
	$o = new blockSettings();
	return $o->appendForm($ep);
});


//SettingForm-Aktionen
rex_extension::register( array('SLICE_ADDED', 'SLICE_UPDATED'), function($ep){
	$o = new blockSettings();
	$o->saveSettings($ep);
});


//bloecks-Addon anbinden
rex_extension::register( 'SLICE_INSERTED', function($ep){
	$o = new blockSettings();
	$o->copySettings($ep);
});

?>