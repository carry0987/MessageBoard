<?php
if (defined('ROOT_PATH') !== true) {
    exit('Access Denied');
}

function getSimpleCaptchaOption($captchaConfig)
{
    if ($captchaConfig !== false) {
        $captcha_option = array(
            'captcha_code' => '',
            'captcha_image_height' => $captchaConfig['image_height'],
            'captcha_image_width' => $captchaConfig['image_width'],
            'captcha_letter' => 'bcdfghjkmnpqrstvwxyz23456789',
            'captcha_font' => $captchaConfig['font_file'],
            'captcha_text_color' => $captchaConfig['text_color'],
            'captcha_noise_color' => $captchaConfig['noise_color'],
            'total_character' => $captchaConfig['total_character'],
            'random_captcha_dots' => $captchaConfig['random_dots'],
            'random_captcha_lines' => $captchaConfig['random_lines'],
            'check_sensitive' => $captchaConfig['check_sensitive']
        );
    } else {
        $captcha_option = array(
            'captcha_code' => '',
            'captcha_image_height' => 60,
            'captcha_image_width' => 250,
            'captcha_letter' => 'bcdfghjkmnpqrstvwxyz23456789',
            'captcha_font' => 'monofont.ttf',
            'captcha_text_color' => '#142864',
            'captcha_noise_color' => '#142864',
            'total_character' => 6,
            'random_captcha_dots' => 50,
            'random_captcha_lines' => 25,
            'check_sensitive' => false
        );
    }
    return $captcha_option;
}

function getSVGCaptchaOption($difficulty)
{
    (int) $difficulty;
    return $difficulty;
}
