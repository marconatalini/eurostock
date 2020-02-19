// any CSS you import will output into a single css file (app.css in this case)
import '../scss/app.scss';

// loads the Bootstrap jQuery plugins

import 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle';
import 'jquery-zoom/jquery.zoom.min';
import {ajax} from "jquery";
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

$(function() {
    $('.pop').on('click', function() {
        // $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        let img_url = $(this).attr('data-src');
        let img_id = $(this).attr('data-image-id');
        $('.imagepreview').attr('src', img_url);

        //zoom
        $('span.photo').zoom({url: img_url});
        $('#imagemodal').modal('show');

        ajax({url: "/image/view/" + img_id, success: function(result){
            // console.log(result, "#views_" + img_id);
            $('#views_' + img_id).html(result);
        }});

    });
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})