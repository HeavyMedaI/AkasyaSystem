<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 30.10.2014
 * Time: 12:39
 */

namespace System\Modules;

use System\Libraries\Request;

Request::load("Libraries/Module.php");

use System\Engines;
use System\Libraries;

class index extends Module {

    private $MySQL;

    public function __construct(){

        $this->MySQL = (new Engines\MySQL())->connect([
            "host" => __MySQL_HOST__,
            "database" => __MySQL_DB__,
            "user" => __MySQL_USER__,
            "password" => __MySQL_PASS__
        ]);

        $this->data["Date"] = new Libraries\Date;

        $this->MySQL->character("utf8");

        if(!$this->MySQL->Status()){

            exit($MySQL->ErrorHandler()->ErrorMessage());

        }

        $Config = $this->getSystemConfig("/config");

        $this->data["config"]["title"] = $Config["project_name"];
        $this->data["config"] = array_merge($this->data["config"], $Config["administrator"]);
        $this->data["config"]["about"] = $this->MySQL->select("/icerikler:*")->where("/name:=:hakkimizda")->where("active:=:1")->asc("/id")->execute(["fetch" => "first"], true)->text;
        $this->data["config"]["genel_aciklama"] = $this->MySQL->select("/icerikler:*")->where("/name:=:genel_aciklama")->where("active:=:1")->asc("/id")->execute(["fetch" => "first"], true)->text;
        $this->data["config"]["ekstra"] = $this->MySQL->select("/icerikler:*")->where("/name:=:ekstra")->where("active:=:1")->asc("/id")->execute(["fetch" => "first"], true)->text;
        $this->data["config"]["etkinlikler"] = $this->MySQL->select("/etkinlikler:*")->where("/active:=:1")->asc("id")->execute(["fetch"=>"all"], true);

        return $this;

    }

    public function index(){

        Request::load("Modules/villa/class/villa.php");

        $Villa = new \Villa;

        $Rezervasyonlar = $this->MySQL->select("/rezervasyon:COUNT(*) AS TOPLAM, villa_id")->where("/approved:=:1")->group("/villa_id")->asc("TOPLAM")->limit("0;3");

        $fetchRezervasyonlar = $Rezervasyonlar->execute();

        $this->data["Villa"] = array();

        for($i=0;$i<$fetchRezervasyonlar->rowCount();$i++){

            $Data = $fetchRezervasyonlar->row($i)->col("villa_id")->select("/villa:*/id:=:villa_id")->execute(["fetch"=>"first"], true);

            $this->data["Villa"][] = array(
                "data" => $Data,
                "attr" => $Villa->ozellikler($Data)
            );

        }
        /*foreach ($Rezervasyonlar->execute(null, true) as $Row) {

            $this->data["Villa"][] = $this->MySQL->select("/villa:*")->where("(id:=:".$Row->villa_id.")")->execute(["fetch"=>"first"], true);

        }*/

        //var_dump($this->data["Villa"][1]);

        Libraries\Response::header("html");

        return $this->render();

    }

    public function villalar(){

        Request::load("Modules/villa/class/villa.php");

        $Villa = new \Villa;

        $Data = $this->MySQL->select("/villa:*")->asc("id")->execute(["fetch"=>"all"], true);

        foreach ($Data as $villa) {

            $this->data["Villa"][] = array(
                "data" => $villa,
                "attr" => $Villa->ozellikler($villa)
            );

        }

        return $this->render();

    }

    public function detay(){

        Request::load("Modules/villa/class/villa.php");

        $Villa = new \Villa;

        $Data = $this->MySQL->select("/villa:*")->asc("id")->execute(["fetch"=>"all"], true);

        foreach ($Data as $villa) {

            $this->data["Villa"][] = array(
                "data" => $villa,
                "attr" => $Villa->ozellikler($villa)
            );

        }

        $DetayData = $this->MySQL->select("/villa:*")->where("/;&&;id:=:".Request::get("villa_id"))->asc("/id")->execute(["fetch" => "first"], true);

        $this->data["Detay"] = array(
            "data" => $DetayData,
            "attr" => $Villa->ozellikler($DetayData)
        );

        $this->data["VillaGaleri"] = $this->MySQL->select("/resimler")->where("/ref_id:=:".Request::get("villa_id"))->where("type:=:villa")->asc("id")->execute(["fetch" => "all"], true);

        return $this->render();

    }

    public function galeri(){

        $this->data["Resim"] = array();

        $Resimler = $this->MySQL->select("/resimler")->asc("id")->execute();

        for($i=0;$i<$Resimler->rowCount();$i++){

            $this->data["Resim"][] = array(
                "resim" => $Resimler->row($i)->get(),
                "ref" => $Resimler->row($i)->col("type/ref_id")->select("{type}:*/id:=:ref_id")->execute(["fetch" => "first"], true)
            );

        }

        return $this->render();

    }

    public function iletisim(){

        return $this->render();

    }

    public function test(){

        #$date = new Libraries\Date;

        #var_dump($date->tarihFormatla("2014-11-26 12:26:00", "readable"));

        var_dump(Libraries\Date::tarihFormatla("2014-11-26 12:26:00", "readable"));

    }

} 