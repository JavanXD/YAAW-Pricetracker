<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 14.02.2017
 * Time: 15:28 Uhr
 */

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once("../functions/getAmazonASIN.php");
require_once("../functions/getAmazonPrice.php");
require_once("../functions/getAmazonRegion.php");
require_once("../functions/sendMail.php");
require_once ('../mysql.php');

if (isset($_REQUEST['product_url']) && !isset($_REQUEST['asin']))
{
    $url = $_REQUEST['product_url']; //http://www.amazon.com/gp/product/1491910291/ref=as_li_tl?ie=UTF8&camp=1789&creative=9325&creativeASIN=1491910291&linkCode=as2&tag=achgu-20&linkId=A3CZKDVUDYL7PUFB
    $ASIN = getAmazonASIN($url);
    $region = getAmazonRegion($url);
} else if (!isset($_REQUEST['product_url']) && isset($_REQUEST['asin']))
{
    $ASIN = $_REQUEST['asin'];
} else {
    $error["error"] = "Fehlende Parameter.";
    echo json_encode( $error );
    exit;
}

if (isset($_REQUEST['email']) && filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) && strlen($ASIN) == 10)
{
    $email = $mysqli->real_escape_string($_REQUEST['email']);
    $priceAlarm = isset($_REQUEST['priceAlarm']) ? floatval($_REQUEST['priceAlarm']) : 0.00;

    $result = $mysqli->query("SELECT UserID FROM Users WHERE Email='".$email."' LIMIT 0,1");
    $row_cnt = $result->num_rows;

    if($row_cnt == 0) {
        // first time created
        $mysqli->query("INSERT INTO Users (Email) VALUES ('".$email."')");
        $UserID = $mysqli->insert_id;

        $subject = "Yet Another AmazonWatcher";
        $message = 'Vielen Dank f&uuml;r deine Anmeldung auf <a href="https://www.yaaw.de/list.html?email='.$email.'"><strong>www.yaaw.de</strong></a>.';
        sendMail($email, $subject, $message);
    }else{
        $row = $result->fetch_assoc();
        $UserID = $row["UserID"];
    }

    $product = getAmazonPrice($region, $ASIN);
    if ($product != null && $product != false && $product['price'] != 0.00) {
        $mysqli->query("INSERT INTO Products (ASIN, Region, ProductCode, ProductTitle, ProductUrl, ProductImage) VALUES ('" . $ASIN . "', '" . $region . "', '" . $mysqli->real_escape_string($product['code']) . "', '" . $mysqli->real_escape_string($product['title']) . "', '" . $mysqli->real_escape_string($product['url']) . "', '" . $mysqli->real_escape_string($product['image']) . "') ON DUPLICATE KEY UPDATE ProductID=LAST_INSERT_ID(`ProductID`)");
        $ProductID = $mysqli->insert_id;
        if ($ProductID > 0) {
            $mysqli->query("INSERT INTO Prices (ProductID, Price) VALUES ( '" . $ProductID . "', '" . $product['price'] . "')");
            $mysqli->query("INSERT INTO Tracks (UserID, ProductID, PriceStarted, PriceAlarm) VALUES ('" . $UserID . "', '" . $ProductID . "', '" . $product['price'] . "', '" . $priceAlarm . "')");
            echo json_encode($product);
            exit;
        } else {
            $error["error"] = "Fehler beim Erstellen des Produkts.";
        }
    } else {
        $error["error"] = "Ups! Versuche es bitte gleich nochmal.";
    }

}else{
    $error["error"] = "Fehlende oder falsche Parameter.";
}

echo json_encode( $error );