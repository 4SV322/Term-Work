<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $authToken = $_POST["authToken"];
    $userText = $_POST["userText"];

    // Путь к файлу с переменной AuthToken
    $authTokenFilePath = "YandexAccessData.php";

    // Обновляем переменную AuthToken в файле
    file_put_contents($authTokenFilePath, "<?php \$authToken = '$authToken';");

    // Подключаем файл с данными для Yandex API
    include("YandexAccessData.php");

    // Создаем новый текст с вставленным значением пользователя
    $newText = "Ты программа по классификации текста элементами (категориями, именованными сущностями) DBpedia. Определите общие темы в тексте и представьте результат в форме утверждения, например: 'Этот текст относится к категории [category from DBpedia]'. Сведите результат в таблицу со следующей структурой столбцов: утверждение, идентификатор или категория элемента из DBpedia. 
    Текст для анализа: '$userText'";

    // Отправляем данные на REST API Yandex
    $jsonData = array("text" => $newText);
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
        $answ["request"] = json_decode($dataToSend);
        $answ["response"] = json_decode($response);

        echo json_encode($answ);
    }
} else {
    // Возвращаем пользователя на главную страницу, если он попытается обратиться напрямую к update.php
    header("Location: index.php");
    exit();
}
?>
