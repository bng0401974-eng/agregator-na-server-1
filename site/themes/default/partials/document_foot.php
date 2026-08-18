 <?php sp_footer(); ?>

<script type="text/javascript">
    (function () {
        window.addEventListener('load', function () {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('<?= base_uri('sw.js') ?>').catch(function (error) {
                    console.warn('PWA service worker registration failed:', error);
                });
            }
        });

        var installPromptKey = 'pwa-install-prompt-dismissed';
        var deferredPrompt = null;
        var popupId = 'pwa-install-popup';
        var installSupported = false;

        function isInstallableContext() {
            return window.isSecureContext || ['localhost', '127.0.0.1', '::1'].indexOf(window.location.hostname) !== -1;
        }

        function createInstallPopup() {
            if (document.getElementById(popupId)) {
                return;
            }

            var popup = document.createElement('div');
            popup.id = popupId;
            popup.innerHTML = '<div class="pwa-install-card" role="dialog" aria-modal="true" aria-label="PWA install prompt"><h3>Дали сакате да ја инсталирате оваа апликација?</h3><p>Оваа опција е достапна во поддржани browser-и. Ако не се појави прозорецот, можете да ја инсталирате преку менито на браузерот.</p><div class="pwa-install-actions"><button type="button" class="pwa-install-btn">Затвори</button><button type="button" class="pwa-dismiss-btn">Не сега</button></div></div>';
            document.body.appendChild(popup);

            popup.querySelector('.pwa-install-btn').addEventListener('click', function () {
                popup.remove();
            });

            popup.querySelector('.pwa-dismiss-btn').addEventListener('click', function () {
                popup.remove();
                localStorage.setItem(installPromptKey, '1');
            });
        }

        function toggleInstallTriggers(show) {
            var triggers = document.querySelectorAll('.pwa-install-trigger');
            triggers.forEach(function (trigger) {
                if (trigger.getAttribute('aria-label') === 'Go to top' || trigger.title === 'Go to top') {
                    trigger.style.display = '';
                    return;
                }
                trigger.style.display = show ? '' : 'none';
            });
        }

        function bindInstallTrigger() {
            var triggers = document.querySelectorAll('.pwa-install-trigger, .pwa-install-test-btn');
            if (!triggers.length) {
                return;
            }

            triggers.forEach(function (trigger) {
                trigger.addEventListener('click', function (event) {
                    event.preventDefault();

                    if (trigger.classList.contains('go-top-btn')) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        return;
                    }

                    if (document.getElementById(popupId)) {
                        return;
                    }

                    createInstallPopup();
                });
            });
        }

        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            deferredPrompt = event;
            installSupported = true;
            toggleInstallTriggers(true);

            if (localStorage.getItem(installPromptKey) === '1') {
                return;
            }

            window.setTimeout(createInstallPopup, 1500);
        });

        window.addEventListener('load', function () {
            installSupported = isInstallableContext();
            bindInstallTrigger();
            toggleInstallTriggers(installSupported);
        });

        window.addEventListener('appinstalled', function () {
            var popup = document.getElementById(popupId);
            if (popup) {
                popup.remove();
            }
        });
    })();
</script>

<script type="text/javascript">
    <?php if (sp_is_enqueued('parsley')) : ?>
    $("form").parsley({
       errorClass: 'is-invalid text-danger',
       successClass: 'is-valid',
       errorsWrapper: '<span class="form-text text-danger"></span>',
       errorTemplate: '<span></span>',
       trigger: 'focusout',
       focusInvalid: true,
   });
    <?php endif; ?>


    <?php if (sp_is_enqueued('jquery-autocomplete')) : ?>
        var suggestionEndpoint = '<?= js_string(url_for('site.suggest_queries')) ?>';
        var xhr;
        $('input[name="q"]').autoComplete({
          source: function(term, response){
            try { xhr.abort(); } catch(e){}
            xhr = $.getJSON(suggestionEndpoint, { q: term }, function(data){ response(data); });
          },
          onSelect : function (e, term, item) {
            $("#searchForm").submit();
          }
        });

    <?php endif;?>

    jQuery(document).ready(function($) {

    <?php if (sp_is_enqueued('jquery-unveil')) : ?>
        $("img").unveil();
    <?php endif; ?>

    if (window.screen.width >= 768) {
        var sidebar = $('.sidebar').stickySidebar({
          topSpacing: 66,
          bottomSpacing: 60,
          resizeSensor: false,
        });
      }

    });
</script>
