(function ($){

    'use strict';

    if ( typeof wpea === 'undefined' || wpea === null ) {
        return;
    }

    wpea = $.extend( {
        cached: 0,
        inputs: []
    }, wpea );

    $( function() {
        wpea.supportHtml5 = ( function() {
            var features = {};
            var input = document.createElement( 'input' );

            features.placeholder = 'placeholder' in input;

            var inputTypes = [ 'email', 'url', 'tel', 'number', 'range', 'date' ];

            $.each( inputTypes, function( index, value ) {
                input.setAttribute( 'type', value );
                features[ value ] = input.type !== 'text';
            } );

            return features;
        } )();

        $( 'div.wpea > form' ).each( function() {
            var $form = $( this );
            wpea.initForm( $form );

            if ( wpea.cached ) {
                wpea.refill( $form );
            }
        } );
    } );

    wpea.initForm = function( form ) {
        var $form = $( form );

        wpea.setStatus( $form, 'init' );

        $form.submit( function( event ) {
            if ( ! wpea.supportHtml5.placeholder ) {
                $( '[placeholder].placeheld', $form ).each( function( i, n ) {
                    $( n ).val( '' ).removeClass( 'placeheld' );
                } );
            }

            if ( typeof window.FormData === 'function' ) {
                wpea.submit( $form );
                event.preventDefault();
            }
        } );

        $( '.wpea-submit', $form ).after( '<span class="ajax-loader"></span>' );

        wpea.toggleSubmit( $form );

        $form.on( 'click', '.wpea-acceptance', function() {
            wpea.toggleSubmit( $form );
        } );

        // Exclusive Checkbox
        $( '.wpea-exclusive-checkbox', $form ).on( 'click', 'input:checkbox', function() {
            var name = $( this ).attr( 'name' );
            $form.find( 'input:checkbox[name="' + name + '"]' ).not( this ).prop( 'checked', false );
        } );

        // Free Text Option for Checkboxes and Radio Buttons
        $( '.wpea-list-item.has-free-text', $form ).each( function() {
            var $freetext = $( ':input.wpea-free-text', this );
            var $wrap = $( this ).closest( '.wpea-form-control' );

            if ( $( ':checkbox, :radio', this ).is( ':checked' ) ) {
                $freetext.prop( 'disabled', false );
            } else {
                $freetext.prop( 'disabled', true );
            }

            $wrap.on( 'change', ':checkbox, :radio', function() {
                var $cb = $( '.has-free-text', $wrap ).find( ':checkbox, :radio' );

                if ( $cb.is( ':checked' ) ) {
                    $freetext.prop( 'disabled', false ).focus();
                } else {
                    $freetext.prop( 'disabled', true );
                }
            } );
        } );

        // Placeholder Fallback
        if ( ! wpea.supportHtml5.placeholder ) {
            $( '[placeholder]', $form ).each( function() {
                $( this ).val( $( this ).attr( 'placeholder' ) );
                $( this ).addClass( 'placeheld' );

                $( this ).focus( function() {
                    if ( $( this ).hasClass( 'placeheld' ) ) {
                        $( this ).val( '' ).removeClass( 'placeheld' );
                    }
                } );

                $( this ).blur( function() {
                    if ( '' === $( this ).val() ) {
                        $( this ).val( $( this ).attr( 'placeholder' ) );
                        $( this ).addClass( 'placeheld' );
                    }
                } );
            } );
        }

        if ( wpea.jqueryUi && ! wpea.supportHtml5.date ) {
            $form.find( 'input.wpea-date[type="date"]' ).each( function() {
                $( this ).datepicker( {
                    dateFormat: 'yy-mm-dd',
                    minDate: new Date( $( this ).attr( 'min' ) ),
                    maxDate: new Date( $( this ).attr( 'max' ) )
                } );
            } );
        }

        if ( wpea.jqueryUi && ! wpea.supportHtml5.number ) {
            $form.find( 'input.wpea-number[type="number"]' ).each( function() {
                $( this ).spinner( {
                    min: $( this ).attr( 'min' ),
                    max: $( this ).attr( 'max' ),
                    step: $( this ).attr( 'step' )
                } );
            } );
        }

        // Character Count
        wpea.resetCounter( $form );

        // URL Input Correction
        $form.on( 'change', '.wpea-validates-as-url', function() {
            var val = $.trim( $( this ).val() );

            if ( val
                && ! val.match( /^[a-z][a-z0-9.+-]*:/i )
                && -1 !== val.indexOf( '.' ) ) {
                val = val.replace( /^\/+/, '' );
                val = 'http://' + val;
            }

            $( this ).val( val );
        } );
    };

    wpea.submit = function( form ) {
        if ( typeof window.FormData !== 'function' ) {
            return;
        }

        var $form = $( form );

        $( '.ajax-loader', $form ).addClass( 'is-active' );
        wpea.clearResponse( $form );

        var formData = new FormData( $form.get( 0 ) );

        var detail = {
            id: $form.closest( 'div.wpea' ).attr( 'id' ),
            status: 'init',
            inputs: [],
            formData: formData
        };

        $.each( $form.serializeArray(), function( i, field ) {
            if ( '_wpea' == field.name ) {
                detail.contactFormId = field.value;
            } else if ( '_wpea_version' == field.name ) {
                detail.pluginVersion = field.value;
            } else if ( '_wpea_locale' == field.name ) {
                detail.contactFormLocale = field.value;
            } else if ( '_wpea_unit_tag' == field.name ) {
                detail.unitTag = field.value;
            } else if ( '_wpea_container_post' == field.name ) {
                detail.containerPostId = field.value;
            } else if ( field.name.match( /^_/ ) ) {
                // do nothing
            } else {
                detail.inputs.push( field );
            }
        } );

        var is_errors = false;
        var email_reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        $form.find('.wpea-validates-as-required').each(function(){
            var input = $(this);
            if(
                input.hasClass('wpea-validates-as-email') &&
                !new RegExp(email_reg).test(input.val())
            ){
                input.addClass('wpea-error');

            }else if(input.val().length < 2 ){
                input.addClass('wpea-error');
            }
        });

        var ajaxSuccess = function( data, status, xhr, $form ) {
            detail.id = $( data.into ).attr( 'id' );
            detail.status = data.status;
            detail.apiResponse = data;
            console.log(data);
            switch ( data.status ) {
                case 'init':
                    wpea.setStatus( $form, 'init' );
                    break;
                case 'validation_failed':
                    $.each( data.invalid_fields, function( i, n ) {
                        $( n.into, $form ).each( function() {
                            wpea.notValidTip( this, n.message );
                            $( '.wpea-form-control', this ).addClass( 'wpea-not-valid' );
                            $( '[aria-invalid]', this ).attr( 'aria-invalid', 'true' );
                        } );
                    } );

                    wpea.setStatus( $form, 'invalid' );
                    wpea.triggerEvent( data.into, 'invalid', detail );
                    break;
                case 'acceptance_missing':
                    wpea.setStatus( $form, 'unaccepted' );
                    wpea.triggerEvent( data.into, 'unaccepted', detail );
                    break;
                case 'spam':
                    wpea.setStatus( $form, 'spam' );
                    wpea.triggerEvent( data.into, 'spam', detail );
                    break;
                case 'aborted':
                    wpea.setStatus( $form, 'aborted' );
                    wpea.triggerEvent( data.into, 'aborted', detail );
                    break;
                case 'mail_sent':
                    wpea.setStatus( $form, 'sent' );
                    wpea.triggerEvent( data.into, 'mailsent', detail );
                    $form.find('.form-body').hide();
                    break;
                case 'mail_failed':
                    wpea.setStatus( $form, 'failed' );
                    wpea.triggerEvent( data.into, 'mailfailed', detail );
                    break;
                default:
                    wpea.setStatus( $form,
                        'custom-' + data.status.replace( /[^0-9a-z]+/i, '-' )
                    );
            }

            wpea.refill( $form, data );

            wpea.triggerEvent( data.into, 'submit', detail );

            if ( 'mail_sent' == data.status ) {
                $form.each( function() {
                    this.reset();
                } );

                wpea.toggleSubmit( $form );
                wpea.resetCounter( $form );
            }

            if ( ! wpea.supportHtml5.placeholder ) {
                $form.find( '[placeholder].placeheld' ).each( function( i, n ) {
                    $( n ).val( $( n ).attr( 'placeholder' ) );
                } );
            }

            $( '.wpea-response-output', $form )
                .html( '' ).append( data.message ).slideDown( 'fast' );

            $( '.screen-reader-response', $form.closest( '.wpea' ) ).each( function() {
                var $response = $( this );
                $response.html( '' ).append( data.message );

                if ( data.invalid_fields ) {
                    var $invalids = $( '<ul></ul>' );

                    $.each( data.invalid_fields, function( i, n ) {
                        if ( n.idref ) {
                            var $li = $( '<li></li>' ).append( $( '<a></a>' ).attr( 'href', '#' + n.idref ).append( n.message ) );
                        } else {
                            var $li = $( '<li></li>' ).append( n.message );
                        }

                        $invalids.append( $li );
                    } );

                    $response.append( $invalids );
                }

                $response.focus();
            } );

            if ( data.posted_data_hash ) {
                $form.find( 'input[name="_wpea_posted_data_hash"]' ).first()
                    .val( data.posted_data_hash );
            }
        };

        var action = $form.attr('action').replace('/#', '', );
        var url = 'emailarts/'+action+'/feedback';

        if(!is_errors) {
            $.ajax( {
                type: 'POST',
                url: url,
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false
            } ).done( function( data, status, xhr ) {
                ajaxSuccess( data, status, xhr, $form );
                $( '.ajax-loader', $form ).removeClass( 'is-active' );
            } ).fail( function( xhr, status, error ) {
                var $e = $( '<div class="ajax-error"></div>' ).text( error.message );
                $form.after( $e );
            } );
        }
    };

    wpea.triggerEvent = function( target, name, detail ) {
        var event = new CustomEvent( 'wpea' + name, {
            bubbles: true,
            detail: detail
        } );

        $( target ).get( 0 ).dispatchEvent( event );
    };

    wpea.setStatus = function( form, status ) {
        var $form = $( form );
        var prevStatus = $form.data( 'status' );

        $form.data( 'status', status );
        $form.addClass( status );

        if ( prevStatus && prevStatus !== status ) {
            $form.removeClass( prevStatus );
        }
    }

    wpea.toggleSubmit = function( form, state ) {
        var $form = $( form );
        var $submit = $( 'input:submit', $form );

        if ( typeof state !== 'undefined' ) {
            $submit.prop( 'disabled', ! state );
            return;
        }

        if ( $form.hasClass( 'wpea-acceptance-as-validation' ) ) {
            return;
        }

        $submit.prop( 'disabled', false );

        $( '.wpea-acceptance', $form ).each( function() {
            var $span = $( this );
            var $input = $( 'input:checkbox', $span );

            if ( ! $span.hasClass( 'optional' ) ) {
                if ( $span.hasClass( 'invert' ) && $input.is( ':checked' )
                    || ! $span.hasClass( 'invert' ) && ! $input.is( ':checked' ) ) {
                    $submit.prop( 'disabled', true );
                    return false;
                }
            }
        } );
    };

    wpea.resetCounter = function( form ) {
        var $form = $( form );

        $( '.wpea-character-count', $form ).each( function() {
            var $count = $( this );
            var name = $count.attr( 'data-target-name' );
            var down = $count.hasClass( 'down' );
            var starting = parseInt( $count.attr( 'data-starting-value' ), 10 );
            var maximum = parseInt( $count.attr( 'data-maximum-value' ), 10 );
            var minimum = parseInt( $count.attr( 'data-minimum-value' ), 10 );

            var updateCount = function( target ) {
                var $target = $( target );
                var length = $target.val().length;
                var count = down ? starting - length : length;
                $count.attr( 'data-current-value', count );
                $count.text( count );

                if ( maximum && maximum < length ) {
                    $count.addClass( 'too-long' );
                } else {
                    $count.removeClass( 'too-long' );
                }

                if ( minimum && length < minimum ) {
                    $count.addClass( 'too-short' );
                } else {
                    $count.removeClass( 'too-short' );
                }
            };

            $( ':input[name="' + name + '"]', $form ).each( function() {
                updateCount( this );

                $( this ).keyup( function() {
                    updateCount( this );
                } );
            } );
        } );
    };

    wpea.notValidTip = function( target, message ) {
        var $target = $( target );
        $( '.wpea-not-valid-tip', $target ).remove();

        $( '<span></span>' ).attr( {
            'class': 'wpea-not-valid-tip',
            'role': 'alert',
            'aria-hidden': 'true',
        } ).text( message ).appendTo( $target );

        if ( $target.is( '.use-floating-validation-tip *' ) ) {
            var fadeOut = function( target ) {
                $( target ).not( ':hidden' ).animate( {
                    opacity: 0
                }, 'fast', function() {
                    $( this ).css( { 'z-index': -100 } );
                } );
            };

            $target.on( 'mouseover', '.wpea-not-valid-tip', function() {
                fadeOut( this );
            } );

            $target.on( 'focus', ':input', function() {
                fadeOut( $( '.wpea-not-valid-tip', $target ) );
            } );
        }
    };

    wpea.refill = function( form, data ) {
        var $form = $( form );

        var refillCaptcha = function( $form, items ) {
            $.each( items, function( i, n ) {
                $form.find( ':input[name="' + i + '"]' ).val( '' );
                $form.find( 'img.wpea-captcha-' + i ).attr( 'src', n );
                var match = /([0-9]+)\.(png|gif|jpeg)$/.exec( n );
                $form.find( 'input:hidden[name="_wpea_captcha_challenge_' + i + '"]' ).attr( 'value', match[ 1 ] );
            } );
        };

        var refillQuiz = function( $form, items ) {
            $.each( items, function( i, n ) {
                $form.find( ':input[name="' + i + '"]' ).val( '' );
                $form.find( ':input[name="' + i + '"]' ).siblings( 'span.wpea-quiz-label' ).text( n[ 0 ] );
                $form.find( 'input:hidden[name="_wpea_quiz_answer_' + i + '"]' ).attr( 'value', n[ 1 ] );
            } );
        };

        if ( typeof data === 'undefined' ) {
            $.ajax( {
                type: 'GET',
                url: wpea.apiSettings.getRoute(
                    '/forms/' + wpea.getId( $form ) + '/refill' ),
                beforeSend: function( xhr ) {
                    var nonce = $form.find( ':input[name="_wpnonce"]' ).val();

                    if ( nonce ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', nonce );
                    }
                },
                dataType: 'json'
            } ).done( function( data, status, xhr ) {
                if ( data.captcha ) {
                    refillCaptcha( $form, data.captcha );
                }

                if ( data.quiz ) {
                    refillQuiz( $form, data.quiz );
                }
            } );

        } else {
            if ( data.captcha ) {
                refillCaptcha( $form, data.captcha );
            }

            if ( data.quiz ) {
                refillQuiz( $form, data.quiz );
            }
        }
    };

    wpea.clearResponse = function( form ) {
        var $form = $( form );
        $form.siblings( '.screen-reader-response' ).html( '' );

        $( '.wpea-not-valid-tip', $form ).remove();
        $( '[aria-invalid]', $form ).attr( 'aria-invalid', 'false' );
        $( '.wpea-form-control', $form ).removeClass( 'wpea-not-valid' );

        $( '.wpea-response-output', $form ).hide().empty();
    };

    // wpea.apiSettings.getRoute = function( path ) {
    //     var url = path;
    //     // var url = wpea.apiSettings.root;
    //     //
    //     // url = url.replace(
    //     //     wpea.apiSettings.namespace,
    //     //     wpea.apiSettings.namespace + path );
    //
    //     return url;
    // };

})(jQuery);