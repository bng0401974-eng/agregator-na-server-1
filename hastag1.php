<?php

// URL-то на агрегаторот
$url = "https://lativm.com/agregator3/";

// Ги вчитуваме податоците од страницата
$html = file_get_contents($url);

if ($html === FALSE) {
    die("Грешка при вчитување на страницата.");
}

// Го користиме DOMDocument за парсирање на HTML-от
$dom = new DOMDocument();
@$dom->loadHTML($html);

$xpath = new DOMXPath($dom);
// Ги наоѓаме сите H3 наслови на вестите
$nodes = $xpath->query("//h3");

$titles = [];
foreach ($nodes as $node) {
    $titles[] = trim($node->textContent);
}

// Функција за генерирање на хаштагови и соодветни URL-а
function generateArchiveUrls($titles) {
    $stopWords = ['на', 'во', 'со', 'за', 'од', 'по', 'и', 'се', 'ли', 'како', 'дека', 'го'];
    $hashtagsData = [];

    foreach ($titles as $title) {
        // Чистиме специјални знаци
        $cleanTitle = preg_replace('/[^\p{L}\p{N}\s]/u', '', $title);
        $words = explode(' ', $cleanTitle);

        foreach ($words as $word) {
            $word = trim($word);
            // Земаме зборови со подолжина поголема од 3 букви што не се во стоп-листата
            if (mb_strlen($word) > 3 && !in_array(mb_strtolower($word), $stopWords)) {
                $tag = '#' . ucfirst(mb_strtolower($word));
                
                // Хаштаг без знак # за во URL-то
                $tagClean = mb_strtolower($word);
                
                // Комбинирање со бараната патека
                $generatedUrl = "archive?s=" . urlencode($tagClean);

                $hashtagsData[$tag] = $generatedUrl;
            }
        }
    }

    return $hashtagsData;
}

$results = generateArchiveUrls($titles);

?>

<!DOCTYPE html>
<html lang="mk">
<head>
    <meta charset="UTF-8">
    <title>Архива со хаштагови</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        .tag-box { background: #fff; padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <h2>Генерирани архивирани URL-а по хаштаг:</h2>

    <?php foreach ($results as $tag => $link): ?>
        <div class="tag-box">
            <strong>Хаштаг:</strong> <?= htmlspecialchars($tag) ?><br>
            <strong>Архива URL:</strong> <a href="<?= htmlspecialchars($link) ?>" target="_blank"><?= htmlspecialchars($link) ?></a>
        </div>
    <?php endforeach; ?>

</body>
</html>