/* Event Calendar Module JS */
var clicking = false, mainevent,
        clickedElement, self = {}, appointmentBook = [];

$(document).ready(function() {
   /* Event form processing js code*/
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
						$('#calendar').fullCalendar('refetchEvents');
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
		
		$('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        selectable: true,
        selectHelper: true,
        editable: true,
        droppable: true,
		 drop: function(date, allDay) { // this function is called when something is dropped
            
            var originalEventObject = $(this).data('eventObject');
            var copiedEventObject = $.extend({}, originalEventObject);
            var realUpdateStartDate = date;
            if (copiedEventObject.end != 'null')
            {
                var _dateDiff = getDaysBetweenDates(convert(copiedEventObject.start), convert(copiedEventObject.end));
                var movedDate = new Date(convert(date));
                var realUpdateEndDate = movedDate.setDate((movedDate.getDate() + _dateDiff));
                realUpdateEndDate = new Date(realUpdateEndDate);
            }
            else
            {
                realUpdateEndDate = convert(realUpdateStartDate);
            }
           
            var start = convert(realUpdateStartDate);
            var end = convert(realUpdateEndDate);
            var start_old = convert(copiedEventObject.start);
            var start_old_array = start_old.split(' ');
            if (start_old_array[1] != '00:00')
            {
                start = start.replace('00:00', start_old_array[1]);
            }
            else
            {
                start = start;
            }
            if (copiedEventObject.end != 'null')
            {
                var end_old = convert(copiedEventObject.end);
                var end_old_array = end_old.split(' ');
                if (end_old_array[1] != '00:00')
                {
                    end = end.replace('00:00', end_old_array[1]);
                }
                else
                {
                    end = end;
                }
            }
            else
            {
                end = end;
            }

            //alert(start +' : ' + end + ' : ' + allDay );
            $('#form_bubble').hide();
            start1 = start.split(' ');
            var y1 = start1[0].substring(0, 4);
            var m1 = start1[0].substring(5, 7);
            var d1 = start1[0].substring(8, 10);


            var today = new Date();
            if (today > start && (copiedEventObject.type == 'event' || copiedEventObject.type == 'appointments'))
            {
                alert('Cannot move Events to past dates');
                self.calendar.calendar.refetchEvents();
            } else {
                $.post(baseUrl + 'calendar/changeDate', {start: start, end: end, id: copiedEventObject.unique_id, etype: copiedEventObject.type, allday: copiedEventObject.allDay}, function(data) {
                });
            }

        },
        eventDragStart: function(event, jsEvent, ui, view) {
            dragged = [ui.helper[0], event];
        },
        viewDisplay: function(view) {
            var parentDiv = $('.fc-agenda-slots:visible').parent();
            var timeline = parentDiv.children('.timeline');
            if (timeline.length == 0) { //if timeline isn't there, add it


                timeline = $('<hr style=\'z-index:10\'>').addClass('timeline');
                parentDiv.prepend(timeline);
            }
            //console.log(appointmentBook);
            var curTime = new Date();
            var curCalView = view;
            if (curCalView.visStart < curTime && curCalView.visEnd > curTime) {
                timeline.show();
            } else {


                timeline.hide();
            }

            var curSeconds = (curTime.getHours() * 60 * 60) + (curTime.getMinutes() * 60) + curTime.getSeconds();
            var percentOfDay = curSeconds / 86400; //24 * 60 * 60 = 86400, # of seconds in a day
            var topLoc = Math.floor(parentDiv.height() * percentOfDay);

            timeline.css('top', topLoc + 'px');

            if (curCalView.name == 'agendaWeek' || curCalView.name == 'fourDay') { //week view, don't want the timeline to go the whole way across
                var dayCol = $('.fc-today:visible');
                if (dayCol.length > 0) {
                    var dayColPosition = dayCol.position();
                    var left = dayColPosition.left + 1;
                    var width = dayCol.width();
                    timeline.css({
                        left: left + 'px',
                        width: width + 'px'
                    });
                }
            }
        },
        
        eventAfterRender: function(event, element, view) {
            if (view.name == 'month')
                doEventsRangeCount(event, view);
        },
        eventAfterAllRender: function(view) {
            self.calendar = this;
            if (view.name == 'month') {
                enableEvents();
                getviewMore(3, this, view);
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
						$('#calendar').fullCalendar('removeEvents');
						if (events.length <= 0) {
							return false;
						} else {
							for (var i = 0; i < events.length; i++) {
								$('#calendar').fullCalendar('renderEvent', events[i], true);
							}
						}
					}
				});
			},
			select: function(start, end, allDay, jsEvent, view) {
          
            var clickedElement = jsEvent.target;
            var offsetData = $(clickedElement).position();
            var targ = jsEvent.target;
			//console.log(targ);
            var elem = $.trim($(targ).data("show"));
			if (elem == 'view_more') {
            } else {
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
					$('#calendar').fullCalendar('unselect');
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
            }

        },
		eventClick: function(event, jsEvent, view) {
				
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
						$('#calendar').fullCalendar('refetchEvents');
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

function deleterenderEvent(event_id) {
    $('#form_bubble').remove();
    self.calendar.calendar.removeEvents(event_id);
}

function daySegSetTops(segs, rowTops) { // also triggers eventAfterRender
    var i;
    var segCnt = segs.length;
    var seg;
    var element;
    var event;

    for (i = 0; i < segCnt; i++) {
        seg = segs[i];
        element = seg;
        if ($(element[0]).hasClass('event-1950')) {
        }
        if (element) {
            $(element[0]).css('top', rowTops[seg.row] + (seg.top || 0) + 'px');
            event = seg.event;
        }

    }
}

function getRowTops(rowDivs) {
    var i;
    var rowCnt = rowDivs.length;
    var tops = [];
    var off;
    for (i = 0; i < rowCnt; i++) {
        off = $(rowDivs[i]).offset();


    }
    return tops;
}

function getRowDivs() {
    var i;
    var rowCnt = $('.fc-week').length;
    var rowDivs = [];
    for (i = 0; i < rowCnt; i++) {
        rowDivs[i] = $('#calendar tr:eq(' + i + ') td')
                .find('div.fc-day-content > div'); // optimal selector?
    }
    return rowDivs;
}

function doEventsRangeCount(event, calInstance) {

    var eventStart = event._start,
            eventEnd = event._end || event._start,
            dateRange = expandDateRange(eventStart, eventEnd),
            eventElement = event.element;


    $(dateRange).each(function(i) {

        var td = getCellFromDate(dateRange[i], calInstance), currentCount;
        if (typeof td != 'undefined') {
            if (td.data() == null || typeof td.data().appointments === 'undefined') {
                td.data().appointments = [event];
                currentCount = (td.data('apptCount') || 0) + 1;
                td.data('apptCount', currentCount);

            } else {
                var appointmentsExist = td.data().appointments;
                var count = 0;
                $.each(appointmentsExist, function(i, val) {
                    if (val.id == event.id) {
                        count = 1;
                    }
                });
                if (count == 0) {
                    currentCount = (td.data('apptCount') || 0) + 1;
                    td.data('apptCount', currentCount);
                    td.data().appointments.push(event);
                }
            }
        }

    });
}

function expandDateRange(start, end) {
    view1 = start;

    var value = new Date(start.getFullYear(), start.getMonth(), start.getDate()),
            values = [];

    end = new Date(end.getFullYear(), end.getMonth(), end.getDate());

    if (value > end)
        throw "InvalidRange";

    while (value <= end) {
        values.push(value);
        var value1 = new Date(value.getFullYear(), value.getMonth(), value.getDate());
        value = new Date(value1.setDate(value1.getDate() + 1));
    }
    return values;
}

function getCellFromDate(thisDate, calInstance) {

    calInstance = $('#calendar');
    var start = calInstance.fullCalendar('getView').visStart,
            end = calInstance.fullCalendar('getView').visEnd,
            td;

    var mon = (parseInt(thisDate.getMonth()) + 1);
    if ((parseInt(thisDate.getMonth()) + 1) <= 9) {
        mon = '0' + (parseInt(thisDate.getMonth()) + 1);
    }

    var dayr = thisDate.getDate();
    if (thisDate.getDate() <= 9) {
        dayr = '0' + thisDate.getDate();
    }
    // thisDate = Date.parse(thisDate);
    var dataDate = thisDate.getFullYear() + '-' + mon + '-' + dayr;


    if (thisDate >= start && thisDate < end) {

        td = $('.fc-day-number').filter(function() {
            return $(this).text() === $.fullCalendar.formatDate(thisDate, 'd')
        }).closest('td');



        if (thisDate < start) { //date is in last month
            td = td.filter(':first');
        } else if (thisDate >= end) {  //date is in next month
            td = td.filter(':last');
        } else { //date is in this month
            td = td.filter(function() {
                return $(this).hasClass('fc-other-month') === false;
            });
        }
        td = $('.fc-day[data-date=' + dataDate + ']');
    }

    return td;
}

function resetEventsRangeCounts() {

    $('.fc-widget-content').each(function(i) {
        $(this).find('.events-view-more').remove();
        $.removeData(this, "apptCount");
        $.removeData(this, "appointments");
        $.removeData(this, "startEvents");
        $.removeData(this, "existEvents");
    });
}

function getviewMore(maxLimit, cal, view, nocall) {
    var tds = $('.fc-widget-content');
    $('#form_bubble').hide();
    var eventtomove = '';
    self.maxEvents = maxLimit;
    $('.count_to_show').remove();
    var clientEvents = self.calendar.calendar.clientEvents();
    resetEventsRangeCounts();
    $(tds).each(function(i, val) {
        //console.log($(val).data());
    });
    $(clientEvents).each(function(i, val) {
        doEventsRangeCount(val, '');
    });

    $(tds).each(function(i, val) {

        var apptCount = $(val).data().apptCount;
        var appointments = $(val).data().appointments;
        var countToShow = 0;

        $(appointments).each(function(i, ev) {
            if ($('.' + ev.className).length == 0) {
                countToShow++;
            }
        });

        if (countToShow) {
            var viewmore = 0;
            if (typeof $(val).data('viewmore') != 'undefined')
                viewmore = 1;
            if ($(val).find('div.count_to_show').length) {
                $(val).find('div.count_to_show').remove();
            }

            if (viewmore) {
                $(val).find(' > div')
                        .append('<div class="count_to_show" style="float: left"><a href="#" data-show="view_more">+' + countToShow + 'More</a></div>');
                $(val).data('viewmore', 1);
            } else {
                $(val).data('viewmore', 1);

                $(val).find(' > div')
                        .append('<div class="count_to_show" style="float: left;"><a href="#" data-show="view_more">+' + countToShow + 'More</a></div>')
                        .click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (e.target.nodeName === 'A') {
                        
						generateHtml($(val), e, view);
                    }
                });
            }
        }

    });
}

