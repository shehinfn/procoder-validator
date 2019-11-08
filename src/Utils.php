<?php
/**
 * Created by PhpStorm.
 * User: shehin
 * Date: 20/10/19
 * Time: 5:51 PM
 */

namespace shehin\procodervalidator;


class Utils
{
    public static function arrayToText($array)
    {
        $last  = array_slice($array, -1);
        $first = join(', ', array_slice($array, 0, -1));
        $both  = array_filter(array_merge(array($first), $last), 'strlen');
        return join(' and ', $both);
        
    }


    public static function ordinalSuffix($n)
    {
        return date('S',mktime(1,1,1,1,( (($n>=10)+($n>=20)+($n==0))*10 + $n%10) ));

    }


}