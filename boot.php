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
//Backend
require_once(rex_path::addon($mypage)."/functions/functions.inc.php");

//SettingForm bei Slice-ADD/EDIT einbinden
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



//Backend + Frontend
//Slice Output-Check (BE/FE)
rex_extension::register( 'SLICE_SHOW', function($ep){
	$o = new blockSettings();
	return $o->checkOnlinestatus($ep);
});




// Asstes im Backend einbinden (z.B. style.css) - es wird eine Versionsangabe angehängt, damit nach einem neuen Release des Addons die Datei nicht aus dem Browsercache verwendet wird
rex_view::addCssFile($this->getAssetsUrl('style.css?v=' . $this->getVersion()));
rex_view::addJsFile($this->getAssetsUrl('script.js?v=' . $this->getVersion()));
rex_view::addCssFile($this->getAssetsUrl('datepicker/jquery.datetimepicker.min.css?v=' . $this->getVersion()));
rex_view::addJsFile($this->getAssetsUrl('datepicker/jquery.datetimepicker.full.min.js?v=' . $this->getVersion()));
?>