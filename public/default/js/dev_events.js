/* Event Calendar Module JS */

/******** Ready function starts ********************/
$(document).ready(function(){
	
	$("#event_form_body").submit(function(e){
		$('#event_form_body').validate({errorElement: 'span', errorPlacement: function(error, element) {
            }});
        if ($('#event_form_body').valid())
        {
			e.preventDefault();
            $.ajax({
                type: 'POST',
                url: baseUrl + '/events/process',
                data: $('form#event_form_body').serializeArray(),
                dataType: 'json',
                success: function(data) {
					if(data.error){
						$(".error_div").show();
						$(".error_div").html(data.error);
						setTimeout(function(){
							$(".error_div").html();
							$(".error_div").hide();
						},2000);
					} else {
						$('#event_form').hide('slide', {direction: 'left'}, 1000);
						$('.calendar').show('slide', {direction: 'left'}, 1000);
						$(".success_message").show();
						$(".success_message").html(data.success);
						$('#event_calendar').fullCalendar('refetchEvents');
						setTimeout(function(){
							$(".success_message").html();
							$(".success_message").hide();
						},2000);
					}
					
					
				}
			});
		}
		else{
			return false;
		}
		});
		
/******** loading a fullcalendar into a calendar div ********************/
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$('#event_calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			selectable: true,
			selectHelper: true,
			editable: true,
			select: function(start, end, allDay, jsEvent, view) {
				//console.log(allDay);
				var start_date = convert(start);
				start_date = start_date.split(' ');
				end_date = convert(end);
                end_date = end_date.split(' ');
				
				var ys1 = start_date[0].substring(0, 4);
                var ms1 = start_date[0].substring(5, 7);
                var ds1 = start_date[0].substring(8, 10);
				
				var ye1 = end_date[0].substring(0, 4);
                var me1 = end_date[0].substring(5, 7);
                var de1 = end_date[0].substring(8, 10);

                var stDate = new Date(ys1, ms1 - 1, ds1);
				
				var enDate = new Date(ye1, me1 - 1, de1);

                var today = new Date(y, m, d);
				
				if (today > stDate)
                {
                    alert("You can not create a backdate event.");
					//dialoge_close();
					$('#event_calendar').fullCalendar('unselect');
					return false;
                } else {
					$('.calendar').hide('slide', {direction: 'left'}, 1000);
					$('#event_form').show('slide', {direction: 'left'}, 1000);
					if(allDay==true){
						$('#allday').attr('checked',true);
						$('#event_start_time').attr('disabled',true);
						$('#event_end_time').attr('disabled',true);
					} else {
						$('#allday').attr('checked',false);
						$('#event_start_time').attr('disabled',false);
						$('#event_end_time').attr('disabled',false);
					}
					$("#event_id").val('');
					$("#event_form_body input[type=text],textarea").val('');
					
					
					
					
					$("#event_start_date").datepicker('setDate', start);
					$("#event_end_date").datepicker('setDate', end);
					$('#event_start_time').timepicker('setTime', start);
					$('#event_end_time').timepicker('setTime', end);
				}
				
				
				//$("#event_start_date").val(start_date[0]);
				//$("#event_end_date").val(end_date[0]);
				
				
               

                /*var strt = dateFormat(start[0]);
                var endt = dateFormat(end[0]);
                var strtTime = start_obj_date.toLocaleTimeString().toLowerCase().replace(/:\d\d ([ap]m) .+$/, ' $1');
                var endTime = end_obj_date.toLocaleTimeString().toLowerCase().replace(/:\d\d ([ap]m) .+$/, ' $1');*/
               
					
					//alert('Create event functionality is in under preocess');
			},
			eventClick: function(event, jsEvent, view) {
				console.log(event.end_date);
				$('.calendar').hide('slide', {direction: 'left'}, 1000);
				$('#event_form').show('slide', {direction: 'left'}, 1000);
				
				$('.header_events h2').html('Edit Event');
				$('#event_name').val(event.title);
				$('#event_venue').val(event.event_location);
				$('#event_address').val(event.event_address);
				$('#event_type').val(event.event_type);
				$("#event_start_date").datepicker('setDate', new Date(event.start));
				$("#event_end_date").datepicker('setDate', new Date(event.end_date));
				
				if(event.allDay==true){
					$('#allday').attr('checked','checked');
					$('#event_start_time').attr('disabled',true);
					$('#event_end_time').attr('disabled',true);
					$('#event_start_time').timepicker();
					$('#event_end_time').timepicker();
				} else {
					$('#event_start_time').attr('disabled',false);
					$('#event_end_time').attr('disabled',false);
					$('#event_start_time').timepicker('setTime', new Date(event.start));
					$('#event_end_time').timepicker('setTime', new Date(event.end_date));
				}
				$("#event_id").val(event.id);
				$('#event_description').val(event.description);
			},
			eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
					if (event.end == null){
						end = start;
					}
					var start = convert(event.start);
					var end = convert(event.end);
					//alert(start +' : ' + end + ' : ' + allDay );
					
					start1 = start.split(' ');
					var y1 = start1[0].substring(0, 4);
					var m1 = start1[0].substring(5, 7);
					var d1 = start1[0].substring(8, 10);

					var today = new Date();
					if (today > event.start)
					{
						alert('Cannot move an event to past dates.');
						$('#event_calendar').fullCalendar('refetchEvents');
					} else {
						var allDay = (allDay == true) ? 1 : 0;
						$.post(baseUrl + '/events/drop', {start: start, end: end, id: event.id, allday: allDay}, function(data) {
							
						});
					}
			},
			eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
					var end_date = '';
					if (event.end == null) {
						end_date = convert(event.start);
					} else {
						end_date = convert(event.end);
					}
					//alert(end_date);
					if (confirm(' Event end time changed. Is this okay?')) {

						$.ajax({
							type: 'POST',
							url: baseUrl + '/events/resize',
							data: 'endDate=' + end_date + '&eventId=' + event.id ,
							success: function(data) {
								
							}
						});

					} else {
						self.calendar.calendar.refetchEvents();
					}
			},
			events: function(start, end, callback) {
				 var startDate = start.getFullYear() + '-' + (parseInt(start.getMonth()) + 1) + '-' + start.getDate() + ' ' + start.getHours() + ':' + start.getMinutes() + ':' + start.getSeconds();
				 var endDate = end.getFullYear() + '-' + (parseInt(end.getMonth()) + 1) + '-' + end.getDate() + ' ' + end.getHours() + ':' + end.getMinutes() + ':' + end.getSeconds();
				 $.ajax({
					url: baseUrl + '/events/load',
					type: 'POST',
					data: {
						start: startDate,
						end: endDate
					},
					dataType: 'json',
					success: function(data) {
						var events = data;
						//console.log(events);
						$('#event_calendar').fullCalendar('removeEvents');
						if (events.length <= 0) {
							return false;
						} else {
							for (var i = 0; i < events.length; i++) {
								$('#event_calendar').fullCalendar('renderEvent', events[i], true);
							}
						}
					}
				});
			}
		});
		

		/************ loading datepicker and timepicker ************************/
		 $('#event_start_date').datepicker({
			changeMonth: false,
			changeYear: false,
			minDate: 0,
			dateFormat: 'mm/dd/yy',
			onSelect: function(selectedDate) {
				$("#event_end_date").datepicker("option", "minDate", selectedDate);
			}
		});
		
		 $('#event_end_date').datepicker({
                changeMonth: false,
                changeYear: false,
                minDate: 0,
                dateFormat: 'mm/dd/yy',
                onSelect: function(selectedDate) {
                    $("#event_start_date").datepicker("option", "maxDate", selectedDate);
                    
                }
            });
			
			$('#event_start_time').timepicker();
			$('#event_end_time').timepicker();
		
		$("#allday").click(function(){
				var chkStatus = $(this).attr("checked");
				if(chkStatus=="checked"){
					$('.time').attr("disabled","disabled");
				} else {
					$('.time').removeAttr("disabled");
				}
		});
		
		

});
/******** Ready function ends ********************/

