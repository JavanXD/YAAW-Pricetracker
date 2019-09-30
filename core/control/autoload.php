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
 * This file must be called every minute by a server via a cron job
 * To keep the prices in the database up to date
 */

$initime = time();

require_once ('../mysql.php');
require_once ('../functions/sendMail.php');
require_once ('../functions/getAmazonPrice.php');


$sql = "SELECT Tracks.TrackID, Tracks.ProductID, Tracks.LastEmail, Tracks.LastEmailPrice, Products.ProductTitle, Products.ProductUrl, Products.ASIN, Products.Region, Tracks.PriceAlarm, Users.Email 
        FROM Tracks, Users, Products
        WHERE Products.ProductID = Tracks.ProductID
        AND Users.UserID = Tracks.UserID
        AND IsActive = 1
        AND Products.LastUpdate < UNIX_TIMESTAMP()-15*60
        ORDER BY Products.LastUpdate ASC
        LIMIT 0,15";
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

            // sende Email nach erreichen des Wunschpreises
            if($product['price'] <= $row["PriceAlarm"])
            {
                // sende Email nur alle 24h und wenn Benachrichtigungen aktiviert sind
                if ($row["LastEmail"] < time()-24*60*60 && $row["IsMuted"] != 0)
                {
                    // sende Email nur wenn sich der Preis von der letzten Email geändert hat
                    if ($product['price'] != $row["LastEmailPrice"])
                    {
                        $message = createMessage($row, $product);
                        sendMail($row["Email"], $row["ProductTitle"], $message);
                        echo "<p>" . $product['price'] . " <= " . $row["PriceAlarm"] . " - Sende Email an " . $row['Email'] . "</p>";
                        $mysqli->query("UPDATE Tracks SET LastEmail=UNIX_TIMESTAMP(), LastEmailPrice='" . $product['price'] . "' WHERE TrackID='" . $row["TrackID"] . "'");
                    }else{
                        echo "<p>" . $product['price'] . " <= " . $row["PriceAlarm"] . " - Email wurde bereits versandt. (" . $row['Email'] . ")</p>";
                    }

                }else{
                    echo "<p>" . $product['price'] . " <= " . $row["PriceAlarm"] . " - Email wurde bereits versandt. (" . $row['Email'] . ")</p>";
                }
            }
        }else {
            echo "<p>" . $row["ProductID"] . "/" . $row["TrackID"] . "  - Fehler beim Aufruf der Amazon API.</p>";
        }
        $mysqli->query("UPDATE Products SET LastUpdate=UNIX_TIMESTAMP() WHERE ProductID='" . $row["ProductID"] . "'");

        flush();
        ob_flush();
        usleep(90000);
    }
    echo "<p>Finished.</p>";
} else {
    echo "<p>Empty.</p>";
}


echo "<p>PHP Time: ".(time()-$initime)." secs </p>";
?>

</body>
</html>
