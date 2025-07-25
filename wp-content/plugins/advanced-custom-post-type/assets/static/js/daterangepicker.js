var $ = jQuery.noConflict();

jQuery(function() {

    const initDatePicker = () => {
        const daterangepickerElement = $('.acpt-daterangepicker');

        const value = daterangepickerElement.attr("value");
        const startDate = value ? value.split(" - ")[0] : formatDate(today);
        const endDate = value ? value.split(" - ")[1] : formatDate(plus7Days);
        const maxDate = daterangepickerElement.data('max-date');
        const minDate = daterangepickerElement.data('min-date');

        const settings = {
            drops: 'up',
            startDate: startDate,
            endDate: endDate,
            locale: {
                format: 'YYYY-MM-DD'
            }
        };

        if(typeof maxDate !== "undefined"){
            settings.maxDate = maxDate;
        }

        if(typeof minDate !== "undefined"){
            settings.minDate = minDate;
        }

        daterangepickerElement.daterangepicker(settings);
    };

    document.addEventListener("acpt_grouped_element_added", function(){
        initDatePicker();
    });

    initDatePicker();
});