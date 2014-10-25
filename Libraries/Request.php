<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 15:27
 */

namespace System\Libraries;

use \System\Config;
use \System\Engines;

class Request {

    public function __construct(){

    }

    public static function get($Index){

        if(isset($_GET[$Index])&&!empty($_GET[$Index])){

            return $_GET[$Index];

        }

        return FALSE;

    }

    public static function post($Index){

        if(isset($_POST[$Index])&&!empty($_POST[$Index])){

            return $_POST[$Index];

        }

        return FALSE;

    }

    public static function load($Path){

        if(Stream::exist($Path)){

            require_once $Path;

            return TRUE;

        }

        return FALSE;

    }

    public static function module($Module = NULL){

        /*$FireWall =  new Engines\FireWall;

        if(Config\Config::$private){



        }*/

        if($Module != NULL){

            $Module = explode("/", $Module);

            $LoadModule = new $Module[0];

            $Module[1] = ($Module[1]) ? $Module[1] : "index";

            Response::render(
                $LoadModule->$Module[1](),
                Request::get("path")."/".Request::get("module")."/".Request::get("command")
            );

            return TRUE;

        }

        $M =  "\\System\\Modules\\".Request::get("module");

        $LoadModule = new $M;

        $Command = Request::get("command");

        Response::render(
            $Command(),
            Request::get("path")."/".Request::get("module")."/".Request::get("command")
        );

        return TRUE;

    }

    public static function error($ErrCode){

        require_once "Modules/error/".$ErrCode.".html";

    }

} 