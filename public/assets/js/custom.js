'use strict';
$(document).ready(function() {

    // validazione form
    $('.form-validation').validate();

    $('.form-mailinglist').validate({
        rules: {
            file: {
                extension: "xls|xlsx"
            }
        }
    });

    // ckeditor
    /*ClassicEditor
        .create( document.querySelector( '#editor' ),{
            ckfinder: {
                uploadUrl: '/image-upload?_token=' + $('meta[name="csrf-token"]').attr('content'),
            },
            toolbar: {
                items: [
                    'undo', 'redo',
                    '|',
                    'heading',
                    '|',
                    'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                    '|',
                    'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                    '|',
                    'link', 'uploadImage', 'blockQuote', 'codeBlock',
                    '|',
                    'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent'
                ],
                shouldNotGroupWhenFull: false
            }
        })
        .catch( error => {
        console.error( error );
        });*/

    $(function() {
        $('.select2').select2();
    });

    // anteprima newsletter
    $('#newsletter').on('change', function() {

        var newsletter = $(this).val();

        $.ajax({
            url:"/get-newsletter",
            method:"GET",
            data: {newsletter: newsletter},
            success:function(data){
                //alert(data)
                //return false;

                $('#newsletterPreview').html(data.corpo);
                $('#oggetto').val(data.oggetto);

            }

        });

    })

    // newsletter singola (solo per avere la ricerca)
    $('#newsletter').select2({
        width: '100%',
        placeholder: 'Seleziona newsletter',
        allowClear: true
    });

    // mailinglists multi
    $('#mailinglists').select2({
        width: '100%',
        placeholder: $('#mailinglists').data('placeholder') || 'Seleziona mailing list',
        allowClear: true,
        closeOnSelect: false
    });

});
