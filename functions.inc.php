<?php
/*
	Redaxo-Addon Blocksettings
	Globale-Funktionen
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/


/*
Auslesen von Settings in der Modulausgabe:

$s = new blockSettings();
print_r( $s->getSettings(REX_SLICE_ID) );					//gibt alle Settings als Array aus
echo $s->getSettings(REX_SLICE_ID, 'feldname');				//gibt Setting-Wert des Feldes 'feldname' aus
*/


class blockSettings
{
	public function __construct()
	{	//JSON-Datei holen
		$this->addon 	= rex_addon::get('blocksettings');
	}


	//Speichern/Löschen der Settings
	public function saveSettings($ep)
	{	//Variablen deklarieren
		$slice_ep = 	@$ep->getName();
		$slice_id = 	intval(@$ep->getParam('slice_id'));
		//$slice_mid = 	intval(@$ep->getParam('module_id'));
		//$slice_aid = 	intval(@$ep->getParam('article_id'));
		
		$vars = @$_REQUEST['blockSettings'];
		

		if ($slice_id > 0 && is_array($vars) && !empty($vars)):
			//Settings im Slice speichern
			$db = rex_sql::factory();
			$db->setTable(rex::getTable('article_slice'));
		
			$db->setValue("bs_settings", serialize($vars) );

			$db->setWhere("id = '".$slice_id."'");
			$dbreturn = $db->update();
		endif;
	}


