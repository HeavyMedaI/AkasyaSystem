<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 25.10.2014
 * Time: 19:04
 */

namespace System\Modules;

abstract class Module {

    protected $data = array();

    protected function render(){

        return $this->data;

    }

} 