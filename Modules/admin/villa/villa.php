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

        $Res = array("response" => false, "insert_id" => false);

        $Values = "/";

        foreach (Request::post() as $col => $val) {

            if(empty($val)||$val==null||strlen($val)<=0){

                continue;

            }

            $Values .= "{$col}::{$val};;";

        }

        $Insert = $this->MySQL->insert("/villa")->data($Values);

        $Res["response"] = $Insert->execute();

        $Res["insert_id"] = ($Res["response"]) ? $Insert->insertId() : false;

        Libraries\Response::header("json");

        Libraries\Response::json($Res);


    }

    public function update(){

        $ThumbnailDir = "_assets/images/rooms/";

    }

    public function uploadGallery(){

        $StoreFolder = '../../villa/index/_assets/images/gallery/';

        $tempFile = Request::file("file")['tmp_name'];

        $targetPath = dirname( __FILE__ ) ."/". $StoreFolder;

        $targetFile =  $targetPath.Request::file("file")['name'];

        $Upload = move_uploaded_file($tempFile,$targetFile);

    }

    public function addGallery(){

        $StorePath = '_assets/images/gallery/';

        $Res = array("response" => false);

        $Villa = $this->MySQL->select("/villa:*name/id:=:".Request::post("villa_id"))->asc("/id")->execute(["fetch" => "first"], true);

        $ImageSrc = "/ref_id::".Request::post("villa_id").";;src::".$StorePath.Request::post("file_name");
        $ImageSrc .= ";;alt::".$Villa->name.";;title::".$Villa->name;

        $Insert = $this->MySQL->insert("/resimler")->data($ImageSrc);

        $Res["response"] = $Insert->execute();

        Libraries\Response::header("json");

        Libraries\Response::json($Res);

    }

    public function removeGallery(){

        exit(json_encode(array("response" => true)));

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

        $Insert = $this->MySQL->insert("/villa")->data("/name::Deneme Adı;;description::Deneme Açıklama")->execute();

        var_dump($Insert);

    }

} 