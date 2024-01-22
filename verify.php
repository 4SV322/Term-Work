<?php

// Читаем данные из файла links.txt
$fileContent = file_get_contents('links.txt');

// Разбиваем строки на массив
$links = explode("\n", $fileContent);

// Убираем пустые элементы
$links = array_filter($links);

// Проверяем каждую ссылку
foreach ($links as $originalLink) {
    // Проверяем, начинается ли ссылка с указанного префикса
    if (strpos($originalLink, 'http://dbpedia.org/resource/') === 0) {
        // Заменяем префикс, если условие выполняется
        $link = 'http://dbpedia.org/page/' . substr($originalLink, strlen('http://dbpedia.org/resource/'));
    } else {
        // Используем исходную ссылку, если префикс не найден
        $link = $originalLink;
    }

    // Инициализация cURL-сессии
    $ch = curl_init($link);

    // Установка параметров cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Выполнение cURL-запроса
    curl_exec($ch);

    // Получение кода состояния HTTP
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Закрытие cURL-сессии
    curl_close($ch);

    // Печать информации о состоянии ссылки
    echo "{$link} - " . ($httpCode == 200 ? 'существует' : 'не существует') . "\n";
}
?>
