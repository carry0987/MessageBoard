$(document).ready(function() {
    var mod = 'seo';
    $('.type-select>.radio input:radio').change(function() {
        if ($('#show-setting').length) {
            showSetting('show_seo_type', mod, '');
        } else {
            showSetting('show_seo_type', mod, 'append');
        }
    });

    $(window).on('load', showSetting('show_seo_type', mod, ''));
});
