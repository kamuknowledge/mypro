$(document).ready(function(){
	$("#login_user").submit(function(ev) {
		ev.preventDefault();
	});
	
	$("#registration").submit(function(ev) {
		ev.preventDefault();
	});
	
	$("#form_about_us").submit(function(ev) {
		ev.preventDefault();
	});
		
	$("#login_user").validate({
		rules : {
			email_id : {
				required : true,
				email : true
			},
			password : {
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
			}
		},
		submitHandler: function(form) {
			var str = $("#login_user").serialize();
			//$("#login_user").html("Loading...");
			$.ajax({
				type : "POST",
				url : baseUrl+"/signin/login",
				data : str,
				beforeSend : function() {
					$("#login_loading").show();
					$("#login_user").hide();
				},
				success : function(data) {
					$("#login_loading").hide();
					$("#login_user").html(data);
					$("#login_user").show();
					return false;
				}
			});
		}
	});
		
	$("#registration").validate({
		rules : {
			email_id : {
				required : true,
				email : true,
				minlength : 3,
				maxlength : 50
			},
			first_name : {
				required : true,
				minlength : 3,
				maxlength : 20
			},
			last_name : {
				required : true,
				minlength : 3,
				maxlength : 20
			},
			password : {
				required : true,
				minlength : 8,
				maxlength : 16
			},
			mobile : {
				required : true,
				number : true,
				minlength : 10,
				maxlength : 10
			},
			term_conditions : {
				required : true
			}
		},
		messages : {
			email_id : {
				required : "Email id is required.",
				email : "Email id is invalid.",
				minlength : "Minimum length should be 3 characters",
				maxlength : "Maximum length allowed 50 characters"
			},
			first_name : {
				required : "First Name is required.",
				minlength : "Minimum length should be 3 characters",
				maxlength : "Maximum length allowed 20 characters"
			},
			last_name : {
				required : "Last Name is required.",
				minlength : "Minimum length should be 3 characters",
				maxlength : "Maximum length allowed 20 characters"
			},
			password : {
				required : "Password is required.",
				minlength : "Minimum length should be 8 characters",
				maxlength : "Maximum length allowed 16 characters"
			},
			mobile : {
				required : "Mobile is required.",
				number : "Enter only numbers.",
				minlength : "Minimum length should be 10 characters",
				maxlength : "Maximum length allowed 10 characters"
			},
			term_conditions : {
				required : "Check the terms and conditions."
			}
		},
		submitHandler: function(form) {
			var str = $("#registration").serialize();
			//$("#registration").html("Loading...");
			$.ajax({
				type : "POST",
				url : baseUrl+"/signup/register",
				data : str,
				beforeSend : function() {
					$("#registration").hide();
					$("#reg_loading").show();
				},
				success : function(data) {
					$("#reg_loading").hide();
					$("#registration").html(data);
					$("#registration").show();
					return false;
				}
			});
		}
	});
	// About_us Functionality
	$(".edit_aboutus").live("click", function(){
		$("#view_about_us").hide();
		$("#edit_about_us").show();
	});
	$("#form_about_us").validate({
		rules : {
			about_us : {
				required : true
			}
		},
		submitHandler: function(form) {
			var str = $("#form_about_us").serialize();
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/updateAboutus",
				data : str,
				beforeSend : function() {
					$("#edit_about_us").hide();
					$("#loading_about_us").show();
				},
				success : function(data) {
					$("#loading_about_us").hide();
					//$("#view_about_us").html(data);
					$("#view_about_us").show();
					return false;
				}
			});
		}
	});
}) 