$(document).ready(function() {
    var mod = 'upload';
    $('.option-set>.radio input:radio').change(function() {
        if ($(this).val() === 'disable') {
            hideSettingDIV();
            $('#file_choose').prop('disabled', true);
            $('.setting-list-test').prop('disabled', true);
            $('.sub-config-list .radio input').prop('disabled', true);
            $('.setting-test-submit>button').prop('disabled', true);
            $('.notification-widget').css('display', 'block');
        } else {
            showSettingDIV();
            $('#file_choose').prop('disabled', false);
            $('.setting-list-test').prop('disabled', false);
            $('.sub-config-list .radio input').prop('disabled', false);
            $('.setting-test-submit>button').prop('disabled', false);
            $('.notification-widget').css('display', 'none');
            if ($('#current_library').length === 0) {
                if ($('#show-setting').length) {
                    showSetting('show_upload_type', mod, '');
                } else {
                    showSetting('show_upload_type', mod, 'append');
                }
            }
        }
    });

    $('.type-select>.radio input').change(function() {
        if ($('#change_type').attr('value').length) {
            detectSetting('show_upload_type', mod);
        } else {
            showSetting('show_upload_type', mod, '');
        }
    });

    $('.type-select>.radio').click(function(event) {
        $('#change_type').attr('value', event.target.id);
    });

    $('.sub-config-list>.radio').click(function(event) {
        $('#change_library').attr('value', event.target.value);
    });

    if ($('.option-set>.radio input:checked').attr('value') === 'enable') {
        if ($('#current_library').length === 0) {
            $(window).on('load', showSetting('show_upload_type', mod, ''));
            $('.notification-widget').css('display', 'none');
        } else {
            $('#file_choose').prop('disabled', false);
            $('.setting-list-test').prop('disabled', false);
        }
    } else {
        hideSettingDIV();
        $('#file_choose').prop('disabled', true);
        $('.setting-list-test').prop('disabled', true);
        $('.sub-config-list .radio input').prop('disabled', true);
        $('.setting-test-submit>button').prop('disabled', true);
        $('.notification-widget').css('display', 'block');
    }
});
