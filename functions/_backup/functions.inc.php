<?php
/*
Aufrufe für Modul-Ein-/Ausgaben
===============================
Moduleingabe:
echo moduleSettings::getForm();										//gibt alle Modulsettings als Eingabeformular aus, mit Ausnahme der Blacklist-Gruppen bzw. spezieller Settings für ein Modul
echo moduleSettings::getForm(REX_MODULE_ID);							//gibt alle Modulsettings sowie die speziellen Settings für das Modul xyz als Eingabeformular aus, mit Ausnahme der Blacklist-Gruppen

Modulausgabe:
echo moduleSettings::getSettings(REX_SLICE_ID);						//gibt ein Array mit allen Setting-Werten des Slices zurück

*/


class moduleSettings
{
	public function __construct()
	{	//JSON-Datei holen
		$this->addon 	= rex_addon::get('modulesettings');
        $this->jsonpath	= $this->addon->getDataPath('settings.json');
		
		$this->json = @file_get_contents($this->jsonpath);
			if (empty($this->json)):
				$this->jsonpath	= $this->addon->getPath('data/settings.json');
				$this->json = @file_get_contents($this->jsonpath);
			endif;
			
		$this->settings = (!empty($this->json)) ? json_decode($this->json, TRUE) : '';
		$this->settings = (array_key_exists('settings', $this->settings)) ? @$this->settings['settings'] : array();
	}


	//Speichern bzw. Löschen der Settings in der DB
	public function saveSettings($ep)
	{	//Variablen deklarieren
		$mypage = "aa_slice";
	
		$slice_ep = 	@$ep->getName();
		$slice_id = 	@$ep->getParam('slice_id');
		$slice_mid = 	@$ep->getParam('module_id');
		$slice_aid = 	@$ep->getParam('article_id');
		

		echo "<pre>";
			/*
			echo "EP: ".$slice_ep."<br>";
			echo "SLICE ID: ".$slice_id."<br>";
			echo "MODULE ID: ".$slice_mid."<br>";
			echo "ARTICLE ID: ".$slice_aid."<br>";
			
			print_r($ep);
			
			echo "<br><br>";
			*/
			
			$vars = $_REQUEST;
				print_r($vars['REX_INPUT_VALUE']);
				echo "<br>";
				print_r($vars['A1234_SETTINGS']);
			
		echo "</pre>";

		
		if ($slice_ep == 'SLICE_ADDED'):
			//Aktion beim hinzufügen (neuer Slice)
			echo "Slice wurde gespeichert";
			
			//hier die Settings in DB speichern
			
		elseif ($slice_ep == 'SLICE_UPDATED'):
			//Aktion beim editieren (bestehender Slice)
			echo "Slice wurde editiert";
			
			//hier die Settings in DB updaten
			
		elseif ($slice_ep == 'SLICE_DELETED'):
			//Aktion beim löschen (bestehender Slice)
			echo "Slice wurde gelöscht";
			
			//hier die Settings in DB löschen
			
		else:
			//Meldung ggf. ausgeben, wenn keine gültige Aktion vorliegt
		endif;
	}
	
	
	//Aufbereitung und Ausgabe des Settings-Eingabeformulars
	public function getForm($mid = 0)
	{	$mid = intval($mid);
	
		//hier alle Setting-Gruppen (außer Blacklist sowie Speziell) ausgeben
		return "alle unbeschränkten Setting-Gruppen als Formular ausgeben";
	
		if ($mid > 0):
			//hier zusätzlich die speziellen Settings nur für das Modul ausgeben
			return "Setting-Gruppe nur für das Modul ".$mid." als Formular ausgeben";
		endif;
	}
	
	
	//Ausgabe der Settings in der Modulausgabe
	public function getSettings($sid = 0)
	{	$sid = intval($sid);
	
		if ($sid > 0):
			return $sid."-FALKO";
		endif;
	}
	









