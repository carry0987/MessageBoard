<?php
function bbcode2html($content)
{
    $content = preg_replace('/\[b\](.*)\[\/b\]/i', '<b>$1</b>', $content);
    $content = preg_replace('/\[i\](.*)\[\/i\]/i', '<i>$1</i>', $content);
    $content = preg_replace('/\[u\](.*)\[\/u\]/i', '<u>$1</u>', $content);
    $content = preg_replace('/\[url\]((?:https?:)?\/?\/[^\s\'"]+)\[\/url\]/i', '<a href="$1">$1</a>', $content);
    $content = preg_replace('/\[url=((?:https?:)?\/?\/[^\s\'"]+)\](.*)\[\/url\]/i', '<a href="$1">$2</a>', $content);
    $content = preg_replace('/\[img\]((?:https?:)?\/?\/[^\s\'"]+)\[\/img\]/i', '<img src="$1"></img>', $content);
    $content = preg_replace('/\[quote\](.*)\[\/quote\]/is', '<blockquote><p>$1</p></blockquote>', $content);
    $content = preg_replace('/\[code\](.*)\[\/code\]/is', '<pre>$1</pre>', $content);
    $content = preg_replace('/\[size=(\d+)\](.*)\[\/size\]/i', '<span style="font-size:$1px;">$2</span>', $content);
    $content = preg_replace('/\[color=(#[a-f0-9]{6}|#[a-f0-9]{3}|[a-z]+)\](.*)\[\/color\]/i', '<span style="color:$1;">$2</span>', $content);
    return $content;
}
