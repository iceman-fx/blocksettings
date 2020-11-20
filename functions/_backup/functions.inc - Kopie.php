<?php
/*
	Redaxo-Addon Blocksettings
	Globale-Funktionen
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/


/*
Modulausgabe:

$s = new blockSettings();
print_r( $s->getSettings(REX_SLICE_ID) );					//gibt alle Settings als Array aus
echo $s->getSettings(REX_SLICE_ID, 'feldname');				//gibt einzelnen Setting aus
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
		$mypage = "blocksettings";
	
		$slice_ep = 	@$ep->getName();
		$slice_id = 	@$ep->getParam('slice_id');
		$slice_mid = 	@$ep->getParam('module_id');
		$slice_aid = 	@$ep->getParam('article_id');
		
		$vars = $_REQUEST['blockSettings'];


		//echo "<pre>"; print_r($vars); echo "</pre>";
		
		
		//Prüfen, ob bestehender Slice schon einen DB-Eintrag hat (wenn nicht, dann Aktion für ADDED ausführen)
		$db = rex_sql::factory();
		$db->setQuery("SELECT id FROM ".rex::getTable('1604_blocksettings_slice')." WHERE id_slice = '".$slice_id."' LIMIT 0,1"); 
		$slice_ep = ($slice_ep == 'SLICE_UPDATED' && $db->getRows() <= 0) ? 'SLICE_ADDED' : $slice_ep;
		
		
		if ($slice_ep == 'SLICE_ADDED'):
			//Aktion beim hinzufügen (neuer Slice)
			//Settings in DB speichern
			$db = rex_sql::factory();
			$db->setTable(rex::getTable('1604_blocksettings_slice'));
		
			$db->setValue("id_slice", $slice_id);
			$db->setValue("settings", serialize($vars) );

			$dbreturn = $db->insert();			
			
		elseif ($slice_ep == 'SLICE_UPDATED'):
			//Aktion beim editieren (bestehender Slice)
			//Settings in DB speichern
			$db = rex_sql::factory();
			$db->setTable(rex::getTable('1604_blocksettings_slice'));
		
			$db->setValue("settings", serialize($vars) );

			$db->setWhere("id_slice = '".$slice_id."'");
			$dbreturn = $db->update();			
			
		elseif ($slice_ep == 'SLICE_DELETED'):
			//Aktion beim löschen (bestehender Slice)
			//Settings aus DB löschen
			$db = rex_sql::factory();
			$db->setTable(rex::getTable('1604_blocksettings_slice'));

			$db->setWhere("id_slice = '".$slice_id."'");
			$dbreturn = $db->delete();			
			
		else:
			//Meldung ggf. ausgeben, wenn keine gültige Aktion vorliegt
		endif;
	}
	
	
	//Ausgabe der Settings
	public function getSettings($sid = 0, $field = "")
	{	$sid = intval($sid);
		$field = $this->cleanName($field);
		$val = "";
		
		if ($sid > 0):
			$db = rex_sql::factory();
			$db->setQuery("SELECT settings FROM ".rex::getTable('1604_blocksettings_slice')." WHERE id_slice = '".$sid."' LIMIT 0,1"); 
	
			$val = ($db->getRows() > 0) ? $db->getValue('settings') : '';
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
				$json = $db->getValue('settings');
				$settings = (!empty($json)) ? json_decode($json, TRUE) : '';
				$settings = (array_key_exists('settings', $settings)) ? @$settings['settings'] : array();
				
				//Definition auswerten			
				if (count($settings) > 0):
					//Setting-JSON enthält Inhalte
					$tabs = count($settings);
					
					
					//Tab-Nav erstellen
					$tabnav = "";
					if ($tabs > 1):
						$tabnav .= '<ul class="nav nav-tabs">';
							for ($i=0; $i<$tabs; $i++):
								$tabnav .= '<li><a href="#fmBS-content-'.$i.'" data-toggle="tab">'.$settings[$i]['tab'].'</a></li>';
							endfor;
						$tabnav .= '</ul>';
					endif;
					
				
					//Formfelder erstellen
					$tabcnt = "";
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
$formcode = <<<EOD
<div class="fmBlockSettings">
	<div class="fmBS-inner">
		<div class="fmBS-toggler" id="fmBS-toggler" title="Zusätzliche Einstellungen dieses Blockes">
			<div><i class="rex-icon fa-cog"></i><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
			Block-Einstellungen
		</div>
		
		<div class="fmBS-content" id="fmBS-content">
			$tabnav		
			$tabcnt
		</div>
	</div>
</div>
<script>$(function(){ $('#fmBS-toggler').click(function(){ $('#fmBS-content').slideToggle('fast'); }); });</script>
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
						
						$value = ($this->mode == 'add') ? '' : $this->getSettings($this->sid, $field['name']);
						
						/*
						echo "<br>>>sid: ".$this->sid;						
						echo "<br>".$field['name'];
						echo "<br>".$field['type'];
						echo "<br>".$value;
						*/

						switch ($field['type']):
							case 'text':			$tabcnt .= $this->getField_text($field, $width, $value);			break;
							case 'color':			$tabcnt .= $this->getField_color($field, $width, $value);			break;
							case 'number':			$tabcnt .= $this->getField_number($field, $width, $value);			break;
							case 'range':			$tabcnt .= $this->getField_range($field, $width, $value);			break;
							case 'textarea':		$tabcnt .= $this->getField_textarea($field, $width, $value);		break;
							case 'select':			$tabcnt .= $this->getField_select($field, $width, $value);			break;
							case 'checkbox':		$tabcnt .= $this->getField_checkbox($field, $width, $value);		break;
							case 'radio':			$tabcnt .= $this->getField_radio($field, $width, $value);			break;
							case 'rexmedia':		$tabcnt .= $this->getField_rexmedia($field, $width, $value);		break;
							case 'rexlink':			$tabcnt .= $this->getField_rexlink($field, $width, $value);			break;
							case 'rexmedialist':	$tabcnt .= $this->getField_rexmedialist($field, $width, $value);	break;
							case 'rexlinklist':		$tabcnt .= $this->getField_rexlinklist($field, $width, $value);		break;
						endswitch;
					
					$tabcnt .= '</dd></dl>';
				endforeach;					
			endif;
			
		endif;
		
		return $tabcnt;
	}
	
	
	function getField_text($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '<input type="text" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" placeholder="'.$ph.'" maxlength="'.@$field['maxlength'].'" class="form-control" '.$w.' />';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_color($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '<input type="color" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" title="'.$ph.'" maxlength="'.@$field['maxlength'].'" pattern="^#([A-Fa-f0-9]{6})$" class="form-control" '.$w.' />';
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_number($field, $w, $v)
	{	$tabcnt = "";
		$v = intval($v);
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '<input type="number" name="blockSettings['.$name.']" id="fmBS_'.$name.'" value="'.$v.'" maxlength="'.$field['maxlength'].'" min="'.$field['min'].'" max="'.$field['max'].'" class="form-control" '.$w.' />';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_range($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_textarea($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_select($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$multiple = (@$field['multiple'] === true) ? 'size="4" multiple' : 'size="1"';
			
			$options = "";
				foreach ($field['value'] as $key=>$val):
					$v = (!empty($v)) ? $v : @$field['default'];
					$sel = ($v == $key) ? 'selected="selected"' : '';
					$options .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
				endforeach;

			$tabcnt .= '<select name="blockSettings['.$name.']" id="fmBS_'.$name.'" '.$multiple.' class="form-control">'.$options.'</select>';
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_checkbox($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '<div class="checkbox"><label for="blockSettings['.$name.']"><input name="fmBS_'.$name.'" type="checkbox" id="fmBS_'.$field['name'].'" value="1" >'.$ph.'</label></div>';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_radio($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexmedia($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
				$name = 'blockSettings['.$name.']';
			$ph = htmlspecialchars(@$field['placeholder']);
			
$tabcnt .= <<<EOD
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
			
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexlink($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			
$tabcnt .= <<<EOD
<div class="input-group">
	<input class="form-control" type="text" name="REX_LINK_NAME[1]" value="" id="REX_LINK_1_NAME" readonly="">
	<input type="hidden" name="$name" id="$name" value="0">
	<span class="input-group-btn">
		<a href="#" class="btn btn-popup" onclick="openLinkMap('$name', '&amp;clang=1');return false;" title="Link auswählen"><i class="rex-icon rex-icon-open-linkmap"></i></a>
		<a href="#" class="btn btn-popup" onclick="deleteREXLink('$name');return false;" title="Ausgewählten Link löschen"><i class="rex-icon rex-icon-delete-link"></i></a>
	</span>
</div>			
EOD;
			
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexmedialist($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '';
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexlinklist($field, $w, $v)
	{	$tabcnt = "";
	
		if (isset($field['name']) && !empty($field['name'])):
			$name = $this->cleanName($field['name']);
			$ph = htmlspecialchars(@$field['placeholder']);
			$tabcnt .= '';
		endif;
	
		return $tabcnt;
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