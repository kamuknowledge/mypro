/* Videos Module JS */
$(document).ready(function(){
	$('#add_category').live('click',function(){
		$.ajax({
			url: "addcategory",
			success: function(data) {
				$(".fancybox").fancybox();
			}
		});
		//alert('venu');
		//$('#add_category').show().fancyBox();
	});
});