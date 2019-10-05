/** 
 * Diese JavaScript Datei beinhaltet die AJAX-Kommunikation.
 * 
 * @projectname YAAW.de
 * @version 0.1
 * @author Javan Rasokat
 * @copyright 2017
 * 
 */

url = "https://www.yaaw.de";
path = url + "/core/control/";
 
 /**
 * Gibt den Cookiewert zurück.
 *
 * @param {string} cname - Name der Cookie.
 */
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

 /**
 * Setzen einer Cookie.
 *
 * @param {string} cname - Name der Cookie.
 * @param {string} cvalue - Wert der Cookie.
 * @param {string} exdays - Dauer der Cookie.
 */
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

 /**
 * Gibt den GET Parameterwert zurück.
 *
 * @param {string} name - Name des Parameters.
 */
$.urlParam = function(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return (results != null) ? results[1] || 0 : "undefined";
}

 /**
 * Prüft den Parameter auf Gültigkeit.
 *
 * @param {string} emailAddress - E-Mail-Adresse.
 */
function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

email = decodeURIComponent($.urlParam('email'));
urlIntent = decodeURIComponent($.urlParam('url'));

if (email == "undefined") {
    if (urlIntent != "undefined") {
        email = getCookie("email");
        if (email != "undefined") {
            location.href = "list.html?email=" + email + "&url=" + urlIntent;
        }else{
            location.href = "index.html?nocookie";
        }
    }else{
        location.href = "index.html?noemail";
    }
}

// redirect to login page
if ( !isValidEmailAddress( email ) ) 
{
    location.href = "index.html?notvalid";
}

// save email to cookie to remember login
setCookie("email", email, 32);

// insert a product by sharing
if (urlIntent != "undefined") 
{
    $('#product_url').val(urlIntent);
    $("html, body").animate({ scrollTop: $(document).height() }, "slow");
}

 /**
 * Sendet einen AJAX-Request auf den REST-Service.
 * Fügt das Produkt hinzu.
 *
 * @param {object} form - Beinhaltet die Eingabefelder aus dem Formular.
 * @param {object} event - Das durch den Klick ausgelöste Event.
 */
function addProduct(form, event) {
    event.preventDefault();

    var productLink = form.product_url.value;
    var priceAlarm = form.price.value;

    // remove warning
    $('input[name=poduct_url]').parent().removeClass("has-danger");
    $('input[name=price]').parent().removeClass("has-danger");

        // Fehlerhafte Eingabe optisch markieren
        if (productLink == "" && priceAlarm == ""){
            // both fields are empty
            $('input[name=product_url]').parent().addClass("has-danger");
            $('input[name=price]').parent().addClass("has-danger");

            alert("Produkt-URL und Wunsch-Preis eingeben.");
        }
        else if (productLink == ""){
            //product input is empty
            $('input[name=product_url]').parent().addClass("has-danger");
            alert("Produkt-URL eingeben.");
        }
        else if (priceAlarm == ""){
            // price input is empty
            $('input[name=price]').parent().addClass("has-danger");
            alert('Wunsch-Preis eingeben.');


        }else{
            // Ajax request auf back-end
            var addProduct = $.get(path + "addProduct.php", {
                email: email,
                product_url: productLink,
                priceAlarm: priceAlarm
            }, function (response) {

                if(typeof response["error"] != "undefined"){
                    alert(response["error"]);
                }else{
                    //addProductToLayout(response);
                    loadProductList();

                    // make fields empty
                    form.product_url.value = "";
                    form.price.value = "";

                }

            })
                .done(function () {
                    // remove warning
                    $('input[name=poduct_url]').parent().removeClass("has-danger");
                    $('input[name=price]').parent().removeClass("has-danger");
                })
                .fail(function () {
                    alert("Fehler beim Speichern. Überprüfe deine Eingabefelder.");
                });
        }

    return false;

}

 /**
 * Sendet einen AJAX-Request auf den REST-Service.
 * Entfernt ein Produkt aus der Produktliste.
 *
 * @param {int} TrackID - Die ID des Tracks.
 */
function removeProduct(TrackID) {

    var removeQuestion = window.confirm("Möchtest du die Überwachung wirklich beenden und das Produkt aus der Liste entfernen?");
    if (removeQuestion)
    {
        // Ajax request auf back-end
        var removeProduct = $.get(path + "removeProduct.php", {"email": email, TrackID: TrackID}, function () {
            loadProductList();
        })
            .done(function () {
            })
            .fail(function () {
                console.log("error: removeProduct");
            });
    }
}

 /**
 * Fügt ein Produkt dem Layout hinzu.
 *
 * @param {object} product - Das Produkt-Objekt.
 */
