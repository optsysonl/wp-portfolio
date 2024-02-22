(function($){
    $(document).ready(function(){
        rc_plugin();

        reviews_carousel();
    });

    /**
     * @name reviews_carousel
     * @returns {boolean}
     */
    function reviews_carousel(){
        if($('.rc-review-carousel').length == 0){
            return false;
        }

        var options = {
            container: null,
            config: {
                dots: false,
                autoplay: false,
                arrows:false,
                autoplaySpeed: 3000,
                infinite: true,
                pauseOnHover: false,
                responsive: [
                    {
                        breakpoint: 980,
                        settings: {
                            slidesToShow: 2,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                        }
                    },
                ],
                slidesToShow: 2,
                slidesToScroll: 1,
                speed: 300,
            }
        };

        main();

        function main(){
            options.container = $('.rc-review-carousel');

            init_carousel();
        }
        function init_carousel(){
            options.container.each(function(){
                let opts = options.config;
                let data = $(this).data();

                opts.autoplay = (data.autoplay == 1) ? true : false;
                opts.autoplaySpeed = data.autoplayspeed;
                opts.slidesToShow = data.columns;
                opts.speed = data.speed;
                opts.arrows = (data.displaynavigation == 1) ? true : false;

                $(this).slick(opts);
            });
        }
    }

    function rc_plugin(){
        var options = {
            rc_data: rc_data,
            toggle_popup_button: null,
            is_popup: false,
            popup: null,
            popup_overlay: null,
            close_btn: null,
            active: {
                item: null,
                item_content: null,
                step: 0
            }
        };
        main();

        function main(){
            options.is_popup = ($('.rc-popup-container').length > 0) ? true : false;

            // if(options.is_popup){
                options.toggle_popup_button = $('.rc-popup-button');
                options.popup = $('.rc-popup-container');
                options.popup_overlay = $('.rc-popup-overlay');
                options.close_btn = options.popup.find('.rc-close');
            // }
            svg_activate('img[src*=".svg"]');



            handlers();
        }

        function handlers(){
            options.toggle_popup_button.on('click', function () {

                if ($(this).parents().is('.rc-with-content-open-popup')) {
                    $('.rc-first-button-container').hide();
                    $('.with-leave-a-review-button .rc-popup-content').show();
                } else {
                    clearForm();
                    options.popup.toggleClass('rc-visible');
                    options.popup_overlay.toggleClass('rc-visible');
                }
            });
            options.close_btn.on('click', function(){
                options.popup.removeClass('rc-visible');
                options.popup_overlay.removeClass('rc-visible');
                clearForm();
            });

            $('.rc-star-item').on('mouseover', function(){
                let parent = $(this).parent();
                let rating = $(this).data('rating');
                $(this).addClass('item-on-active');
                parent.find('.rc-star-item').each(function(){
                    if($(this).data('rating')-0 < rating-0){
                        $(this).addClass('item-on-active');
                    }
                })
            });
            $('.rc-star-item').on('mouseleave', function(){
                $('.rc-star-item.item-on-active').removeClass('item-on-active')
            });

            $('.rc-stars-container-inner').on('click', '.rc-star-item', function(){
                clearForm();
                let self = $(this);
                let _rating = self.data('rating');
                let _action = 'get_variants';

                options.active.item = (self.parents('.rc-shortcode-content').length > 0) ? self.parents('.rc-shortcode-content') : self.parents('.rc-popup-container');
                options.active.item_content = (self.parents('.rc-shortcode-content').length > 0) ? options.active.item : options.active.item.find('.rc-inner');
                options.active.step = 0;

                let data = {
                    action: _action,
                    nonce: options.rc_data.nonce,
                    rating: _rating
                };
                _ajax(data, function(response){
                    options.active.item_content.find('[data-item-step="0"]').hide();
                    options.active.step = options.active.step + 1;
                    let step = $(response);
                    options.active.item.find('[data-item-step="1"]').empty().append(step);
                    options.active.item_content.find('[data-item-step="1"]').show();
                });
            });

            $('body').on('click', '.get-social-form', function(){
                _ajax({
                    action: 'get_social_form',
                    nonce: options.rc_data.nonce
                }, function(response){
                    options.active.step = options.active.step + 1;
                    let step = $(response);
                    options.active.item.find('[data-item-step="1"]').empty().append(step);
                    options.active.item_content.find('[data-item-step="1"]').show();
                });
            });
            $('body').on('click', '.get-our-site-form', function(){
                let rating = $('.rc-review-from').attr("data-rating");
                _ajax({
                    rating: rating,
                    action: 'get_base_form',
                    nonce: options.rc_data.nonce
                }, function(response){
                    options.active.step = options.active.step + 1;
                    let step = $(response);
                    options.active.item.find('[data-item-step="1"]').empty().append(step);
                    options.active.item_content.find('[data-item-step="1"]').show();
                });
            });
            $('body').on('submit', '#base-review-form', function(e){
                 e.preventDefault();

                 let without_errors = true;

                $('#base-review-form input, #base-review-form textarea').each(function(){
                    $(this).parent('.rc-form-row').removeClass('error');
                    if($(this).val().trim() == ''){
                        without_errors = false;
                        $(this).parent('.rc-form-row').addClass('error');
                    }
                });
                 if(without_errors) {
                    _ajax({
                        action: 'save_review',
                        nonce: options.rc_data.nonce,
                        data: $('#base-review-form').serializeArray()
                    }, function (response) {
                        options.active.step = options.active.step + 1;
                        let step = $(response);
                        options.active.item.find('[data-item-step="1"]').empty().append(step);
                        options.active.item_content.find('[data-item-step="1"]').show();
                    });
                 }
            });
        }

        function clearForm(){
            $('.rc-inner *[data-item-step]').each(function(){
                let self = $(this);
                if(self.data('item-step') !== 0){
                    self.hide();
                }else{
                    self.show();
                }
            });
        }

        /**
         * @name _ajax
         * @param data
         * @param callback
         * @private
         */
        function _ajax(data, callback){
            $.ajax({
                type: 'post',
                url: options.rc_data.ajax_url,
                data: data,
                dataType: 'json',
                success:  function( response ) {
                    if(typeof(callback) === 'function'){
                        callback(response);
                    }
                }
            });
        }

        function show_popup(){
            options.popup.addClass('rc-visible');
        }
        function hide_popup(){
            options.popup.removeClass('rc-visible');
        }
        function svg_activate(string){
            jQuery(string).each(function(){
                var $img = jQuery(this);
                var imgID = $img.attr('id');
                var imgClass = $img.attr('class');
                var imgURL = $img.attr('src');

                jQuery.get(imgURL, function(data) {
                    // Get the SVG tag, ignore the rest
                    var $svg = jQuery(data).find('svg');

                    // Add replaced image's ID to the new SVG
                    if(typeof imgID !== 'undefined') {
                        $svg = $svg.attr('id', imgID);
                    }
                    // Add replaced image's classes to the new SVG
                    if(typeof imgClass !== 'undefined') {
                        $svg = $svg.attr('class', imgClass+' replaced-svg');
                    }

                    // Remove any invalid XML tags as per http://validator.w3.org
                    $svg = $svg.removeAttr('xmlns:a');

                    // Replace image with new SVG
                    $img.replaceWith($svg);

                }, 'xml');
            });
        }
    }
})(jQuery);