<!DOCTYPE html>
<html dir="ltr">
<head>
    <?php insert('partials/document_head.php'); ?>
</head>
<?php $backgroundImage = trim((string) get_option('site_background_image')); $backgroundImageLink = trim((string) get_option('site_background_image_link')); $backgroundLinkHref = !empty($backgroundImageLink) ? $backgroundImageLink : url_for('site.home'); ?>
<body class="<?= e_attr($t['body_class']); ?>">
    <?php if (!empty($backgroundImage)) : ?>
        <a id="site-background-layer" href="<?= e_attr($backgroundLinkHref); ?>" style="position:fixed; inset:0; z-index:0; display:block; cursor:pointer; background-image:url('<?= e_attr($backgroundImage); ?>'); background-position:center center; background-repeat:no-repeat; background-attachment:fixed; background-size:cover; text-decoration:none; pointer-events:auto; opacity:1;"></a>
    <?php endif; ?>
    <div id="fb-root"></div>
    <div style="position:relative; z-index:2; pointer-events:auto;">
        <?php
        // SVG Sprites
        insert('partials/sprites.svg');

        // Header
        if (!$t['hide_header']) {
            insert('partials/nav/header.php');
        }
        ?>

        <?php
        /**
         * Before content section
         */
        section('before_content');
        ?>

        <button type="button" class="pwa-install-test-btn go-top-btn" title="Go to top" aria-label="Go to top">
            <?= svg_icon('arrow-forward', 'svg-md go-top-icon'); ?>
        </button>

        <div id="content">
            <?php
            /**
             * Content section
             */
            section('content', 'No content block found');
            ?>
        </div>

        <?php
        /**
         * After content section
         */
        section('after_content');
        ?>

        <?php if (!$t['hide_footer']) : ?>
            <?php insert('partials/nav/footer.php'); ?>
        <?php endif; ?>
        <?php insert('partials/document_foot.php'); ?>

        <?php section('before_body_closure'); ?>
    </div>
    <?php if (!empty($backgroundImage)) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var layer = document.getElementById('site-background-layer');
            if (!layer) {
                return;
            }
            layer.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