function addProductToLayout(product) {
    var favColor = "",
        priceInfo = "";
    if (product['IsFavorite'] == 1) {
        favColor = "text-danger";
    }
     if (product['IsMuted'] == 1) {
         muteColor = "fa-bell-slash";
     } else {
         muteColor = "fa-bell text-success";
     }
    priceInfo = ''
        +'Preishoch: <strong>' + product['Max'] + '</strong> <i class="fa fa-long-arrow-up text-danger" aria-hidden="true"></i><br class="hidden-sm-up" /> '
        +' Preistief: <strong>' + product['Min'] + '</strong> <i class="fa fa-long-arrow-down text-success" aria-hidden="true"></i><br class="hidden-sm-up" /> '
        +' Preismittel: <strong>' + product['AVG'] + '</strong> <b class="text-warning" aria-hidden="true">Ø</b> ';

    var card = ''
        +'<div class="card-header" role="tab" id="heading_' + product['TrackID'] + '"><h5 class="mb-0 text-truncate notranslate">'
        +'<button role="button" title="Beobachtung beenden" class="btn btn-sm btn-outline-secondary fa fa-remove" onclick="removeProduct(' + product['TrackID'] + ')"></button> '
        +'<button role="button" title="Favorit markieren" class="btn btn-sm btn-outline-secondary '+ favColor + ' fa fa-heart" onclick="favoriteTrack(' + product['TrackID'] + ')"></button> '
        +'<button role="button" title="E-Mail Benachrichtigung (de)aktivieren" class="btn btn-sm btn-outline-secondary ' + muteColor + ' fa" onclick="muteTrack(' + product['TrackID'] + ')"></button> '
        +'<a data-toggle="collapse" data-parent="#accordion" href="#collapse_' + product['TrackID'] + '" aria-expanded="false" aria-controls="collapse_' + product['TrackID'] + '">'
        +'' + product['ProductTitle'] + ''
        +'</a></h5></div>'
        +'<div id="collapse_' + product['TrackID'] + '" class="collapse" role="tabpanel" aria-labelledby="heading_' + product['TrackID'] + '"><div class="card-block">'
        +'<img src="' + product['ProductImage'] + '" alt="" class="pull-left amazon-image" />'
        +'<a href="' + decodeURIComponent(product['ProductUrl']) + '" target="_blank" rel="noreferrer noopener" class="text-muted">' + product['ProductTitle'] + '</a>'
        +'<p><i class="fa fa-play" aria-hidden="true"></i> Preis gestartet bei <strong>' + product['PriceStarted'] + ' ' + product['ProductCode'] + '</strong> '
        +'<br /><i class="fa fa-bell" aria-hidden="true"></i> Benachrichtigung bei <strong>' + product['PriceAlarm'] + ' ' + product['ProductCode'] + '</strong></p>'
        +'<a href="' + decodeURIComponent(product['ProductUrl']) + '" target="_blank" rel="noreferrer" class="btn btn-outline-secondary text-success pull-right"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Zum Angebot</a>'
        +'<p>' + priceInfo + '</p>'
        +'<button onclick="loadChart(' + product['ProductID'] + ', ' + product['TrackID'] + ');$(this).hide();" role="button" class="btn btn-outline-secondary text-warning"><i class="fa fa-line-chart" aria-hidden="true"></i> Preisverlauf anzeigen</button>'

        +'<div id="chart_' + product['TrackID'] + '" class="chart"></div>'
        +'</div></div>';

    $("#accordion").append('<div class="card" id="card_' + product['TrackID'] + '">' + card + '</div>');
}

 /**
 * Aktualisieren der Produktliste.
 *
 */
function loadProductList() {

    var loadProductList = $.get(path + "getProductList.php", {email: email}, function (response) {
        if(typeof response["error"] != "undefined"){
            console.log(response["error"]);

            if(response["error"] == "Empty.")
            {
                $(".card").remove();
            }
        }else{
            $("#accordion").empty();
            $.each(response, function (index) {
                addProductToLayout(this);
            });
        }
    })
        .done(function () {

        })
        .fail(function () {
            console.log("error: loadProductList");
        });
}

 /**
 * Sendet einen AJAX-Request auf den REST-Service.
 * Favorisieren eines Tracks.
 *
 * @param {int} TrackID - Die ID des Tracks.
 */
function favoriteTrack(TrackID) {

    // Ajax request auf back-end
    var favoriteTrack = $.get( path + "favoriteTrack.php", { "email": email, TrackID: TrackID }, function(response) {
        if(typeof response["IsFavorite"] != "undefined"){

        }
    })
        .done(function() {
            loadProductList();
        })
        .fail(function() {
            console.log("error: favoriteTrack");
        });
}

/**
 * Sendet einen AJAX-Request auf den REST-Service.
 * Stummschalten eines Tracks.
 *
 * @param {int} TrackID - Die ID des Tracks.
 */
function muteTrack(TrackID) {

    // Ajax request auf back-end
    var muteTrack = $.get( path + "muteTrack.php", { "email": email, TrackID: TrackID}, function(response) {
        if(typeof response["IsMuted"] != "undefined"){

        }
    })
        .done(function() {
            loadProductList();
        })
        .fail(function() {
            console.log("error: muteTrack");
        });
}

 /**
 * Liest ein JSON-Objekt aus einem REST-Service um daraus das Preisverlauf-Diagramm mittels Google Charts zu zeichnen.
 *
 * @param {int} TrackID - Die ID des Tracks.
 * @param {int} ProductID - Die ID des Produkts.
 */
function loadChart(ProductID, TrackID) {

    // Create the data table.
    var jsonData = $.ajax({
        url: path + "getProductPrices.php",
        data: {
            "ProductID": ProductID
        },
        dataType: "json",
        async: false
    }).responseText;

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages': ['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(function() { drawChart(jsonData, ProductID, TrackID); });

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.

    function drawChart(jsonData, ProductID, TrackID) {

        // Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);

        // Set chart options
        var options = {
            title: 'Preisverlauf',
           // width: 500,
            height: 100,
            legend : { position: "none"},
            legend: 'none',
            explorer: {
                axis: 'horizontal',
                keepInBounds: true,
                maxZoomIn: 4.0
            },
            colors: ['#FF9F0E']
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('chart_' + TrackID));
        chart.draw(data, options);
    }

    // draw chart again if window is resized
    $(window).resize(function(){

        if ( document.getElementById('chart_' + TrackID).innerHTML.length > 10 )
        {   // only if chart is opened
            drawChart(jsonData, ProductID, TrackID);
        }
    });
}


/* Document is loaded */
$(function() {
    loadProductList();

});

/* Loading image während eines AJAX Aufruf der zB. etwas länger brauch */
$(document).on({
    ajaxStart: function () {
        $(".loader").show();
    },
    ajaxStop: function () {
        $(".loader").hide('slow');
    }
});