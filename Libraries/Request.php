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
use \System\Libraries;

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

    public static function login($Path = NULL){

        $Path = ($Path) ? $Path : Request::get("app");

        Response::render(array(), $Path."/login/index");

        exit;

    }

    public static function isPage($Object, $Method){

       if(method_exists($Object, $Method)){

           return TRUE;

        }

        return FALSE;

    }

    public static function snapshot($Path){

        Request::load($Path);

    }

    public static function module($Module = NULL){

        Libraries\Session::start();

        if(!Request::load("Modules/".Request::get("app")."/".Request::get("module")."/".Request::get("module").__EXTENSION__)){

            Request::error(404);

            exit;

        }

        Request::load("Modules/".Request::get("app")."/config.inc");

        $FireWall =  new Engines\FireWall;

        $Conf = new Engines\Config;

        $ConfigApp = $Conf->load("Modules/".Request::get("app")."/config.yml");

        Request::load("Modules/".Request::get("app")."/".Request::get("module")."/config.inc");

        $ConfigModule = $Conf->load("Modules/".Request::get("app")."/".Request::get("module")."/config.yml");

        /*if($Conf->isPrivate()){

            Request::login(Request::get("app"));

            return FALSE;

        }*/

        if($Module != NULL){

            $Module = explode("/", $Module);

            $LoadModule = new $Module[0];

            $Command = ($Module[1]) ? $Module[1] : "index";

            if(!Request::isPage($LoadModule, $Command)){

                Request::error(404);

                exit;

            }

            $Render = $LoadModule->$Command();

            if(is_array($Render)){

                Response::render(
                    $Render,
                    Request::get("app")."/".Request::get("module")."/".Request::get("command")
                );

            }

            /*if($Render){

                Response::render(
                    $Render,
                    Request::get("app")."/".Request::get("module")."/".Request::get("command")
                );

            }*/

            return true;

        }

        $M =  "\\System\\Modules\\".Request::get("module");

        $LoadModule = new $M;

        $Command = (Request::get("command")) ? Request::get("command") : "index";

        if(!Request::isPage($LoadModule, $Command)){

            Request::error(404);

            exit;

        }

        $Render = $LoadModule->$Command();

        if($Render){

            Response::render(
                $Render,
                Request::get("app")."/".Request::get("module")."/".Request::get("command")
            );

        }

        return TRUE;

    }

    public static function error($ErrCode){

        if(Request::load("Modules".Request::get("app")."/error/".$ErrCode.".html")){

            return TRUE;

        }

        return Request::load("Modules/error/".$ErrCode.".html");

    }

} 