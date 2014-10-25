<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 19:50
 */

namespace System\Libraries;


//use System\Libraries\Request;

class Response {

    public function __construct(){



    }

    public static function render(Array $Data, $Themplate = NULL){

        extract($Data);

        require_once "Modules/".$Themplate.".html";

    }

} 