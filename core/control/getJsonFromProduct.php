<?php

header('Content-type:application/json;charset=utf-8');

require_once("../functions/getAmazonASIN.php");
require_once("../functions/getAmazonRegion.php");
require_once("../functions/getAmazonPrice.php");

if (isset($_REQUEST['product_url']) && !isset($_REQUEST['asin']))
{
    $url = $_REQUEST['product_url'];
    $asin = getAmazonASIN($url);
    $region = getAmazonRegion($url);
} else if (!isset($_REQUEST['product_url']) && isset($_REQUEST['asin']))
{
    $asin = $_REQUEST['asin'];
    $region = "de";
} else {
    $error["error"] = "para error";
    echo json_encode( $error );
    exit;
}

if (strlen($asin) == 10)
{
    $response = getAmazonPrice($region, $asin);
    echo json_encode($response);
} else {
    $error["error"] = "asin error";
    echo json_encode($error);
    exit;
}
