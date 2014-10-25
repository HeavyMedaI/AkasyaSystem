<?php

namespace System\Config;

class Config{

    public static $private = TRUE;

}

use System\Libraries\Request;

Request::load("Engines/FireWall.php");