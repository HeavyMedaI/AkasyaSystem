<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 15:11
 */
namespace System;

use System\Libraries\Request;

require_once "config.inc";

Request::load("Modules/".Request::get("path")."/".Request::get("module")."/config.inc");

if(!Request::load("Modules/".Request::get("path")."/".Request::get("module")."/".Request::get("module").__EXTENSION__)){

    Request::error(404);

    exit;

}

Request::module("\\System\\Modules\\".Request::get("module")."/".Request::get("command"));