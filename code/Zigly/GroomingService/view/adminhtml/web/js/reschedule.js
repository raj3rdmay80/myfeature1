/**
 * Copyright (C) 2020  Zigly


 * @package Zigly_GroomingService
 */
define([
    'jquery',
    'moment',
    'Zigly_GroomingService/js/lib/fullcalendar.min',
    'jquery/ui',
    'mage/translate',
    "domReady!"
], function($, moment) {
    'use strict';
    $(function() {
        $('.zigly-reschedule-save').prop('disabled', true);

        var existServerTime = setInterval(function() {
            if (window.serverTime) {
                clearInterval(existServerTime);
                var startDate = new Date(window.serverTime.replace(/-/g, "/"));
                startDate.setDate(startDate.getDate() - 1)
                var endDate = new Date(window.serverTime.replace(/-/g, "/"));
                endDate.setDate(endDate.getDate() + 7)
                var calendarEl = document.getElementById('grooming-calendar-slot');
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    // firstDay: 1,
                    // firstDay: new Date().getDay(),
                    // eventColor: '#d7f0f7',
                    // showNonCurrentDates: false,
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
                        var days = document.querySelectorAll(".selectedDate");
                        var clickedDay = info.date.getTime();
                        let today = new Date(window.serverTime.replace(/-/g, "/"))
                        var from = startDate.getTime();
                        var to = endDate.getTime();
                        if (clickedDay >= from && clickedDay <= to) {
                            days.forEach(function(day) {
                                day.classList.remove("selectedDate");
                            });
                            info.dayEl.classList.add("selectedDate");
                            var dateString = moment(info.date).format('YYYY-MM-DD');
                            $('input[name="selected_date"]').val(dateString).change();
                            new $.toggleSaveButton();
                            var todayString = moment(today).format('YYYY-MM-DD');

                            // var coeff = 1000 * 60 * 30;
                            // today = new Date(Math.round(today.getTime() / coeff) * coeff)
                            let availableTimesSlots = '';
                            if (!window.availableTimesSlotsHtml) {
                                window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
                            }
                            if (todayString == dateString && parseInt(moment(today).format('H')) > (parseInt(window.startHr) - 2)) {
                                if (parseInt(moment(today).format('H')) < 23) {
                                    var coeff = 1000 * 60 * 30;
                                    today = new Date(Math.round(today.getTime() / coeff) * coeff)
                                }
                                let $time = today;
                                let currentHr = moment(today).format('H')
                                let currentMin = moment(today).format('mm')
                                today.setMinutes(today.getMinutes() + 120)
                                let firstSlotHr = moment(today).format('H')
                                let firstSlotMin = moment(today).format('mm')
                                var endHr = parseInt(window.endHr)
                                var endMin = parseInt(window.endMin)
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
                                        if ($prev == (window.endTime)) {
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
                    // dateClick: function(info) {
                    //     var days = document.querySelectorAll(".selectedDate");

                    //     var today = info.date;
                    //     var from = startDate.getTime();
                    //     var to = endDate.getTime();
                    //     if (today >= from && today <= to) {
                    //         days.forEach(function(day) {
                    //             day.classList.remove("selectedDate");
                    //         });
                    //         info.dayEl.classList.add("selectedDate");
                    //         var dateString = moment(info.date).format('YYYY-MM-DD');
                    //         $('input[name="selected_date"]').val(dateString).change();
                    //         new $.toggleSaveButton();
                    //     }
                    // },
                    events: {},
                });
                calendar.render();
            }
        }, 100); 

        if (!window.availableTimesSlotsHtml) {
            window.availableTimesSlotsHtml = $('#grooming-timeslot ol')[0].innerHTML
            $('<div class="message-notice notice message" id="noticemsg">Please select a date before selecting time.</div>').insertAfter('#grooming-timeslot ol');
            $("#grooming-timeslot ol").html('');
        }
        
        $('#grooming-timeslot').on('click', 'ol .slot-time', function(){
            $("#grooming-timeslot .slot-time").removeClass('selected-time');
            $('input[name="selected_time"]').val($(this).data('slot')).change();
            
            $(this).addClass('selected-time');
            new $.toggleSaveButton();
        });

        $.toggleSaveButton = function() {
            if (!$('input[name="selected_time"]').val() || !$('input[name="selected_date"]').val()) {
                $('.zigly-reschedule-save').prop('disabled', true);
            } else {
                $('.zigly-reschedule-save').prop('disabled', false);
            }
        };
    });


});