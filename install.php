<?php
/*
	Redaxo-Addon Blocksettings
	Installation
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Vorgaben vornehmen
if (!$this->hasConfig()):
	$this->setConfig('config', [
		'editor'			=> '',
		'editor_height'		=> '200',
		'editor_profile'	=> 'default',
	]);
endif;



//Datenbank-Einträge vornehmen
$db = rex_sql::factory();
$db->setQuery("CREATE TABLE IF NOT EXISTS ".rex::getTable('1604_blocksettings')." (
																`id` int(100) NOT NULL AUTO_INCREMENT,
																`status` varchar(10) NULL,
																`settings` text NOT NULL,
																`whitelistmode` varchar(10) NULL,
																`whitelist` text NOT NULL,
																PRIMARY KEY (`id`)
																) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Blocksettings - Settings'"
);

$db = rex_sql::factory();
$db->setQuery("INSERT INTO ".rex::getTable('1604_blocksettings')." (id) SELECT * FROM (SELECT '1') AS tmp WHERE NOT EXISTS ( SELECT id FROM ".rex::getTable('1604_blocksettings')." WHERE id = '1' ) LIMIT 1");			//nur für single-version



/*
$db = rex_sql::factory();
$db->setQuery("CREATE TABLE IF NOT EXISTS ".rex::getTable('1604_blocksettings_slice')." (
																`id` int(100) NOT NULL AUTO_INCREMENT,
																`id_slice` int(100) NOT NULL,
																`settings` text NOT NULL,
																PRIMARY KEY (`id`)
																) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Blocksettings - SliceSettings'"
);
*/

rex_sql_table::get(rex::getTable('article_slice'))
	->ensureColumn(new rex_sql_column('bs_settings', 'text'))
    ->alter();



//Module anlegen


//Aktionen anlegen


//Templates anlegen


//Data-Ordner kopieren
//rex_dir::copy($this->getPath('data'), $this->getDataPath());

?>