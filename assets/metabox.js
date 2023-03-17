(function ($, wp, options) {

    if (!wp) {
        return
    }

    /**
     * Upload Files
     */
    function initUploader() {

        if (!wp.media) {
            return
        }

        $(document).on('click', '[data-upload-btn]', function () {

            var $btn = $(this),
                $input_hidden = $($btn.attr('data-upload-input')),
                $input_text = $($btn.attr('data-upload-preview')),
                post_id = $btn.attr('data-upload-postid'),
                mime_type = $btn.attr('data-upload-mimes')

            mime_type = JSON.parse(mime_type)

            wp.media.model.settings.post.id = post_id

            var frame = wp.media.frames.fw_frame = wp.media({
                title: 'Select or upload image',
                library: {
                    type: mime_type,
                    post_parent: post_id,
                },
                button: {
                    text: 'Select',
                },
                multiple: false,
            })

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON()

                $input_hidden.val(attachment.id).trigger('change')
                $input_text.val(attachment.url).trigger('change')

                // Destroy the frame.
                if (frame) {
                    frame.dispose()
                }

                wp.media.model.settings.post.id = 0

            })

            frame.open()

        })

    }

    /**
     * Select2
     */
    function initSelect2() {

        if (!$.fn.select2) {
            return
        }

        // Select2 Default Options
        // @see https://select2.org/configuration/defaults
        $.fn.select2.defaults.set("width", '100%')
        $.fn.select2.defaults.set("minimum-input-length", 3)
        $.fn.select2.defaults.set("close-on-select", false)
        $.fn.select2.defaults.set("ajax--cache", true)
        $.fn.select2.defaults.set("ajax--data-type", "json")


        // Select2 Aajx Search Media
        $('.select2-ajax-search-users').select2({
            ajax: {
                url: window.ajaxurl,
                type: "post",
                xhrFields: {
                    withCredentials: true,
                },
                data: function (params, d) {
                    return {
                        action: options.actions.searchUsers,
                        search: params.term,
                        _wpnonce: options.ajaxNonce,
                    }
                },
                processResults: function (data) {

                    if (!data.success) {
                        return []
                    }

                    var results = []

                    $.each(data.data, function (index, user) {
                        results.push({
                            id: user.ID,
                            text: "#" + user.ID + " " + user.display_name + " (" + user.user_email + ")",
                        })
                    })

                    return {
                        results: results,
                    }
                },
            },
        })


    }

    $(initUploader)
    $(initSelect2)

})(jQuery, wp, FW_STIMULSOFT)