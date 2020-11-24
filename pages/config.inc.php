<?php
/*
	Redaxo-Addon Blocksettings
	Verwaltung: Einstellungen
	v1.0
	by Falko Müller @ 2020
	package: redaxo5
*/

//Variablen deklarieren
$form_error = 0;

//Formular dieser Seite verarbeiten
if ($func == "save" && isset($_POST['submit'])):
	//Konfig speichern
	$res = $this->setConfig('config', [
		'editor'				=> rex_post('editor'),
		'editor_height'			=> rex_post('editor_height'),
		'editor_profile'		=> rex_post('editor_profile'),
	]);

	//Rückmeldung
	echo ($res) ? rex_view::info($this->i18n('a1604_settings_saved')) : rex_view::warning($this->i18n('a1604_error'));

	//reload Konfig
	$config = $this->getConfig('config');
endif;
?>


<script>setTimeout(function() { jQuery('.alert-info').fadeOut(); }, 5000);</script>

<form action="index.php?page=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
<input type="hidden" name="func" value="save" />

<section class="rex-page-section">
    <div class="panel panel-edit">
    
		<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1604_head_config'); ?></div></header>
        
		<div class="panel-body">
        
        	<legend><?php echo $this->i18n('a1604_subheader_config1'); ?></legend>
            

            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo $this->i18n('a1604_config_editor'); ?></label></dt>
                <dd>
                    <select name="editor" size="1" class="form-control" <?php echo $dis; ?>>
                        <?php
						$eds = array("CKEditor 4"=>"ckeditor", "CKEditor 5"=>"cke5", "TinyMCE 4"=>"tinymce4", "TinyMCE 5"=>"tinymce5");				//, "Redactor"=>"redactor", "Redactor 2"=>"redactor2"
						
						$founded = 0;
						foreach ($eds as $key=>$val):
							if (!rex_addon::get($val)->isAvailable()) { continue; }
						
							$sel = ($val == $config['editor']) ? 'selected="selected"' : '';
							echo '<option value="'.$val.'" '.$sel.'>'.$key.'</option>';
							$founded++;
						endforeach;
						
						echo ($founded <= 0) ? '<option value="0">'.$this->i18n('a1604_config_editor_notfound').'</option>' : '';
						?>
                    </select>
                </dd>
            </dl>
            

            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo $this->i18n('a1604_config_editor_profile'); ?></label></dt>
                <dd>
                    <input type="text" size="25" name="editor_profile" id="editor_profile" value="<?php echo $config['editor_profile']; ?>" maxlength="50" class="form-control" />
                </dd>
            </dl>
            

            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo $this->i18n('a1604_config_editor_height'); ?></label></dt>
                <dd>
                	<div class="input-group">
	                    <input type="text" size="25" name="editor_height" id="editor_height" value="<?php echo $config['editor_height']; ?>" maxlength="3" class="form-control" />
                        <span class="input-group-addon"><div>px</div></span>
                    </div>
                </dd>
            </dl>

            
            <!--      
			<p>&nbsp;</p>
            <legend></legend>
			<p><?php echo $this->i18n('a1604_text1'); ?></p>
            -->
                                                              
        </div>
        
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