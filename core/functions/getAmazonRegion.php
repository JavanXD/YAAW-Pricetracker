<?php

function getAmazonRegion($url){
    $host = parse_url($url, PHP_URL_HOST);
    if (mb_strpos($host, ".com") !== false) {
        return "com";
    }else if (mb_strpos($host, ".de") !== false) {
        return "de";
    }else{
        return "de";
    }
}