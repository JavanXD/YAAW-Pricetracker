<?php

function sendMail($to, $pSubject, $pMessage) {

    $subject = 'YAAW.de - ' . $pSubject;
    $from = 'AmazonWatcher@yaaw.de';

    $message = '<html><body>';
    $message .= '' . $pMessage . '';
    $message .= '</body></html>';

    // To send HTML mail, the Content-type header must be set
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
    $headers .= "X-MSMail-Priority: High" . "\r\n";
    $headers .= "From: $from" . "\r\n" .
        "Reply-To: $from" . "\r\n" .
        "Return-Path: $from" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();
    // Sending email
    return mail($to, $subject, $message, $headers);
}

function createMessage($row, $product) {

    $message = '<h1 style="color:#f40;">Neuer Preisalarm auf Amazon'.$row["Region"].'!</h1>';
    $message .= '<p><a href="' . $row["ProductUrl"] . '" rel="noreferrer noopener">' . $row["ProductTitle"] . '</a>" wurde günstiger und ist nun für einen Preis von <strong>' . $product['price'] . ' ' . $row["ProductCode"] . '</strong> zu haben.</p>';
    $message .= '<p><img src="' . $row["ProductImage"] . '" alt="Produktbild" title="' . $row["ProductTitle"] . '"/></p>';
    $message .= '<p><a href="' . WATCHER_URI  . '/list.html?email=' . urlencode($row["Email"]) . '">Alle Preisverläufe auf YAAW.de ansehen.</a></p>';
    $message .= '<p><a href="' . WATCHER_URI  . '/core/control/removeProduct.php?email=' . urlencode($row["Email"]) . '&TrackID=' . $row["TrackID"] . '&mute=false&directAccess">Überwachung für dieses Produkt entfernen.</a></p>';
    $message .= '<p><a href="' . WATCHER_URI  . '/list.html?email=' . urlencode($row["Email"]) . '&product_url=' . $row["ProductUrl"] . '#add">Dieses Produkt mit neuer Preisschwelle der Überwachung hinzufügen.</a></p>';
    $message .= '<p><a href="' . WATCHER_URI  . '/core/control/muteTrack.php?email=' . urlencode($row["Email"]) . '&TrackID=' . $row["TrackID"] . '&mute=true&directAccess">Benachrichtigungen für dieses Produkt stummschalten.</a></p>';
    $message .= '<p><a href="' . WATCHER_URI  . '/core/control/muteTrack.php?email=' . urlencode($row["Email"]) . '&TrackID=' . $row["TrackID"] . '&mute=false&directAccess">Benachrichtigungen (wieder) aktivieren.</a></p>';

    return $message;
}

function welcomeMessage($email) {

    $message = '<p>Vielen Dank f&uuml;r Deine Anmeldung auf deinem Preiswächter für Amazon <a href="' . WATCHER_URI  . '/list.html?email='.$email.'"><strong>YAAW.de</strong></a>.<br>Um Deine beobachten Produkte zu verwalten, kannst du einfach auf <a href="' . WATCHER_URI  . '/list.html?email='.$email.'">den Link</a> klicken.</p>';
    $message .= '<p>Das warst nicht Du? Dann schreib uns eine Mail.</p>';
    return $message;
}