function generateHtml(element, event, view) {
    var appointments = element.data().appointments;
    var cellDate = element.data().date;
    var formatedDate = self.calendar.calendar.formatDates(new Date(cellDate), null, 'd MMM');
    var iconImage = "";
    //console.log(appointments);
    var positions = element.position();
	var htmlData = '<div><span class="moretitle">' + formatedDate + '</span><a href="#" style="float: right;" class="bubble_close">X</a><ul style="clear: both;cursor: pointer;">';
    $(appointments).each(function(i, val) {
        
        var str = val.title;
        if (val.title.length > 10) {
            str = val.title.substr(0, 10);
            str += '..';
        }
        var classDrag = '';
        if (appointments[i].type == "event")
            classDrag = "event-drag";
        htmlData += '<li class="' + classDrag + '" style="background-color : ' + appointments[i].color + '" data-apptcount=' + i + ' data-apptid=' + appointments[i].id + '>'  + str + '</li>';
    });
    htmlData += '</ul></div>';
    if ($('#form_bubble').length > 0) {
        $('#form_bubble').html(htmlData);
    } else {
        $('#form_bubble').remove();
        $('#calendar').append('<div id ="form_bubble">' + htmlData + '</div>');
    }

    $('#form_bubble li').each(function(i, val) {
        var apptId = $(val).data().apptid;
        for (var i = 0; i < appointments.length; i++) {
            if (appointments[i].id == apptId) {
                $(val).data('appointments', appointments[i]);
                break;
            }
        }
    });

    $('#form_bubble ul').data('.appointments', appointments);
    $('#form_bubble').show().css({
        position: 'absolute',
        zIndex: '999',
        top: positions.top + 180,
        left: positions.left + 345,
        width: element.width(),
        height: '120px'
    });
    enableDrag(element, view);
    enableClick();
    enableBubbleClose();
}

