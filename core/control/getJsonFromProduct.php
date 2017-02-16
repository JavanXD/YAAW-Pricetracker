<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 14.02.2017
 * Time: 13:11 Uhr
 */
header('Content-type:application/json;charset=utf-8');

require_once("../functions/getAmazonASIN.php");
require_once("../functions/getAmazonPrice.php");

if (isset($_REQUEST['url']) && !isset($_REQUEST['asin']))
{
    $url = $_REQUEST['url']; //http://www.amazon.com/gp/product/1491910291/ref=as_li_tl?ie=UTF8&camp=1789&creative=9325&creativeASIN=1491910291&linkCode=as2&tag=achgu-20&linkId=A3CZKDVUDYL7PUFB
    $asin = getAmazonASIN($url);
} else if (!isset($_REQUEST['url']) && isset($_REQUEST['asin']))
{
    $asin = $_REQUEST['asin'];
} else {
    $error["error"] = "para error";
    echo json_encode( $error );
    exit;
}



if (strlen($asin) == 10)
{
    // Region code and Product ASIN
    $response = getAmazonPrice("com", $asin);
    echo json_encode($response);
} else {
    $error["error"] = "asin error";
    echo json_encode( $error );
    exit;
}
