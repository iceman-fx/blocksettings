// moduleSettings rexLinkMap/Media-Fork
// v1.0



$(function(){
	
	//Fallback-Einbindung des Setting-Forms
	$(document).on("rex:ready", function(){
		url = new URLSearchParams(window.location.search);
		page 	= url.get("page");
		func 	= url.get("function");
		mid 	= parseInt(url.get("module_id"));
		sid		= parseInt(url.get("slice_id"));
		
		if (page == 'content/edit' && func == 'add' && mid > 0 && sid <= 0) {
		//if (page == 'content/edit' && (func == 'add' || func == 'edit') && mid > 0) {
			//console.log("neuer Slice soll angelegt werden");			
			
			$.get("", "rex-api-call=a1604_appendForm", function(data){
				if (data != 'undefined' && data != "") {
					$('.rex-slice-add footer.panel-footer').before(data);
				}
			});
		}
	});	
	
});





// LINKMAP
function fmBS_deleteREXLink(id)
{	var link;
	link = new getObj(id);
	link.obj.value = "";
	link = new getObj(id+"_NAME");
	link.obj.value = "";
}

function fmBS_openREXLinklist(id, param)
{	var linklist = id;
	var linkselect = 'REX_LINKLIST_SELECT_'+id;
	var needle = new getObj(linkselect);
	var source = needle.obj;
	var sourcelength = source.options.length;
	if (typeof(param) == 'undefined') { param = ''; }
	for (var ii = 0; ii < sourcelength; ii++) {
		if (source.options[ii].selected) {
			param = '&action=link_details&file_name='+ source.options[ii].value;
			break;
		}
	}

	return newLinkMapWindow('index.php?page=linkmap&opener_input_field='+linklist+param);
}


// MEDIAPOOL - MEDIA
function fmBS_openREXMedia(id,param)
{	var mediaid = id;
	if (typeof(param) == 'undefined') { param = ''; }
	
	return newPoolWindow('index.php?page=mediapool/media'+param+'&opener_input_field='+mediaid);
}

function fmBS_deleteREXMedia(id)
{	var a = new getObj(id);
	a.obj.value = "";
}

function fmBS_addREXMedia(id,params)
{	if (typeof(params) == 'undefined') { params = ''; }
	
	return newPoolWindow('index.php?page=mediapool/upload&opener_input_field='+id+params);
}

// MEDIAPOOL - MEDIALIST
function fmBS_openREXMedialist(id,param)
{	var medialist = id;
	var mediaselect = 'REX_MEDIALIST_SELECT_' + id;
	var needle = new getObj(mediaselect);
	var source = needle.obj;
	var sourcelength = source.options.length;
	if (typeof(param) == 'undefined') { param = ''; }	
	for (ii = 0; ii < sourcelength; ii++) {
		if (source.options[ii].selected) {
			param += '&file_name='+ source.options[ii].value;
			break;
		}
	}
	
	return newPoolWindow('index.php?page=mediapool/media'+param+'&opener_input_field='+medialist);
}

function fmBS_viewREXMedialist(id,param)
{	var medialist = 'REX_MEDIALIST_' + id;
	var mediaselect = 'REX_MEDIALIST_SELECT_' + id;
	var needle = new getObj(mediaselect);
	var source = needle.obj;
	var sourcelength = source.options.length;
	if ( typeof(param) == 'undefined') { param = ''; }
	for (ii = 0; ii < sourcelength; ii++) {
		if (source.options[ii].selected) {
			param += '&file_name='+ source.options[ii].value;
			break;
		}
	}
	
	if(param != '')
		return newPoolWindow('index.php?page=mediapool/media' + param + '&opener_input_field=' + medialist);
}

function fmBS_addREXMedialist(id,params)
{	if (typeof(params) == 'undefined') { params = ''; }
	
	return newPoolWindow('index.php?page=mediapool/upload&opener_input_field='+id+params);
}