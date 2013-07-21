$(document).ready(function(){

	
	$("#product_view_more").live('click',function() {
	
		var ID=$(".more_box:last").attr("id");
		var cid=$("#cid").val();
		//alert(ID);
		//alert("Handler for .click() called.");
		
			$.ajax({
				type : "POST",
				url : baseUrl+"/products/listajax/id/"+cid+"/start/"+ID,				
				beforeSend : function() {
					$("#product_view_more_loading").show();
					$("#product_view_more").hide();
				},
				success : function(data) {					
					$("#"+ID).html(data);					
					return false;
				}
			});	
	});		
		
});