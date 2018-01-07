<?php

class Workaround
{
    public static $mysqli;
}

define('MYSQL_ASSOC', MYSQLI_ASSOC);

if (!function_exists('mysql_connect')) {
    function mysql_connect($host, $user, $pass)
    {
        return Workaround::$mysqli = mysqli_connect($host, $user, $pass);
    }
}

if (!function_exists('mysql_close')) {
    function mysql_close($link)
    {
        return mysqli_close($link);
    }
}

if (!function_exists('mysql_query')) {
    function mysql_query($query, $link = null)
    {
        if (null === $link) {
            return mysqli_query(Workaround::$mysqli, $query);
        } else {
            return mysqli_query($link, $query);
        }
    }
}

if (!function_exists('mysql_select_db')) {
    function mysql_select_db($dbname, $link)
    {
        return mysqli_select_db($link, $dbname);
    }
}

if (!function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($result, $type)
    {
        return mysqli_fetch_array($result, $type);
    }
}

if (!function_exists('mysql_num_rows')) {
    function mysql_num_rows($result)
    {
        return mysqli_num_rows($result);
    }
}

if (!function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($string, $link = null)
    {
        if (null === $link) {
            return mysqli_real_escape_string(Workaround::$mysqli, $string);
        } else {
            return mysqli_real_escape_string($link, $string);
        }
    }
}

if (!function_exists('mysql_errno')) {
    function mysql_errno($link = null)
    {
        if (null === $link) {
            return mysqli_errno(Workaround::$mysqli);
        } else {
            return mysqli_errno($link);
        }
    }
}

if (!function_exists('mysql_error')) {
    function mysql_error($link = null)
    {
        if (null === $link) {
            return mysqli_error(Workaround::$mysqli);
        } else {
            return mysqli_error($link);
        }
    }
}
