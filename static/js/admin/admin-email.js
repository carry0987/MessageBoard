$(document).ready(function() {
    var mod = 'email';
    $('.option-set>.radio input:radio').change(function() {
        if ($(this).val() === 'disable') {
            hideSettingDIV();
            $('.setting-test-submit>button').prop('disabled', true);
            $('.notification-widget').css('display', 'block');
        } else {
            showSettingDIV();
            $('.setting-test-submit>button').prop('disabled', false);
            $('.notification-widget').css('display', 'none');
            if ($('#show-setting').length) {
                showSetting('show_email_type', mod, '');
            } else {
                showSetting('show_email_type', mod, 'append');
            }
        }
    });

    $('.type-select>.radio input').change(function() {
        if ($('#change_type').attr('value').length) {
            detectSetting('show_email_type', mod);
        } else {
            showSetting('show_email_type', mod, '');
        }
    });

    $('.type-select>.radio').click(function(event) {
        $('#change_type').attr('value', event.target.id);
    });

    if ($('.option-set>.radio input:checked').attr('value') === 'enable') {
        $(window).on('load', showSetting('show_email_type', mod, ''));
        $('.notification-widget').css('display', 'none');
    } else {
        hideSettingDIV();
        $('.setting-test-submit>button').prop('disabled', true);
        $('.notification-widget').css('display', 'block');
    }
});
