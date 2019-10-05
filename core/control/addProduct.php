<?php

require_once ('../mysql.php');

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: ' . CORS);

require_once("../functions/getAmazonASIN.php");
require_once("../functions/getAmazonPrice.php");
require_once("../functions/getAmazonRegion.php");
require_once("../functions/sendMail.php");

if (isset($_REQUEST['product_url']) && !isset($_REQUEST['asin']))
{
    $url = $_REQUEST['product_url']; // e.g. https://www.amazon.de/Amazon-Fire-TV-Stick-4K-Ultra-Hd-Alexa-Sprachfernbedienung-Alexa/dp/B079QHMFWC/ref=as_li_ss_tl?__mk_de_DE=%C3%85M%C3%85%C5%BD%C3%95%C3%91&keywords=fire+tv+stick&qid=1569871128&s=gateway&sr=8-2&linkCode=ll1&tag=yaaw-21&linkId=436f40f5871021a4100c6eba1c75548c&language=de_DE
    $ASIN = getAmazonASIN($url);
    $region = getAmazonRegion($url);
} else if (!isset($_REQUEST['product_url']) && isset($_REQUEST['asin']))
{
    $region = "de";
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

        $subject = "Anmeldung";
        $message = welcomeMessage($email);
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

// Send Result
echo json_encode( $error );
