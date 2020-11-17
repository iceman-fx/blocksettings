<?php
/*
	Redaxo-Addon Blocksettings
	Verwaltung: default (single version)
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/

//Variablen deklarieren
$form_error = 0;
$id = 1;


//Formular dieser Seite verarbeiten
if ($func == "save" && isset($_POST['submit']) ):
	//Eintrag speichern
	$db = rex_sql::factory();
	$db->setTable(rex::getTable('1604_blocksettings'));

	$db->setValue("settings", rex_post('f_settings') );
	
	$db->setValue("whitelistmode", rex_post('f_whitelistmode'));
	
		if (rex_post('f_whitelistmode') == 'manual'):
			$tmp = implode("#", rex_post('f_whitelist'));
			$db->setValue("whitelist", '#'.$tmp.'#');
		endif;
	
	$db->setValue("status", 'checked');

	$db->setWhere("id = '".$id."'");
	$dbreturn = $db->update();

	//Rückmeldung
	echo ($dbreturn) ? rex_view::info($this->i18n('a1604_entry_saved')) : rex_view::warning($this->i18n('a1604_error'));
endif;


//Formular ausgeben
if ($id > 0):
	$db = rex_sql::factory();
	$db->setQuery("SELECT * FROM ".rex::getTable('1604_blocksettings')." WHERE id = '".$id."' LIMIT 0,1"); 
	$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
	
	//$id_kategorie1 = aFM_arrayString($dbe[0]['id_kategorie1'], 'array');
endif;


//Beispiel-JSON holen
$example = rex_file::get(rex_addon::get('blocksettings')->getPath('data/example.json'));


//Ausgabe: Formular (Update / Insert)
?>


<script>setTimeout(function() { jQuery('.alert-info').fadeOut(); }, 5000);</script>
<script>jQuery(function() { jQuery('#f_settings').focus(); });</script>
<script id="json-example" type="text/template"><?php echo $example; ?></script>


<form action="index.php?page=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
<input type="hidden" name="func" value="save" />

<section class="rex-page-section">
	<div class="panel panel-edit">
	
		<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1604_head_basics'); ?></div></header>
		
		<div class="panel-body">
		
			<dl class="rex-form-group form-group hiddenreplace" id="rtype_text">
				<dt><label for=""><?php echo $this->i18n('a1604_bas_settings'); ?></label></dt>
			  <dd>
				<textarea name="f_settings" cols="25" class="form-control rex-code" id="f_settings"><?php echo aFM_maskChar($dbe[0]['settings']); ?></textarea>
				<span class="infoblock"><a href="javascript:$('#f_settings').val($('#json-example').html());"><?php echo $this->i18n('a1604_bas_settings_example'); ?></a></span>
				</dd>
			</dl>
			

			<dl class="rex-form-group form-group"><dt></dt></dl>
			
			
			<dl class="rex-form-group form-group">
				<dt><label for=""><?php echo $this->i18n('a1604_bas_whitelistmode'); ?></label></dt>
                <dd>
                    <div class="radio">
                    <label for="pos1">
                        <input class="f_whitelistmode" name="f_whitelistmode" type="radio" value="auto" data-fid="wlmode_auto" id="pos1" <?php echo ($dbe[0]['whitelistmode'] != "manual") ? 'checked' : ''; ?> /> <?php echo $this->i18n('a1604_bas_whitelistmode_auto'); ?>
                    </label>
                    <label for="pos2">
                        <input class="f_whitelistmode" name="f_whitelistmode" type="radio" value="manual" data-fid="wlmode_manual" id="pos2" <?php echo ($dbe[0]['whitelistmode'] == "manual") ? 'checked' : ''; ?> /> <?php echo $this->i18n('a1604_bas_whitelistmode_manual'); ?>
                    </label>
                    </div>
                </dd>
			</dl>
			
			
			<dl class="rex-form-group form-group" id="wlmode_manual">
				<dt><label for=""><?php echo $this->i18n('a1604_bas_whitelist'); ?></label></dt>
				<dd>
					<select name="f_whitelist[]" id="f_whitelist" size="10" multiple class="form-control">
					<?php
                    $db = rex_sql::factory();
                    $db->setQuery("SELECT id, name FROM ".rex::getTable('module')." ORDER BY name, id");
                    
                    foreach ($db as $dbi):
						//$sel = (in_array($eid, $id_kategorie1)) ? 'selected="selected"' : '';
						$sel = (preg_match("/#".$dbi->getValue('id')."#/i", $dbe[0]['whitelist'])) ? 'selected="selected"' : '';
                        echo '<option value="'.$dbi->getValue('id').'" '.$sel.'>'.aFM_maskChar($dbi->getValue('name')).'</option>';
                    endforeach;
                    ?>
					</select>
				</dd>
			</dl>

		</div>
        
        
		<script type="text/javascript">
        $('#wlmode_manual').hide();
        $("input.f_whitelistmode").click(function(){ 
			var dst = $(this).attr('data-fid'); 
			var wldst = $('#wlmode_manual');
			
			wldst.find('select').attr('disabled', 'disabled');
			$("#"+dst).find('select').removeAttr('disabled');
			if (dst == 'wlmode_manual'){ wldst.show(); } else { wldst.hide(); }
		});
        	$("input.f_whitelistmode:checked").trigger('click');
        </script>
        
		
		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<input class="btn btn-save rex-form-aligned" type="submit" name="submit" title="<?php echo $this->i18n('a1604_save'); ?>" value="<?php echo $this->i18n('a1604_save'); ?>" />
				</div>
			</div>
		</footer>
		
	</div>
</section>
	
</form>