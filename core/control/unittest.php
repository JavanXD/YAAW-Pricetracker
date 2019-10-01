<?php
/**
 * This file should not be deployed into production.
 * It is usabe to test PHP Version and to check if the most required functions are enabled.
 */

error_reporting(E_ALL);

header('Content-type:text/plain;charset=utf-8');

require_once ("../functions/getAmazonASIN.php");
require_once ("../functions/getAmazonPrice.php");
require_once ("../functions/getAmazonRegion.php");
require_once ('../mysql.php');

/**
 * Test php functions
 */
if ( !function_exists( 'mail' ) )
{
    echo 'mail() has been disabled';
}
if ( !function_exists('curl_setopt') OR !function_exists('curl_setopt')  ) {
    echo 'Requires cURL and CLI installations.' ; exit ;
}

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

/**
 * Test sending mail
 */
// To send HTML mail, the Content-type header must be set
$from = 'AmazonWatcher@yaaw.de';
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
$headers .= "X-MSMail-Priority: High" . "\r\n";
$headers .= "From: $from" . "\r\n" .
    "Reply-To: $from" . "\r\n" .
    "Return-Path: $from" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();
$mailed = mail("AmazonWatcher@yaaw.de", "Unittest - Success", "Yes, someone did a unittest.php", $headers);
var_dump($mailed);