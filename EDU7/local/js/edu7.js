var Edu7 = BX.namespace('Edu7');

Edu7.field = {
    changeRegion: function(dropdown) {
        var city = $(dropdown).next('select'),
            cityOptions = city.find('option'),
            regionId = $(dropdown).val();

        // Reset selected city
        city.val('');

        // Filter cities based on the current region
        cityOptions
            .hide()
            .filter(function() {
                return ($(this).data('region-id') == regionId);
            })
            .show();
    },
    changeCity: function(dropdown) {
        var region = $(dropdown).prev('select'),
            input = $(dropdown).next('input');

        // Get value
        var value = $(region).val() + '.' + $(dropdown).val();

        // Save value to hidden input
        input.val(value);
    }
};