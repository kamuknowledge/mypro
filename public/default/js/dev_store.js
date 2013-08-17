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
	
	
	/*
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
	*/
	
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
		//alert(temp_cart_id);
		
			$.ajax({
				type : "POST",
				url : baseUrl+"/products/removeiteam/temp_cart_id/"+temp_cart_id,				
				beforeSend : function() {
					$("#ajax_loading").show();
					$("#cart-table").hide();
				},
				success : function(data) {
					//alert(data);
					$("#cart-table").show();
					$("#cart-table").html(data);
					$("#ajax_loading").hide();
					return false;
				}
			});
	}
}




function changeqty(value,product_id){
	//alert(value);
	//alert(product_id);
	
	if($.trim(value)=='chnage'){
			if(document.getElementById('product_quantity_field_'+product_id)){
				document.getElementById('product_quantity_field_'+product_id).style.display='block';
			}
			if(document.getElementById('product_quantity_field_'+product_id)){
				document.getElementById('product_quantity_field_'+product_id).style.visibility='visible';
			}
			if(document.getElementById('product_quantity_show_'+product_id)){
				document.getElementById('product_quantity_show_'+product_id).style.display='none';
			}
			if(document.getElementById('product_quantity_show_'+product_id)){
				document.getElementById('product_quantity_show_'+product_id).style.visibility='hidden';
			}
		}else{
	
			if(document.getElementById('product_quantity_'+product_id))
				{
					var product_qty=document.getElementById('product_quantity_'+product_id).value;
				}
				
				$.ajax({
					type : "POST",
					url : baseUrl+"/products/viewcartupdate/temp_cart_id/"+product_id+"/product_qty/"+product_qty,				
					beforeSend : function() {
						$("#ajax_loading").show();
						$("#cart-table").hide();
					},
					success : function(data) {
						//alert(data);
						$("#cart-table").show();
						$("#cart-table").html(data);
						$("#ajax_loading").hide();
						return false;
					}
				});
		}
}