$('.popup_cls').live('click', function() {
    $("#form_pop_up").hide();
    $('#calendar').fullCalendar('unselect');
	
});

function enableClick() {
    $('#form_bubble li').on('click', function(e) {
        if (e.target.nodeName != 'IMG') {
            var eventData = $(this).data().appointments;

            //console.log(eventData);
            

            $('#start_date').val('');
            $('#start_time').val('');
            $('#end_date').val('');
            $('#end_time').val('');
            $('#allDay').val('');
            $('#event_name').val('');
            $('#calendar_group').val('');
            $('#event_name').removeClass('error');
            $('#error_message_event').html('');
            $('#error_message_cal_group').html('');
            $('#event_id').val('');
            $('#task_id').val('');
            $('#task_start_date').val('');
            $('#task_start_time').val('');
            $('#task_end_date').val('');
            $('#task_end_time').val('');
            $('#task').val('');
            $('#task_description').val('');
            $('#task').removeClass('error');
            $('#task_description').removeClass('error');
            $('#error_message_task').html('');

            $('#event_edit_text').show();
            $('#task_edit_text').show();
            $('#task_desc_edit_text').show();
            start = convert(eventData.start);
            start = start.split(' ');
            var strt = dateFormat(start[0]);
            //$('.date_contianer').text(strt);
            $('.nav').hide();
            $('.popup_cls').css('top', '11px');
            var cid = new Array();
            cid = eventData.id;
            end = convert(eventData.end);
            end = end.split(' ');

            if (eventData.type == 'Birthday' || eventData.type == 'Anniversary') {
                $('#Event').hide();
                $('#Task').show();
                $('#Bdays').show();
                if (eventData.type == 'Birthday') {

                    var eventTitle = decodeURIComponent(eventData.title);
                    var icon = baseUrl + 'images/calendar/cake.png';
                } else if (eventData.type == 'Anniversary') {
                    var eventTitle = decodeURIComponent(eventData.title);
                    var icon = baseUrl + 'images/calendar/anniversary-big.png';
                }
                //$('#bday_div_title').html('<img src='+icon+' style=\"float: left;margin-top:4px;margin-right: 5px;\" title=\"'+eventTitle+'\"/>'+eventTitle).css('color',eventColor);
                var badyStart = dateFormat(start[0]);
                var realBday = badyStart.split(',')
                //$('#bday_div_date').html(realBday[0]).css('color','#B04C5C');

                $(this).css('position', 'relative');
                var offsetData = $(this).position();

                var htmlData = '<div class="event_popup_view1" style="border: 1px solid #CCC;background-color: white; z-index: 9999; position: absolute; width: 390px;height: 120px; padding: 10px 20px 15px 20px; clear: left;" id="form_pop_up"><div class="popup_cls closetop" style="top: 11px;right: 10px;"></div><div class="editCnt" id="bday_div">';
                htmlData = htmlData + '<div class="title"><div id="bday_div_title" style="color:' + eventData.color + '"><img src=' + icon + ' style=\"float: left;margin-top:4px;margin-right: 5px;\" title=\"' + eventTitle + '\"/>' + eventTitle + '</div>';
                htmlData = htmlData + '<div class="dateN" style="padding-top: 8px;font-size:15px;"><label id="bday_div_date" style="color:#B04C5C;">' + realBday[0] + '</label>';
                htmlData = htmlData + '</div></div></div></div>';
                $('#form_pop_up').remove();
                var left = $("#form_bubble").css("left");
                var top = $("#form_bubble").css("top");
                $("#form_bubble").after(htmlData);
                $('#form_pop_up').css('top', top);
                $('#form_pop_up').css('left', left);

            }

            else if (eventData.type == "event") {
                $('#event_id').val('');
                $('#event_id').val(eventData.unique_id);
                $('#start_date').val(start[0]);
                $('#start_time').val(start[1]);
                if (eventData.end != null) {
                    $('#end_date').val(end[0]);
                    $('#end_time').val(end[1]);
                    end_date = end[0];
                    end_time = end[1];
                    start_obj_date = eventData.start;
                    end_obj_date = eventData.end;
                } else {
                    $('#end_date').val(start[0]);
                    $('#end_time').val(start[1]);
                    end_date = start[0];
                    end_time = start[1];
                    start_obj_date = eventData.start;
                    end_obj_date = eventData.start;
                }
                $('#Task').hide();
                $('#Event').show();
                var strtTime = formatAMPM(new Date(start_obj_date));
                var endTime = formatAMPM(new Date(end_obj_date));
                if (eventData.allDay == 1 && start[0] == end_date) {
                    var realDate = dateFormat(start[0]);
                } else if (eventData.allDay == 1 && start[0] != end_date) {
                    var realDate = dateFormat(start[0]) + ' <b>to</b> ' + dateFormat(end[0]);
                } else {
                    var realDate = dateFormat(start[0]) + ' ' + strtTime + ' <b>to</b> ' + dateFormat(end_date) + ' ' + endTime;
                }
                var title_len = decodeURIComponent(eventData.title).length;
                if (title_len > 20)
                {
                    var title = decodeURIComponent(eventData.title).substring(0, 30) + '...';
                }
                else
                {
                    var title = decodeURIComponent(eventData.title);
                }

                $('#example-two').remove();
                $('#event_create_form').hide();
                $('#edit_event_div_container').show();
                var desc_len = decodeURIComponent(eventData.desc).length;
                if (eventData.desc == null)
                {
                    var desc = '';
                    var minHeight = '100px';
                }
                else if (desc_len <= 100)
                {
                    var desc = decodeURIComponent(eventData.desc);
                    var minHeight = '135px';
                }
                else
                {
                    var desc = decodeURIComponent(eventData.desc).substring(0, 110) + '...';
                    var moreUrl = baseUrl + 'calendar/events/detail/' + eventData.id;
                    desc = desc + '<a href=\"' + moreUrl + '\" class=\'more\'>More</a>';
                    var minHeight = '160px';
                }
                $('#edit_event_description').html('');
                $('#edit_event_description').html(desc);
                $('#event_name').val(title);
                $('#event_edit_text').html(title).css('color', eventData.color);
                $('#event_name').hide();
                $('#calendar_group').hide();
                $('#event_edit_cal_group').show();
                var group_cal = (eventData.group == "" || eventData.group == 0) ? 'My Calendar' : eventData.group;
                $('#event_edit_cal_group').html(group_cal);
                $('#event_hint').hide();
                $('#event_delete_small').show();
                $('#event_edit').show();
                $('#event_submit_small').hide();
                $(this).css('position', 'relative');
                var offsetData = $(this).position();
                var htmlData = '<div class="event_popup_view1" style="border: 1px solid #CCC;background-color: white; z-index: 9999; position: absolute; width: 390px;padding: 0px 10px 0px 10px; clear: left;" id="form_pop_up"><div class="popup_cls closetop" style="top: 11px;right: 10px;"></div><div class="editCnt" id="edit_event_div_container" style="min-height:' + minHeight + '">';
                htmlData = htmlData + '<div class="title"><div id="event_edit_text" style="color:' + eventData.color + ';">' + title + '</div></div>';
                htmlData = htmlData + '<div class="date"><label class="daylabel date_contianer">' + realDate + '</label>';
                htmlData = htmlData + '<span class="description wordwrap" style="width:380px;"  id="edit_event_description">' + desc + '</span></div>';
                htmlData = htmlData + '<input type="hidden" id="event_id" name="event_id" value="' + eventData.unique_id + '" data-event_id="' + eventData.id + '"/>';
                htmlData = htmlData + '<a href="#" class="delete" id="event_delete_small" onClick="deleteEvent(' + eventData.unique_id + ');" style="">Delete</a>';
                htmlData = htmlData + '<a href="#" class="editevent" id="event_edit_small" onClick="editEvent();">Edit Event</a>';
                htmlData = htmlData + '</div></div>';


                var left = $("#form_bubble").css("left");
                var top = $("#form_bubble").css("top");
                $('#form_pop_up').remove();
                $('#form_bubble').after(htmlData);
                $('#form_pop_up').css('top', top).css('left', left);
                $('#form_pop_up').css('position', 'absolute');
                $('#form_pop_up').show();


                /*$('#form_pop_up').remove();
                 var left = $(".form-bubble").css("left");
                 var top = $(".form-bubble").css("top");
                 $(".form-bubble").after(htmlData);
                 $('#form_pop_up').css('top',top);
                 $('#form_pop_up').css('left',left);
                 $('#form_pop_up').css('position', 'absolute');*/
            } else {
                $('#task_id').val('');
                $('#task_id').val(eventData.id);
                $('#task_start_date').val(start[0]);
                $('#task_start_time').val(start[1]);
                if (eventData.end != null) {
                    $('#task_end_date').val(end[0]);
                    $('#task_end_time').val(end[1]);
                    end_date = end[0];
                    end_time = end[1];
                    start_obj_date = eventData.start;
                    end_obj_date = eventData.end;
                } else {
                    $('#task_end_date').val(start[0]);
                    $('#task_end_time').val(start[1]);
                    end_date = start[0];
                    end_time = start[1];
                    start_obj_date = eventData.start;
                    end_obj_date = eventData.start;
                }
                $('#Event').hide();
                $('#Task').show();

                var strtTime = formatAMPM(new Date(start_obj_date));
                var endTime = formatAMPM(new Date(end_obj_date));
                if (eventData.allDay == 1 && start[0] == end_date) {
                    var realDate = dateFormat(start[0]);
                } else if (eventData.allDay == 1 && start[0] != end_date) {
                    var realDate = dateFormat(start[0]) + ' <b>to</b> ' + dateFormat(end[0]);
                } else {
                    var realDate = dateFormat(start[0]) + ' ' + strtTime + ' <b>to</b> ' + dateFormat(end_date) + ' ' + endTime;
                }
                /*if(start[0]==end_date){
                 var realDate = dateFormat(start[0]);
                 } else if(event.allDay==1 && start[0]!=end_date){
                 var realDate = dateFormat(start[0])+' <b>to</b> '+dateFormat(end_obj_date);
                 } else {
                 var realDate = dateFormat(start[0])+' <b>to</b> '+dateFormat(end_obj_date);
                 }*/
                var title_len = decodeURIComponent(eventData.title).length;
                if (title_len > 20)
                {
                    var title = decodeURIComponent(eventData.title).substring(0, 30) + '...';
                }
                else
                {
                    var title = decodeURIComponent(eventData.title);
                }
                $('#task').val(title);
                $('#task_create_form').hide();
                $('#edit_task_div_container').show();

                var desc_len = decodeURIComponent(eventData.desc).length;
                if (eventData.desc == null)
                {
                    var desc = '';
                    var minHeight = '100px';
                }
                else if (desc_len <= 100)
                {
                    var desc = decodeURIComponent(eventData.desc);
                    var minHeight = '135px';
                }
                else
                {
                    var desc = decodeURIComponent(eventData.desc).substring(0, 110) + '...';
                    var moreUrl = baseUrl + 'calendar/tasks/details/' + eventData.id;
                    desc = desc + '<a href=\"' + moreUrl + '\" class=\'more\'>More</a>';
                    var minHeight = '160px';
                }
                $('#edit_task_description').html('');
                $('#edit_task_description').html(desc);

                $('#edit_task_description').html('');
                $('#edit_task_description').html(desc);
                $('#task').hide();
                $('#task_edit_text').html(title).css('color', eventData.color);
                var desc = decodeURIComponent(desc);
                $('#task_description').val(desc);
                $('#task_description').hide();
                $('#task_desc_edit_text').html(desc);
                $('#task_delete_small').show();
                $('#task_edit').show();
                $('#task_submit_small').hide();
                $(this).css('position', 'relative');
                var offsetData = $(this).position();
                var htmlData = '<div class="event_popup_view1" style="border: 1px solid #CCC;background-color: white; z-index: 9999; position: absolute;width: 390px; padding: 0px 10px 0px 10px; clear: left;" id="form_pop_up"><div class="popup_cls closetop" style="top: 11px;right: 10px;"></div><div class="editCnt" id="edit_event_div_container" style="min-height:' + minHeight + '">';
                htmlData = htmlData + '<div class="title"><div id="event_edit_text" style="color:' + eventData.color + ';">' + title + '</div></div>';
                htmlData = htmlData + '<div class="date"><label class="daylabel date_contianer">' + realDate + '</label>';
                htmlData = htmlData + '<span class="description wordwrap" style="width:380px;" id="edit_event_description">' + desc + '</span></div>';
                htmlData = htmlData + '<input type="hidden" id="task_id" name="task_id" value="' + eventData.id + '" />';

                htmlData = htmlData + '<a href="#" class="delete" id="task_delete_small" onClick="deleteTask();">Delete</a>';
                htmlData = htmlData + ' <a href="#" class="editevent" id="task_delete_small" onClick="editTask();">Edit Task</a>';
                htmlData = htmlData + '</div></div>';

                $('#form_pop_up').remove();
                var left = $("#form_bubble").css("left");
                var top = $("#form_bubble").css("top");
                $("#form_bubble").after(htmlData);
                $('#form_pop_up').css('top', top);
                $('#form_pop_up').css('left', left);
            }
        }
    });
}

