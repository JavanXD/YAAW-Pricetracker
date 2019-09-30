<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 03.04.2017
 * Time: 11:13 Uhr
 */

header('Content-type:application/json;charset=utf-8');

require_once("functions/getAmazonASIN.php");
require_once("functions/getAmazonPrice.php");
require_once("functions/getAmazonRegion.php");
require_once ('mysql.php');

/**
 * Teste Methoden getAmazonASIN, getAmazonRegion, getAmazonPrice
 */

// prüfe 10mal hintereinander
for ($i = 0; $i < 10; $i++)
{

    $url = "http://www.amazon.com/gp/product/1491910291/ref=as_li_tl?ie=UTF8&camp=1789&creative=9325&creativeASIN=1491910291&linkCode=as2&tag=achgu-20&linkId=A3CZKDVUDYL7PUFB";
    
    $ASIN = getAmazonASIN($url);
    var_dump($ASIN);

    $region = getAmazonRegion($url);
    var_dump($region);

    $product = getAmazonPrice($region, $ASIN);
    var_dump($product);

    // warte 1sec vor dem nächsten Test
    sleep(1);
}