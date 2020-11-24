// blockSettings
// v1.0

$(function(){
	
	//Einbindung des Setting-Forms
	$(document).on("rex:ready", function() { getBlockSettings(); });				//Settings bei AJAX-Aktion einbinden
	getBlockSettings();																//Settings bei URL-Direktaufruf einbinden
	
	function getBlockSettings()
	{	url = new URLSearchParams(window.location.search);
		page 	= url.get("page");
		func 	= url.get("function");
		mid 	= parseInt(url.get("module_id"));
		sid		= parseInt(url.get("slice_id"));
		
		//if (page == 'content/edit' && func == 'add' && mid > 0 && sid <= 0) {
		if (page == 'content/edit' && (func == 'add' || func == 'edit')) {
			//console.log("append blocksettings");
			//console.log("mid: " +mid+ " / sid: " +sid);
			
			$.get("", "rex-api-call=a1604_appendForm", function(data){
				//append Form
				if (data != 'undefined' && data != "") {
					$('.rex-slice-add footer.panel-footer, .rex-slice-edit footer.panel-footer').before(data);
				}
				
				
				//Datepicker-Einbindung
				$.datetimepicker.setLocale('de');
				$('.fmBS_datepicker-widget input').each(function(){
					lazy = ($(this).attr('data-datepicker-lazy') == 'true' ? true : false);
					mask = ($(this).attr('data-datepicker-mask') == 'true' ? true : false);
					time = ($(this).attr('data-datepicker-time') == 'true' ? true : false);
					format = (time ? 'd.m.Y H:i' : 'd.m.Y');
					now = new Date();
						start = 2000;
						end = now.getFullYear() + 10;
					$(this).datetimepicker({
						format: format, formatDate: 'd.m.Y', formatTime: 'H:i', yearStart: start, yearEnd: end, dayOfWeekStart: 1,
						mask: mask, lazyInit: lazy, week: true, timepicker: time, step: 15
					});
				});
				
				$('.fmBS_datepicker-widget a').click(function(){
					dst = $(this).attr('data-datepicker-dst');
					if (dst != "" && dst != 'undefined') { $('#'+dst).datetimepicker('show'); }
				});
				
				$('.fmBS_datepicker-widget input').each(function(){
					if ($(this).val() == '__.__.____' || $(this).val() == '__.__.____ __:__') { $(this).val(""); }						//Kalender-Value bei Reload korrigieren
				});
				
				
				//Range + Color-Abgleich
				$('.fmBS-range-input-group input[type=range]').on("input change", function(){
					$(this).nextAll('input[type=hidden]').val(this.value);
					$(this).nextAll('span.fmBS-rangetext').text(this.value);
				});
			
				$('.fmBS-color-input-group input[type=color]').on("input change", function(){
					$(this).parent().prevAll('input[type=text]').val(this.value);
				});
				$('.fmBS-color-input-group input[type=text]').on("input change", function(){
					$(this).next().children('input[type=color]').val(this.value);
				});
				
				
			});
		}
	}
	
});