function enableBubbleClose() {
    $('.bubble_close').on('click', function(e) {
        e.preventDefault();
        $('#form_bubble').hide();
    });
}

function enableDrag(element, view) {

    var starttd = 0;
    var count = 0;
    var apptno;
    var appointments = $(element).data().appointments;



    $('#form_bubble li.event-drag').draggable({
        zIndex: 999,
        revert: true,
        revertDuration: 0,
        stop: function(event, ui) {
        },
    });

    $('.fc-widget-content').droppable({
        out: function(event, ui) {
            count++;
            apptno = ui.draggable.data().apptcount;
        },
        drop: function(jsevent, ui) {
            $(".fc-widget-content").droppable("destroy");
            var app = [];
            var movedTargetDate = $(jsevent.target).data().date;

            var apptid = ui.draggable.data().apptid;
            var app = {};
            app = $.map(appointments, function(i, val) {
                if (appointments[val]._id == apptid) {
                    return appointments[val];
                }
            });

            if (typeof app[0] != 'undefined') {
                var eventStart = app[0].start;
                movedTargetDate = new Date(movedTargetDate);

                movedTargetDate = (parseInt(movedTargetDate.getMonth()) + 1) + '/' + movedTargetDate.getDate() + '/' + movedTargetDate.getFullYear();
                var eventStartDate = (parseInt(eventStart.getMonth()) + 1) + '/' + eventStart.getDate() + '/' + eventStart.getFullYear()

                var stepToMove = datediff(eventStartDate, movedTargetDate);

                self.calendar.eventDrop($('.' + app[0].className), app[0], stepToMove, 0, app[0].allDay, jsevent, view);
                $('.bubble_close').trigger('click');
            } else {
                //alert('problem in moving event');     //TODO : Need to make necessary changes for drag and drop
                self.calendar.calendar.refetchEvents();
            }
        }
    });
}