	//Kopieren der Settings bei bloecks-Addon
	public function copySettings($ep)
	{	//Variablen deklarieren
		$slice_ep = 	@$ep->getName();
		$slice_old = 	@$ep->getParam('source_slice_id')->getId();
		$slice_new = 	@$ep->getParam('inserted_slice_id');
		

		if ($slice_old > 0 && $slice_new > 0):
			//Settings in neuen Slice kopieren
			$s = $this->getSettings($slice_old);							//Settings aus altem Slice holen
			
			if (!empty($s)):
				$db = rex_sql::factory();
				$db->setTable(rex::getTable('article_slice'));
			
				$db->setValue("bs_settings", serialize($s) );
	
				$db->setWhere("id = '".$slice_new."'");
				$dbreturn = $db->update();
			endif;
		endif;
	}
	
	
	//Auslesen der Settings
	public function getSettings($sid = 0, $field = "", $convert = "")
	{	$sid = intval($sid);
		$field = $this->cleanName($field);
		$val = "";
		
		if ($sid > 0):
			$db = rex_sql::factory();
			$db->setQuery("SELECT bs_settings FROM ".rex::getTable('article_slice')." WHERE id = '".$sid."' LIMIT 0,1"); 
	
			$val = ($db->getRows() > 0) ? $db->getValue('bs_settings') : '';
			$val = unserialize($val);
			
			if (is_array($val) && !empty($field)):
				$val = @$val[$field];
				
				//Konvertierungen
				switch ($convert):
					case "int":		$val = intval($val);
									break;
									
					case "time":	$val = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4}$/i", $val)) ? $val.' 00:00' : $val;
									$val = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4} [0-9]{1,2}:[0-9]{1,2}$/i", $val)) ? intval(@date_format(date_create_from_format('d.m.Y H:i', $val), 'U')) : 0;
									break;
					
					
					//inoffizielle Konnvertierung
					case "timeend":	$val = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4}$/i", $val)) ? $val.' 23:59' : $val;
									$val = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4} [0-9]{1,2}:[0-9]{1,2}$/i", $val)) ? intval(@date_format(date_create_from_format('d.m.Y H:i', $val), 'U')) : 0;
									break;
				endswitch;
			endif;
		endif;
		
		return $val;
	}
	
	
	//Onlinestatus prüfen (benötigt mind. 1 Feld)
	public function getOnlinestatus($sid = 0, $f_from = "", $f_to = "")
	{	$sid = intval($sid);
		$return = true;
		
		if ($sid > 0 && !empty($f_from) && !empty($f_to)):
			$f_from = $this->getSettings($sid, $f_from, 'time');
			$f_to = $this->getSettings($sid, $f_to, 'timeend');
			$now = time();
		
			$return = (($f_from == 0 || $now >= $f_from) && ($f_to == 0 || $now <= $f_to)) ? true : false;
		endif;
		
		return $return;
	}
	
	
	//Onlinestatus prüfen und Modulausgabe anpassen/blockieren
	function checkOnlinestatus($ep)
	{	$op = $ep->getSubject();
		$sid = intval($ep->getParam('slice_id'));
		
		$config = rex_addon::get('blocksettings')->getConfig('config');
			$f_from = @$config['input_onlinefrom'];
			$f_to = @$config['input_onlineto'];
		
		if (!empty($f_from) && !empty($f_to)):
			//Felder sind angegeben > Status prüfen
			if (!$this->getOnlinestatus($sid, $f_from, $f_to)):
				if (!rex::isBackend()):
					//im Frontend Ausgabe blocken
					return false;
				else:
					//im Backend Info ausgeben
					$lang = rex_addon::get('blocksettings');
					
					$field_from = $this->getSettings($sid, $f_from);
					$field_to = $this->getSettings($sid, $f_to);
					
						$from = (!empty($field_from) && !empty($field_to)) ? $lang->i18n('a1604_mod_visibility_from').' '.$field_from : '';
						$from = (!empty($field_from) && empty($field_to)) ? $lang->i18n('a1604_mod_visibility_asof').' '.$field_from : $from;
							$from .= (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4} [0-9]{1,2}:[0-9]{1,2}$/i", $field_from)) ? ' '.$lang->i18n('a1604_mod_visibility_clock') : '';
						
						$to = (!empty($field_to)) ? ' - '.$field_to : '';
						$to .= (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4} [0-9]{1,2}:[0-9]{1,2}$/i", $field_to)) ? ' '.$lang->i18n('a1604_mod_visibility_clock') : '';
					
					$op .= '<script>$("#slice'.$sid.'").not(".rex-slice-offline").addClass("fmBlock-offline"); $("#slice'.$sid.' header").after(\'<div class="fmBlockOfflinestatus"><div class="fmBS-icon-left"><i class="rex-icon fa-clock-o"></i></div> '.$lang->i18n('a1604_mod_visibility').' '.$from.' '.$to.'</div>\');</script>';
				endif;
			endif;
		endif;
				
		return $op;
	}


	//Aufbereitung Feld-Name
	function cleanName($name = "")
	{	$name = str_replace(array("'", '"', "[", "]", "|"), "_", $name);
		
		return $name;
	}


	//Settings-Form jedem Modul anheften
	public function appendForm($ep, $noembed = false)
	{	//Variablen deklarieren
		$search = $replace = array();
		$noembed = ($noembed === true) ? true : false;
		
		
		//Vorgaben einlesen
		if ($noembed || $ep->getParam('function') == 'add' || $ep->getParam('function') == 'edit'):
			$url = $_SERVER['REQUEST_URI'];
			preg_match("/function=([a-z]+)/i", $url, $mode);														//Modus (add/edit) des Blockes aus URL holen
				$mode = $mode[1];
			preg_match("/module_id=([0-9]+)/i", $url, $mid);														//Modul-ID des Blockes aus URL holen
				$mid = @intval($mid[1]);
			preg_match("/slice_id=([0-9]+)/i", $url, $sid);															//Slice-ID des Blockes aus URL holen
				$sid = @intval($sid[1]);
			
				if ($sid > 0 && empty($mid) && $mode == 'edit'):
					//mid aus DB holen
					$db = rex_sql::factory();
					$db->setQuery("SELECT module_id FROM ".rex::getTable('article_slice')." WHERE id = '".$sid."' LIMIT 0,1");
					$mid = ($db->getRows() > 0) ? intval($db->getValue('module_id')) : 0;
				endif;
		
			if (!$noembed):
				$op = $ep->getSubject();																			//Content des ExtPoint (Modulinput)
				$mid = ($ep->getParam('function') == 'add') ? @intval($mid[1]) : @$ep->getParam('module_id');		//Modul-ID setzen
				//$sid = ($ep->getParam('function') == 'add') ? @intval($sid[1]) : @$ep->getParam('slice_id');		//Slice-ID setzen
				$sid = @intval($sid[1]);																			//muss aus URL gezogen werden, da SLICE_SHOW alle vorhandenen Blöcke auf einmal durchläuft und damit die ID falsch wäre	
				$mode = $ep->getParam('function');
			endif;
			
			
			//Parameter zwischenspeichern
			$this->mid = ($mid > 0) ? $mid : 0;
			$this->sid = ($sid > 0) ? $sid : 0;
			$this->mode = $mode;
			
			/*
			echo "<br>Mode: ".$this->mode;
			echo "<br><br>MID: ".$this->mid;
			echo "<br><br>SID: ".$this->sid."<br><br>";
			*/			
			
			//Definition einladen und auswerten
			$db = rex_sql::factory();
			$db->setQuery("SELECT * FROM ".rex::getTable('1604_blocksettings')." WHERE id = '1' AND status = 'checked' LIMIT 0,1"); 
			
			
			if ($db->getRows() > 0):
				//wenn manueller Modus, prüfen ob Modul-ID in Whitelist
				if ( $db->getValue('whitelistmode') == 'manual' && !empty($this->mid) && !preg_match("/#".$this->mid."#/i", $db->getValue('whitelist')) ) { return; }
				
				//Definition aufbereiten
				$tabnav = $tabcnt = "";
				
				$json = $db->getValue('settings');
				$settings = (!empty($json)) ? json_decode($json, TRUE) : '';
				$settings = (is_array($settings) && array_key_exists('settings', $settings)) ? @$settings['settings'] : array();
				
				//Definition auswerten			
				if (count($settings) > 0):
					//Setting-JSON enthält Inhalte
					$tabs = count($settings);
					
					
					//Tab-Nav erstellen
					if ($tabs > 1):
						$tabnav .= '<ul class="nav nav-tabs">';
							for ($i=0; $i<$tabs; $i++):
								$tabnav .= '<li><a href="#fmBS-content-'.$i.'" data-toggle="tab">'.$settings[$i]['tab'].'</a></li>';
							endfor;
						$tabnav .= '</ul>';
					endif;
					
				
					//Formfelder erstellen
					$tabcnt .= '<div class="tab-content">';
					
					for ($i=0; $i<$tabs; $i++):
						$tabcnt .= ($tabs > 1) ? '<div class="tab-pane fade" id="fmBS-content-'.$i.'">' : '';
	
						//Gruppen auswerten
						if (array_key_exists('groups', $settings[$i])):
							//Gruppen durchlaufen
							foreach ($settings[$i]['groups'] as $group):
								//echo "<pre>"; print_r($group); echo "</pre>";
							
								$name 	= (isset($group['name'])) ? $group['name'] : '';
								$css	= (@$group['inlinefields'] === true) ? 'fmBS-fieldset-inline' : '';
								
								$tabcnt .= '<div class="fmBS-fieldset '.$css.'">';
									$tabcnt .= (!empty($name)) ? '<span>'.$name.'</span>' : '';
									
									//Felder auswerten
									$tabcnt .= $this->getFormFields($group);
								$tabcnt .= '</div>';
							endforeach;					
						else:
							//keine Gruppen > Felder auswerten
							$tabcnt .= $this->getFormFields($settings[$i]);
						endif;
	
						$tabcnt .= ($tabs > 1) ? '</div>' : '';				
					endfor;
				
					$tabcnt .= ($tabs > 1) ? '<script>$(function(){ $(\'a[href="#fmBS-content-0"]\').tab("show"); });</script>' : '';					//immer ersten Tab einblenden
				endif;
	
				
				//Ausgabe
				$lang = rex_addon::get('blocksettings');
				$lang_toggler 		= $lang->i18n('a1604_mod_toggler');
				$lang_toggler_info 	= $lang->i18n('a1604_mod_toggler_info');
				
$formcode = <<<EOD
<div class="fmBlockSettings">
	<div class="fmBS-inner">
		<div class="fmBS-toggler" id="fmBS-toggler" title="$lang_toggler_info">
			<div class="fmBS-icon-left"><i class="rex-icon fa-cog"></i></div>
			$lang_toggler
			<div class="fmBS-icon-right"><i class="rex-icon fa-angle-down"></i></div>
		</div>
		
		<div class="fmBS-content" id="fmBS-content">
			$tabnav		
			$tabcnt
		</div>
	</div>
</div>
<script>$(function(){ $('#fmBS-toggler').click(function(){ $(this).find('.fmBS-icon-right i').toggleClass('fa-angle-down').toggleClass('fa-angle-up'); $('#fmBS-content').slideToggle('fast'); }); });</script>
EOD;
	
				//Form in Modulcontent einbetten/zurückgeben
				if ($noembed):
					//Code direkt zurückgeben
					return $formcode;	
				else:
					//Code in Input-Code einbetten
					$search 	= '/(<footer(?!.*<footer))/is';
					$replace = $formcode.'<footer';
					
					$op = preg_replace($search, $replace, $op);
					return $op;
				endif;
			endif;
			
		endif;        
	}



	function getFormFields($set)
	{	$tabcnt = "";
	
		if (is_array($set) && count($set) > 0):
	
			//Felder auswerten
			if (array_key_exists('fields', $set)):
				//Felder durchlaufen
				foreach ($set['fields'] as $field):
					$tabcnt .= '<dl class="rex-form-group form-group">';
						$tabcnt .= '<dt><label>'.$field['label'].'</label></dt>';
						$tabcnt .= '<dd>';
						
						$width = (isset($field['width'])) ? intval($field['width']) : 0;
							$width = ($width > 0) ? 'style="width:'.$width.'px"' : '';
						
						$value = (!is_array(@$field['value'])) ? @$field['value'] : '';
							$value = ($this->mode == 'add') ? $value : $this->getSettings($this->sid, $field['name']);				//gespeicherten Wert für Value einladen (hat nichts mit den möglichen Values des Feldes zu tun)
							$value = (empty($value)) ? @$field['default'] : $value;											//falls nichts gespeichert, dann default-Wert nutzen
						
						
						//echo "<br>SliceID: ".$this->sid;						
						//echo "<br>Name: ".$field['name'];
						//echo "<br>Type: ".$field['type'];
						//echo "<br>Val: ".$value;
						

						switch ($field['type']):
							case 'text':			$tabcnt .= $this->getField_text($field, $width, $value);				break;
							case 'color':			$tabcnt .= $this->getField_color($field, $width, $value);				break;
							case 'number':			$tabcnt .= $this->getField_number($field, $width, $value);				break;
							case 'range':			$tabcnt .= $this->getField_range($field, $width, $value);				break;
							case 'textarea':		$tabcnt .= $this->getField_textarea($field, $width, $value);			break;
							case 'select':			$tabcnt .= $this->getField_select($field, $width, $value);				break;
							case 'checkbox':		$tabcnt .= $this->getField_checkbox($field, $width, $value);			break;
							case 'radio':			$tabcnt .= $this->getField_radio($field, $width, $value);				break;
							case 'rexmedia':		$tabcnt .= $this->getField_rexmedia($field, $width, $value);			break;
							case 'rexlink':			$tabcnt .= $this->getField_rexlink($field, $width, $value);				break;
							case 'rexmedialist':	$tabcnt .= $this->getField_rexmedialist($field, $width, $value);		break;
							case 'rexlinklist':		$tabcnt .= $this->getField_rexlinklist($field, $width, $value);			break;
							case 'date':			$tabcnt .= $this->getField_datetime($field, $width, $value, 'date');	break;
							case 'datetime':		$tabcnt .= $this->getField_datetime($field, $width, $value);			break;
						endswitch;
					
					$tabcnt .= '</dd></dl>';
				endforeach;					
			endif;
			
		endif;
		
		return $tabcnt;
	}
	
	
	function getInputGroup($field, $input, $w = "")
	{	$cnt = "";
	
		if (is_array($field) && !empty($input)):
			$pre = htmlspecialchars(@$field['prefix']);
			$suf = htmlspecialchars(@$field['suffix']);
		
			$cnt .= (!empty($pre) || !empty($suf)) ? '<div class="input-group" '.$w.'>' : '';
			$cnt .= (!empty($pre)) ? '<span class="input-group-addon"><div>'.$pre.'</div></span>' : '';
				$cnt .= $input;
			$cnt .= (!empty($suf)) ? '<span class="input-group-addon"><div>'.$suf.'</div></span>' : '';
			$cnt .= (!empty($pre) || !empty($suf)) ? '</div>' : '';
		else:
			$cnt .= $input;
		endif;
		
		return $cnt;
	}
	
	
	
	// ************************************
	// Definition der Formularfeldausgaben
	// ************************************
	
	function getField_text($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			$input = '<input type="text" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" placeholder="'.$ph.'" maxlength="'.@$field['maxlength'].'" class="form-control" '.$w.' />';
            
			$cnt .= $this->getInputGroup($field, $input, $w);
		endif;
	
		return $cnt;
	}
	
	
	function getField_color($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$pre = htmlspecialchars(@$field['prefix']);
			$suf = htmlspecialchars(@$field['suffix']);
			
			$input = '<div class="input-group fmBS-color-input-group">';
				$input .= (!empty($pre)) ? '<span class="input-group-addon"><div>'.$pre.'</div></span>' : '';
			
				$input .= '<input type="text" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" maxlength="7" placeholder="'.$ph.'" pattern="^#([A-Fa-f0-9]{6})$" class="form-control" />';
				$input .= '<span class="input-group-addon fmBS-colorinput"><input type="color" id="fmBS_'.$name.'_color" value="'.$v.'" pattern="^#([A-Fa-f0-9]{6})$" class="form-control" /></span>';
				
				$input .= (!empty($suf)) ? '<span class="input-group-addon"><div>'.$suf.'</div></span>' : '';
			$input .= '</div>';

			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	
	
	function getField_number($field, $w, $v)
	{	$cnt = "";
		$v = intval($v);
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$min = (isset($field['min'])) ? 'min="'.$field['min'].'"' : '';
			$max = (isset($field['max'])) ? 'max="'.$field['max'].'"' : '';
			
			$input = '<input type="number" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" maxlength="'.@$field['maxlength'].'" '.$min.' '.$max.' class="form-control" '.$w.' />';
			
			$cnt .= $this->getInputGroup($field, $input, $w);
		endif;
	
		return $cnt;
	}
	
	
	function getField_range($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$min = (isset($field['min'])) ? 'min="'.$field['min'].'"' : '';
			$max = (isset($field['max'])) ? 'max="'.$field['max'].'"' : '';
			$step = (isset($field['step'])) ? 'step="'.$field['step'].'"' : '';
			$pre = htmlspecialchars(@$field['prefix']);
			$suf = htmlspecialchars(@$field['suffix']);
			
			$input = '<div class="input-group fmBS-range-input-group">';
				$input .= (!empty($pre)) ? '<span class="input-group-addon"><div>'.$pre.'</div></span>' : '';
				
				$input .= '<input type="range" id="fmBS_'.$name.'_range" value="'.$v.'" '.$min.' '.$max.' '.$step.' class="form-control" />';
				$input .= '<input type="hidden" name="blockSettings['.$name.']" id="fmBS_'.$name.'_value" value="'.$v.'" />';
				$input .= '<span class="input-group-addon fmBS-rangetext" id="fmBS_'.$name.'_text">'.$v.'</span>';
				
				$input .= (!empty($suf)) ? '<span class="input-group-addon"><div>'.$suf.'</div></span>' : '';
			$input .= '</div>';
			
			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	
	
	function getField_textarea($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			$config = rex_addon::get('blocksettings')->getConfig('config');
			$h = $edc = $edh = $edp = $edi = "";
				if (!empty($config['editor']) && @$field['editor'] === true):
					$ed = $config['editor'];
					if (rex_addon::get($ed)->isAvailable()):
					
						//Editor
						$edc = 	($ed == 'ckeditor') 	? 'ckeditor' : $edc;
						$edc = 	($ed == 'cke5') 		? 'cke5-editor' : $edc;
						$edc = 	($ed == 'tinymce4') 	? 'tinyMCEEditor' : $edc;
						$edc = 	($ed == 'tinymce5') 	? 'tiny5-editor' : $edc;
						//$edc = 	($ed == 'redactor') 	? 'redactor-editor' : $edc;
						//$edc = 	($ed == 'redactor2') 	? 'redactorEditor2' : $edc;
						
						//Editorprofile
						$tmp = $config['editor_profile'];
							$edp = 	($ed == 'ckeditor') 	? 'data-ckeditor-profile="'.$tmp.'"' : $edp;
							$edp = 	($ed == 'cke5') 		? 'data-profile="'.$tmp.'"' : $edp;
							$edp = 	($ed == 'tinymce4') 	? '' : $edp;
							$edp = 	($ed == 'tinymce5') 	? 'data-profile="'.$tmp.'"' : $edp;
							//$edc .= ($ed == 'redactor') ? '--'.$tmp : '';
							//$edc .= ($ed == 'redactor2')? '-'.$tmp : '';
						
						//Editorhöhe
						$tmp = intval($config['editor_height']);
							$h = 	($tmp > 0) ? 'style="height: '.$tmp.'px"' : '';
							$edh = 	($ed == 'ckeditor' && $tmp > 0) 	? 'data-ckeditor-height="'.$tmp.'"' : $edh;
							$edh = 	($ed == 'cke5' && $tmp > 0) 		? 'data-min-height="'.$tmp.'"' : $edh;
							
						//Editor-Init
						$edi = '<script>$(function(){ ';
							$edi .= ($ed == 'ckeditor') 	? 'rex_ckeditor_init($("#fmBS_'.$name.'"));' : '';
							$edi .= ($ed == 'cke5') 		? 'cke5_init($("#fmBS_'.$name.'"));' : '';
							$edi .= ($ed == 'tinymce4') 	? 'tinymce4_remove($("#fmBS_'.$name.'"), false); tinymce4_init();' : '';
							$edi .= ($ed == 'tinymce5') 	? 'tiny5_init($("#fmBS-content"));' : '';
							//$edi .= ($ed == 'redactor') 	? 'rex_ckeditor_init($("#fmBS_'.$name.'"));' : '';
							//$edi .= ($ed == 'redactor2') 	? 'rex_ckeditor_init($("#fmBS_'.$name.'"));' : '';
						$edi .= ' });</script>';
						
					endif;
				endif;			
			
			$input = '<textarea name="blockSettings['.$name.']" id="fmBS_'.$name.'" placeholder="'.$ph.'" rows="5" class="form-control '.$edc.'" '.$edp.' '.$edh.' '.$w.' '.$h.' />'.$v.'</textarea>';
            $input .= $edi;
            
			$cnt .= $this->getInputGroup($field, $input, $w);
		endif;
	
		return $cnt;
	}
	
	
	function getField_select($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$multiple = (@$field['multiple'] === true) ? 'size="4" multiple' : 'size="1"';
			
			$options = "";
				if (is_array($field['value'])):
					foreach ($field['value'] as $val=>$title):
						$sel = ($v == $val) ? 'selected="selected"' : '';
						$options .= '<option value="'.$val.'" '.$sel.'>'.$title.'</option>';
					endforeach;
				else:
					return;
				endif;
				
			$input = '<select name="blockSettings['.$name.']" id="fmBS_'.$name.'" '.$multiple.' class="form-control" '.$w.'>'.$options.'</select>';
            
			$cnt .= $this->getInputGroup($field, $input, $w);
		endif;
	
		return $cnt;
	}
	
	
	function getField_checkbox($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$val = @$field['value'];
			
			$sel = ($v == $val) ? 'checked="checked"' : '';
			$input = '<div class="checkbox"><label for="fmBS_'.$name.'"><input type="checkbox" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$val.'" '.$sel.' />'.$ph.'</label></div>';
            
			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	
	
	function getField_radio($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);

			$input = ""; $i = 0;
				if (is_array($field['value'])):
					foreach ($field['value'] as $val=>$title):
						$sel = ($v == $val) ? 'checked="checked"' : '';
						$input .= '<dl class="rex-form-group form-group radio"><dd>';
						$input .= '<div class="radio"><label for="fmBS_'.$name.'-'.$i.'"><input type="radio" name="blockSettings['.$name.']" id="fmBS_'.$name.'-'.$i.'" value="'.$val.'" '.$sel.' />'.$title.'</label></div>';
						$input .= '</dd></dl>';
						$i++;
					endforeach;
				else:
					return;
				endif;
			
			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	

	function getField_datetime($field, $w, $v, $caltype = '')
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$pre = htmlspecialchars(@$field['prefix']);
			$suf = htmlspecialchars(@$field['suffix']);
			
			$v = (preg_match("/^[0-9]+$/i", $v)) ? date("d.m.Y H:i", $v) : $v;
            $v = (preg_match("/^[0-9]+$/i", $v) && $caltype == 'date') ? date("d.m.Y", $v) : $v;
			
			$lang = rex_addon::get('blocksettings');
			$lang_calendar = $lang->i18n('a1604_mod_calendar');
			
			$picker_time = ($caltype != 'date') ? 'true' : 'false';
			
			$input = '<div class="input-group fmBS_datepicker-widget">';
				$input .= (!empty($pre)) ? '<span class="input-group-addon"><div>'.$pre.'</div></span>' : '';
				$input .= '<input type="text" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" maxlength="'.@$field['maxlength'].'" class="form-control" data-datepicker-time="'.$picker_time.'" data-datepicker-mask="true" />';
				$input .= '<span class="input-group-btn"><a class="btn btn-popup" onclick="return false;" title="'.$lang_calendar.'" data-datepicker-dst="fmBS_'.$name.'"><i class="rex-icon fa-calendar"></i></a><div></div></span>';
			$input .= '</div>';
			
			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	
	
	//REDAXO-Buttons
	function getField_rexmedia($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
				$name = 'blockSettings['.$name.']';
			$ph = htmlspecialchars(@$field['placeholder']);
			$id = $this->cleanName($name);
			$mtypes = @$field['mediatypes'];
				$mtypes = (!empty($mtypes)) ? '&args[types]='.urlencode($mtypes) : '';
			
			$lang 		= rex_addon::get('blocksettings');
			$lang_btn1 	= $lang->i18n('a1604_media_select');
			$lang_btn2 	= $lang->i18n('a1604_media_add');
			$lang_btn3 	= $lang->i18n('a1604_media_delete');
			$lang_btn4 	= $lang->i18n('a1604_media_preview');			
			
$input = <<<EOD
<div class="rex-js-widget rex-js-widget-media">
	<div class="input-group">
		<input class="form-control" type="text" name="$name" value="$v" id="REX_MEDIA_$id" readonly="">
		<span class="input-group-btn">
			<a href="#" class="btn btn-popup" onclick="openREXMedia('$id', '$mtypes'); return false;" title="$lang_btn1"><i class="rex-icon rex-icon-open-mediapool"></i></a>
			<a href="#" class="btn btn-popup" onclick="addREXMedia('$id', '$mtypes'); return false;" title="$lang_btn2"><i class="rex-icon rex-icon-add-media"></i></a>
			<a href="#" class="btn btn-popup" onclick="deleteREXMedia('$id'); return false;" title="$lang_btn3"><i class="rex-icon rex-icon-delete-media"></i></a>
			<a href="#" class="btn btn-popup" onclick="viewREXMedia('$id', '$mtypes'); return false;" title="$lang_btn4"><i class="rex-icon rex-icon-view-media"></i></a>
		</span>
	</div>
	<div class="rex-js-media-preview"></div>
</div>
EOD;

			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	
	
	function getField_rexmedialist($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
				$name = 'blockSettings['.$name.']';
			$ph = htmlspecialchars(@$field['placeholder']);
			$id = $this->cleanName($name);
			$mtypes = @$field['mediatypes'];
				$mtypes = (!empty($mtypes)) ? '&args[types]='.urlencode($mtypes) : '';
			
			$options = "";
				if (!empty($v)):
					$ml = explode(',', $v);
					foreach ($ml as $m) { $options .= '<option value="'.$m.'">'.$m.'</option>'; }
				endif;
			
			$lang 		= rex_addon::get('blocksettings');
			$lang_btn1 	= $lang->i18n('a1604_media_select');
			$lang_btn2 	= $lang->i18n('a1604_media_add');
			$lang_btn3 	= $lang->i18n('a1604_media_delete');
			$lang_btn4 	= $lang->i18n('a1604_media_preview');
			$lang_btn5 	= $lang->i18n('a1604_media_movetop');
			$lang_btn6 	= $lang->i18n('a1604_media_moveup');
			$lang_btn7 	= $lang->i18n('a1604_media_movedown');
			$lang_btn8 	= $lang->i18n('a1604_media_movebottom');
		
$input = <<<EOD
<div class="rex-js-widget rex-js-widget-medialist">
	<div class="input-group">
		<select class="form-control" name="REX_MEDIALIST_SELECT[$id]" id="REX_MEDIALIST_SELECT_$id" size="10">$options</select>
		<input type="hidden" name="$name" id="REX_MEDIALIST_$id" value="$v" />
		<span class="input-group-addon">
			<div class="btn-group-vertical">
				<a href="#" class="btn btn-popup" onclick="moveREXMedialist('$id', 'top'); return false;" title="$lang_btn5"><i class="rex-icon rex-icon-top"></i></a>
				<a href="#" class="btn btn-popup" onclick="moveREXMedialist('$id', 'up'); return false;" title="$lang_btn6"><i class="rex-icon rex-icon-up"></i></a>
				<a href="#" class="btn btn-popup" onclick="moveREXMedialist('$id', 'down'); return false;" title="$lang_btn7"><i class="rex-icon rex-icon-down"></i></a>
				<a href="#" class="btn btn-popup" onclick="moveREXMedialist('$id', 'bottom'); return false;" title="$lang_btn8"><i class="rex-icon rex-icon-bottom"></i></a></div><div class="btn-group-vertical">
				<a href="#" class="btn btn-popup" onclick="openREXMedialist('$id', '$mtypes'); return false;" title="$lang_btn1"><i class="rex-icon rex-icon-open-mediapool"></i></a>
				<a href="#" class="btn btn-popup" onclick="addREXMedialist('$id', '$mtypes'); return false;" title="$lang_btn2"><i class="rex-icon rex-icon-add-media"></i></a>
				<a href="#" class="btn btn-popup" onclick="deleteREXMedialist('$id'); return false;" title="$lang_btn3"><i class="rex-icon rex-icon-delete-media"></i></a>
				<a href="#" class="btn btn-popup" onclick="viewREXMedialist('$id', '$mtypes'); return false;" title="$lang_btn4"><i class="rex-icon rex-icon-view-media"></i></a>
			</div>
		</span>
	</div>
	<div class="rex-js-media-preview"></div>
</div>
EOD;

			$cnt .= $input;
		endif;
	
		return $cnt;
	}	
	
	
	function getField_rexlink($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
				$name = 'blockSettings['.$name.']';
			$ph = htmlspecialchars(@$field['placeholder']);
			$id = $this->cleanName($name);
			$rid = 'REX_LINK_'.$id.'_NAME';
			
			$aid = intval($v);
			$aname = "";
				if ($aid > 0) { $aoo = rex_article::get($aid); if ($aoo){ $aname = $aoo->getName(); } }
			
			$lang 		= rex_addon::get('blocksettings');
			$lang_btn1 	= $lang->i18n('a1604_link_select');
			$lang_btn2 	= $lang->i18n('a1604_link_delete');
			
$input = <<<EOD
<div class="input-group">
	<input class="form-control" type="text" name="REX_LINK_NAME[$id]" value="$aname" id="$rid" readonly="">
	<input type="hidden" name="$name" id="REX_LINK_$id" value="$aid">
	<span class="input-group-btn">
		<a href="#" class="btn btn-popup" onclick="openLinkMap('REX_LINK_$id', '&clang=1&category_id=0'); return false;" title="$lang_btn1"><i class="rex-icon rex-icon-open-linkmap"></i></a>
		<a href="#" class="btn btn-popup" onclick="deleteREXLink('$id'); return false;" title="$lang_btn2"><i class="rex-icon rex-icon-delete-link"></i></a>
	</span>
</div>			
EOD;

			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	
	
	function getField_rexlinklist($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
				$name = 'blockSettings['.$name.']';
			$ph = htmlspecialchars(@$field['placeholder']);
			$id = mt_rand().ceil(rand(0,9999999));
			
			$options = "";
				if (!empty($v)):
					$ll = explode(',', $v);
					foreach ($ll as $l):
						$aid = intval($l);
						$aname = "";
							if ($aid > 0) { $aoo = rex_article::get($aid); if ($aoo){ $aname = $aoo->getName(); } }

						$options .= '<option value="'.$aid.'">'.$aname.'</option>';
					endforeach;
				endif;
			
			$lang 		= rex_addon::get('blocksettings');
			$lang_btn1 	= $lang->i18n('a1604_link_select');
			$lang_btn2 	= $lang->i18n('a1604_link_delete');
			$lang_btn3 	= $lang->i18n('a1604_link_movetop');
			$lang_btn4 	= $lang->i18n('a1604_link_moveup');
			$lang_btn5 	= $lang->i18n('a1604_link_movedown');
			$lang_btn6 	= $lang->i18n('a1604_link_movebottom');
			
$input = <<<EOD
<div class="input-group">
	<select class="form-control" name="REX_LINKLIST_SELECT[$id]" id="REX_LINKLIST_SELECT_$id" size="10">$options</select>
	<input type="hidden" name="$name" id="REX_LINKLIST_$id" value="$v" />
	<span class="input-group-addon"><div class="btn-group-vertical">
		<a href="#" class="btn btn-popup" onclick="moveREXLinklist('$id','top'); return false;" title="$lang_btn3"><i class="rex-icon rex-icon-top"></i></a>
		<a href="#" class="btn btn-popup" onclick="moveREXLinklist('$id','up'); return false;" title="$lang_btn4"><i class="rex-icon rex-icon-up"></i></a>
		<a href="#" class="btn btn-popup" onclick="moveREXLinklist('$id','down'); return false;" title="$lang_btn5"><i class="rex-icon rex-icon-down"></i></a>
		<a href="#" class="btn btn-popup" onclick="moveREXLinklist('$id','bottom'); return false;" title="$lang_btn6"><i class="rex-icon rex-icon-bottom"></i></a></div><div class="btn-group-vertical">
		<a href="#" class="btn btn-popup" onclick="openREXLinklist('$id', '&clang=1&category_id=0'); return false;" title="$lang_btn1"><i class="rex-icon rex-icon-open-linkmap"></i></a>
		<a href="#" class="btn btn-popup" onclick="deleteREXLinklist('$id'); return false;" title="$lang_btn2"><i class="rex-icon rex-icon-delete-link"></i></a></div>
	</span>
</div>
EOD;

			$cnt .= $input;
		endif;	
	
		return $cnt;
	}
	
}





//rexAPI Klassen-Erweiterung (Ajax-Abfrage)
class rex_api_a1604_appendForm extends rex_api_function
{	protected $published = false;		//true = auch im Frontend

	function execute()
	{	//Formular laden
		$o = new blockSettings();
		$op = $o->appendForm('', true);
		
		//Ajax-Rückgabe
		header('Content-type: text/html; charset=UTF-8');
		exit($op);		//Rückgabe ausgeben + Anfrage beenden
	}
}

?>