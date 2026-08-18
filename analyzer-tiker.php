<?php
/**
 * Модул: analyzer-tiker.php
 * Опис: Анализа на наслови, филтрирање на не-именки, броење фреквенција и топ 20 хаштагови.
 */

function analyzeTitlesToTags($titles) {
    // Проширена стоп-листа која ги елиминира предлозите, заменките, придавките и честите глаголи/прилози
    $stopWords = [
        'на', 'во', 'со', 'за', 'од', 'по', 'и', 'се', 'ли', 'како', 'дека', 'го', 
        'не', 'да', 'туку', 'или', 'но', 'па', 'ексклузивно', 'ова', 'тоа', 'тие', 
        'овие', 'кои', 'кој', 'која', 'што', 'бил', 'беше', 'до', 'прочитајте',
        'ги', 'му', 'им', 'ме', 'неа', 'нему', 'оној', 'онаа', 'оние', 'некој', 
        'некоја', 'некои', 'секој', 'секоја', 'секои', 'сите', 'нешто', 'ништо', 
        'којшто', 'којашто', 'коишто', 'чиј', 'чија', 'чие', 'чии', 'воопшто', 
        'може', 'можат', 'има', 'имаат', 'нема', 'немаат', 'треба', 'само', 
        'исто така', 'околу', 'преку', 'меѓу', 'над', 'под', 'пред', 'после', 
        'без', 'поради', 'заради', 'кај', 'кроз', 'прочитај', 'нова', 'нов', 
        'нови', 'ново', 'донесе', 'објави', 'соопшти', 'повеќе', 'помал', 'голем'
    ];
    
    $wordCounts = [];

    foreach ($titles as $title) {
        $cleanTitle = preg_replace('/[^\p{L}\p{N}\s]/u', '', $title);
        $words = explode(' ', $cleanTitle);

        foreach ($words as $word) {
            $word = trim($word);
            $wordLower = mb_strtolower($word);

            // Услов: Зборот да е подолг од 3 карактери и да го нема во стоп-листата за именки
            if (mb_strlen($word) > 3 && !in_array($wordLower, $stopWords)) {
                if (!isset($wordCounts[$wordLower])) {
                    $wordCounts[$wordLower] = 0;
                }
                $wordCounts[$wordLower]++;
            }
        }
    }

    arsort($wordCounts);

    // Ограничување на топ 20 најповторувани именки/поими
    $wordCounts = array_slice($wordCounts, 0, 20, true);

    $hashtagsData = [];
    foreach ($wordCounts as $wordClean => $count) {
        $tag = '#' . ucfirst($wordClean);
        $generatedUrl = "archive?s=" . urlencode($wordClean);
        
        $hashtagsData[$tag] = [
            'url' => $generatedUrl,
            'count' => $count
        ];
    }

    return $hashtagsData;
}