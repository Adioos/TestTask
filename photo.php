<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Генератор изображений</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
        }

        form {
            width: 50%;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="file"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            display: block;
            margin: 0 auto;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Генератор изображений</h1>
    
    <form action="GD.php" method="post" enctype="multipart/form-data">
        <label for="image">Выберите изображение:</label>
        <input type="file" name="image" id="image" required>
        
        <label for="title">Заголовок:</label>
        <input type="text" name="title" id="title" required>
        
        <label for="description">Описание:</label>
        <textarea name="description" id="description" required></textarea>
        
        <input type="submit" value="Сгенерировать и скачать">
    </form>
</body>
</html>
