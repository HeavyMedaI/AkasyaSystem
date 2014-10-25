<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 20:41
 */

namespace System\Modules;
\System\Libraries\Request::load("Libraries/Module.php");

class villa extends Module {

    public function villaekle(){

        $this->data["villa_adi"] = "Deneme Villa";

        return $this->render();

    }

} 