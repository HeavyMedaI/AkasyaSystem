<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 14:58
 */

namespace System\Libraries;


class Stream {

    public function __construct(){



    }

    public static function exist($Path){

        if(file_exists($Path)){

            return TRUE;

        }

        return FALSE;

    }

    public static function create($Path){

        return fopen($Path, "x+");

    }

} 