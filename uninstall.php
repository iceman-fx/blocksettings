<?php
/*
	Redaxo-Addon Blocksettings
	Deinstallation
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = ""; $notice = "";


//Datenbank-Einträge löschen
$db = rex_sql::factory();
$db->setQuery("DROP TABLE IF EXISTS ".rex::getTable('1604_blocksettings'));


$db = rex_sql::factory();
$db->setQuery("DROP TABLE IF EXISTS ".rex::getTable('1604_blocksettings_slice'));


//Module löschen
//$notice .= $I18N->msg('a140_deletemodule');	//'Bitte löschen Sie die installierten Addon-Module von Hand.<br />';


//Aktionen löschen
//$notice .= 'Bitte löschen Sie die installierten Addon-Aktionen von Hand.<br />';


//Templates löschen
//$notice .= 'Bitte löschen Sie die installierten Addon-Templates von Hand.<br />';


//Data-Ordner löschen
rex_dir::delete($this->getDataPath());

?>