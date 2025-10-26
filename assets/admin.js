(function($){
    if (!$) {
        return;
    }

    function updateRangeOutput($input, selector) {
        var value = $input.val();
        $(selector).text(value + 'px');
    }

    function updateColorVisibility(optionName) {
        var skin = $('input[name="' + optionName + '[skin]"]:checked').val();
        if (skin === 'cosmic' || skin === 'business') {
            $('.color-settings').show();
        } else {
            $('.color-settings').hide();
        }
    }

    function setStatus(message, type) {
        var $status = $('#miri-webhook-status');
        $status.removeClass('success error');
        if (type) {
            $status.addClass(type);
        }
        $status.text(message || '');
    }

    $(function(){
        var optionName = $('#miri-settings-form').data('option');

        // Tabs
        $('.miri-tab').on('click', function(){
            var tab = $(this).data('tab');
            $('.miri-tab').removeClass('active');
            $(this).addClass('active');
            $('.miri-tab-content').removeClass('active');
            $('.miri-tab-content[data-content="' + tab + '"]').addClass('active');
        });

        // Color pickers
        if ($.fn.wpColorPicker) {
            $('.miri-color-picker').wpColorPicker();
        }

        // Range sliders
        $('#button-size-range').on('input change', function(){
            updateRangeOutput($(this), '#button-size-output');
        });
        $('#chat-width-range').on('input change', function(){
            updateRangeOutput($(this), '#chat-width-output');
        });
        $('#chat-height-range').on('input change', function(){
            updateRangeOutput($(this), '#chat-height-output');
        });

        // Initialize outputs
        updateRangeOutput($('#button-size-range'), '#button-size-output');
        updateRangeOutput($('#chat-width-range'), '#chat-width-output');
        updateRangeOutput($('#chat-height-range'), '#chat-height-output');

        // Show/hide excluded pages
        $('#miri-show-on').on('change', function(){
            if ($(this).val() === 'exclude') {
                $('.excluded-pages-row').show();
            } else {
                $('.excluded-pages-row').hide();
            }
        });

        // Skin selection
        $('input[name="' + optionName + '[skin]"]').on('change', function(){
            $('.skin-option').removeClass('active');
            $(this).closest('.skin-option').addClass('active');
            updateColorVisibility(optionName);
        });
        updateColorVisibility(optionName);

        // Logo uploader
        $('#miri-upload-logo').on('click', function(e){
            e.preventDefault();

            var frame = wp.media({
                title: 'בחר לוגו',
                button: { text: 'השתמש בתמונה זו' },
                multiple: false
            });

            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#miri-logo-url').val(attachment.url);
                var $preview = $('.miri-logo-preview');
                if ($preview.length) {
                    $preview.attr('src', attachment.url);
                } else {
                    $('<img>', {
                        src: attachment.url,
                        class: 'miri-logo-preview',
                        alt: 'לוגו'
                    }).prependTo('.miri-logo-upload');
                }
                if (!$('#miri-remove-logo').length) {
                    $('<button>', {
                        type: 'button',
                        id: 'miri-remove-logo',
                        class: 'button',
                        text: 'הסר לוגו'
                    }).insertAfter('#miri-upload-logo');
                }
            });

            frame.open();
        });

        $(document).on('click', '#miri-remove-logo', function(){
            $('#miri-logo-url').val('');
            $('.miri-logo-preview').remove();
            $(this).remove();
        });

        // Webhook test
        $('#miri-test-webhook').on('click', function(){
            var $button = $(this);
            var webhook = $('#miri-webhook-url').val();

            if (!webhook) {
                setStatus(miriAdmin.strings.webhookError, 'error');
                return;
            }

            setStatus(miriAdmin.strings.testingWebhook);
            $button.prop('disabled', true);

            $.post(miriAdmin.ajaxurl, {
                action: 'miri_test_webhook',
                nonce: miriAdmin.nonce,
                webhook_url: webhook
            }).done(function(response){
                if (response && response.success) {
                    setStatus(response.data || miriAdmin.strings.webhookSuccess, 'success');
                } else {
                    setStatus((response && response.data) || miriAdmin.strings.webhookError, 'error');
                }
            }).fail(function(){
                setStatus(miriAdmin.strings.webhookError, 'error');
            }).always(function(){
                $button.prop('disabled', false);
            });
        });

        // Reset settings
        $('#miri-reset-settings').on('click', function(){
            if (!confirm(miriAdmin.strings.confirmReset)) {
                return;
            }

            var $button = $(this);
            $button.prop('disabled', true);

            $.post(miriAdmin.ajaxurl, {
                action: 'miri_reset_settings',
                nonce: miriAdmin.nonce
            }).done(function(response){
                if (response && response.success) {
                    alert(miriAdmin.strings.resetSuccess);
                    location.reload();
                } else {
                    alert(response && response.data ? response.data : miriAdmin.strings.webhookError);
                }
            }).fail(function(){
                alert(miriAdmin.strings.webhookError);
            }).always(function(){
                $button.prop('disabled', false);
            });
        });
    });
})(jQuery);
