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
			var str = $("#form_about_us").serializeArray();
			var input_data = postArray("#form_about_us");
			var about_us = input_data.about_us;
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/editaboutus",
				data : str,
				beforeSend : function() {
					$("#edit_about_us").hide();
					$("#loading_about_us").show();
				},
				success : function(data) {
					$("#loading_about_us").hide();
					if(data == 1) {
						$("#view_about_us").text(about_us);
						$("#view_about_us").show();
					} else {
						$("#edit_about_us").html(data);
						$("#edit_about_us").show();
					}
					return false;
				}
			});
		}
	});
	// Experiance Functionality
	$(".edit_experiance").live("click", function(){
		var id_str = $(this).attr('id');
		var id_array = id_str.split("_");
		var id = id_array[2];
		if($("#experiance_edit_"+id).html() == "") {
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/addeditexperiance",
				data : "type=edit&id="+id,
				beforeSend : function() {
					//$("#edit_experiance_"+id).hide();
					if(id != "new")
					$("#experiance_edit_loading_"+id).show();
				},
				success : function(data) {
					if(id != "new") {
						$("#experiance_edit_loading_"+id).hide();
						$("#experiance_view_"+id).hide();
						$("#experiance_edit_"+id).html(data);
						$("#experiance_edit_"+id).show();
					} else {
						$("#experiance_edit_"+id).html(data);
						$("#experiance_edit_"+id).show();
					}
					return false;
				}
			});
		} else {
			$("#experiance_view_"+id).hide();
			$("#experiance_edit_"+id).show();
		}
	});
// Education Functionality
	$(".edit_education").live("click", function(){
		var id_str = $(this).attr('id');
		var id_array = id_str.split("_");
		var id = id_array[2];
		if($("#education_edit_"+id).html() == "") {
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/addediteducation",
				data : "type=edit&id="+id,
				beforeSend : function() {
					if(id != "new")
					$("#education_edit_loading_"+id).show();
				},
				success : function(data) {
					if(id != "new") {
						$("#education_edit_loading_"+id).hide();
						$("#education_view_"+id).hide();
						$("#education_edit_"+id).html(data);
						$("#education_edit_"+id).show();
					} else {
						$("#education_edit_"+id).html(data);
						$("#education_edit_"+id).show();
					}
					return false;
				}
			});
		} else {
			$("#education_view_"+id).hide();
			$("#education_edit_"+id).show();
		}
	});
// Personal Information
	$(".edit_personal").live("click", function(){
		$.ajax({
			type : "POST",
			url : baseUrl+"/profile/addeditpersonal",
			data : "type=editform",
			beforeSend : function() {
				//$("#personal_edit_loading").show();
			},
			success : function(data) {
				$("#personal_view").html(data);
				return false;
			}
		});
	});
// Addreess Information
	$(".edit_address").live("click", function(){
		$.ajax({
			type : "POST",
			url : baseUrl+"/profile/addeditaddress",
			data : "type=editform",
			beforeSend : function() {
				//$("#personal_edit_loading").show();
			},
			success : function(data) {
				$("#address_view").html(data);
				return false;
			}
		});
	});	
});
function submit_form(){
	$("#form_about_us").submit();
}
function cancel_form(){
	$("#edit_about_us").hide();
	$("#view_about_us").show();
}
function postArray(form){ 
	var data = {}; 
	form = $(form).serializeArray(); 
	for(var i in form) data[form[i].name] = form[i].value; 
	return data; 
}