<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 14:58
 */

namespace System\Libraries;


class Stream {

    private static $StreamTypes = array(
        "STREAM_C" => "w",
        "STREAMING_O" => "r",
        "STREAMING" => "r+",
        "WRITE_O" => "a",
    );

    public function __construct(){


    }

    public static function stream($Path, $StreamType){

        return fopen($Path, self::$StreamTypes[$StreamType]);

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

    public static function read($Source){

        return file_get_contents($Source);

    }

    public static function  write($Stream, $Data){

        fwrite($Stream, $Data);

    }

} 