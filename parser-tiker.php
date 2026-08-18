<?php
/**
 * Модул: parser-tiker.php
 * Опис: Внесува URL, го симнува HTML-от и ги вади сите H3 наслови.
 */

function fetchAndParseTitles($url) {
    // 1. Подесување на контекст за cURL / file_get_contents со User-Agent за да не блокираат сајтовите
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
            "timeout" => 5
        ]
    ];
    $context = stream_context_create($options);

    // 2. Симнување на HTML содржината
    $htmlContent = @file_get_contents($url, false, $context);

    if ($htmlContent === FALSE) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $htmlContent = curl_exec($ch);
            curl_close($ch);
        }
    }

    if (empty($htmlContent)) {
        return ["Грешка: Не може да се вчита содржината од зададената адреса."];
    }

    // 3. Користење на DOMDocument и DOMXPath за прецизно вадење на H3 насловите
    libxml_use_internal_errors(true); // Заштита од грешки при лош HTML
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    
    // Ги наоѓа сите h3 елементи
    $nodes = $xpath->query('//h3'); 
    
    $titles = [];
    foreach ($nodes as $node) {
        $cleanTitle = trim($node->textContent);
        if (!empty($cleanTitle)) {
            $titles[] = $cleanTitle;
        }
    }

    return $titles;
}