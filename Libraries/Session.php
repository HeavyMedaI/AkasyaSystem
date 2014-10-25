<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 14:28
 */

namespace System\Libraries;

class Session {

    public function __construct(){

        if(!isset($_COOKIE[__PROJECT_NAME__])||empty($_COOKIE[__PROJECT_NAME__])||strlen($_COOKIE[__PROJECT_NAME__])<=8){

            setcookie(__PROJECT_NAME__, md5(sha1(md5(date("d-m-Y H:i:s")+time()+rand(1, 9999999999999999999)))));

        }

        //$this->Stream = new Libraries\Stream;

        $this->load();

    }

    private function load(){

        /*if($this->Stream->exist("Sessions/".@$_COOKIE[__PROJECT_NAME__])==FALSE){

             $this->Stream::create("Sessions/".@$_COOKIE[__PROJECT_NAME__]);

        }*/

        if(Stream::exist("Sessions/".@$_COOKIE[__PROJECT_NAME__])==FALSE){

            Stream::create("Sessions/".@$_COOKIE[__PROJECT_NAME__]);

        }

    }

    public static function  get(){


    }

} 