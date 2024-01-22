<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $authToken = $_POST["authToken"];
    $tod = $_POST["instruction"];

    if ($tod == '1') {
        $userText = $_POST["userText"];
    } elseif ($tod == '2') {
        $url = $_POST["link"];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($response === false) {
            echo 'Ошибка cURL: ' . curl_error($ch);
            exit(); // Добавляем выход из скрипта в случае ошибки
        }
        curl_close($ch);

        // Устанавливаем максимально допустимую длину текста
        $maxTextLength = 7000;

        // Проверяем длину текста
        if (strlen($response) > $maxTextLength) {
            $userText = substr($response, 0, $maxTextLength); // Обрезаем текст до максимально допустимой длины
        } else {
            $userText = $response;
        }
    }
    
    // Путь к файлу с данными для Yandex API
    $yandexAccessDataFilePath = "YandexAccessData.php";

    // Читаем содержимое файла
    $yandexAccessDataContent = file_get_contents($yandexAccessDataFilePath);

    // Заменяем значение переменной $AuthToken
    $yandexAccessDataContent = preg_replace('/\$AuthToken="(.+?)";/', "\$AuthToken=\"$authToken\";", $yandexAccessDataContent);

    // Перезаписываем файл с обновленным значением $AuthToken
    file_put_contents($yandexAccessDataFilePath, $yandexAccessDataContent);

    // Подключаем обновленный файл с данными для Yandex API
    include($yandexAccessDataFilePath);

    $model = "general";
    $instruction = "Ты программа по классификации текста элементами (категориями, именованными сущностями) DBpedia. Определите общие темы в тексте и представьте результат в форме утверждения, например: 'Этот текст относится к категории [url dbpedia]'. Например: 'Berlin - http://dbpedia.org/resource/Category:Berlin'. Приведи больше пяти ссылок. 
    Текст для анализа:";
    $role = "Ассистент";
    $partialResults = false;
    $temperature = 0.2;
    $maxTokens = 7400;

    // Отправляем данные на REST API Yandex
    if (isset($userText) && !empty($userText)) {
        $jsonData = array(
            "model" => $model,
            "generationOptions" => array(
                "partialResults" => $partialResults,
                "temperature" => $temperature,
                "maxTokens" => $maxTokens
            ),
            "messages" => array(
                array(
                    "role" => $role,
                    "text" => $userText
                )
            ),
            "instructionText" => $instruction
        );
        $dataToSend = json_encode($jsonData, JSON_UNESCAPED_UNICODE);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $Path);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: $AuthToken",
            "Content-Type: application/json",
            "x-folder-id: $Folder"
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $responseData = json_decode($response, true);

            // Проверяем наличие ключа "text" в ответе
            if (isset($responseData["result"]["message"]["text"])) {
                $text = $responseData["result"]["message"]["text"];

                // Извлекаем ссылки с помощью регулярного выражения
                $matches = [];
                $pattern = '/http:\/\/dbpedia\.org\/resource\/[^\s".]+/i';
                preg_match_all($pattern, $text, $matches);

                // Сохраняем ссылки в файл
                if (!empty($matches[0])) {
                    $links = implode("\n", $matches[0]);
                    file_put_contents('links.txt', $links);
                    echo 'Ссылки успешно сохранены в файл links.txt';
                } else {
                    echo 'Ссылки не найдены в ответе';
                }
            } else {
                echo 'Ответ не содержит текста';
            }

            echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        // Возвращаем пользователя на главную страницу, если он попытается обратиться напрямую к update.php
        header("Location: index.php");
        exit();
    }
}
?>