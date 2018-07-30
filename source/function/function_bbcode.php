<?php
function bbcode2html($content) {
    $content = htmlspecialchars($content);
    $content = preg_replace('/\[b\](.*)\[\/b\]/is', '<b>$1<br /></b>', $content);
    $content = preg_replace('/\[i\](.*)\[\/i\]/is', '<i>$1<br /></i>', $content);
    $content = preg_replace('/\[u\](.*)\[\/u\]/is', '<u>$1<br /></u>', $content);
    $content = preg_replace('/\[s\](.*)\[\/s\]/is', '<strike>$1<br /></strike>', $content);
    $content = preg_replace('/\[sub\](.*)\[\/sub\]/is', '<sub>$1<br /></sub>', $content);
    $content = preg_replace('/\[sup\](.*)\[\/sup\]/is', '<sup>$1<br /></sup>', $content);
    $content = preg_replace('/\[left\](.*)\[\/left\]/i', '<p style="text-align: left;">$1<br /></p>', $content);
    $content = preg_replace('/\[center\](.*)\[\/center\]/i', '<p style="text-align: center;">$1<br /></p>', $content);
    $content = preg_replace('/\[right\](.*)\[\/right\]/i', '<p style="text-align: right;">$1<br /></p>', $content);
    $content = preg_replace('/\[justify\](.*)\[\/justify\]/is', '<div style="text-align: justify;">$1<br /></div>', $content);
    $content = preg_replace('/\[ul\](.*)\[\/ul\]/is', '<ul>$1</ul>', $content);
    $content = preg_replace('/\[ol\](.*)\[\/ol\]/is', '<ol>$1</ol>', $content);
    $content = preg_replace('/\[li\](.*)\[\/li\]/i', '<li>$1<br /></li>', $content);
    $content = preg_replace('/\[hr\]/is', '<hr />', $content);
    $content = preg_replace('/\[url\]((?:https?:)?\/?\/[^\s\'"]+)\[\/url\]/i', '<a href="$1">$1</a>', $content);
    $content = preg_replace('/\[url=((?:https?:)?\/?\/[^\s\'"]+)\](.*)\[\/url\]/i', '<a href="$1">$2</a>', $content);
    $content = preg_replace('/\[img\]((?:https?:)?\/?\/[^\s\'"]+)\[\/img\]/i', '<img src="$1"></ismg>', $content);
    $content = preg_replace('/\[img=(\d+)x(\d+)\]((?:https?:)?\/?\/[^\s\'"]+)\[\/img\]/i', '<img src="$3" width="$1" height="$2"></img>', $content);
    $content = preg_replace('/\[quote\](.*)\[\/quote\]/i', '<blockquote>$1</blockquote>', $content);
    $content = preg_replace('/\[code\](.*)\[\/code\]/i', '<code>$1<br /></code>', $content);
    $content = preg_replace('/\[size=(\d+)\](.*)\[\/size\]/i', '<font size="$1;">$2<br /></font>', $content);
    $content = preg_replace('/\[color=(#[a-f0-9]{6}|#[a-f0-9]{3}|[a-z]+)\](.*)\[\/color\]/i', '<span style="color: $1;">$2<br /></span>', $content);
    return $content;
}
