(function($) {
	// jQuery plugin definition
	$.fn.customDrop = function(params){
		params = $.extend({containerID: 'pop-up', content: '#header',right:'0px',position:null}, params);
			var $t = $(this); //params.content
			var dynLft = $t.width();
			$t.click(function(){
				$t.parent().css('position','relative');
				if($('#'+params.containerID).length==0){
					$t.parent().append('<div id="'+params.containerID+'" class="share pop-up" style="right:'+params.right+'"><div class="container"><span class="arrow-up" style="left: 173px;"></span><div class="content">'+$(params.content).html()+'</div></div></div>')
				}else{
					if($('#'+params.containerID).is(':visible')){
						$('#'+params.containerID).hide();
					}else{
						$('#'+params.containerID).show();
					}
				}
				
			})
		return this;
	};
	$.fn.fancyBox = function(params){
		params = $.extend({content: '#register'}, params);
			var $t = $(this); //params.content
			$('body').append('<div class="transparent"></div>');
			$t.find('.containerbox').append('<a href="javascript:void(0)" class="close">X</a>');
			$t.addClass('light-box');
			$('a.close').live('click',function(){
					$t.hide();
					$t.removeClass('light-box');
					$('.transparent').remove();
					$(this).remove();
			})
		return this;
	};
})(jQuery);
$(document).ready(function(){
	/*$('#checkoutSteps ul li a').click(function(){
		$('#checkoutSteps ul li').removeClass('active');
		$(this).parent().addClass('active');
		 $('#checkoutSteps .container .cont').hide();
		 var id = $(this).attr('rel');
		 $(id).show();
	});*/
	
	$("#registration").submit(function(e) {
		e.preventDefault();
	});
		
	$("#registration").validate({
		rules : {
			email_id : {
				required : true,
				email : true
			},
			password : {
				required : true
			},
			mobile : {
				required : true,
				minlength : 10,
				maxlength : 15
			},
			term_conditions : {
				required : true
			}
		},
		messages : {
			email_id : {
				required : "Email id is required.",
				email : "Email id is invalid."
			},
			password : {
				required : "Password is required."
			},
			mobile : {
				required : "Mobile is required.",
				minlength : "Minimum length should be 10 characters",
				maxlength : "Maximum length allowed 15 characters"
			},
			term_conditions : {
				required : "Check the terms and conditions."
			}
		},
		submitHandler: function(form) {
			
			$.ajax({
				type : "POST",
				url : "http://localhost/mypro/signup/register",
				data : "first_name=first&last_name=last&email_id=email&mobile=7898&password=shasjd&gender=Male",
				success : function(data) {
					alert(data);
					return false;
				}
			});
		}
	});

	$('#signup').live('click',function(){
		$('#register_cont').show().fancyBox();
	});
	$('#share').customDrop({containerID:'share-popup',content:'#share-content',right:'-55px'});
	$('#myaccount').live('click',function(){
		if(!$('#myac-options').is(':visible')){
			$('#myac-options').slideDown('medium');
		}else{
			$('#myac-options').slideUp('medium');
		}
	})
	$('.category-widget .cat-header').click(function(){
		$this = $(this);
		$t = $(this).parent();
		$cont = $t.find('.cat-cont');
		if($cont.is(':visible')){
			$cont.slideUp(200,function(){
				$this.addClass('closed');
				$this.removeClass('opened');
			});	
		}else{
			$cont.slideDown(200,function(){
				$this.addClass('opened');
				$this.removeClass('closed');
			});
		}
	});
	/* Slider Scripts*/
	$('#slider ul li').live('click',function(){
		if(!$(this).hasClass('active')){
			$('#slider ul li').removeClass('active');
			$(this).addClass('active');
			var id =$('#slider ul li.active').attr('rel');
			$('#slider img.show').fadeOut('fast',function(){
				$(this).removeClass('show');
			})
			$(id).css({position:'absolute',top:'0px', left:'0px'})
			$(id).fadeIn('fast',function(){
				$(this).addClass('show');
				$(this).css('position','')
			})
		}
	})
	var i = setInterval(function(){
		var ele = $('#slider ul li.active');
		if(ele.is(':last-child')){
			$('#slider ul li:first').trigger('click');
		}else{
			ele.next('li').trigger('click');
		}
	},2000);	
}) 