/**************** Close Dialog *******************/
function dialoge_close(){
	$('#event_form').hide('slide', {direction: 'left'}, 1000);
	$('.calendar').show('slide', {direction: 'left'}, 1000);
}

function convert(str) {
	var date = new Date(str),
	mnth = ('0' + (date.getMonth()+1)).slice(-2),
	day  = ('0' + date.getDate()).slice(-2);
	fdate = [ date.getFullYear(), mnth, day].join('/');

	hours = ('0' + date.getHours()).slice(-2);
	mints = ('0' + date.getMinutes()).slice(-2);
	ftime = [ hours, mints].join(':');

	return [fdate, ftime].join(' ');

}
//function for date formating
function dateFormat(date1)
{
	date1 = new Date(date1);
	var month = date1.getMonth();
	var day = date1.getDate();
	var year = date1.getFullYear();
	var months = ['January', 'February', 'March', 'April','May','June','July','August','September','October','November','December'];
	var dat = months[month] + ' ' + day + ', ' + year;
	return dat;
}
function formatAMPM(date) {
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var ampm = hours >= 12 ? 'pm' : 'am';
	hours = hours % 12;
	hours = hours ? hours : 12; // the hour '0' should be '12'
	minutes = minutes < 10 ? '0'+minutes : minutes;
	var strTime = hours + ':' + minutes + ' ' + ampm;
	return strTime;
}

function getDaysBetweenDates(d0, d1) {

	  var msPerDay = 8.64e7;

	  // Copy dates so don't mess them up
	  var x0 = new Date(d0);
	  var x1 = new Date(d1);

	  // Set to noon - avoid DST errors
	  x0.setHours(12,0,0);
	  x1.setHours(12,0,0);

	  // Round to remove daylight saving errors
	  return Math.round( (x1 - x0) / msPerDay );
}


