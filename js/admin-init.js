/**
 * Created by moridrin on 5-12-16.
 */
jQuery(function ($) {
    $(document).ready(function () {
        ssv_init_date_time_pickers();
    });
});

function ssv_init_date_time_pickers() {
    jQuery(function ($) {
        var dateTimePickers = $('.datetimepicker');
        dateTimePickers.each(function () {
            var inline = $(this).attr('inline');
            var minDateAttr = $(this).attr('minDate');
            minDateAttr = minDateAttr !== "false" ? minDateAttr : false;
            var maxDateAttr = $(this).attr('maxDate');
            maxDateAttr = maxDateAttr !== "false" ? maxDateAttr : false;
            var value = $(this).attr('value') ? $(this).attr('value') : 'now';
            $(this).datetimepicker({
                inline: inline === "true" || inline === "inline" || inline === "yes",
                mask: '9999-19-39 29:59',
                format: 'Y-m-d H:i',
                value: value,
                minDate: minDateAttr,
                maxDate: maxDateAttr
            });
        });
        var datePickers = $('.datepicker');
        datePickers.each(function () {
            var inline = $(this).attr('inline');
            var minDateAttr = $(this).attr('minDate');
            minDateAttr = minDateAttr !== "false" ? minDateAttr : false;
            var maxDateAttr = $(this).attr('maxDate');
            maxDateAttr = maxDateAttr !== "false" ? maxDateAttr : false;
            var value = $(this).attr('value') ? $(this).attr('value') : 'now';
            $(this).datetimepicker({
                timepicker: false,
                inline: inline === "true" || inline === "inline" || inline === "yes",
                mask: '9999-19-39',
                format: 'Y-m-d',
                value: value,
                minDate: minDateAttr,
                maxDate: maxDateAttr
            });
        });
        var timePickers = $('.timepicker');
        timePickers.each(function () {
            var inline = $(this).attr('inline');
            var value = $(this).attr('value') ? $(this).attr('value') : 'now';
            $(this).datetimepicker({
                datepicker: false,
                inline: inline === "true" || inline === "inline" || inline === "yes",
                mask: '29:59',
                format: 'H:i',
                value: value
            });
        });
    });
}
