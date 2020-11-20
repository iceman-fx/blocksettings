<?php
/*
	Redaxo-Addon Modulesettings
	Verwaltung: default
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/

//Variablen deklarieren
$mode = rex_request('mode');
$id = intval(rex_request('id'));
	$id = 1;										//nur für erste Version, bis Multi-Settings möglich sind
$form_error = 0;


//Formular dieser Seite verarbeiten
if ($func == "save" && (isset($_POST['submit']) || isset($_POST['submit-apply'])) ):
	//Pflichtfelder prüfen
	$fields = array("f_settings");
		foreach ($fields as $field):
			$tmp = rex_post($field);
			$form_error = (empty($tmp)) ? 1 : $form_error;
		endforeach;
		
	if ($form_error):
		//Pflichtfelder fehlen
		echo rex_view::warning($this->i18n('a1604_entry_emptyfields'));
	else:
		//Eintrag speichern
		$db = rex_sql::factory();
		$db->setTable(rex::getTable('1604_modulesettings'));
	
		$db->setValue("settings", rex_post('f_settings'));
		
		$db->setValue("use_whitelist", rex_post('f_use_whitelist'));
		$db->setValue("whitelist", rex_post('f_whitelist'));
		
		$db->setValue("status", rex_post('f_status'));

		if ($id > 0):
			$db->setWhere("id = '".$id."'");
			$dbreturn = $db->update();
			
			$form_error = (isset($_POST['submit-apply'])) ? 1 : $form_error;
		else:
			$dbreturn = $db->insert();
		endif;

		echo ($dbreturn) ? rex_view::info($this->i18n('a1604_entry_saved')) : rex_view::warning($this->i18n('a1604_error'));
	endif;

/*
elseif ($func == "status" && $id > 0):
	//Status setzen
	$db = rex_sql::factory();
	$db->setQuery("SELECT status FROM ".rex::getTable('1604_modulesettings')." WHERE id = '".$id."' LIMIT 0,1");
	$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
	
	$newstatus = ($dbe[0]['status'] != "checked") ? "checked" : "";
	
	$db = rex_sql::factory();
	$db->setTable(rex::getTable('1604_modulesettings'));
	$db->setWhere("id = '".$id."'");

	$db->setValue("status", $newstatus);
	$db->update();
	
elseif ($func == "delete" && $id > 0):
	//Eintrag löschen - mit möglicher Prüfung auf Zuweisung
	$db = rex_sql::factory();
	$db->setQuery("SELECT `search` FROM ".rex::getTable('1604_modulesettings')." WHERE id = '".$id."' LIMIT 0,1");
	$dbe = $db->getArray();	//mehrdimensionales Array kommt raus

	$db = rex_sql::factory();
	$db->setQuery("SELECT id FROM ".rex::getTable('1604_modulesettings')." WHERE BINARY `replace` like '%".aFM_maskSql($dbe[0]['search'])."%' AND id <> '".$id."'"); 
	
	if ($db->getRows() <= 0):
		//löschen
		$db = rex_sql::factory();
		$db->setTable(rex::getTable('1604_modulesettings'));		
		$db->setWhere("id = '".$id."'");
			
		echo ($db->delete()) ? rex_view::info($this->i18n('a1604_entry_deleted')) : rex_view::warning($this->i18n('a1604_error_deleted'));
	else:
		//nicht löschen aufgrund gültiger Zuweisung
		echo rex_view::warning($this->i18n('a1604_entry_used'));
	endif;	
	
elseif ($func == "duplicate" && $id > 0):
	//Eintrag duplizieren
	$db = rex_sql::factory();
	$db->setQuery("SELECT * FROM ".rex::getTable('1604_modulesettings')." WHERE id = '".$id."'"); 
	
	if ($db->getRows() > 0):
		$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
		$db = rex_sql::factory();
		$db->setTable(rex::getTable('1604_modulesettings'));
		
		foreach ($dbe[0] as $key=>$val):			
			if ($key == 'id') { continue; }
			if ($key == 'status') { continue; }
			if ($key == 'search') { $val = a1604_phDuplicateName($val); }
			
			$db->setValue($key, $val);
		endforeach;
		
		$dbreturn = $db->insert();
	endif;
*/

endif;


$func = "update";		//nur für erste Version, bis Multi-Settings möglich sind


