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

    public function session(){

        return false;

    }

} 