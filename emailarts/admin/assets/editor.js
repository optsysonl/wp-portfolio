(function ($) {

    'use strict';

    $(document).ready(function () {

        formPage();

        generateForm();

        settingsPage();
    });

    function formPage(){
        if($('#form-editor-tabs').length == 0){
            return false;
        }

        main();
        function main(){
            handlers();
        }

        function handlers(){
            $('#api-list').on('change', function(){
                $('#button-apply-list-changes').show();
            });
            $('#button-apply-list-changes').on('click', function(){
                apply_list_changes();
            });
            $('#form-editor-tabs li:not(.disabled)').on('click', function () {
                if(!$(this).hasClass('active')) {
                    $('#form-editor-tabs li').removeClass('active');
                    $(this).addClass('active');
                    $('.form-editor-panel.displayed').removeClass('displayed');
                    $('#' + $(this).data('id')).addClass('displayed');
                }
            });
            $('.form-tag-item').on('click', function(e){
                e.preventDefault();

                var tag_shortcode = $(this).data('shortcode');
                var text = $('#wpea-form').text();

                $('#wpea-form').text(text + tag_shortcode);

            })
        }

        function apply_list_changes(){
            jQuery.ajax({
                url: ajaxurl,
                method: 'post',
                data: {
                    action: 'wpea_form_apply_list_changes',
                    post_ID: $('#post_ID').val(),
                    list_ID: $('#api-list').val()
                },
                dataType: 'json',
                success: function (response) {
                    try{
                        console.log('apply_list_changes');
                        console.log(response)

                        $('.form-configuration-step-2').show();
                        console.log(response.template);
                        $('#form-configuration-step-2-fields').empty().append(response.template);

                        // form.find('.notifications').removeClass('error updated');
                        // form.find('.notifications').addClass(response.result);
                        // form.find('.notifications').empty().text(response.message);
                        //
                        // if(response.result === 'updated') {
                        //     form.find('.api-status').hide();
                        //     form.find('.api-status.not-connected').show();
                        // }
                    }catch(error){
                        console.log(error);
                    }
                },
                fail: function (err) {
                    alert("There was an error: " + err);
                }
            });
        }

    }

    function settingsPage(){
        var form = $('#wpea-api-connection');
        form.on('submit', function(e){
            e.preventDefault();
            jQuery.ajax({
                url: ajaxurl,
                method: 'post',
                data: {
                    action: 'wpea_save_api_connection_credentials',
                    public_key: $('#wpea-global-settings-public-key').val(),
                    private_key: $('#wpea-global-settings-private-key').val()
                },
                dataType: 'json',
                success: function (response) {
                    try{
                        console.log(response)

                        form.find('.notifications').removeClass('error updated');
                        form.find('.notifications').addClass(response.result);
                        form.find('.notifications').empty().text(response.message);

                        if(response.result === 'updated') {
                            form.find('.api-status').hide();
                            form.find('.api-status.connected').show();

                            // $('#wpea-form').val(response.template)
                            // console.log(response)
                        }
                    }catch(error){
                        console.log(error);
                    }
                },
                fail: function (err) {
                    alert("There was an error: " + err);
                }
            });
        });
        $('#remove-api-connection').on('click', function(e){
            e.preventDefault();
            jQuery.ajax({
                url: ajaxurl,
                method: 'post',
                data: {
                    action: 'wpea_remove_api_connection_credentials'
                },
                dataType: 'json',
                success: function (response) {
                    try{
                        console.log(response)

                        form.find('.notifications').removeClass('error updated');
                        form.find('.notifications').addClass(response.result);
                        form.find('.notifications').empty().text(response.message);

                        if(response.result === 'updated') {
                            form.find('.api-status').hide();
                            form.find('.api-status.not-connected').show();
                        }
                    }catch(error){
                        console.log(error);
                    }
                },
                fail: function (err) {
                    alert("There was an error: " + err);
                }
            });
        })
    }

    function generateForm(){
        $('#generate_form').on('click', function(){
            jQuery.ajax({
                url: ajaxurl,
                method: 'post',
                data: {
                    action: 'wpea_create_form',
                    form_id: $('#post_ID').val()
                },
                dataType: 'json',
                success: function (response) {
                    try{
                        if(response.result === 'success') {
                            $('#wpea-form').val(response.template)
                        }
                    }catch(error){
                        console.log(error);
                    }
                },
                fail: function (err) {
                    alert("There was an error: " + err);
                }
            });
        });
    }

})(jQuery);