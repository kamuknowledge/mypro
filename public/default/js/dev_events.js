/* Event Calendar Module JS */

/******** Ready function starts ********************/
$(document).ready(function(){
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
					$('#event_form').dialog({
						width:'800px',
						show: {
							effect: "blind",
							duration: 1000
						},
						hide: {
							effect: "explode",
							duration: 1000
						}
					});
					//alert('Create event functionality is in under preocess');
			},
			eventClick: function(start, end, allDay, jsEvent, view) {
					alert('Edit event functionality is in under preocess');
			},
			eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
					alert('Event Drop functionality is in under preocess');
			},
			eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
					alert('Event Resize functionality is in under preocess');
			},
			events: [
				{
					title: 'All Day Event',
					start: new Date(y, m, 1)
				},
				{
					title: 'Long Event',
					start: new Date(y, m, d-5),
					end: new Date(y, m, d-2)
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d-3, 16, 0),
					allDay: false
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d+4, 16, 0),
					allDay: false
				},
				{
					title: 'Meeting',
					start: new Date(y, m, d, 10, 30),
					allDay: false
				},
				{
					title: 'Lunch',
					start: new Date(y, m, d, 12, 0),
					end: new Date(y, m, d, 14, 0),
					allDay: false
				},
				{
					title: 'Birthday Party',
					start: new Date(y, m, d+1, 19, 0),
					end: new Date(y, m, d+1, 22, 30),
					allDay: false
				},
				{
					title: 'Click for Google',
					start: new Date(y, m, 28),
					end: new Date(y, m, 29),
					url: 'http://google.com/'
				}
			]
		});
		

		/************ loading datepicker and timepicker ************************/
		$('.datepicker').datepicker();
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
		
		$("#event_form_body").submit(function(){
			$("#event_form_body").validate();
			if($("#event_form_body").validate()){
				return true;
			} else {
				return false;
			}
		})

});
/******** Ready function ends ********************/

/**************** Close Dialog *******************/
function dialoge_close(){
	$('#event_form').dialog('close');
}
