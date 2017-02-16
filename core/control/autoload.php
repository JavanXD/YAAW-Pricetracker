<?php

/**
 * Diese Datei muss über einen Cron-Job jede Minute von einem Server aufgerufen werden
 * Damit die Preise in der Datenbank aktuell bleiben
 */

header( 'Content-type: text/html; charset=utf-8' );
$initime = time();

function shutdown($start)
{
    $dauer = time()-$start;
    echo "<p>" . microtime(true) . " - Das Skript wurde beendet. Dauer: $dauer secs</p>";
}
register_shutdown_function('shutdown' , $initime);
set_time_limit(55);


$i = 0;
while($i < 30)
{
    echo "$i <br />";
    $i++;
    sleep(1);

    //TODO: Abrufen der Amazon-Produkte und Eintragen der Preise in die DB
}


echo "<p>PHP Time: ".(time()-$initime)." secs </p>";
?>