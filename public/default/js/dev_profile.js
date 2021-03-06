var error_flag = 0;
$(document).ready(function(){
	/*$.fn.fancyBox = function(params){
		params = $.extend({content: '.main-wrapper'}, params);
			var $t = $(this); //params.content
			$('body').append('<div class="transparent"></div>');
			$t.find('#main-wrapper').append('<a href="javascript:void(0)" class="close">X</a>');
			$t.addClass('light-box');
			$('a.close').live('click',function(){
					$t.hide();
					$t.removeClass('light-box');
					$('.transparent').remove();
					$(this).remove();
			})
		return this;
	};*/
	$("body").on({
		ajaxStart: function() { 
			$(this).addClass("loading"); 
		},
		ajaxStop: function() { 
			$(this).removeClass("loading"); 
		}    
	});
	
	$("#login_user").submit(function(ev) {
		ev.preventDefault();
	});
	
	$("#login_header").submit(function(ev) {
		error_flag = 0;
		ev.preventDefault();
	});
	
	$("#registration").submit(function(ev) {
		ev.preventDefault();
	});
	
	$("#form_about_us").submit(function(ev) {
		ev.preventDefault();
	});
	
	$("#change_pwd_form").submit(function(ev) {
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
	
	$("#login_header").validate({
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
		onkeyup: false,
		errorPlacement: function(error,element){
			if($("input[name=email_id]").val() != "") {
				if(error_flag == 0) {
					$("input[name=password]").val("");
					$("input[name=password]").attr("placeholder", error.html());
				}
			} else {
				element.attr("placeholder", error.html());
			}
			error_flag = 1;
			return false;
	    },
		submitHandler: function(form) {
			var str = $("#login_header").serialize();
			$.ajax({
				type : "POST",
				url : baseUrl+"/signin/loginheader",
				data : str,
				beforeSend : function() {
					/*$("#login_loading").show();
					$("#login_user").hide();*/
				},
				success : function(data) {
					//$("#login_loading").hide();
					$("#login_header").html(data);
					//$("#login_user").show();
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
	// Change Password
	$("#change_pwd_form").validate({
		rules : {
			old_pwd : {
				required : true
			},
			new_pwd : {
				required : true
			},
			cnf_pwd : {
				required : true,
				equalTo : "#new_pwd"
			}
		},
		messages : {
			old_pwd : {
				required : "Old password is required."
			},
			new_pwd : {
				required : "New password is required."
			},
			cnf_pwd : {
				required : "Confirm password is required.",
				equalTo : "Confirm password not matching."
			}
		},
		submitHandler: function(form) {
			var str = $("#change_pwd_form").serialize();
			$.ajax({
				type : "POST",
				url : baseUrl+"/signin/chgpwd",
				data : str,
				beforeSend : function() {
					$("#change_pwd_form").hide();
					$("#for_loading").show();
				},
				success : function(data) {
					$("#for_loading").hide();
					$("#change_pwd_form").html(data);
					$("#change_pwd_form").show();
					return false;
				}
			});
		}
	});
	// Forgot Password
	$("#forgot").validate({
		rules : {
			email_id : {
				required : true,
				email : true,
				minlength : 3,
				maxlength : 50
			}
		},
		messages : {
			email_id : {
				required : "Email id is required.",
				email : "Email id is invalid.",
				minlength : "Minimum length should be 3 characters",
				maxlength : "Maximum length allowed 50 characters"
			}
		},
		submitHandler: function(form) {
			var str = $("#forgot").serialize();
			$.ajax({
				type : "POST",
				url : baseUrl+"/signup/forgot",
				data : str,
				beforeSend : function() {
					$("#forgot").hide();
					$("#for_loading").show();
				},
				success : function(data) {
					$("#for_loading").hide();
					$("#forgot").html(data);
					$("#forgot").show();
					return false;
				}
			});
		}
	});
	// About_us Functionality
	$(".edit_aboutus").live("click", function(){
		cancel_all();
		$("#view_about_us").hide();
		$("#edit_about_us").show();
	});
	$("#form_about_us").validate({
		rules : {
			// about_us : {
				// required : true
			// }
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
				},
				success : function(data) {
					if(data == 1) {
						CreateSuccess("About me saved successfully.");
						if(about_us == ""){
							$("#view_about_us").html('<div class="add_about edit_aboutus" id="mem_abt_edit"> <a href="javascript:void(0)"></a>Add About</div>');
						} else {
							$("#view_about_us").text(about_us);
						}
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
			cancel_all();
			if($(".edit_experiance_bar")) $(".edit_experiance_bar").hide();
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/addeditexperiance",
				data : "type=edit&id="+id,
				beforeSend : function() {
					//$("#edit_experiance_"+id).hide();
					/*if(id != "new")
					$("#experiance_edit_loading_"+id).show();*/
				},
				success : function(data) {
					if(id != "new") {
						/*$("#experiance_edit_loading_"+id).hide();*/
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
			cancel_all();
			if($(".edit_experiance_bar")) $(".edit_experiance_bar").hide();
			$("#experiance_view_"+id).hide();
			$("#experiance_edit_"+id).show();
		}
	});
	$("#edit_skill_bar").live("click", function(){
		alert("fds");
	});
	// Skill set Functionality
	$("#edit_skill_new").live("click", function(){
		var user_skill = '<input type="text" id="user_skill" name="user_skill" />';
		var skill_version = '<input type="text" id="skill_version" name="skill_version" />';
		var last_used = '<select id="last_used"><option value="">Year</option>';
		for(var i = 2013; i > 1950; i--)
		last_used += '<option value="'+i+'">'+i+'</option>';
		last_used += '</select>';
		var experience_years = '<select id="experience_years"><option value="">Year(s)</option>';
		for(var i = 0; i < 31; i++)
		experience_years += '<option value="'+i+'">'+i+'</option>';
		experience_years += '</select>';
		var experience_months = '<select id="experience_months"><option value="">Month(s)</option>';
		for(var i = 0; i < 12; i++)
		experience_months += '<option value="'+i+'">'+i+'</option>';
		experience_months += '</select>';
		var actions = '<a href="javascript:void(0);" id="save_skill_set">Save</a><a href="javascript:void(0);" id="cancel_skill_set">Cancel</a>';
		if($(".skills_views table").length) {
			$(".skills_views table").append('<tr data-id="new"><td>'+user_skill+'</td><td>'+skill_version+'</td><td>'+last_used+'</td><td>'+experience_years+experience_months+'</td><td>'+actions+'</td></tr>');
		} else {
			$(".edit_skill_bar").hide();
			$(".skills_views").append('<table style="width:100%;"><tr><th>Skill</th><th>Version</th><th>Last Used</th><th>Experiance</th><th>&nbsp;</th></tr><tr data-id="new"><td>'+user_skill+'</td><td>'+skill_version+'</td><td>'+last_used+'</td><td>'+experience_years+experience_months+'</td><td>'+actions+'</td></tr></table>');
		}
	});
	$("#edit_skill_old").live("click", function(){
		var id = $(this).parents("tr").attr("data-id");
		var elm = $(this).parents("tr");
		if(id) {
			var user_skill = '<input type="text" id="user_skill" name="user_skill" value="'+elm.find("#user_skill").val()+'"/>';
			var skill_version = '<input type="text" id="skill_version" name="skill_version" value="'+elm.find("#skill_version").val()+'"/>';
			var last_used_val = elm.find("#last_used").val();
			var last_used = '<select id="last_used"><option value="">Year</option>';
			for(var i = 2013; i > 1950; i--)
				last_used += '<option value="'+i+'" '+((last_used_val == i) ? 'selected' : '')+'>'+i+'</option>';
			last_used += '</select>';
			var experience_years_val = elm.find("#experience_years").val();
			var experience_years = '<select id="experience_years"><option value="">Year(s)</option>';
			for(var i = 0; i < 31; i++)
				experience_years += '<option value="'+i+'" '+((experience_years_val == i) ? 'selected' : '')+'>'+i+'</option>';
			experience_years += '</select>';
			var experience_months_val = elm.find("#experience_months").val();
			var experience_months = '<select id="experience_months"><option value="">Month(s)</option>';
			for(var i = 0; i < 12; i++)
				experience_months += '<option value="'+i+'" '+((experience_months_val == i) ? 'selected' : '')+'>'+i+'</option>';
			experience_months += '</select>';
			var actions = '<a href="javascript:void(0);" id="save_skill_set">Save</a><a href="javascript:void(0);" id="cancel_skill_set">Cancel</a>';
			elm.html('<td>'+user_skill+'</td><td>'+skill_version+'</td><td>'+last_used+'</td><td>'+experience_years+experience_months+'</td><td>'+actions+'</td>');
		}
	});
	$("#delete_skill_set").live("click", function(){
		var elm = $(this).parents("tr");
		var id = elm.attr("data-id");
		if(elm && id) {
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/addeditskillset",
				data : "type=delete&id="+id,
				success : function(data) {
					if(data) {
						elm.remove();
						if(!$(".skills_views table tr td").length) {
							$(".skills_views table").remove();
							$(".edit_skill_bar").show();
						}
						CreateSuccess("Skill set deleted successfully.");
					} else {
						CreateError("Unknown server error!");
					}
					return false;
				}
			});
		}
	});
	$("#save_skill_set").live("click", function(){
		var id = $(this).parents("tr").attr("data-id");
		var elm = $(this).parents("tr");
		if(elm && id) {
			var user_skill = elm.find("#user_skill").val();
			var skill_version = elm.find("#skill_version").val();
			var last_used_val = elm.find("#last_used").val();
			var experience_years_val = elm.find("#experience_years").val();
			var experience_months_val = elm.find("#experience_months").val();
			var type = "edit";
			if(id == "new") {
				type = "new";
			}
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/addeditskillset",
				data : "type="+type+"&id="+id+"&user_skill="+user_skill+"&skill_version="+skill_version+"&last_used_val="+last_used_val+"&experience_years_val="+experience_years_val+"&experience_months_val="+experience_months_val,
				success : function(data) {
					if(id == "new") {
						if(data) {
							elm.remove();
							var html = '<td>'+user_skill+'<input type="hidden" id="user_skill" name="user_skill" value="'+user_skill+'" /></td><td>'+skill_version+'<input type="hidden" id="skill_version" name="skill_version" value="'+skill_version+'" /></td><td>'+last_used_val+'<input type="hidden" id="last_used" name="last_used" value="'+last_used_val+'" /></td><td>'+experience_years_val+' year(s) '+experience_months_val+' month(s)<input type="hidden" id="experience_years" name="experience_years" value="'+experience_years_val+'" /><input type="hidden" id="experience_months" name="experience_months" value="'+experience_months_val+'" /></td><td style="text-align:right"><a href="javascript:void(0);" id="edit_skill_old">Edit</a><a href="javascript:void(0);" id="delete_skill_set">Delete</a></td>';
							$(".skills_views table").append('<tr data-id="'+data+'">'+html+'</tr>');
							CreateSuccess("Skill set created successfully.");
						} else {
							CreateError("Unknown server error!");
						}
					} else {
						if(data) {
							var html = '<td>'+user_skill+'<input type="hidden" id="user_skill" name="user_skill" value="'+user_skill+'" /></td><td>'+skill_version+'<input type="hidden" id="skill_version" name="skill_version" value="'+skill_version+'" /></td><td>'+last_used_val+'<input type="hidden" id="last_used" name="last_used" value="'+last_used_val+'" /></td><td>'+experience_years_val+' year(s) '+experience_months_val+' month(s)<input type="hidden" id="experience_years" name="experience_years" value="'+experience_years_val+'" /><input type="hidden" id="experience_months" name="experience_months" value="'+experience_months_val+'" /></td><td style="text-align:right"><a href="javascript:void(0);" id="edit_skill_old">Edit</a><a href="javascript:void(0);" id="delete_skill_set">Delete</a></td>';
							elm.html(html);
							CreateSuccess("Skill set edited successfully.");
						} else {
							CreateError("Unknown server error!");
						}
					}
					return false;
				}
			});
		}
	});
	$("#edit_skill_old").live("click", function(){
		var id = $(this).parents("tr").attr("data-id");
		var elm = $(this).parents("tr");
		if(id) {
			var user_skill = '<input type="text" id="user_skill" name="user_skill" value="'+elm.find("#user_skill").val()+'"/>';
			var skill_version = '<input type="text" id="skill_version" name="skill_version" value="'+elm.find("#skill_version").val()+'"/>';
			var last_used_val = elm.find("#last_used").val();
			var last_used = '<select id="last_used"><option value="">Year</option>';
			for(var i = 2013; i > 1950; i--)
				last_used += '<option value="'+i+'" '+((last_used_val == i) ? 'selected' : '')+'>'+i+'</option>';
			last_used += '</select>';
			var experience_years_val = elm.find("#experience_years").val();
			var experience_years = '<select id="experience_years"><option value="">Year(s)</option>';
			for(var i = 0; i < 31; i++)
				experience_years += '<option value="'+i+'" '+((experience_years_val == i) ? 'selected' : '')+'>'+i+'</option>';
			experience_years += '</select>';
			var experience_months_val = elm.find("#experience_months").val();
			var experience_months = '<select id="experience_months"><option value="">Month(s)</option>';
			for(var i = 0; i < 12; i++)
				experience_months += '<option value="'+i+'" '+((experience_months_val == i) ? 'selected' : '')+'>'+i+'</option>';
			experience_months += '</select>';
			var actions = '<a href="javascript:void(0);" >Save</a><a href="javascript:void(0);" id="cancel_skill_set">Cancel</a>';
			elm.html('<td>'+user_skill+'</td><td>'+skill_version+'</td><td>'+last_used+'</td><td>'+experience_years+experience_months+'</td><td>'+actions+'</td>');
		}
	});
	$("#cancel_skill_set").live("click", function(){
		var elm = $(this).parents("tr");
		var typ = elm.attr("data-id");
		if(typ == "new") {
			elm.remove();
			if(!$(".skills_views table tr td").length) {
				$(".skills_views table").remove();
				$(".edit_skill_bar").show();
			}
		} else {
			var user_skill = elm.find("#user_skill").val();
			var skill_version = elm.find("#skill_version").val();
			var last_used_val = elm.find("#last_used").val();
			var experience_years_val = elm.find("#experience_years").val();
			var experience_months_val = elm.find("#experience_months").val();
			var html = '<td>'+user_skill+'<input type="hidden" id="user_skill" name="user_skill" value="'+user_skill+'" /></td><td>'+skill_version+'<input type="hidden" id="skill_version" name="skill_version" value="'+skill_version+'" /></td><td>'+last_used_val+'<input type="hidden" id="last_used" name="last_used" value="'+last_used_val+'" /></td><td>'+experience_years_val+' year(s) '+experience_months_val+' month(s)<input type="hidden" id="experience_years" name="experience_years" value="'+experience_years_val+'" /><input type="hidden" id="experience_months" name="experience_months" value="'+experience_months_val+'" /></td><td style="text-align:right"><a href="javascript:void(0);" id="edit_skill_old">Edit</a><a href="javascript:void(0);" id="delete_skill_set">Delete</a></td>';
			elm.html(html);
		}
	});
// Education Functionality
	$(".edit_education").live("click", function(){
		var id_str = $(this).attr('id');
		var id_array = id_str.split("_");
		var id = id_array[2];
		if($("#education_edit_"+id).html() == "") {
			cancel_all();
			if($(".edit_education_bar")) $(".edit_education_bar").hide();
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/addediteducation",
				data : "type=edit&id="+id,
				beforeSend : function() {
					/*if(id != "new")
					$("#education_edit_loading_"+id).show();*/
				},
				success : function(data) {
					if(id != "new") {
						/*$("#education_edit_loading_"+id).hide();*/
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
			cancel_all();
			if($(".edit_education_bar")) $(".edit_education_bar").hide();
			$("#education_view_"+id).hide();
			$("#education_edit_"+id).show();
		}
	});
// Personal Information
	$(".edit_personal").live("click", function(){
		cancel_all();
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
		cancel_all();
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
// Member Profile title icon
	$("#member_profile_title_icon").live("click", function(){
		cancel_all();
		$("#member_profile_title").hide();
		$("#member_profile_title_edit").show();
		return false;
	});
	$("#cancel_member_profile_title").live("click", function(){
		$("#member_profile_title_edit").hide();
		$("#member_profile_title").show();
		return false;
	});
	$("#save_member_profile_title").live("click", function(){
		$(".member_profile_title_edit").submit();
		return false;
	});
	$(".member_profile_title_edit").validate({
		submitHandler: function(form) {
			var str = $(".member_profile_title_edit").serialize();
			//$("#registration").html("Loading...");
			var input_data = postArray(".member_profile_title_edit");
			var fname = input_data.fname;
			var lname = input_data.lname;
			var timezone = $("#timezone option:selected").text();
			$.ajax({
				type : "POST",
				url : baseUrl+"/profile/editmembertitle",
				data : str,
				beforeSend : function() {
					
				},
				success : function(data) {
					if(data == 1) {
						CreateSuccess("Basic member details saved successfully.");
						$("#h2_name").html(fname+" "+lname);
						$("#timezone_dis").html(timezone);
						$("#member_profile_title_edit").hide();
						$("#member_profile_title").show();
					} else {
						$("#member_profile_title_edit").html(data);
					}
					return false;
				}
			});
		}
	});
	$("#avatar").live("change", function() {
		cancel_all();
		$("#upload_image").submit();
	});
	$("#delete_image").live("click", function(){
		$.ajax({
			type : "POST",
			url : baseUrl+"/profile/deleteimage",
			success : function(data) {
				CreateSuccess("Image deleted successfully.");
				location.reload();
			}
		});
	});
	$('#upload_image').on('submit', function(e) {
		e.preventDefault(); // <-- important
		// $(this).ajaxSubmit({
			// target: '#output'
		// });
		$('#upload_image').ajaxSubmit(function(responseText) { 
			//var out_arr = responseText.split("|");
			if(responseText == 0) {
				alert("Invalid file type.");
			} else {
				//$('#popup_content img').attr("src",baseUrl+"/public/uploads/"+out_arr[1]);
				//$('#cropbox').attr("src",baseUrl+"/public/uploads/"+out_arr[1]);
				$('#popup_content').html(responseText).show().fancyBox();
				//$("#popup_content").show().f
			}
		});
	});
	$(".present_working").live("click", function() {
		var id = $(this).attr("id");
		var id = id.replace("present_working_","");
		var check = ($(this).attr("checked")) ? true : false;
		if(check) {
			$("#to_month_"+id).hide();
			$("#to_year_"+id).hide();
		} else {
			$("#to_month_"+id).show();
			$("#to_year_"+id).show();
		}
	});
	$(".auto_count").live("keyup", function(){
		var str = $(this).val();
		var len = str.length;
		var min = 0;
		var max = 500;
		if(len <= 500) {
			$(this).parent().find(".info").find("span").html(max - len);
		} else {
			$(this).val(str.substring(min, max));
			$(this).parent().find(".info").find("span").html(min);
		}
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
function cancel_all(){
	$.each($('.common_cancel_btn'), function() {
		$(this).click();
	});
}
function CreateSuccess(val){
	var elem = $(".member_success_msg");
	elem.html(val);
	elem.slideDown("slow");
	setTimeout(function(){
		elem.slideUp("slow");
	},3000);
}
function CreateError(val){
	var elem = $(".member_error_msg");
	elem.html(val);
	elem.slideDown("slow");
	setTimeout(function(){
		elem.slideUp("slow");
	},3000);
}
function reloadExperiance() {
	$.ajax({
		type : "POST",
		url : baseUrl+"/profile/reloadexperianceview",
		success : function(data) {
			$(".all_experiances_view").html(data);
		}
	});
}
function reloadEducation() {
	$.ajax({
		type : "POST",
		url : baseUrl+"/profile/reloadeducationview",
		success : function(data) {
			$(".all_education_view").html(data);
		}
	});
}