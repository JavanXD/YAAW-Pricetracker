<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 14.02.2017
 * Time: 15:19 Uhr
 */

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
    $message .= '<p style="font-size:18px;"><a href="' . $row["ProductUrl"] . '">' . $row["ProductTitle"] . '</a> wurde günstiger und ist nun für einen Preis von <strong>' . $product['price'] . ' ' . $row["ProductCode"] . '</strong> zu haben.</p>';
    $message .= '<p><img src="' . $row["ProductImage"] . '" alt="Produktbild" title="' . $row["ProductTitle"] . '"/></p>';
    $message .= '<p><a href="https://www.yaaw.de/list.html?email=' . urlencode($row["Email"]) . '">Alle Preisverläufe auf YAAW.de ansehen.</a></p>';
    $message .= '<p><a href="https://www.yaaw.de/core/control/removeProduct.php?email=' . urlencode($row["Email"]) . '&TrackID=' . $row["TrackID"] . '&mute=false&directAccess">Produkt aus Überwachungs-Liste endgültig entfernen.</a></p>';
    $message .= '<p><a href="https://www.yaaw.de/list.html?email=' . urlencode($row["Email"]) . '&product_url=' . $row["ProductUrl"] . '#add">Dieses Produkt mit neuer Preisschwelle der Überwachung hinzufügen.</a></p>';
    $message .= '<p><a href="https://www.yaaw.de/core/control/muteTrack.php?email=' . urlencode($row["Email"]) . '&TrackID=' . $row["TrackID"] . '&mute=true&directAccess">Benachrichtigungen für dieses Produkt dauerhaft stummschalten.</a></p>';
    $message .= '<p><a href="https://www.yaaw.de/core/control/muteTrack.php?email=' . urlencode($row["Email"]) . '&TrackID=' . $row["TrackID"] . '&mute=false&directAccess">Benachrichtigungen (wieder) aktivieren.</a></p>';



    return $message;
}