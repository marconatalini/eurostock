import '../scss/upload.scss';

import 'typeahead.js';
import Bloodhound from 'bloodhound-js';
import '../js/bootstrap-tagsinput'


$(function() {

    let $input = $('#image_tags');

    let engine = new Bloodhound({
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        local: $input.data('tags')
    });
    engine.initialize();

    $input.tagsinput({
        trimValue: true,
        focusClass: 'focus',
        typeaheadjs: {
            name: 'tags',
            source: engine.ttAdapter()
        },
    });

    let description = $('#image_description');
    let default_txt = description.attr('data-default');
    description.val(default_txt);
});


//aggiungo il nome del file selezionato
$('#image_imageFile_file').change(function () {
    let fileName = this.value.split('\\').pop();
    $('.custom-file-label').text(fileName);
});


$('select#image_category').change(function () {
    let category = this.value;

    let help_message = $('#image_category').data('help-message');

    let days, descrizione, safe;
    for (let i = 0; i < help_message.length; i++){
        let id = help_message[i].id;
        if (category == id){
            days = help_message[i].days_before_delete;
            descrizione = help_message[i].description;
            safe = help_message[i].safe;
            break;
        }
    }

    if (safe === true){
        $('#image_category_help').html(`${descrizione}.\nLa foto sarà sempre disponibile.`);
    } else {
        $('#image_category_help').html(`${descrizione}.\nLa foto sarà disponibile per ${days} giorni.`);
    }

});