function dstrToUTC(ds) {
    var dsarr = ds.split("/");
    var mm = parseInt(dsarr[0], 10);
    var dd = parseInt(dsarr[1], 10);
    var yy = parseInt(dsarr[2], 10);
    return Date.UTC(yy, mm - 1, dd, 0, 0, 0);
}

function datediff(ds1, ds2) {
    var d1 = dstrToUTC(ds1);
    var d2 = dstrToUTC(ds2);
    var oneday = 86400000;
    return (d2 - d1) / oneday;
}

function getEvents(event) {
    var calInstance = $('#calendar');
    var currentView = calInstance.fullCalendar('month');


}

function applyBackgroundColor(element) {
    var dateDiff = datediff(self.calendar.calendar.formatDates(new Date($(clickedElement).data().date), null, 'M/d/yyyy'), self.calendar.calendar.formatDates(new Date($(element).data().date), null, 'M/d/yyyy')),
            startDate = new Date($(clickedElement).data().date),
            endDate = new Date($(element).data().date),
            positioning = 1,
            formatdate, dateRanges;
    if (dateDiff < 0) {
        positioning = 0;
    }
    if (!positioning) {
        startDate = new Date($(element).data().date);
        endDate = new Date($(clickedElement).data().date);
    }
    dateRanges = expandDateRange(startDate, endDate);
    $('td').css('backgroundColor', 'white');
    $(dateRanges).each(function(i, val) {
        formatdate = self.calendar.calendar.formatDates(val, null, 'yyyy-MM-dd');
        $('td[data-date=' + formatdate + ']').css('backgroundColor', 'pink');
    });
}


function enableEvents() {

    $('.fc-widget-content').on('mousedown', function(e) {
        if (self.calendar.calendar.getView().name == 'month') {
            clicking = true;
            $('.fc-widget-content').css('background-color', 'white');
            var currentElem = e.currentTarget;
            clickedElement = currentElem;
            $(currentElem).css('background-color', 'lightblue');
        } else {
            $('.fc-widget-content').css('background', 'none');
        }

    });

    $('.fc-widget-content,.fc-event').on('mouseup', function(e) {
        if (self.calendar.calendar.getView().name == 'month') {
            clicking = false;
        } else {
            $('.fc-widget-content').css('background', 'none');
        }
    });

    $('.fc-widget-content').on('mousemove', function(e) {
        if (self.calendar.calendar.getView().name == 'month') {
            if (clicking) {
                var currentElem = e.currentTarget;
                applyBackgroundColor(currentElem);
            }
        } else {
            $('.fc-widget-content').css('background', 'none');
        }
    });
}






