<!doctype html>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Результат тестирования</title>
    <style>
        p {
            font-family: Helvetica, Arial, sans-serif;
            color: #000000;
            margin-top: 40px;
            font-size: 14px;
            line-height: 1.3rem;
        }

        #title {
            font-size: 20px;
        }

        div {
            max-width: 600px;
            margin: auto;
        }
    </style>
<body>
    <div>
        <p id="title">
            <b>Здравствуйте</b>
        </p>
        <p>Настоящим &laquo;{{$brokerName}}&raquo; уведомляет Вас @if($testStatus == 'failed')об <b>отрицательной@elseо <b>положительной@endif оценке</b> результата Вашего тестирования, проведенного в отношении &laquo;{{$categoryDescription}}&raquo;.
        </p>
    </div>
</body>
</html>
