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
		
		/*
        $this->jsonpath	= $this->addon->getDataPath('settings.json');
		$this->json = @file_get_contents($this->jsonpath);
			if (empty($this->json)):
				$this->jsonpath	= $this->addon->getPath('data/example.json');
				$this->json = @file_get_contents($this->jsonpath);
			endif;

		$db = rex_sql::factory();
		$db->setQuery("SELECT settings FROM ".rex::getTable('1604_blocksettings')." WHERE id = '1' AND status = 'checked' LIMIT 0,1"); 
		
		$this->json = ($db->getRows() > 0) ? $db->getValue('settings') : '';
		$this->settings = (!empty($this->json)) ? json_decode($this->json, TRUE) : '';
		$this->settings = (array_key_exists('settings', $this->settings)) ? @$this->settings['settings'] : array();
		*/
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
	
	
	//Ausgabe der Settings
	public function getSettings($sid = 0, $field = "")
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
			endif;
		endif;
		
		return $val;
	}


	//Aufbereitung Feldname
	function cleanName($name = "")
	{	$name = str_replace(array("'", '"', "[", "]"), "_", $name);
		
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
			preg_match("/module_id=([0-9]+)/i", $url, $mid);														//Modul-ID des neuen Blockes aus URL holen
			preg_match("/slice_id=([0-9]+)/i", $url, $sid);															//Slice-ID des neuen Blockes aus URL holen
			preg_match("/function=([a-z]+)/i", $url, $mode);														//Modus (add/edit) des neuen Blockes aus URL holen
		
			if ($noembed):
				$mid = @intval($mid[1]);																			//Modul-ID setzen aus URL
				$sid = @intval($sid[1]);																			//Slice-ID setzen aus URL
				$mode = $mode[1];
			else:
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
			echo "<br><br>MID: ".$this->mid."<br><br>";
			*/			
			
			//Definition einladen und auswerten
			$db = rex_sql::factory();
			$db->setQuery("SELECT * FROM ".rex::getTable('1604_blocksettings')." WHERE id = '1' AND status = 'checked' LIMIT 0,1"); 
			
			
			if ($db->getRows() > 0):
				//wenn manueller Modus, prüfen ob Modul-ID in Whitelist
				if ( $db->getValue('whitelistmode') == 'manual' && !preg_match("/#".$this->mid."#/i", $db->getValue('whitelist')) ) { return; }
				
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
/*<script>$(function(){ $('#fmBS-toggler').click(function(){ $(this).find('.fmBS-icon-right i').toggleClass('fa-angle-down').toggleClass('fa-angle-up'); $('#fmBS-content').slideToggle('fast'); }); });</script>*/
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
						
						$value = ($this->mode == 'add') ? '' : $this->getSettings($this->sid, $field['name']);				//gespeicherten Wert für Value einladen (hat nichts mit den möglichen Values des Feldes zu tun)
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
							case 'time':			$tabcnt .= $this->getField_datetime($field, $width, $value, 'time');	break;
							case 'datetime':		$tabcnt .= $this->getField_datetime($field, $width, $value);			break;
						endswitch;
					
					$tabcnt .= '</dd></dl>';
				endforeach;					
			endif;
			
		endif;
		
		return $tabcnt;
	}
	
	
	function getInputGroup($field, $input)
	{	$cnt = "";
	
		if (is_array($field) && !empty($input)):
			$pre = htmlspecialchars(@$field['prefix']);
			$suf = htmlspecialchars(@$field['suffix']);
		
			$cnt .= (!empty($pre) || !empty($suf)) ? '<div class="input-group">' : '';
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
			$cnt .= $this->getInputGroup($field, $input);
		endif;
	
		return $cnt;
	}
	
	
	function getField_color($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			$input = '<input type="color" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" title="'.$ph.'" pattern="^#([A-Fa-f0-9]{6})$" class="form-control" '.$w.' />';
			$cnt .= $this->getInputGroup($field, $input);
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
			$cnt .= $this->getInputGroup($field, $input);
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
			
			$input = '<div class="input-group rex-range-input-group">';
				$input .= (!empty($pre)) ? '<span class="input-group-addon"><div>'.$pre.'</div></span>' : '';
				
				$input .= '<input type="range" id="fmBS_'.$name.'_range" value="'.$v.'" '.$min.' '.$max.' '.$step.' class="form-control" '.$w.' />';
				$input .= '<input type="hidden" name="blockSettings['.$name.']" id="fmBS_'.$name.'_value" value="'.$v.'" />';
				$input .= '<span class="input-group-addon fmBS-rangetext" id="fmBS_'.$name.'_text">'.$v.'</span>';
				
				$input .= (!empty($suf)) ? '<span class="input-group-addon"><div>'.$suf.'</div></span>' : '';
			$input .= '</div>';
			
			$input .= '<script>$(function(){ $("#fmBS_'.$name.'_range").on("input change", function(){ $("#fmBS_'.$name.'_value").val(this.value); $("#fmBS_'.$name.'_text").text(this.value); }); });</script>';
			
			$cnt .= $input;
		endif;
	
		return $cnt;
	}
	
	
	function getField_textarea($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			$input = '<textarea name="blockSettings['.$name.']" id="fmBS_'.$name.'" placeholder="'.$ph.'" rows="5" class="form-control" '.$w.' />'.$v.'</textarea>';
			$cnt .= $this->getInputGroup($field, $input);
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
				foreach ($field['value'] as $key=>$val):
					$sel = ($v == $key) ? 'selected="selected"' : '';
					$options .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
				endforeach;

			$input = '<select name="blockSettings['.$name.']" id="fmBS_'.$name.'" '.$multiple.' class="form-control">'.$options.'</select>';
			$cnt .= $this->getInputGroup($field, $input);
		endif;
	
		return $cnt;
	}
	
	
	function getField_checkbox($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			// HIER FEHLT NOCH DAS AUSWERTEN DES VALUE -> SETZEN DES CHECKED STATUS
			
			$input = '<div class="checkbox"><label for="fmBS_'.$name.'"><input type="checkbox" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" >'.$ph.'</label></div>';
			$cnt .= $this->getInputGroup($field, $input);
		endif;
	
		return $cnt;
	}
	
	
	function getField_radio($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			$input = '';
			$cnt .= $this->getInputGroup($field, $input);
		endif;
	
		return $cnt;
	}
	
	
	function getField_rexmedia($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
				$name = 'blockSettings['.$name.']';
			$ph = htmlspecialchars(@$field['placeholder']);
			
$input = <<<EOD
<div class="rex-js-widget rex-js-widget-media">
	<div class="input-group">
		<input class="form-control" type="text" name="$name" value="" id="$name" readonly="">
		<span class="input-group-btn">
			<a href="#" class="btn btn-popup" onclick="fmBS_openREXMedia('$name', ''); return false;" title="Medium auswählen"><i class="rex-icon rex-icon-open-mediapool"></i></a>
			<a href="#" class="btn btn-popup" onclick="fmBS_addREXMedia('$name', '');  return false;" title="Neues Medium hinzufügen"><i class="rex-icon rex-icon-add-media"></i></a>
			<a href="#" class="btn btn-popup" onclick="fmBS_deleteREXMedia('$name');  return false;" title="Ausgewähltes Medium löschen"><i class="rex-icon rex-icon-delete-media"></i></a>
		</span>
	</div>
	<div class="rex-js-media-preview"></div>
</div>
EOD;

			$cnt .= $this->getInputGroup($field, $input);
		endif;
	
		return $cnt;
	}
	
	
	function getField_rexlink($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
$input = <<<EOD
<div class="input-group">
	<input class="form-control" type="text" name="REX_LINK_NAME[1]" value="" id="REX_LINK_1_NAME" readonly="">
	<input type="hidden" name="$name" id="$name" value="0">
	<span class="input-group-btn">
		<a href="#" class="btn btn-popup" onclick="openLinkMap('$name', '&amp;clang=1');return false;" title="Link auswählen"><i class="rex-icon rex-icon-open-linkmap"></i></a>
		<a href="#" class="btn btn-popup" onclick="deleteREXLink('$name');return false;" title="Ausgewählten Link löschen"><i class="rex-icon rex-icon-delete-link"></i></a>
	</span>
</div>			
EOD;

			$cnt .= $this->getInputGroup($field, $input);
		endif;
	
		return $cnt;
	}
	
	
	function getField_rexmedialist($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			$input = '';
			$cnt .= $this->getInputGroup($field, $input);
		endif;
	
		return $cnt;
	}
	
	
	function getField_rexlinklist($field, $w, $v)
	{	$cnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
			$input = '';
			$cnt .= $this->getInputGroup($field, $input);
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
			
			$v = (preg_match("/[0-9]+/i", $v)) ? date("d.m.Y H:i", $v) : "";
			
			$lang = rex_addon::get('blocksettings');
			$lang_calendar = $lang->i18n('a1604_mod_calendar');
			
			$picker_time = $picker_date = 'true';
			switch ($caltype):
				case 'time':	$picker_date = 'false';		break;
				case 'date':	$picker_time = 'false';		break;
			endswitch;
			
			$input = '<div class="input-group">';
				$input .= (!empty($pre)) ? '<span class="input-group-addon"><div>'.$pre.'</div></span>' : '';
				$input .= '<input type="text" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" maxlength="'.@$field['maxlength'].'" class="form-control" data-datepicker="'.$picker_date.'" data-datepicker-time="'.$picker_time.'" data-datepicker-mask="true" />';
				$input .= '<span class="input-group-btn"><a class="btn btn-popup" onclick="return false;" title="'.$lang_calendar.'" data-datepicker-dst="fmBS_'.$name.'"><i class="rex-icon fa-calendar"></i></a></span>';
				$input .= (!empty($suf)) ? '<span class="input-group-addon"><div>'.$suf.'</div></span>' : '';
			$input .= '</div>';
			
			$cnt .= $input;
		endif;
	
		return $cnt;
		
		
		// KALENDER-SCRIPT & JQUERY MUSS NOCH EINGEBUDNEN WERDEN !!!
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