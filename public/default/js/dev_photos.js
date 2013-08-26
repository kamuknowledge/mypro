/* Videos Module JS */
/* Add ne wphoto category */
$(document).ready(function(){
	$('#add_photocategory').live('click',function(){
		$('#add_category_form').show().fancyBox();
	});
	
// Search video categorys,
   search_category = function(){
  	var search = $('#search_word').val(); 
		$.post(baseUrl + '/photos/searchcategories', { search_word: search },
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
		$.post(baseUrl + '/photos/searchphoto', { cat_id: select_val,search_word: search_words },
		   function(msg){
			$('#photolists').html(msg);
	   }, "html");
   
   } 

//Add new video 
$('#addphoto').live('click',function(){
	$('#add_photo_form').show().fancyBox();
	});   


// Search videos ,
   search_photo = function(){
  	var search = $('#searchphoto').val(); 
  	var catId = ''; 
		$.post(baseUrl + '/photos/searchphoto', { search_word: search,cat_id:catId },
		   function(msg){
			$('#photolists').html(msg);
		   }, "html");
   
   }	
 
// Edit videos
   edit_video = function(videoid){
   //alert(videoid);
  		$.post(baseUrl + '/videos/editvideo', { video_id: videoid },
		   function(msg){
		  // alert(msg);
			//$.fancyBox.showActivity(msg);
			$('#editvideo_form').show().fancyBox(msg);
			//$.fancyBox(msg);
			//$('#photolists').html(msg);
		   }, "html");
		   
		   
   
   }

//View more video


	$("#video_view_more").live('click',function() {
	
		var ID=$(".more_box:last").attr("id");
		$('#'+ID).removeClass();
		var cid=$("#cid").val();
		//alert(ID);
		//alert("Handler for .click() called.");
		
			$.ajax({
				type : "POST",
				url : baseUrl+"/photos/listajax/cat_id/"+cid+"/start/"+ID,				
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