//Formular oder Liste ausgeben
if ($func == "update" || $func == "insert" || $form_error == 1):
	//Formular ausgeben
	if (($mode == "update" || $func == "update") && $id > 0):
		$db = rex_sql::factory();
		$db->setQuery("SELECT * FROM ".rex::getTable('1604_modulesettings')." WHERE id = '".$id."' LIMIT 0,1"); 
		$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
	endif;
	
	//Std.vorgaben der Felder setzen
	if (!isset($dbe) || (is_array($dbe) && count($dbe) <= 0)):
		$dbe[0]['status'] = $dbe[0]['settings'] = $dbe[0]['use_whitelist'] = $dbe[0]['whitelist'] = '';
	endif;
	//$dbe[0] = array_map('htmlspecialchars', $dbe[0]);
	
	//Insert-Vorgaben
	if ($mode == "insert" || $id <= 0):
		//$dbe[0]["date"] = time();
	endif;
	
	if ($form_error):
		//Formular bei Fehleingaben wieder befüllen
		$dbe[0]['id'] = $id;
		$dbe[0]["status"] = rex_post('f_status');
		$dbe[0]["settings"] = rex_post('settings');
		
		$dbe[0]["use_whitelist"] = rex_post('f_use_whitelist');
		$dbe[0]["whitelist"] = rex_post('f_whitelist');
		
		$func = $mode;
	endif;

	
	//Werte aufbereiten


	//Ausgabe: Formular (Update / Insert)
	?>

	<script type="text/javascript">jQuery(function() { jQuery('#f_text').focus(); });</script>
    
    <style type="text/css"></style>
    
    <form action="index.php?page=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
    <!-- <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" /> -->
    <input type="hidden" name="func" value="save" />
    <input type="hidden" name="id" value="<?php echo $dbe[0]['id']; ?>" />
	<input type="hidden" name="mode" value="<?php echo $func; ?>" />
    
    <section class="rex-page-section">
        <div class="panel panel-edit">
        
            <header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1604_head_placeholder'); ?></div></header>
            
            <div class="panel-body">
            
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1604_status'); ?></label></dt>
                    <dd>
                    	<div class="checkbox">
                        	<label for="f_status"> <input type="checkbox" name="f_status" id="f_status" value="checked" <?php echo $dbe[0]['status']; ?> class="" /> <?php echo $this->i18n('a1604_yes').$this->i18n('a1604_statusinfo'); ?> </label>
                        </div>
                    </dd>
                </dl>

        
                <dl class="rex-form-group form-group"><dt></dt></dl>
                
               
                <dl class="rex-form-group form-group hiddenreplace" id="rtype_text">
                    <dt><label for=""><?php echo $this->i18n('a1604_settings'); ?></label></dt>
                  <dd>
                    <textarea name="f_settings" cols="25" class="form-control rex-code" id="f_settings"><?php echo aFM_maskChar($dbe[0]['settings']); ?></textarea>
                    <span class="infoblock"><a href="#">Beispiel übernehmen...</a></span>
                    </dd>
                </dl>
                

                <dl class="rex-form-group form-group"><dt></dt></dl>
                
                
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1604_use_whitelist'); ?></label></dt>
                    <dd>
                    	<div class="checkbox">
                        	<label for="f_use_whitelist"> <input type="checkbox" name="f_use_whitelist" id="f_use_whitelist" value="checked" <?php echo $dbe[0]['use_whitelist']; ?> class="" /> <?php echo $this->i18n('a1604_yes').$this->i18n('a1604_use_whitelistinfo'); ?> </label>
                        </div>
                    </dd>
                </dl>
                
                
                <dl class="rex-form-group form-group hiddenreplace" id="rtype_text">
                    <dt><label for=""><?php echo $this->i18n('a1604_whitelist'); ?></label></dt>
                    <dd>
                    	<select name="f_whitelist" id="f_whitelist" size="10" multiple class="form-control">
                        </select>
                    </dd>
                </dl>

                
                <p>&nbsp;</p>
                <p><?php /*echo $this->i18n('a1604_text10');*/ ?></p>
                                                                  
            </div>
            
            <footer class="panel-footer">
                <div class="rex-form-panel-footer">
                    <div class="btn-toolbar">
                        <input class="btn btn-save rex-form-aligned" type="submit" name="submit" title="<?php echo $this->i18n('a1604_save'); ?>" value="<?php echo $this->i18n('a1604_save'); ?>" />
                        <?php if ($func == "update"): ?>
                        <input class="btn btn-save" type="submit" name="submit-apply" title="<?php echo $this->i18n('a1604_apply'); ?>" value="<?php echo $this->i18n('a1604_apply'); ?>" />
                        <?php endif; ?>
                        <input class="btn btn-abort" type="submit" name="submit-abort" title="<?php echo $this->i18n('a1604_abort'); ?>" value="<?php echo $this->i18n('a1604_abort'); ?>" />
                    </div>
                </div>
            </footer>
            
        </div>
    </section>
        
    </form>


<?php
endif;

