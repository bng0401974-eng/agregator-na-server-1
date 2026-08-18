<?php
/**
 * Модул: index-tiker.php
 * Опис: Главна страна која ги влече насловите, ги анализира и ги прикажува најповторуваните хаштагови.
  */

// Вклучување на логиката за парсирање и анализа
require_once 'parser-tiker.php';
require_once 'analyzer-tiker.php';

// 1. Дефинирање на целното URL од каде што ги собираме насловите
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

// 2. Влечење на насловите преку парсерот
$extractedTitles = fetchAndParseTitles($targetUrl);

// 3. Анализа и генерирање на хаштагови преку анализаторот
$hashtagsData = [];
if (!empty($extractedTitles) && !isset($extractedTitles['Грешка'])) {
    $hashtagsData = analyzeTitlesToTags($extractedTitles);
}
?>
<!DOCTYPE html>
<html lang="mk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тикер со најповторувани зборови и хаштагови</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0056b3;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 10px;
            font-size: 19.8px;
        }
        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }
        .hashtag-pill {
            background-color: #e3f2fd;
            color: #0d47a1;
            padding: 4px 10px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            font-size: 11.5px;
            transition: all 0.2s ease;
            border: 1px solid #bbdefb;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .hashtag-pill:hover {
            background-color: #0d47a1;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .count-badge {
            background-color: #ff9800;
            color: white;
            padding: 1px 5px;
            border-radius: 10px;
            font-size: 9.1px;
        }
        .error-msg {
            color: #d32f2f;
            background: #ffebee;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🔥 Најповторувани зборови и хаштагови (Тикер)</h1>
    <p>Извлечени од: <a href="<?php echo htmlspecialchars($targetUrl); ?>" target="_blank"><?php echo htmlspecialchars($targetUrl); ?></a></p>

    <div class="tags-container">
        <?php if (!empty($hashtagsData)): ?>
            <?php foreach ($hashtagsData as $tag => $data): ?>
                <a href="<?php echo htmlspecialchars($data['url']); ?>" class="hashtag-pill">
                    <?php echo htmlspecialchars($tag); ?>
                    <span class="count-badge"><?php echo (int)$data['count']; ?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="error-msg">Нема доволно податоци за анализа или насловите не се пронајдени.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>