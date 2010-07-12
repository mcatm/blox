(function($) {
	$.fn.Editor = function(options) {
		initEditor($(this), options);
	};
	
	function initEditor(el, options) {//set Editor
		//var util = '<div id="editor-util"><a class="a">a</a> <a class="blockquote">quote</a> <a class="strong">b</a> <a class="youtube">youtube</a></div>';
		var util = '<div id="editor-util" class="clearfix"><a class="a">a</a> <a class="blockquote">quote</a> <a class="strong">b</a>';// <a class="youtube">youtube</a></div>';
		//youtube機能はつくまで暫定（CSSも参照）
		
		el.each(function() {
			if ($(this).prev().attr('id') == 'editor-util') $(this).prev().remove();
			$(this).before(util);
		});
		
		setFn();
	};
	
	function setFn() {
		$('#editor-util a').each(function() {
			$(this).bind('click', function(e) {
				var tag = $(this).attr('class')
				
				switch(tag) {
					case 'a':
					setLink(e);
					break;
					
					case 'youtube':
					setYoutube(e);
					break;
					
					default:
					setTag(e, tag);
					break;
				};
			});
		});
	};
	
	function setTag(e, tag) {
		getSelection(e, tag);
	};
	
	function setLink(e) {
		var url = prompt('URL', 'http://');
		url = qEsc(url);
		getSelection(e, 'a href="' + url + '" target="_blank"', 'a');
	}
	
	function setYoutube(e) {
		var url = prompt('Youtube URL', '');
		url = qEsc(url);
		//getSelection(e, '[youtube:' + url + ']');
		
		var obj = $(e.target);
		var form_id = obj.parent().next(["input"]).attr('id');
		
		var form = document.getElementById(form_id);//textareaObject
		var pos = getAreaRange(form);
		
		var val = form.value;
		var range = val.slice(pos.start, pos.end);
		var beforeNode = val.slice(0, pos.start);
		var afterNode = val.slice(pos.end);
		var insertNode;
		
		if ((range || pos.start != pos.end) && url == '') {
			insertNode = '[youtube:' + range + ']';
			form.value = beforeNode + insertNode + afterNode;
		} else {
			insertNode = '[youtube:' + yt_str + ']';
			form.value = beforeNode + insertNode + afterNode;
		}
	}
	
	function getSelection(e, tag, tag_close) {
		var obj = $(e.target);
		var form_id = obj.parent().next(["input"]).attr('id');
		
		var form = document.getElementById(form_id);//textareaObject
		var pos = getAreaRange(form);
		
		var val = form.value;
		var range = val.slice(pos.start, pos.end);
		var beforeNode = val.slice(0, pos.start);
		var afterNode = val.slice(pos.end);
		var insertNode;
		
		if (tag_close == undefined) tag_close = tag;
		
		if (range || pos.start != pos.end) {
			insertNode = '<' + tag + '>' + range + '</' + tag_close + '>';
			form.value = beforeNode + insertNode + afterNode;
		} else if (pos.start == pos.end) {
			insertNode = '<' + tag + '>' + '</' + tag_close + '>';
			form.value = beforeNode + insertNode + afterNode;
		}
	}
	
	function getAreaRange(obj) {
		var pos = new Object();
		
		if (isIE) {
			obj.focus();
			var range = document.selection.createRange();
			var clone = range.duplicate();
			
			clone.moveToElementText(obj);
			clone.setEndPoint( 'EndToEnd', range );
			
			pos.start = clone.text.length - range.text.length;
			pos.end = clone.text.length - range.text.length + range.text.length;
		} else if(window.getSelection()) {
			pos.start = obj.selectionStart;
			pos.end = obj.selectionEnd;
		}
		return pos;
		//alert(pos.start + "," + pos.end);
	}
	
	function qEsc(str) {
		if (str.search(/\"/) != -1) {
			str = str.replace(/\"/, '');
			str = qEsc(str);
		}
		return str;
	}
	
	var isIE = (navigator.appName.toLowerCase().indexOf('internet explorer')+1?1:0);
})(jQuery);