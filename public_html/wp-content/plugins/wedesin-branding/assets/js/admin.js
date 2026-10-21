/**
 * digihood branding — Admin JS
 */
(function ($) {
    'use strict';

    const DH = window.digihoodAdmin || {};

    // Toast notification
    function showToast(message, type) {
        const toast = $('<div class="dh-toast dh-toast-' + type + '">' + message + '</div>');
        $('body').append(toast);
        setTimeout(() => toast.addClass('is-visible'), 50);
        setTimeout(() => {
            toast.removeClass('is-visible');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function ajaxPost(action, data, onSuccess) {
        data = data || {};
        data.action = action;
        data.nonce = DH.nonce;

        $.post(DH.ajaxurl, data, function (response) {
            if (response.success) {
                showToast(typeof response.data === 'string' ? response.data : response.data.message, 'success');
                if (onSuccess) onSuccess(response.data);
            } else {
                showToast(response.data || 'Chyba', 'error');
            }
        }).fail(function () {
            showToast('Chyba spojení se serverem.', 'error');
        });
    }

    $(function () {

        // Save settings
        $('#dh-settings-form').on('submit', function (e) {
            e.preventDefault();
            ajaxPost('digihood_save_settings', {
                reply_to_email: $('#dh-reply-to').val(),
                mail_to_email: $('#dh-mail-to').val()
            });
        });

        // Start recording
        $('#dh-start-recording').on('click', function () {
            const btn = $(this);
            btn.prop('disabled', true).text('Zahajuji...');
            ajaxPost('digihood_start_recording', {}, function () {
                location.reload();
            });
        });

        // Stop recording
        $('#dh-stop-recording').on('click', function () {
            const btn = $(this);
            btn.prop('disabled', true).text('Ukončuji...');
            ajaxPost('digihood_stop_recording', {}, function () {
                location.reload();
            });
        });

        // Send report
        $('#dh-send-report').on('click', function () {
            if (!confirm('Opravdu chcete odeslat report klientovi?')) return;
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Odesílám...');

            ajaxPost('digihood_send_report', {
                note: $('#dh-note').val()
            }, function () {
                setTimeout(() => location.reload(), 1000);
            });
        });

        // Clear logs
        $('#dh-clear-logs').on('click', function () {
            if (!confirm('Smazat všechny nezpracované logy?')) return;
            const source = $(this).data('source') || 'digihood';
            ajaxPost('digihood_clear_logs', { source: source }, function () {
                setTimeout(() => location.reload(), 500);
            });
        });

        // Collapsible sections
        $('#dh-toggle-other').on('click', function () {
            $(this).toggleClass('is-open');
            $('#dh-other-body').slideToggle(200);
        });

    });

})(jQuery);
