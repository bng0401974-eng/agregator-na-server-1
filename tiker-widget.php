<?php
/**
 * Модул: tiker-widget.php
 * Опис: Самостоен и вградлив виџет за приказ на најповторуваните хаштагови од агрегаторот.
 */

require_once __DIR__ . '/fetcher-tiker.php';

if (defined('TIKER_TARGET_URL')) {
    $targetUrl = TIKER_TARGET_URL;
} elseif (function_exists('site_uri')) {
    $targetUrl = site_uri('/');
} else {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'lativm.com';
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    $targetUrl = "{$scheme}://{$host}" . ($scriptDir ? "{$scriptDir}/" : "/agregator3/");
}

$forceRefresh = false;
if (isset($_GET['refresh']) && in_array($_GET['refresh'], ['1', 'true', 'TRUE'], true)) {
    $forceRefresh = true;
}
if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
    $forceRefresh = true;
}

$widgetContext = [];
if (isset($t) && is_array($t)) {
    $widgetContext = $t;
}

$cacheTtl = defined('TIKER_WIDGET_CACHE_TTL') ? (int) TIKER_WIDGET_CACHE_TTL : 300;
$tickerData = getTickerData($targetUrl, $forceRefresh, $cacheTtl, $widgetContext);

$targetUrl = $tickerData['targetUrl'] ?? $targetUrl;
$extractedTitles = $tickerData['titles'] ?? [];
$hashtagsData = $tickerData['hashtags'] ?? [];

$isDirectAccess = (isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__));
?>
<?php if ($isDirectAccess): ?>
<!DOCTYPE html>
<html lang="mk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тикер Виџет</title>
<?php endif; ?>

<style>
    <?php if ($isDirectAccess): ?>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 20px;
        color: #333;
    }
    .tiker-container {
        max-width: 900px;
        margin: 0 auto;
    }
    <?php endif; ?>

    .tiker-widget-card {
        background: #fff;
        padding: 20px 24px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
    }
    .tiker-widget-card h2.tiker-title {
        color: #0056b3;
        border-bottom: 2px solid #eaeaea;
        padding-bottom: 8px;
        font-size: 14.9px;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 6px;
    }
    .tiker-widget-card p.tiker-subtitle {
        font-size: 10.75px;
        color: #6c757d;
        margin-top: 0;
        margin-bottom: 16px;
    }
    .tiker-widget-card p.tiker-subtitle a {
        color: #0056b3;
        text-decoration: underline;
    }
    .tiker-tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .tiker-hashtag-pill {
        background-color: #e3f2fd;
        color: #0d47a1;
        padding: 4px 10px;
        border-radius: 20px;
        text-decoration: none !important;
        font-weight: 600;
        font-size: 11px;
        transition: all 0.2s ease;
        border: 1px solid #bbdefb;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        line-height: 1.2;
    }
    .tiker-hashtag-pill:hover {
        background-color: #0d47a1;
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }
    .tiker-count-badge {
        background-color: #ff9800;
        color: white;
        padding: 1px 5px;
        border-radius: 10px;
        font-size: 9.1px;
        font-weight: 700;
    }
    .tiker-error-msg {
        color: #d32f2f;
        background: #ffebee;
        padding: 10px 14px;
        border-radius: 4px;
        font-size: 11.5px;
        margin: 0;
    }
</style>

<?php if ($isDirectAccess): ?>
</head>
<body>
<div class="tiker-container">
<?php endif; ?>

<div class="tiker-widget-card">
    <h2 class="tiker-title">🔥 Најповторувани зборови и хаштагови (Тикер)</h2>
    <p class="tiker-subtitle">Извлечени од: <a href="<?php echo htmlspecialchars($targetUrl); ?>" target="_blank"><?php echo htmlspecialchars($targetUrl); ?></a></p>

    <div class="tiker-tags-container">
        <?php if (!empty($hashtagsData)): ?>
            <?php foreach ($hashtagsData as $tag => $data): ?>
                <a href="<?php echo htmlspecialchars($data['url']); ?>" class="tiker-hashtag-pill">
                    <?php echo htmlspecialchars($tag); ?>
                    <span class="tiker-count-badge"><?php echo (int)$data['count']; ?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="tiker-error-msg">Нема доволно податоци за анализа или насловите не се пронајдени.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($isDirectAccess): ?>
</div>
</body>
</html>
<?php endif; ?>