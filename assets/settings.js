(function ($, wp, options) {

    if (!wp.ajax) {
        return
    }

    /**
     * Ajax Save Settings
     */
    $(document).on('submit', 'form.fw-settings', function (e) {
        e.preventDefault()

        var $form = $(this),
            $alert = $('.wrap > h1'),
            $spinner = $form.find('.spinner'),
            formData = $form.serializeArray()

        formData.push({
            name: 'action',
            value: options.actions.saveSettings,
        })

        formData.push({
            name: '_wpnonce',
            value: options.ajaxNonce,
        })

        $('.fw-notice').remove()
        $spinner.addClass('is-active')

        // Send Data
        wp.ajax
          .post(false, formData)

        // Success
          .done(function () {
              $alert.after('<div class="notice notice-success fw-notice"><p>تغییرات شما با موفقیت ذخیره شد.</p></div>')
          })

        // Error
          .fail(function () {
              $alert.after('<div class="notice notice-error fw-notice"><p>خطایی در هنگام ذخیره سازی تنظیمات رخ داد.</p></div>')
          })

        // Complete
          .always(function () {
              $spinner.removeClass('is-active')
          })

    })

})(jQuery, wp, FW_STIMULSOFT)