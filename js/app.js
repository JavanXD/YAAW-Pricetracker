/**
 * Created by Javan on 21.02.2017.
 */

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return (results != null) ? results[1] || 0 : "undefined";
}

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

url = "https://www.yaaw.de";
path = url + "/core/control/";
email = decodeURIComponent($.urlParam('email'));

if(email == "undefined") {
    location.href = "index.html?noemail";
}

if( !isValidEmailAddress( email ) ) {
    location.href = "index.html?notvalid";
}

// save email to cookie to remember login
setCookie("email", email, 32);

function addProduct(form, event) {
    event.preventDefault();

    var productLink = form.product_url.value;
    var priceAlarm = form.price.value;

    if (productLink != "" && priceAlarm != ""){
    // Ajax request auf back-end
        var addProduct = $.get(path + "addProduct.php", {
            email: email,
            product_url: productLink,
            priceAlarm: priceAlarm
        }, function (response) {

            if(typeof response["error"] != "undefined"){
                console.log(response["error"]);
            }else{
                //addProductToLayout(response);
                loadProductList();
            }

        })
            .done(function () {
                // make fields empty
                form.product_url.value = "";
                form.price.value = "";

                // remove warning
                $('input[name=product_url]').parent().removeClass("has-danger");
                $('input[name=price]').parent().removeClass("has-danger");
            })
            .fail(function () {
                alert("error");
            });
    }else{
        // Fehlerhafte Eingabe optisch markieren
        if(productLink == "" && priceAlarm == ""){
            // both fields are empty
            $('input[name=product_url]').parent().addClass("has-danger");
            $('input[name=price]').parent().addClass("has-danger");
            alert("Produkt-URL und Wunsch-Preis eingeben.");
        }
        else if(productLink == ""){
            //product input is empty
            $('input[name=product_url]').parent().addClass("has-danger");
            alert("Produkt-URL eingeben.");
        }
        else if(priceAlarm == ""){
            // price input is empty
            $('input[name=price]').parent().addClass("has-danger");
            alert('Wunsch-Preis eingeben.');

        }
    }
    return false;

}
function removeProduct(TrackID) {

    // Ajax request auf back-end
    var removeProduct = $.get( path + "removeProduct.php", { TrackID: TrackID }, function() {
        loadProductList();
    })
        .done(function() {
        })
        .fail(function() {
            alert( "error" );
        });
}

function addProductToLayout(product) {
    var card = ''
        +'<div class="card-header" role="tab" id="heading_' + product['TrackID'] + '"><h5 class="mb-0 text-truncate">'
        +'<button role="button" class="btn btn-outline-secondary fa fa-remove" onclick="removeProduct(' + product['TrackID'] + ')"></button> '
        +'<a data-toggle="collapse" data-parent="#accordion" href="#collapse_' + product['TrackID'] + '" aria-expanded="false" aria-controls="collapse_' + product['TrackID'] + '">'
        +'' + product['ProductTitle'] + ''
        +'</a></h5></div>'
        +'<div id="collapse_' + product['TrackID'] + '" class="collapse" role="tabpanel" aria-labelledby="heading_' + product['TrackID'] + '"><div class="card-block">'
        +'<img src="' + product['ProductImage'] + '" alt="" class="pull-left" />'
        +'<a href="https://anon.click/?' + decodeURIComponent(product['ProductUrl']) + '" target="_blank" class="text-muted">' + product['ProductTitle'] + '</a>'
        +'<p>Preis gestartet bei ' + product['PriceStarted'] + ' ' + product['ProductCode'] + ' | Benachrichtigung bei ' + product['PriceAlarm'] + ' ' + product['ProductCode'] + '</p>'
        +'</div></div>';

    $("#accordion").append('<div class="card" id="card_' + product['TrackID'] + '">' + card + '</div>');
}

function loadProductList() {

    var loadProductList = $.get(path + "getProductList.php", {email: email}, function (response) {
        if(typeof response["error"] != "undefined"){
            console.log(response["error"]);
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
            alert("error");
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