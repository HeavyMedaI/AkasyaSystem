<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 20:41
 */

namespace System\Modules;

use System\Libraries\Request;
use System\Engines;
use System\Libraries;

Request::load("Libraries/Module.php");

class villa extends Module {

    private $MySQL;

    public function __construct()
    {

        $this->MySQL = (new Engines\MySQL())->connect([
            "host" => __MySQL_HOST__,
            "database" => __MySQL_DB__,
            "user" => __MySQL_USER__,
            "password" => __MySQL_PASS__
        ]);

        $this->MySQL->character("utf8");

        if (!$this->MySQL->Status()) {

            exit($MySQL->ErrorHandler()->ErrorMessage());

        }

        $Config = $this->getSystemConfig("/config");

        $this->data["config"]["title"] = $Config["project_name"];
        $this->data["config"] = array_merge($this->data["config"], $Config["administrator"]);

    }

    public function index(){

        $this->data["Villalar"] = $this->MySQL->select("/villa")->asc("/id")->execute(["fetch" => "all"], true);

        return $this->render();

    }

    public function form(){

        return $this->render();

    }

    public function set(){

       if(Request::isPost("id")){

           return $this->update();

       }

       return $this->insert();

    }

    public function insert(){
        
        $Values = "/";

        /*foreach (Request::post() as $col => $val) {



        }*/

        $Insert = $this->MySQL->insert("/villa")->data("/name:Deneme;aciklama:Deneme");


    }

    public function update(){



    }

    public function sil(){

        $Update = $this->MySQL->query("DELETE FROM villa WHERE id = ".Request::post("id"));

        $Res = array("response" => false);

        if($Update){

            $Res = array("response" => true);

        }

        Libraries\Response::header("json");

        Libraries\Response::json($Res);

    }

    public function active(){

        $Update = $this->MySQL->query("UPDATE villa SET active = 1 WHERE id = ".Request::post("id"));

        $Res = array("response" => false);

        if($Update){

            $Res = array("response" => true);

        }

        Libraries\Response::header("json");

        Libraries\Response::json($Res);

    }

    public function deactive(){

        $Update = $this->MySQL->query("UPDATE villa SET active = 0 WHERE id = ".Request::post("id"));

        $Res = array("response" => false);

        if($Update){

            $Res = array("response" => true);

        }

        Libraries\Response::header("json");

        Libraries\Response::json($Res);

    }

    public function test(){

        $Insert = $this->MySQL->insert("/villa")->data("/name::Deneme Adı;;description::Deneme Açıklama");

        var_dump($Insert->execute());

    }

} 