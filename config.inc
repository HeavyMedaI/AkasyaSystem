<?php

ob_start();

error_reporting(E_ALL);

const __EXTENSION__ = ".php";
const __PROJECT_NAME__ = "AkasyaWeb";

require_once "Libraries/Request.php";
require_once "Libraries/Response.php";
require_once "Libraries/Stream.php";
require_once "Libraries/Session.php";

require_once "Engines/MySQL.php";
require_once "Engines/FireWall.php";