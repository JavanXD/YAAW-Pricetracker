<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 22.02.2017
 * Time: 00:44 Uhr
 */

function getAmazonRegion($url){
    $host = parse_url($url, PHP_URL_HOST);
    if (mb_strpos($host, ".com") !== false) {
        return "com";
    }else if (mb_strpos($host, ".de") !== false) {
        return "de";
    }else{
        return "com";
    }
}