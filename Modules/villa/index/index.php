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

        if(!$this->MySQL->Status()){

            exit($MySQL->ErrorHandler()->ErrorMessage());

        }

    }

    public function index(){

        $this->data["dump"] = $_GET;

        $Rezervasyonlar = $this->MySQL->select("/rezervasyon:COUNT(*), villa_id")->group("/villa_id")->limit("3");

        $SelectedRezervasyonlar = $Rezervasyonlar->execute();

        $PopulerRezervasyonlar = $Rezervasyonlar->fetchAll(Engines\MySQL::FETCH_OBJ);

        $Villalar = $this->MySQL->select("/villa:*")->where("(id:=:".$PopulerRezervasyonlar[0]->villa_id."||id:=:".$PopulerRezervasyonlar[1]->villa_id."||id:=:".$PopulerRezervasyonlar[2]->villa_id.")");

        $Villalar->execute();

        //var_dump($SelectedRezervasyonlar->row(1));

        $SelectedRezervasyonlar->row(1)->col("villa_id")->select("/villa:*/id:=:villa_id")->asc("/id");

        return $this->render();

    }

    public function test(){

        $a = "Foo moo boo tool foo";
        $b = preg_match_all("/[A-Za-z]oo\b/i", $a, $c);
        var_dump($b);

    }

} 