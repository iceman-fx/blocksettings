<?php
/*
	Redaxo-Addon Blocksettings
	Verwaltung: index
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/

//Fehlerhinweise (E_NOTICE) abschalten
error_reporting(E_ALL ^  E_NOTICE);

//Variablen deklarieren
$mypage = $this->getProperty('package');

$page = rex_request('page', 'string');
$subpage = rex_be_controller::getCurrentPagePart(2);		//Subpages werden aus page-Pfad ausgelesen (getrennt mit einem Slash, z.B. page=demo_addon/subpage -> 2 = zweiter Teil)
	$tmp = rex_request('subpage', 'string');
	$subpage = (!empty($tmp)) ? $tmp : $subpage;
$func = rex_request('func', 'string');

$config = $this->getConfig('config');


//Userrechte prüfen
$isAdmin = ( is_object(rex::getUser()) AND (rex::getUser()->hasPerm($mypage.'[config]') OR rex::getUser()->isAdmin()) ) ? true : false;



//Seitentitel ausgeben
echo rex_view::title($this->i18n('a1604_title').'<span class="addonversion">'.$this->getProperty('version').'</span>');



//globales Inline-CSS + Javascript
?>
<style type="text/css">
input.rex-form-submit { margin-left: 190px !important; }	/* Rex-Button auf neue (Labelbreite +10) verschieben */
td.name { position: relative; padding-right: 20px !important; }
.nowidth { width: auto !important; }
.togglebox { display: none; margin-top: 8px; font-size: 90%; color: #666; line-height: 130%; }
.toggler { width: 15px; height: 12px; position: absolute; top: 10px; right: 3px; }
.toggler a { display: block; height: 11px; background-image: url(../assets/addons/<?php echo $mypage; ?>/arrows.png); background-repeat: no-repeat; background-position: center -6px; cursor: pointer; }
.required { font-weight: bold; }
.inlinelabel { display: inline !important; width: auto !important; float: none !important; clear: none !important; padding: 0px  !important; margin: 0px !important; font-weight: normal !important; }
.inlineform { display: inline-block !important; }
.form_auto { width: auto !important; }
.form_plz { width: 25%px !important; margin-right: 6px; }
.form_ort { width: 73%px !important; }
.form_25perc { width: 25% !important; min-width: 120px; }
.form_50perc { width: 50% !important; min-width: 120px; }
.form_75perc { width: 75% !important; }
.form_content { display: block; padding-top: 5px; }
.form_readonly { background-color: #EEE; color: #999; }
.form_isoffline { color: #A00; }
.addonversion { margin-left: 7px; }
.radio label, .checkbox label { margin-right: 20px; }

.form_divider { margin-bottom: 20px; border-bottom: 1px solid #C1C9D4; }
.form_column, .datepicker-widget { display: inline-block; vertical-align: middle; }
	.form_column-spacer, .datepicker-widget-spacer { padding: 0px 5px; }
.daterangepicker { box-shadow: 3px 3px 10px 0px rgb(0,0,0, 0.2); }
.daterangepicker .calendar-table th, .daterangepicker .calendar-table td { padding: 2px; }
.modal-opener { cursor: pointer; }

.addon_failed, .addonfailed { color: #F00; font-weight: bold; margin-bottom: 15px; }
.addon_search { width: 100%; background-color: #EEE; }
.addon_search .searchholder { position: relative; }
	.addon_search .searchholder a { position: absolute; top: -1px; right: 7px; cursor: pointer; }
	@-moz-document url-prefix('') { .addon_search .searchholder a { top: -3px; } /* FF-only */ }
.addon_search .border-top { border-top: 1px solid #DFE9E9; }
.addon_search td { width: 46%; padding: 9px !important; font-size: 90%; color: #333; border: none !important; vertical-align: top !important; }
	.addon_search td.td2 { width: 8%; text-align: center; }
	.addon_search td.td3 { text-align: right;	}
.addon_search input { width: 84px; margin: 0px !important; padding: 2px !important; height: 20px !important; }
	.addon_search input.sbeg { width: 84px; padding: 2px 18px 2px 2px !important; }
.addon_search select { margin: 0px !important; padding: 0px 10px 0px 0px !important; height: 20px !important; min-width: 230px; max-width: 230px; }
	.addon_search select option { margin-right: -10px; padding-right: 10px; }
	.addon_search select.multiple { height: 60px !important; }
	.addon_search select.form_auto { width: auto !important; max-width: 634px; }
.addon_search input.checkbox { display: inline-block; width: auto; margin: 0px 6px !important; padding: 0px !important; height: auto !important; }
.addon_search input.button { font-weight: bold; margin: 0px !important; margin-left: 5px !important; width: auto; padding: 0px 2px 0px 2px !important; height: 21px !important; }
.addon_search label { display: inline-block; width: 90px !important; font-weight: normal; }
	.addon_search label.multiple { vertical-align: top !important; }
	.addon_search label.form_auto { width: auto !important; }
.addon_search a.moreoptions { display: inline-block; vertical-align: sub; }
.addon_search .rightmargin { margin-right: 7px !important; }

.db-order { display: inline; /*width: 20px; height: 10px;*/ padding: 0px 5px; margin-left: 0px; cursor: pointer; }
.db-order-desc { background-position: center bottom; }
.block { display: block; }
.info { font-size: 0.825em; font-weight: normal; }
.info-labels { display: inline-block; padding: 3px 6px; background: #EAEAEA; margin-right: 5px; font-size: 0.80em; }
	.info-green { background: #360; color: #FFF; }
	.info-red { background: #900; color: #FFF; }
.infoblock { display: block; font-size: 0.825em; margin-top: 7px; }
.textblock { width: auto !important; font-weight: normal; padding-bottom: 10px; }
.charlimitreached { background-color: rgba(255,0,0, 0.15) !important; }
a.copyfromabove { cursor: pointer; }

span.ajaxNav { display: inline-block; padding: 2px 4px; margin: 3px 2px 1px; cursor: pointer; }
span.ajaxNav:hover { background-color: #666; color: #FFF; }
span.ajaxNavSel { background-color: #CCC; }

@media (min-width: 1400px) { .modal-content .rex-form-group:not(.rex-form-group-vertical)>dt { width: 210px; } }
</style>


<script type="text/javascript">
setTimeout(function() { jQuery('.alert-info').fadeOut(); }, 5000);			//Rückmeldung ausblenden

//beim Start ausführen
jQuery(function(){
	
});

//Funktionen
function loadAJAX(params, dst, paramNav)
{	if (dst != ""){
		paramNav = parseInt(paramNav);
		if (params != "" && paramNav >= 0) params += '&';
			params += 'limStart='+ encodeURIComponent(paramNav);
		var jlLoader = jQuery('#ajax_loading');
			jlLoader.show();
		jQuery.post("index.php", params, function(resp){ jQuery(dst).html(resp); jlLoader.hide(); });
	}
}

function getset(getid, setid)
{	if (getid != "" && setid != "") {
		var getval = jQuery('#'+getid+' option:selected').val();
			if (getval != "") { jQuery('#'+setid).val(getval); }
	}
}

function toggleContent(dst, src)
{	if (dst != "" && dst != 'undefined') {
		jQuery(dst).toggle();
		if (src.length > 0) {
			console.log(src.length);
			if (typeof src == 'string') { src = jQuery(src); }
			src.toggle();
		}
	}
}
</script>


<?php
//Unterseite einbinden
switch($subpage):
	case "config":				//Einstellungen
								require_once("config.inc.php");
								break;

	case "help":				//Hilfe
								require_once("help.inc.php");
								break;

	default:					//Index = Standardwerte
								require_once("default.inc.php");
								break;
endswitch;
?>


<!-- PLEASE DO NOT REMOVE THIS COPYRIGHT -->
<p><?php echo $this->getProperty('author'); ?></p>
<!-- THANK YOU! -->