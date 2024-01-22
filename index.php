<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Классификация текста</title>
</head>
<body>
    <h1>Ввод данных</h1>
    <form action="update.php" method="post" id="classificationForm">
        <label for="authToken">AuthToken:</label>
        <input type="text" id="authToken" name="authToken" required>
        <br>

        <!-- Instruction select -->
        <label for="instruction">Выберите инструкцию:</label>
        <select id="instruction" name="instruction" required>
            <option value="1">Текст</option>
            <option value="2">Ссылка</option>
        </select>

        <!-- Text input -->
        <div id="textInput" style="display: block;">
            <label for="userText">Текст:</label>
            <textarea id="userText" name="userText"></textarea>
        </div>
        
        <!-- Link input -->
        <div id="linkInput" style="display: none;">
            <label for="link">Ссылка:</label>
            <input type="text" id="link" name="link">
        </div>

        <br>
        <input type="submit" value="Отправить">
    </form>
    
    <p>Проверьте свои ссылки: <a href="verify.php">Верифицировать ссылки</a></p>
    
    <div id="responseContainer">
        <?php
        // Проверяем, был ли получен ответ от REST API
        if (isset($answ)) {
            echo '<pre>' . json_encode($answ, JSON_PRETTY_PRINT) . '</pre>';
        }
        ?>
    </div>

    <script>
        var instructionSelect = document.getElementById('instruction');
        var textInput = document.getElementById('textInput');
        var linkInput = document.getElementById('linkInput');

        instructionSelect.addEventListener('change', function() {
            if (instructionSelect.value === '1') {
                textInput.style.display = 'block';
                linkInput.style.display = 'none';
            } else if (instructionSelect.value === '2') {
                textInput.style.display = 'none';
                linkInput.style.display = 'block';
            }
        });

        document.getElementById('classificationForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch('update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Изменение response.json() на response.text()
    .then(data => {
        document.getElementById("responseContainer").innerHTML = "<pre>" + data + "</pre>";
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

    </script>
</body>
</html>
