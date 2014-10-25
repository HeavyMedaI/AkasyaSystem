<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 15:13
 */

namespace System\Modules;

\System\Libraries\Request::load("Libraries/Module.php");

//use \System\Engines\FireWall;
//use System\Libraries\Request;

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

} 