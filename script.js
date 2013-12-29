$(document).ready(function() {
	$('.hide_show').before($('#data_summary'));
	$('button').click(function() {
		//$('.table').addClass('hide');
		if ($(this).html() === '显示数据') {
			$(this).html('隐藏数据');
			$('.table').slideDown("slow");
		} else {
			$(this).html('显示数据');
			$('.table').slideUp("slow");
		}
	});
});