import '../scss/upload.scss';

import 'typeahead.js';
import Bloodhound from 'bloodhound-js';
import 'bootstrap-tagsinput';

$(function() {

    // Bootstrap-tagsinput initialization
    // http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
    let $input = $('input[data-toggle="tagsinput"]');

    let Name = "Unknown OS";
    if (navigator.userAgent.indexOf("Win") !== -1) Name = "Windows OS";
    if (navigator.userAgent.indexOf("Mac") !== -1) Name = "Macintosh";
    if (navigator.userAgent.indexOf("Linux") !== -1) Name = "Linux OS";
    if (navigator.userAgent.indexOf("Android") !== -1) Name = "Android OS";
    if (navigator.userAgent.indexOf("like Mac") !== -1) Name = "iOS";

    let keys = [13,188];

    if (Name === "Android OS") keys.push(55);

    if ($input.length) {
        let source = new Bloodhound({
            local: $input.data('tags'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            datumTokenizer: Bloodhound.tokenizers.whitespace
        });
        source.initialize();

        $input.tagsinput({
            trimValue: true,
            focusClass: 'focus',
            typeaheadjs: {
                name: 'tags',
                source: source.ttAdapter()
            },
            confirmKeys: keys
        });
    }
});

$('#image_imageFile_file').change(function () {
    // console.log(this.value);
    let fileName = this.value.split('\\').pop();
    $('.custom-file-label').text(fileName);
});


$('select#image_category').change(function () {
    let category = this.value;

    let help_message = $('#image_category').data('help-message');

    let days, descrizione;
    for (let i = 0; i < help_message.length; i++){
        let id = help_message[i].id;
        if (category == id){
            days = help_message[i].days_before_delete;
            descrizione = help_message[i].description;
            break;
        }
    }

    $('#image_category_help').html(`${descrizione}.\nLa foto sarà disponibile per ${days} giorni.`);

});