<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 14:22
 */

namespace System\Engines;

\System\Libraries\Request::load("Engines/MySQL.php");

use \System\Libraries;

class FireWall {

    //private $Session;
    private $Database;

    public function __construct(){

        //$this->Session = new Libraries\Session;
        $this->Database = new MySQL;

    }

    public function isLogged(){

        if(
            //$this->Session->get("/session/login/set") == TRUE &&
            Session::get("/session/login/set") == TRUE &&
            $this->Database->in([
                "/users/username" => Libraries\Session::get("/session/login/username"),
                "/users/password" => Libraries\Session::get("/session/login/password")
            ])
        ){

           return TRUE;

        }

        return FALSE;

    }

} 