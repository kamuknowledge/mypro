/* Videos Module JS */
$(document).ready(function(){
	$('#add_photocategory').live('click',function(){
		/*$.ajax({
			url: "addcategory",
			success: function(data) {
				$('#add_category_form').fancyBox();
				//$(".fancybox").fancyBox(data);
				//$('#add_category_form').html(data);
			}
		});*/
		//alert('venu');
		$('#add_category_form').show().fancyBox();
	});
	
// Search video categorys,
   search_category = function(){
  	var search = $('#search_word').val(); 
	/*alert(search);	
		$.ajax()({
			type: "POST",
			url: baseUrl + '/videos/searchcategories',
			data: "search_key="+search,
			success: function(msg){
			 alert(msg);
			
			}
		});*/
		$.post(baseUrl + '/photos/searchcategories', { search_word: search },
		   function(msg){
			// alert(msg);
			 $('#videos_list').html(msg);
		   }, "html");
   
   }   
});
