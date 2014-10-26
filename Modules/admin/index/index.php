<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 15:13
 */

namespace System\Modules;

use System\Libraries\Request;

//Request::load("Libraries/Spyc.php");

Request::load("Libraries/Module.php");

Request::load("Engines/Config.php");

use System\Engines;
use System\Libraries;

class index extends Module{

    private $FireWall;

    public function __construct(){



    }

    public function index(){

        echo "index:Hello World!";

    }

    public function hello(){

        $this->data["isim"] = "Musa";
        $this->data["soyisim"] = "ATALAY";

        return $this->render();

    }

    public function config(){

        $Config = new Engines\Config;

        $Conf = $Config->load("Modules/".Request::get("path")."/".Request::get("module")."/config.yml");

        print_r($Conf);

        return $this->render();

    }

    public function mysql_in(){

        $MySQL = (new Engines\MySQL())->connect([
            "host" => __MySQL_HOST__,
            "database" => __MySQL_DB__,
            "user" => __MySQL_USER__,
            "password" => __MySQL_PASS__
        ]);

        if(!$MySQL->Status()){

            exit($MySQL->ErrorHandler()->ErrorMessage());
          //exit("{$MySQL->ErrNo()} : {$MySQL->Error()} in {$MySQL->Err()->getFile()} on line: {$MySQL->Err()->getLine()}");

        }

        $User = $MySQL->table("users")->in([
            "/username" => "admin",
            "/password" => "66b65567cedbc743bda3417fb813b9ba"
        ]);

        if(!$User->Status()){

          exit("Error : ".$User->ErrorHandler()->ErrorMessage());

        }

        #var_dump($User->LastQuery()->fetchAll());

        echo "Hello World!";

    }

    public function session(){

        return false;

    }

    public function create_password(){

        $this->data["Password"] = (Request::post("pass")) ? md5(sha1(md5(Request::post("pass")))) : null;

        return $this->render();

    }

} 