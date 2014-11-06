<?php
/**
 * Created by PhpStorm.
 * User: musaatalay
 * Date: 1.11.2014
 * Time: 10:39
 */

class Villa {

    public function __construct(){}

    public function ozellikler($List){

        $Attributes = array(
            "wifi" => "WiFi",
            "havuz" => "Havuz",
            "uydu" => "Uydu TV",
            "sicak_su" => "7/24 Sıcak Su",
            "lcd_tv" => "LCD TV",
            "yatak_sayisi" => "{{ADET}} Adet Yatak",
            "jakuzi" => "Jakuzi"
        );

        $Attr = array();

        foreach ($List as $col => $val) {

            if(!isset($Attributes[$col])){

                continue;

            }

            if($val==1||$col=="yatak_sayisi"){

                if($col=="yatak_sayisi"){

                    $Attr[] = str_replace("{{ADET}}", $val, $Attributes[$col]);

                }else{

                    $Attr[] = $Attributes[$col];

                }

            }

        }

        return $Attr;

    }

} 