/**
 * Copyright (C) 2020  Zigly
 * @package Zigly_Sales
 */
define([
    'jquery',
    'moment',
    'mage/url',
    'Magento_Ui/js/modal/confirm',
    'Zigly_GroomingService/js/lib/fullcalendar.min',
    'jquery/ui',
    'mage/translate',
    "domReady!"
], function($, moment, urlBuilder, confirmation) {
    'use strict';

    return function(config) {
       
        $(document).on('click', '#grooming-timeslot .slot-time', function() {
            $("#errormsg").remove();
            $("#grooming-timeslot .slot-time").removeClass('selected-time');
            if (!$('input[name="selected_date"]').val()) {
                $('<div class="message-error error message groom" id="errormsg">Please select a date before selecting time.</div>').insertBefore('#reschedule-container');
                return false;
            }
            $('input[name="selected_time"]').val($(this).data('slot'));
            $(this).addClass('selected-time');
        });
        $(document).on('click', '#reschedule-container #submit-schedule', function() {
            $("#errormsg").remove();
           if (!$('input[name="selected_date"]').val() || !$('input[name="selected_time"]').val()) {
                $('<div class="message-error error message groom" id="errormsg">Please select a date and time.</div>').insertBefore('#reschedule-container');
                return false;
            }
            let postAddress = {
                selected_date: $('input[name="selected_date"]').val(),
                selected_time: $('input[name="selected_time"]').val(),
                id: $('#reschedule-container').data('id')
            };
            $.ajax({
                url: urlBuilder.build('sales/booking/setnewschedule'),
                method: "POST",
                data: postAddress,
                showLoader: true,
                success: function(response) {
                    if (response.success) {
                        $('<div class="message-success success message" id="errormsg">' + response.message + '</div>').insertAfter('#reschedule-container');
                        setTimeout(function() {
                            window.location = urlBuilder.build('sales/orders/viewbooking/booking_id/')+$('#reschedule-container').data('id')+'/';
                        }, 600);
                    } else {
                        $('<div class="message-error error message groom" id="errormsg">'+response.message+'</div>').insertBefore('#reschedule-container');
                    }
                    console.log(response)
                }
            });
        });

        /* calendar */
        var startDate = new Date(config.serverTime.replace(/-/g, "/"));
        startDate.setDate(startDate.getDate() - 1)
        var endDate = new Date(config.serverTime.replace(/-/g, "/"));
        endDate.setDate(endDate.getDate() + 7)
        var calendarEl = document.getElementById('grooming-calendar-slot');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            eventDisplay: 'block',
            eventTextColor: "#000",
            // disableDragging: true,
            selectable: true,
            selectConstraint: {
                start: startDate,
                end: endDate
            },
            dateClick: function(info) {
                $("#errormsg").remove();
                $("#noticemsg").remove();
                $(".fc-daygrid-day").removeClass('fc-day-today');
                var days = document.querySelectorAll(".selectedDate");
                var clickedDay = info.date.getTime();
                let today = new Date(config.serverTime.replace(/-/g, "/"))
                console.log(config.serverTime)
                console.log(today)
                var from = startDate.getTime();
                var to = endDate.getTime();
                if (clickedDay >= from && clickedDay <= to) {
                    days.forEach(function(day) {
                        day.classList.remove("selectedDate");
                    });
                    info.dayEl.classList.add("selectedDate");
                    var dateString = moment(info.date).format('YYYY-MM-DD');
                    $('input[name="selected_date"]').val(dateString);
                    var todayString = moment(today).format('YYYY-MM-DD');

                    /*var coeff = 1000 * 60 * 30;
                    today = new Date(Math.round(today.getTime() / coeff) * coeff)*/
                    let availableTimesSlots = '';
                    if (!window.availableTimesSlotsHtml) {
                        window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
                    }
                    console.log(config)
                    if(moment(today).minute()> 30){
                        moment(today).minute(30).second(0);
                    } else {
                        moment(today).minute(0).second(0);
                    }
                    if (todayString == dateString && (parseInt(moment(today).format('H')) > (parseInt(config.startHr) - 2))) {
                        if (parseInt(moment(today).format('H')) < 23) {
                            var coeff = 1000 * 60 * 30;
                            today = new Date(Math.ceil(today.getTime() / coeff) * coeff)
                        }
                        if(moment(today).minute() > 30){
                            today.setHours(today.getHours() + 1);
                        }
                        let $time = today;
                        let currentHr = moment(today).format('H')
                        let currentMin = moment(today).format('mm')
                        today.setMinutes(today.getMinutes() + 120)
                        let firstSlotHr = moment(today).format('H')
                        let firstSlotMin = moment(today).format('mm')
                        console.log(config.endMin)
                        var endHr = parseInt(config.endHr)
                        var endMin = parseInt(config.endMin)
                        if ((parseInt(currentHr) == endHr && parseInt(currentMin) >= endMin) || parseInt(currentHr) > endHr) {
                            $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                            $("#grooming-timeslot ol").html('');
                        } else if ((parseInt(firstSlotHr) == endHr && parseInt(firstSlotMin) >= endMin) || parseInt(firstSlotHr) > endHr) {
                            $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                            $("#grooming-timeslot ol").html('');
                        } else {
                            while ($time) {
                                let $prevDate = new Date($time);
                                let $prev = moment($prevDate).format('h:mm a');
                                let $next = $prevDate.setMinutes($prevDate.getMinutes() + 30 );
                                $time = $next;
                                availableTimesSlots += '<li class="slot-time" data-slot="'+$prev+'">'+$prev+'</li>';
                                if ($prev == (config.endTime)) {
                                    break;
                                }
                            }
                            $("#grooming-timeslot ol").html(availableTimesSlots);
                        }
                    } else {
                        if (window.availableTimesSlotsHtml) {
                            $("#grooming-timeslot ol").html(window.availableTimesSlotsHtml);
                        }
                    }
                }
            },
            events: {},
        });
        calendar.render();

        if (!window.availableTimesSlotsHtml) {
            /*window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
            $('<div class="message-notice notice message" id="noticemsg">Please select a date before selecting time.</div>').insertAfter('#grooming-timeslot ol');
            $("#grooming-timeslot ol").html('');*/
            let today = new Date(config.serverTime.replace(/-/g, "/"))
            var from = startDate.getTime();
            var to = endDate.getTime();
            var dateString = moment(new Date()).format('YYYY-MM-DD');
            $('input[name="selected_date"]').val(dateString);
            var todayString = moment(today).format('YYYY-MM-DD');

            let availableTimesSlots = '';
            if (!window.availableTimesSlotsHtml) {
                window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
            }
            console.log(config)
            if(moment(today).minute()> 30){
                moment(today).minute(30).second(0);
            } else {
                moment(today).minute(0).second(0);
            }
            if (todayString == dateString && (parseInt(moment(today).format('H')) > (parseInt(config.startHr) - 2))) {
                if (parseInt(moment(today).format('H')) < 23) {
                    var coeff = 1000 * 60 * 30;
                    today = new Date(Math.ceil(today.getTime() / coeff) * coeff)
                }
                if(moment(today).minute() > 30){
                    today.setHours(today.getHours() + 1);
                }
                let $time = today;
                let currentHr = moment(today).format('H')
                let currentMin = moment(today).format('mm')
                today.setMinutes(today.getMinutes() + 120)
                let firstSlotHr = moment(today).format('H')
                let firstSlotMin = moment(today).format('mm')
                console.log(config.endMin)
                var endHr = parseInt(config.endHr)
                var endMin = parseInt(config.endMin)
                if ((parseInt(currentHr) == endHr && parseInt(currentMin) >= endMin) || parseInt(currentHr) > endHr) {
                    $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                    $("#grooming-timeslot ol").html('');
                } else if ((parseInt(firstSlotHr) == endHr && parseInt(firstSlotMin) >= endMin) || parseInt(firstSlotHr) > endHr) {
                    $('<div class="message-notice notice message" id="noticemsg">Time-slot are unavailable for the Selected day.</div>').insertAfter('#grooming-timeslot ol');
                    $("#grooming-timeslot ol").html('');
                } else {
                    while ($time) {
                        let $prevDate = new Date($time);
                        let $prev = moment($prevDate).format('h:mm a');
                        let $next = $prevDate.setMinutes($prevDate.getMinutes() + 30 );
                        $time = $next;
                        availableTimesSlots += '<li class="slot-time" data-slot="'+$prev+'">'+$prev+'</li>';
                        if ($prev == (config.endTime)) {
                            break;
                        }
                    }
                    $("#grooming-timeslot ol").html(availableTimesSlots);
                }
            } else {
                if (window.availableTimesSlotsHtml) {
                    $("#grooming-timeslot ol").html(window.availableTimesSlotsHtml);
                }
            }
        }
    }
});