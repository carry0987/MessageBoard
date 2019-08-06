$(document).ready(function() {
    var mod = 'security';
    $('.option-set>.radio input:radio').change(function() {
        if ($(this).val() === 'disable') {
            hideSettingDIV();
        } else {
            showSettingDIV();
            if ($('#show-setting').length) {
                showSetting('show_captcha_type', mod, '');
            } else {
                showSetting('show_captcha_type', mod, 'append');
            }
        }
    });


    $('.type-select>.radio input').change(function() {
        if ($('#change_type').attr('value').length) {
            detectSetting('show_captcha_type', mod);
        } else {
            showSetting('show_captcha_type', mod, '');
        }
    });

    $('.type-switch>.apply-switch input[value=login]').change(function() {
        if ($('input[type=checkbox][value=login]').prop('checked')) {
            $('input[name=apply_type\\[login\\]]').attr('value', 'enable');
        } else {
            $('input[name=apply_type\\[login\\]]').attr('value', 'disable');
        }
    });

    $('.type-switch>.apply-switch input[value=signup]').change(function() {
        if ($('input[type=checkbox][value=signup]').prop('checked')) {
            $('input[name=apply_type\\[signup\\]]').attr('value', 'enable');
        } else {
            $('input[name=apply_type\\[signup\\]]').attr('value', 'disable');
        }
    });

    $('.type-select>.radio').click(function(event) {
        $('#change_type').attr('value', event.target.id);
    });

    if ($('.option-set>.radio input:checked').attr('value') === 'enable') {
        $(window).on('load', showSetting('show_captcha_type', mod, ''));
    } else {
        hideSettingDIV();
    }
});
