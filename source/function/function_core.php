<?php
function isBanned($status, $lang_banned, $lang_normal)
{
    $user_status = ($status === 1) ? $lang_banned : $lang_normal;
    return $user_status;
}

function checkReferer()
{
    if (isset($_SERVER['HTTP_REFERER'])) {
        $refererhost = parse_url($_SERVER['HTTP_REFERER']);
        $refererhost['host'] .= (!empty($refererhost['port'])) ? (':'.$refererhost['port']) : '';
        if ($refererhost['host'] != $_SERVER['HTTP_HOST']) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

function checkTimezone($web_tz = '', $user_tz)
{
    $tz = ($user_tz === false) ? $web_tz : $user_tz;
    try {
        $checkTimezone = new DateTimeZone($tz);
    } catch(Exception $e) {
        $checkTimezone = false;
    }
    return $checkTimezone;
}

function getDateTime($web_tz, $user_tz, $timestamp, $format = 'Y-m-d H:i:s')
{
    if (checkTimezone($web_tz, $user_tz) !== false) {
        $tz = checkTimezone($web_tz, $user_tz);
        $dateTime = new DateTime('now', $tz);
        $dateTime->setTimestamp($timestamp);
        $result = $dateTime->format($format);
    } else {
        $result = '';
    }
    return $result;
}

function generateSitemap($array, $sitemapSet, $add = false)
{
    //Set Sitemap
    $sitemapOption = array(
        'version' => '1.0',
        'charset' => 'UTF-8',
        'xml_file' => $sitemapSet['path'],
        'timezone' => $sitemapSet['timezone']
    );
    if (class_exists('SitemapGenerator')) {
        $seo = new SitemapGenerator($sitemapOption, $add);
        if ($add === true) {
            return $seo->updateSitemap($array);
        } else {
            foreach ($array as $sitemapData) {
                $seo->addSitemapNode($sitemapData);
            }
            $seo->generateXML();
        }
        return $seo->checkSitemap();
    } else {
        return false;
    }
}

function checkPage($value)
{
    if (ctype_digit($value)) {
        if ($value == '1' || $value == '' || $value == '0') {
            $page = 1;
        } else {
            $page = $value;
        }
    } else {
        $page = 1;
    }
    return $page;
}

function countArray($array, $limit = 10, $limitMsg = '...', $countSelect = false)
{
    $result = 0;
    $limit = (int) $limit;
    if (isset($array) && is_array($array)) {
        if ($countSelect !== false) {
            foreach ($countSelect as $key1 => $value1) {
                foreach ($array as $key2 => $value2) {
                    if ($array[$key2][$key1] === $value1) {
                        unset($array[$key2]);
                    }
                }
            }
        }
        $result = count($array);
        if ($result > $limit) {
            $result = $limitMsg;
        }
    }
    return $result;
}

function countTotalPage($totalItems, $itemsPerPage = 10)
{
    if (is_array($totalItems)) {
        $totalItems = count($totalItems);
        $result = ceil($totalItems / $itemsPerPage);
    } elseif (is_int($totalItems)) {
        $result = (int) ceil($totalItems / $itemsPerPage);
    } else {
        $result = false;
    }
    return $result;
}

function countLastPageResult($totalItems, $currentPage, $itemsPerPage)
{
    if (is_array($totalItems)) {
        $totalItems = count($totalItems);
        $start = ceil(($currentPage - 1) * $itemsPerPage);
        $result = $totalItems - $start;
    } else {
        $result = false;
    }
    return $result;
}

function getFileSize($size)
{
    $decimals = 2;
    $fullsize['mbyte'] = number_format($size / (1024 * 1024), $decimals);
    $fullsize['kbyte'] = number_format($size / (1024), $decimals);
    $fullsize['byte'] = number_format($size, 0, '', '');
    if ($fullsize['mbyte'] > 1) {
        return $fullsize['mbyte'].' MB';
    } elseif ($fullsize['kbyte'] > 1) {
        return $fullsize['kbyte'].' KB';
    } else {
        return $fullsize['byte'].' Byte';
    }
}

function checkEmpty($array, $checkArray)
{
    $result = true;
    foreach ($checkArray as $key) {
        if (isset($array[$key])) {
            if (empty($array[$key])) {
                $result = false;
            }
        } else {
            $result = false;
        }
    }
    return $result;
}

function checkRedirect($url)
{ 
    if (!empty($url)) {
        $check = parse_url($url);
        if ($check['host'] == $_SERVER['HTTP_HOST'] && isset($check['query'])) {
            $getURL = explode('&', $check['query']);
            $getParam = explode('=', $getURL[0]);
            $getPage = (!empty($getURL[1])) ? explode('page=', $getURL[1]) : '';
            switch ($getParam) {
                case ($getParam[0] === 'aid'):
                    $redirect = 'article.php?aid='.$getParam[1];
                    if (!empty($getPage[1])) {
                        $redirect = 'article.php?aid='.$getParam[1].'&page='.$getPage[1];
                    }
                    break;
                case ($getParam[0] === 'bid'):
                    $redirect = 'board.php?bid='.$getParam[1];
                    if (!empty($getPage[1])) {
                        $redirect = 'board.php?bid='.$getParam[1].'&page='.$getPage[1];
                    }
                    break;
                case ($getParam[0] === 'cid'):
                    $redirect = 'category.php?cid='.$getParam[1];
                    if (!empty($getPage[1])) {
                        $redirect = 'category.php?cid='.$getParam[1].'&page='.$getPage[1];
                    }
                    break;
                default:
                    $redirect = false;
                    break;
            }
        } else {
            $redirect = false;
        }
    } else {
        $redirect = false;
    }
    return $redirect;
}

function checkNotifLink($values = array(), $getArray = array(), $checkArray = array(), $system_timezone, $user_timezone)
{
    //exit(print_r($values));
    foreach ($values as $key1 => $value1) {
        if (!empty($value1)) {
            $replaceArray = array();
            foreach ($getArray as $key2 => $value2) {
                $replaceArray[$getArray[$key2]] = $value1[$getArray[$key2]];
                $values[$key1]['notif_link'] = replaceParam($checkArray[0], $replaceArray);
            }
            $values[$key1]['notif_date'] = getDateTime($system_timezone, $user_timezone, $value1['notif_date'], 'Y-m-d H:m');
        }
    }
    return $values;
}

function replaceParam($value, $param)
{
    foreach ($param as $index => $p) {
        $value = str_replace('(:'.$index.':)', $p, $value);
    }
    return $value;
}
