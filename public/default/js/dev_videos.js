/* Videos Module JS */
$(document).ready(function(){
	$('#add_category').live('click',function(){
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
		$.post(baseUrl + '/videos/searchcategories', { search_word: search },
		   function(msg){
			$('#videos_list').html(msg);
		   }, "html");
   
   }  
// video preview in videos list
 	video_preview = function(videopath,videoid){
	   $('#myframe').attr('src', "http://www.youtube.com/embed/"+videopath)
		$('#preview_video').show().fancyBox();
	}
	
// video search by category
// Search video categorys,
   select_category = function(select_val){
     var search_words = '';
  	//var search = $('#search_word').val(); 
		$.post(baseUrl + '/videos/searchvideo', { cat_id: select_val,search_word: search_words },
		   function(msg){
			$('#videolists').html(msg);
	   }, "html");
   
   } 

//Add new video 
$('#addvideo').live('click',function(){
	$('#add_video_form').show().fancyBox();
	});   


// Search videos ,
   search_video = function(){
  	var search = $('#searchvideo').val(); 
  	var catId = ''; 
		$.post(baseUrl + '/videos/searchvideo', { search_word: search,cat_id:catId },
		   function(msg){
			$('#videolists').html(msg);
		   }, "html");
   
   }	
 
// Edit videos
   edit_video = function(videoid){
   alert(videoid);
  		$.post(baseUrl + '/videos/editvideo', { video_id: videoid },
		   function(msg){
			$('#editvideo_form').show().fancyBox();
			//$('#videolists').html(msg);
		   }, "html");
   
   }
//View more video


	$("#video_view_more").live('click',function() {
	
		var ID=$(".more_box:last").attr("id");
		var cid=$("#cid").val();
		//alert(ID);
		//alert("Handler for .click() called.");
		
			$.ajax({
				type : "POST",
				url : baseUrl+"/videos/listajax/cat_id/"+cid+"/start/"+ID,				
				beforeSend : function() {
					$("#video_view_more_loading").show();
					$("#video_view_more").hide();
				},
				success : function(data) {					
					$("#"+ID).html(data);					
					return false;
				}
			});	
	});
	   

});
