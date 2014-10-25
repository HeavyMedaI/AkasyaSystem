<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 14:28
 */

namespace System\Libraries;

\System\Libraries\Request::load("Libraries/Spyc.php");

class Session {

    public function __construct(){



    }

    public static function start(){

        //$this->Stream = new Libraries\Stream;

        if(!isset($_COOKIE[__PROJECT_NAME__])||empty($_COOKIE[__PROJECT_NAME__])||strlen($_COOKIE[__PROJECT_NAME__])<=8){

            setcookie(__PROJECT_NAME__, md5(sha1(md5(date("d-m-Y H:i:s")+time()+rand(1, 9999999999999999999)))));

        }

        self::load();

    }

    private static function load(){

        /*if($this->Stream->exist("Sessions/".@$_COOKIE[__PROJECT_NAME__])==FALSE){

             $this->Stream::create("Sessions/".@$_COOKIE[__PROJECT_NAME__]);

        }*/

        if(Stream::exist("Sessions/".@$_COOKIE[__PROJECT_NAME__])==FALSE){
                       echo "ss";            Stream::create("Sessions/".@$_COOKIE[__PROJECT_NAME__]);

            $Session = array(
                "session" => array(
                    "login" => array(
                        "set" => true
                    )
                )
            );

            Session::set($Session);

            return false;

        }

    }

    public static function get($Path){

        $Tree = explode("/", ltrim($Path, "/"));

        $Stream = Spyc::YAMLLoadString(Stream::read("Sessions/".@$_COOKIE[__PROJECT_NAME__]));

        $Return = $Stream;

        foreach($Tree as $Session){

             $Return = $Return[$Session];

        }

        return $Return;

    }

    public static function set($Data){

        $Streaming = Spyc::YAMLDump($Data, 4, 60);

        return Stream::write(Stream::stream("Sessions/".@$_COOKIE[__PROJECT_NAME__], "STREAM_C"), $Streaming);

    }

} 