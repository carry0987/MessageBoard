<?php
if (defined('IN_ADMIN') !== true) {
    exit('Access Denied');
}

function isAdmin($identity, $lang_admin, $lang_member)
{
    $user_identity = ($identity === 1) ? $lang_admin : $lang_member;
    return $user_identity;
}

function checkURLRewrite()
{
    if (in_array('mod_rewrite', apache_get_modules()) === true) {
        return true;
    } else {
        return false;
    }
}

function generatePassword($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function checkEmailValid($email)
{
    return preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email);
}

function checkTypeChange($change_type, $type_array)
{
    $result = $type_array[0];
    foreach ($type_array as $key) {
        if ($key === $change_type) {
            $result = $key;
        }
    }
    return $result;
}

function checkApplyChange($change_apply)
{
    foreach ($change_apply as $key => $value) {
        if ($value === 'enable') {
            $result[] = $key;
        } else {
            $result[] = '';
        }
    }
    return $result;
}

function checkDatabaseSize($connectdb, $db_name)
{
    $connectdb->select_db($db_name);
    $q = $connectdb->query('SHOW TABLE STATUS');
    $size = 0;
    while ($row = $q->fetch_array()) {
        $size += $row['Data_length'] + $row['Index_length'];
    }
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