/*
else:
	//Übersichtsliste laden + ausgeben
	// --> wird per AJAX nachgeladen !!!
	
	$addpath = "index.php?page=".$page;
	?>

    <section class="rex-page-section">
        <div class="panel panel-default">
        
            <header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1604_overview').' '.$this->i18n('a1604_placeholder'); ?></div></header>  
              
			<script type="text/javascript">
            jQuery(function() {
                //Ausblenden - Elemente
                jQuery('.search_options').hide();
                
                //Formfeld fokussieren
                jQuery('#s_sbeg').focus();
            
                //Liste - Filtern
                var params = 'page=<?php echo $page; ?>&subpage=load-phlist&sbeg=';
                var dst = '#ajax_jlist';
                
                jQuery('#db-order').click(function() {
                    var btn = jQuery(this);
                    btn.toggleClass('db-order-desc');
                        if (btn.hasClass('db-order-desc')) { btn.attr('data-order', 'desc'); } else { btn.attr('data-order', 'asc'); }
                    loadAJAX(params + getSearchParams(), dst, 0);
                });
                
          		jQuery('#s_aid').change(function(){												loadAJAX(params + getSearchParams(), dst, 0);	});
                jQuery('#s_sbeg').keyup(function(){												loadAJAX(params + getSearchParams(), dst, 0);	});
                jQuery('#s_button').click(function(){											loadAJAX(params + getSearchParams(), dst, 0);	});
                jQuery('#s_resetsbeg').click(function(){		jQuery('#s_aid').val(0); jQuery('#s_sbeg').val("");
                                                                loadAJAX(params, dst, 0);	});
                                                                
                jQuery(document).on('click', 'span.ajaxNav', function(){
                    var navsite = jQuery(this).attr('data-navsite');
                    loadAJAX(params + getSearchParams(), dst, navsite);
                    jQuery("body, html").delay(150).animate({scrollTop:0}, 750, 'swing');
                });
                
                function getSearchParams()
                {	var searchparams = tmp = '';
                        searchparams += encodeURIComponent(jQuery('#s_sbeg').val());								//Suchbegriff = 1.Parameter (param-Name wird in "var params" gesetzt)
						searchparams += '&aid=' + encodeURIComponent(jQuery('#s_aid').val());						//Anzeigestatus online|offline
                        searchparams += '&order=' + encodeURIComponent(jQuery('#db-order').attr('data-order'));		//Sortierrichtung asc|desc
                    return searchparams;
                }
            });
            </script>

            <!-- Suchbox -->
            <table class="table table-striped addon_search" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td class="td1" valign="middle">
					<?php echo $this->i18n('a1604_search_sort'); ?>: <select name="s_aid" size="1" id="s_aid">
                    <option value="0" selected="selected"><?php echo $this->i18n('a1604_search_all'); ?></option>
                    <option value="">&nbsp;</option>                        
                    <?php
					$aid = (isset($_SESSION['as_aid_modulesettings'])) ? $_SESSION['as_aid_modulesettings'] : '';
					$sbeg = (isset($_SESSION['as_sbeg_modulesettings'])) ? $_SESSION['as_sbeg_modulesettings'] : '';
					
					$a = array(1=>aFM_maskChar($this->i18n('a1604_search_sort_onlyactive')), 2=>aFM_maskChar($this->i18n('a1604_search_sort_onlyinactive')));
						foreach ($a as $key => $value):
							$sel = ($key == $aid) ? 'selected="selected"' : '';
							echo '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
						endforeach;
                    ?>
                    </select>
                </td>
                <td class="td2"><img src="/assets/addons/<?php echo $mypage; ?>/indicator.gif" width="16" height="16" border="0" id="ajax_loading" style="display:none;" /></td>
                <td class="td3">
					<?php echo $this->i18n('a1604_search_keyword'); ?>: 
                    <span class="searchholder">
                    	<input name="s_sbeg" id="s_sbeg" type="text" size="10" maxlength="50" value="<?php echo aFM_maskChar($sbeg); ?>" class="sbeg" />
                        <a id="s_resetsbeg" title="<?php echo $this->i18n('a1604_search_reset'); ?>"><img src="/assets/addons/<?php echo $mypage; ?>/reset.gif" width="13" height="13" alt="<?php echo $this->i18n('a1604_search_reset'); ?>" border="0" /></a>
                    </span>
                    <input name="submit" type="button" value="<?php echo $this->i18n('a1604_search_submit'); ?>" class="button" id="s_button" />
				</td>
			</tr>
            </tbody>
            </table>


			<!-- Liste -->
            <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="rex-table-icon"><a href="<?php echo $addpath; ?>&func=insert" accesskey="a" title="<?php echo $this->i18n('a1604_new'); ?> [a]"><i class="rex-icon rex-icon-add-template"></i></a></th>
                <th class="rex-table-id">ID</th>
                <th><?php echo $this->i18n('a1604_ms_list_name'); ?> <a class="db-order db-order-asc" id="db-order" data-order="asc"><i class="rex-icon fa-sort"></i></a></th>
                <th><?php echo $this->i18n('a1604_ms_list_settings'); ?></th>
                <th class="rex-table-action" colspan="3"><?php echo $this->i18n('a1604_statusfunc'); ?></th>
            </tr>
            </thead>

            <tbody id="ajax_jlist">
            <script type="text/javascript">jQuery(function(){ jQuery('#s_button').trigger('click'); });</script>
            </tbody>
            </table>
            

		</div>
	</section>

<?php
endif;
*/
?>