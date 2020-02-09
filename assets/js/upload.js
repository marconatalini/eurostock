import '../scss/upload.scss';

import 'typeahead.js';
import Bloodhound from 'bloodhound-js';
import 'bootstrap-tagsinput';

$(function() {

    // Bootstrap-tagsinput initialization
    // http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
    let $input = $('input[data-toggle="tagsinput"]');
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
            }
        });
    }
});

$('#image_imageFile').change(function () {
    console.log(this.value);
    let fileName = this.value.split('\\').pop();
    $('.custom-file-label').text(fileName);
});


$('select#image_category').change(function () {
    let category = this.value;

    let help_message = $('#image_category').data('help-message');

    let days;
    for (let i = 0; i < help_message.length; i++){
        let id = help_message[i].id;
        if (category == id){
            days = help_message[i].days_before_delete;
            break;
        }
    }

    $('#image_category_help').html(`La foto sarà disponibile per ${days} giorni.`);

});