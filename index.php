<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение данных</title>
</head>
<body>
    <h1>Изменение данных</h1>
    <form action="update.php" method="post">
        <label for="authToken">AuthToken:</label>
        <input type="text" id="authToken" name="authToken" required>
        <br>
        <label for="userText">Текст:</label>
        <input type="text" id="userText" name="userText" required>
        <br>
        <input type="submit" value="Отправить">
    </form>

    <div id="responseContainer">
    </div>

    <script>
        <?php
        // Проверяем, был ли получен ответ от REST API
        if (isset($answ)) {
            echo 'document.getElementById("responseContainer").innerHTML = "<pre>" + JSON.stringify(' . json_encode($answ, JSON_PRETTY_PRINT) . ', null, 2) + "</pre>";';
        }
        ?>
    </script>
</body>
</html>
