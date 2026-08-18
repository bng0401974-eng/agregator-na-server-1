<?php
/**
 * Модул: fetcher-tiker.php
 * Опис: Собира податоци за тикерот, ги кешира и може да се повика од cron.
 */

require_once __DIR__ . '/parser-tiker.php';
require_once __DIR__ . '/analyzer-tiker.php';

function getTickerCacheFilePath() {
    $cacheDir = __DIR__ . '/var/cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    return $cacheDir . '/tiker-widget-cache.json';
}

function extractTickerTitlesFromContext($contextData = []) {
    $extractedTitles = [];

    if (!empty($contextData['latest_posts']) && is_array($contextData['latest_posts'])) {
        foreach ($contextData['latest_posts'] as $p) {
            if (!empty($p['post_title'])) {
                $extractedTitles[] = $p['post_title'];
            }
        }
    }

    if (!empty($contextData['slider_posts']) && is_array($contextData['slider_posts'])) {
        foreach ($contextData['slider_posts'] as $p) {
            if (!empty($p['post_title'])) {
                $extractedTitles[] = $p['post_title'];
            }
        }
    }

    if (!empty($extractedTitles)) {
        return array_values(array_unique($extractedTitles));
    }

    if (class_exists('spark\models\PostModel')) {
        try {
            $postModel = new \spark\models\PostModel();
            $postsTable = $postModel->getTable();
            $posts = $postModel->select(["{$postsTable}.post_title"])
                ->limit(30, 0)
                ->execute()
                ->fetchAll();

            if (!empty($posts)) {
                foreach ($posts as $p) {
                    if (!empty($p['post_title'])) {
                        $extractedTitles[] = $p['post_title'];
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
    }

    return array_values(array_unique($extractedTitles));
}

function getTickerData($targetUrl = null, $forceRefresh = false, $cacheTtl = 300, $contextData = []) {
    if (empty($targetUrl)) {
        if (defined('TIKER_TARGET_URL')) {
            $targetUrl = TIKER_TARGET_URL;
        } elseif (function_exists('site_uri')) {
            $targetUrl = site_uri('/');
        } else {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'lativm.com';
            $targetUrl = "{$scheme}://{$host}/agregator3/";
        }
    }

    $cacheFile = getTickerCacheFilePath();

    if (!$forceRefresh && is_file($cacheFile)) {
        $payload = @json_decode(@file_get_contents($cacheFile), true);
        if (is_array($payload) && isset($payload['hashtags'])) {
            return $payload;
        }
    }

    $extractedTitles = extractTickerTitlesFromContext($contextData);
    if (empty($extractedTitles)) {
        $extractedTitles = fetchAndParseTitles($targetUrl);
    }

    $extractedTitles = array_values(array_unique($extractedTitles));
    $hashtagsData = [];
    if (!empty($extractedTitles) && !isset($extractedTitles['Грешка'])) {
        $hashtagsData = analyzeTitlesToTags($extractedTitles);
    }

    $payload = [
        'targetUrl' => $targetUrl,
        'titles' => $extractedTitles,
        'hashtags' => $hashtagsData,
        'updatedAt' => time(),
    ];

    if ($forceRefresh) {
        @file_put_contents($cacheFile, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    return $payload;
}

if (php_sapi_name() === 'cli' || (isset($_SERVER['argv']) && is_array($_SERVER['argv']))) {
    $data = getTickerData(null, true);
    $targetUrl = $data['targetUrl'] ?? 'unknown';
    $logFile = __DIR__ . '/var/cache/tiker-refresh.log';
    $logLine = date('Y-m-d H:i:s', $data['updatedAt']) . ' - Ticker refreshed - target: ' . $targetUrl . PHP_EOL;
    @file_put_contents($logFile, $logLine, FILE_APPEND);
    echo 'Ticker refreshed at ' . date('Y-m-d H:i:s', $data['updatedAt']) . ' for ' . $targetUrl . PHP_EOL;
}