	//Settings-Form jedem Modul anheften
	public function appendForm($ep)
	{	//Variablen deklarieren
		$cnt = "";
		$search = $replace = array();
		
		
		//Vorgaben einlesen
		if ($ep->getParam('function') == 'add' || $ep->getParam('function') == 'edit'):
			$op = $ep->getSubject();											//Content des ExtPoint (Modulinput)
			$set = $this->settings;
			
			
			/*
			echo "<pre>";
				echo $this->jsonpath;
				//print_r(htmlspecialchars($op));
				print_r($set);
			echo "</pre>";
			*/			
			
			
			if (count($set) > 0):
				//Setting-JSON enthält Inhalte
				$tabs = count($set);
				
				
				//Tab-Nav erstellen
				$tabnav = "";
				if ($tabs > 1):
					$tabnav .= '<ul class="nav nav-tabs">';
						for ($i=0; $i<$tabs; $i++):
							$tabnav .= '<li><a href="#fmMS-content-'.$i.'" data-toggle="tab">'.$set[$i]['tab'].'</a></li>';
						endfor;
					$tabnav .= '</ul>';
				endif;
				
			
				//Formfelder erstellen
				$tabcnt = "";
				$tabcnt .= '<div class="tab-content">';
				
				for ($i=0; $i<$tabs; $i++):
					$tabcnt .= ($tabs > 1) ? '<div class="tab-pane fade" id="fmMS-content-'.$i.'">' : '';

					//Gruppen auswerten
					if (array_key_exists('groups', $set[$i])):
						//Gruppen durchlaufen
						foreach ($set[$i]['groups'] as $group):
							$name 	= (isset($group['name'])) ? $group['name'] : '';
							$css	= ($group['inlineforms'] === true) ? 'fmMS-fieldset-inline' : '';
							
							$tabcnt .= '<div class="fmMS-fieldset '.$css.'">';
								$tabcnt .= (!empty($name)) ? '<span>'.$name.'</span>' : '';
								
								//Felder auswerten
								$tabcnt .= $this->getFormFields($group);
							$tabcnt .= '</div>';
						endforeach;					
					else:
						//keine Gruppen > Felder auswerten
						$tabcnt .= $this->getFormFields($set[$i]);
					endif;

					$tabcnt .= ($tabs > 1) ? '</div>' : '';				
				endfor;
			
				$tabcnt .= ($tabs > 1) ? '<script>$(function(){ $(\'a[href="#fmMS-content-0"]\').tab("show"); });</script>' : '';					//immer ersten Tab einblenden
			endif;
			
			
			//Ausgabe
$formcode = <<<EOD
<div class="fmModuleSettings">
	<div class="fmMS-inner">
		<div class="fmMS-toggler" id="fmMS-toggler" title="Zusätzliche Einstellungen dieses Blockes">
			<div><i class="rex-icon fa-cog"></i><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
			Block-Einstellungen
		</div>
		
		<div class="fmMS-content" id="fmMS-content">
			$tabnav		
			$tabcnt
		</div>
	</div>
</div>
<script>$(function(){ $('#fmMS-toggler').click(function(){ $('#fmMS-content').slideToggle('fast'); }); });</script>
EOD;



/*
$formcode = <<<EOD
<div class="fmModuleSettings">
	<div class="fmMS-inner">
		<div class="fmMS-toggler" id="fmMS-toggler" title="Zusätzliche Einstellungen dieses Blockes">
			<div><i class="rex-icon fa-cog"></i><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
			Block-Einstellungen
		</div>
		
		<div class="fmMS-content" id="fmMS-content">
		
			<ul class="nav nav-tabs">
				<li><a href="#content-1" data-toggle="tab">Inhalte</a></li>
				<li><a href="#content-2" data-toggle="tab">Abstände</a></li>
				<li><a href="#content-3" data-toggle="tab">Hintergrund</a></li>
				<li><a href="#content-4" data-toggle="tab">Sichtbarkeit</a></li>
				<li><a href="#content-5" data-toggle="tab">Sonstiges</a></li>
			</ul>
			
			<div class="tab-content">
			
			
				<div class="tab-pane fade" id="content-1">
				
					<div class="fmMS-fieldset">
						<!-- <span>xxx</span> -->
						
						<dl class="rex-form-group form-group">
							<dt><label for="">Ausgabebreite</label></dt>
							<dd>
								<select name="REX_INPUT_VALUE[2]" size="1" class="form-control">
										<option value="0" selected="">- keine Begrenzung -</option>
										<option value="500">500 Pixel</option><option value="600">600 Pixel</option><option value="700">700 Pixel</option><option value="800">800 Pixel</option><option value="900">900 Pixel</option><option value="1000">1000 Pixel</option><option value="1100">1100 Pixel</option>	</select>
							</dd>
						</dl>

						<dl class="rex-form-group form-group">
							<dt><label for="">Ausrichtung</label></dt>
							<dd>
								<select name="REX_INPUT_VALUE[2]" size="1" class="form-control">
									<option value="left" selected="selected">links</option>
									<option value="mittig">mittig</option>
									<option value="rechts">rechts</option>
								</select>
							</dd>
						</dl>
					</div>
					
				</div>
				
					
			
				<div class="tab-pane fade" id="content-2">
				
					<div class="fmMS-fieldset fmMS-fieldset-inline">
						<span>Abstand innen (Padding)</span>
						
						<dl class="rex-form-group form-group">
							<dt><label for="">links</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>

						<dl class="rex-form-group form-group">
							<dt><label for="">oben</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>

						<dl class="rex-form-group form-group">
							<dt><label for="">rechts</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>

						<dl class="rex-form-group form-group">
							<dt><label for="">unten</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>
					</div>
				
					<div class="fmMS-fieldset fmMS-fieldset-inline">
						<span>Abstand außen (Margin)</span>
						
						<dl class="rex-form-group form-group">
							<dt><label for="">links</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>

						<dl class="rex-form-group form-group">
							<dt><label for="">oben</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>

						<dl class="rex-form-group form-group">
							<dt><label for="">rechts</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>

						<dl class="rex-form-group form-group">
							<dt><label for="">unten</label></dt>
							<dd>
								<input type="number" size="25" name="company" id="company" value="0" maxlength="50" class="form-control" style="width:75px" min="-100" max="100">
							</dd>
						</dl>
					</div>
									
				</div>
				
				
				
				<div class="tab-pane fade" id="content-3">
				
					<div class="fmMS-fieldset">
						<!-- <span>xxx</span> -->
						
						<dl class="rex-form-group form-group">
							<dt><label for="">Hintergrundfarbe</label></dt>
							<dd>
								<input type="color" value="#FFFFFF" id="color" pattern="^#([A-Fa-f0-9]{6})$" required title="Hex-Wert eingeben" placeholder="#11AA99" class="form-control">
							</dd>
						</dl>
						
						<dl class="rex-form-group form-group">
							<dt><label for="">Hintergrundbild</label></dt>
							<dd>
								<div class="rex-js-widget rex-js-widget-media">
									<div class="input-group">
										<input class="form-control" type="text" name="itemprop_image" value="testbild1.jpg" id="REX_MEDIA_1" readonly="">
										<span class="input-group-btn">
											<a href="#" class="btn btn-popup" onclick="openREXMedia(1,'&amp;args[types]=gif%2Cjpg%2Cjpeg%2Cpng');return false;" title="Medium auswählen"><i class="rex-icon rex-icon-open-mediapool"></i></a>
											<a href="#" class="btn btn-popup" onclick="addREXMedia(1,'&amp;args[types]=gif%2Cjpg%2Cjpeg%2Cpng');return false;" title="Neues Medium hinzufügen"><i class="rex-icon rex-icon-add-media"></i></a>
											<a href="#" class="btn btn-popup" onclick="deleteREXMedia(1);return false;" title="Ausgewähltes Medium löschen"><i class="rex-icon rex-icon-delete-media"></i></a>
											<a href="#" class="btn btn-popup" onclick="viewREXMedia(1,'&amp;args[types]=gif%2Cjpg%2Cjpeg%2Cpng');return false;" title="Ausgewähltes Medium anzeigen"><i class="rex-icon rex-icon-view-media"></i></a>
										</span>
									</div>
									<div class="rex-js-media-preview"></div>
								</div>
							</dd>
						</dl>
					</div>

				</div>
				
				
				
				<div class="tab-pane fade" id="content-4">
				
					<div class="fmMS-fieldset">
						<!-- <span>xxx</span> -->
						
						<dl class="rex-form-group form-group">
							<dt><label for="">Block verstecken</label></dt>
							<dd>
								<div class="checkbox">
									<label for="useog">
										<input name="useog" type="checkbox" id="useog" value="checked" > Ja, diesen Block unsichtbar schalten
									</label>
								</div>
							</dd>
						</dl>
						
						<dl class="rex-form-group form-group">
							<dt><label for="">Online vom</label></dt>
							<dd>
							<div class="dropdown bootstrap-select rex-form-select-date bs3 fit-width disabled"><select name="art_online_from[day]" size="1" class="rex-form-select-date selectpicker" data-width="fit" id="rex-metainfo-art_online_from" tabindex="-98" disabled="">
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11" selected="selected">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
									<option value="24">24</option>
									<option value="25">25</option>
									<option value="26">26</option>
									<option value="27">27</option>
									<option value="28">28</option>
									<option value="29">29</option>
									<option value="30">30</option>
									<option value="31">31</option>
							</select></div>
							
							<div class="dropdown bootstrap-select rex-form-select-date bs3 fit-width disabled"><select name="art_online_from[month]" size="1" class="rex-form-select-date selectpicker" data-width="fit" id="rex-metainfo-art_online_from_month" tabindex="-98" disabled="">
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11" selected="selected">11</option>
									<option value="12">12</option>
							</select></div>
							
							<div class="dropdown bootstrap-select rex-form-select-year bs3 fit-width disabled"><select name="art_online_from[year]" size="1" class="rex-form-select-year selectpicker" data-width="fit" id="rex-metainfo-art_online_from_year" tabindex="-98" disabled="">
									<option value="2005">2005</option>
									<option value="2006">2006</option>
									<option value="2007">2007</option>
									<option value="2008">2008</option>
									<option value="2009">2009</option>
									<option value="2010">2010</option>
									<option value="2011">2011</option>
									<option value="2012">2012</option>
									<option value="2013">2013</option>
									<option value="2014">2014</option>
									<option value="2015">2015</option>
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018">2018</option>
									<option value="2019">2019</option>
									<option value="2020" selected="selected">2020</option>
									<option value="2021">2021</option>
									<option value="2022">2022</option>
									<option value="2023">2023</option>
									<option value="2024">2024</option>
									<option value="2025">2025</option>
									<option value="2026">2026</option>
									<option value="2027">2027</option>
									<option value="2028">2028</option>
									<option value="2029">2029</option>
									<option value="2030">2030</option>
							</select></div>
							<input class="rex-metainfo-checkbox" type="checkbox" name="art_online_from[active]" value="1"></dd>
						</dl>
						
						<dl class="rex-form-group form-group">
							<dt><label for="">Online bis zum</label></dt>
							<dd>
							<div class="dropdown bootstrap-select rex-form-select-date bs3 fit-width disabled"><select name="art_online_from[day]" size="1" class="rex-form-select-date selectpicker" data-width="fit" id="rex-metainfo-art_online_from" tabindex="-98" disabled="">
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11" selected="selected">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
									<option value="24">24</option>
									<option value="25">25</option>
									<option value="26">26</option>
									<option value="27">27</option>
									<option value="28">28</option>
									<option value="29">29</option>
									<option value="30">30</option>
									<option value="31">31</option>
							</select></div>
							
							<div class="dropdown bootstrap-select rex-form-select-date bs3 fit-width disabled"><select name="art_online_from[month]" size="1" class="rex-form-select-date selectpicker" data-width="fit" id="rex-metainfo-art_online_from_month" tabindex="-98" disabled="">
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11" selected="selected">11</option>
									<option value="12">12</option>
							</select></div>
							
							<div class="dropdown bootstrap-select rex-form-select-year bs3 fit-width disabled"><select name="art_online_from[year]" size="1" class="rex-form-select-year selectpicker" data-width="fit" id="rex-metainfo-art_online_from_year" tabindex="-98" disabled="">
									<option value="2005">2005</option>
									<option value="2006">2006</option>
									<option value="2007">2007</option>
									<option value="2008">2008</option>
									<option value="2009">2009</option>
									<option value="2010">2010</option>
									<option value="2011">2011</option>
									<option value="2012">2012</option>
									<option value="2013">2013</option>
									<option value="2014">2014</option>
									<option value="2015">2015</option>
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018">2018</option>
									<option value="2019">2019</option>
									<option value="2020" selected="selected">2020</option>
									<option value="2021">2021</option>
									<option value="2022">2022</option>
									<option value="2023">2023</option>
									<option value="2024">2024</option>
									<option value="2025">2025</option>
									<option value="2026">2026</option>
									<option value="2027">2027</option>
									<option value="2028">2028</option>
									<option value="2029">2029</option>
									<option value="2030">2030</option>
							</select></div>
							<input class="rex-metainfo-checkbox" type="checkbox" name="art_online_from[active]" value="1"></dd>
						</dl>
					</div>

				</div>
				
				
	
				<div class="tab-pane fade" id="content-5">
				
					<div class="fmMS-fieldset">
						<dl class="rex-form-group form-group">
							<dt><label for="">CSS-Klasse</label></dt>
							<dd>
								<input type="text" size="25" name="company" id="company" value="" maxlength="50" class="form-control">
							</dd>
						</dl>
					</div>

				</div>
				
			</div>
			
			
		</div>
	</div>
</div>

<script>
$(function(){
	$('#fmMS-toggler').click(function(){ $('#fmMS-content').slideToggle('fast'); });
	$('a[href="#content-1"]').tab('show');          
});
</script>
EOD;
*/


			$search 	= '/(<footer(?!.*<footer))/is';
			$replace = $formcode.'<footer';
			
			$op = preg_replace($search, $replace, $op);
			return $op;		
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
						$tabcnt .= '<dt><label for="">'.$field['label'].'</label></dt>';
						$tabcnt .= '<dd>';
						
						$width = intval($field['width']);
						$width = ($width > 0) ? 'style="width:'.$width.'px"' : '';
						
						$value = "";

						switch ($field['type']):
							case 'text':			$tabcnt .= $this->getField_text($field, $width, $value);			break;
							case 'color':			$tabcnt .= $this->getField_color($field, $width, $value);			break;
							case 'number':			$tabcnt .= $this->getField_number($field, $width, $value);			break;
							case 'range':			$tabcnt .= $this->getField_range($field, $width, $value);			break;
							case 'textarea':		$tabcnt .= $this->getField_textarea($field, $width, $value);		break;
							case 'select':			$tabcnt .= $this->getField_select($field, $width, $value);			break;
							case 'checkbox':		$tabcnt .= $this-> getField_checkbox($field, $width, $value);		break;
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
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '<input type="text" name="fmMS_'.$field['name'].'" id="fmMS_'.$field['name'].'" value="'.$v.'" placeholder="'.@$field['placeholder'].'" maxlength="'.$field['maxlength'].'" class="form-control" '.$w.' />';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_color($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '<input type="color" name="fmMS_'.$field['name'].'" id="fmMS_'.$field['name'].'" value="'.$v.'" placeholder="'.@$field['placeholder'].'" maxlength="'.$field['maxlength'].'" pattern="^#([A-Fa-f0-9]{6})$" class="form-control" '.$w.' />';
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_number($field, $w, $v)
	{	$tabcnt = "";
		$v = intval($v);
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '<input type="number" name="fmMS_'.$field['name'].'" id="fmMS_'.$field['name'].'" value="'.$v.'" maxlength="'.$field['maxlength'].'" min="'.$field['min'].'" max="'.$field['max'].'" class="form-control" '.$w.' />';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_range($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_textarea($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_select($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$width = intval($field['width']);
			$width = ($width > 0) ? 'style="width:'.$width.'px"' : '';
		
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_checkbox($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_radio($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexmedia($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexlink($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexmedialist($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	function getField_rexlinklist($field, $w, $v)
	{	$tabcnt = "";
	
		if (is_array($field) && count($field) > 0):
			$tabcnt .= '';	
		endif;
	
		return $tabcnt;
	}
	
	
	

	
}




?>