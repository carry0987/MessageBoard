$(document).ready(function() {
    var mod = 'social';
    $('.option-set>.radio input:radio').change(function() {
        if ($(this).val() === 'disable') {
            hideSettingDIV();
        } else {
            showSettingDIV();
            if ($('#show-setting').length) {
                showSetting('show_social_type', mod, '');
            } else {
                showSetting('show_social_type', mod, 'append');
            }
        }
    });

    $('.type-select>.radio input').change(function() {
        if ($('#change_type').attr('value').length) {
            detectSetting('show_social_type', mod);
        } else {
            showSetting('show_social_type', mod, '');
        }
    });

    $('.type-select>.radio').click(function(event) {
        $('#change_type').attr('value', event.target.id);
    });

    if ($('.option-set>.radio input:checked').attr('value') === 'enable') {
        $(window).on('load', showSetting('show_social_type', mod, ''));
    } else {
        hideSettingDIV();
    }
});
