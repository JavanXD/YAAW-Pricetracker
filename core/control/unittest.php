<?php

header('Content-type:application/json;charset=utf-8');

require_once ("../functions/getAmazonASIN.php");
require_once ("../functions/getAmazonPrice.php");
require_once ("../functions/getAmazonRegion.php");
require_once ('../mysql.php');

/**
 * Test methods getAmazonASIN, getAmazonRegion, getAmazonPrice
 */

// repeat your tests
for ($i = 0; $i < 1; $i++)
{

    $url = "https://www.amazon.de/Amazon-Fire-TV-Stick-4K-Ultra-Hd-Alexa-Sprachfernbedienung-Alexa/dp/B079QHMFWC/ref=as_li_ss_tl?__mk_de_DE=%C3%85M%C3%85%C5%BD%C3%95%C3%91&keywords=fire+tv+stick&qid=1569871128&s=gateway&sr=8-2&linkCode=ll1&tag=yaaw-21&linkId=436f40f5871021a4100c6eba1c75548c&language=de_DE";
    
    $ASIN = getAmazonASIN($url);
    var_dump($ASIN);

    $region = getAmazonRegion($url);
    var_dump($region);

    $product = getAmazonPrice($region, $ASIN);
    var_dump($product);

    // wait before next test
    sleep(1);
}