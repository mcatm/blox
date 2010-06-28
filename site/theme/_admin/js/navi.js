//navi
$(function() {
	$('#navi ul.menu li.section').hover(function(e) {
		$(this).children('.sub').show();
	}, function() {
		$(this).children('.sub').hide();
	});
	
	$('a#trigger_tool_filter').click(function(e) {
		$('#target_tool_filter').slideToggle({
			duration: 200, 
			easing: 'swing'});
	});
	
	$('#btn-tool-search').click(function() {
		$('#tool-profile').hide();
		$('#tool-search').slideToggle({
			duration: 200, 
			easing: 'swing'});
	});
	
	$('#trigger-tool-profile').click(function() {
		$('#tool-search').hide();
		$('#tool-profile').slideToggle({
			duration: 200, 
			easing: 'swing'});
	});
	
	$('#tool-search-query').keydown(function(e) {
		if (e.keyCode === 13) {
			$.ajax({
				type:'post',
				url:base_url + 'request/get/post/0/',
				datatype:'html',
				data:{
					q: $(this).val(),
					type: 'html',
					tpl: '_request/search.head',
					theme: '_admin'
				},
				success:function(msg) {
					//alert(typeof msg);
					//var html;
					/*$.each(msg.post, function(){
						$('#tool-search-result').html('YYY');
					});
					for (i in post) {
						$('#tool-search-result').html(i);
					}*/
					$('#tool-search-result').html(msg);
				}
			});
		}
	});
});