/* Store Module JS */

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

	
	/* Add to Cart Form Submit */
	$('#add_to_cart').click(function() {
	  $('#product_details_form').submit();
	});	
	
	
	$("#continue_shopping").click(function() {
	  //alert("Handler for .click() called.");
	  window.location = baseUrl+'/index';
	});
		
});



function removeItem(temp_cart_id){
	if(confirm('Are you sure, Do you want to delete?')){
		alert(temp_cart_id);
	}
}


