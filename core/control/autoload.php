<?php
header('Content-Type: text/html; charset=utf-8');

// I think maybe you can set output_buffering using ini_set here, but I'm not sure.
// It didn't work for me the first time at least, but now it does sometimes...
// So I set output_buffering to Off in my php.ini,
// which normally, on Linux, you can find at the following location: /etc/php5/apache2/php.ini

@ini_set('output_buffering','Off');
@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1);
@ob_end_clean();
set_time_limit(0);
ob_start();

echo str_repeat('        ',1024*8); //<-- For some reason it now even works without this, in Firefox at least?
?>
<!DOCTYPE html>
<html>
<head>
    <title>Autoload</title>
</head>
<body>

<?php

/**
 * Diese Datei muss über einen Cron-Job jede Minute von einem Server aufgerufen werden
 * Damit die Preise in der Datenbank aktuell bleiben
 */

$initime = time();

require_once ('../mysql.php');
require_once ('../functions/sendMail.php');
require_once ('../functions/getAmazonPrice.php');


$sql = "SELECT Tracks.TrackID, Products.ProductID, Products.ProductTitle, Products.ProductUrl, Products.ASIN, Products.Region, Tracks.PriceAlarm, Users.Email 
        FROM Tracks, Users, Products, Prices
        WHERE Products.ProductID = Tracks.ProductID
        AND Users.UserID = Tracks.UserID
        AND Prices.ProductID = Products.ProductID
        AND IsActive = 1
        AND Tracks.TrackID != ''
        GROUP BY Tracks.TrackID
        ORDER BY Prices.Time ASC
        LIMIT 0,20";
$result = $mysqli->query($sql);

if ($result->num_rows > 0)
{
    ob_flush();
    flush();
    // output data of each row
    while ($row = $result->fetch_assoc())
    {
        $product = getAmazonPrice($row["Region"], $row["ASIN"]);
        if($product != null && $product != false && $product['price'] != 0.00)
        {
            $mysqli->query("INSERT INTO Prices (ProductID, Price) VALUES ('" . $row["ProductID"] . "', '" . $product['price'] ."')");
            $PricesID = $mysqli->insert_id;
            echo "<p>" . $row["ProductID"] . "/" . $row["TrackID"] . "  - Neuen Preis hinzugefügt: " . $product['price'] . " (" . $row['Email'] . ")</p>";
            if($product['price'] <= $row["PriceAlarm"])
            {
                $subject = $row["ProductTitle"];
                $message = '<a href="' . $row["ProductUrl"] . '">' . $row["ProductTitle"] . '</a> wurde günstiger und ist nun für einen Preis von <strong>'.$product['price'].'</strong> zu haben.';
                sendMail($row["Email"], $subject, $message );
                echo "<p>" . $product['price'] . " < " . $row["PriceAlarm"] . " - Sende Email an " . $row['Email'] . "</p>";
            }
        }else {
            echo "<p>" . $row["ProductID"] . "/" . $row["TrackID"] . "  - Fehler beim Aufruf der Amazon API.</p>";
        }
        flush();
        ob_flush();
        usleep(50000);
    }
    echo "<p>Finished.</p>";
} else {
    echo "<p>Empty.</p>";
}


echo "<p>PHP Time: ".(time()-$initime)." secs </p>";
?>

</body>
</html>
