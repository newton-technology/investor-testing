<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Код авторизации</title>
    <style>
        p {
            font-family: Helvetica, Arial, sans-serif;
            color: #000000;
            margin-top: 40px;
            font-size: 14px;
        }
        #title {
            font-size: 20px;
        }
        #code {
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
        <b>Скопируйте код, чтобы войти на сайт</b>
    </p>
    <p>После этого вы сможете пройти тестирование знаний об инструментах фондового рынка,
        чтобы приобретать сложные финансовые инструменты. </p>
    <p id="code">
        <b>{{$code}}</b>
    </p>
    <p>Вы получили это сообщение, потому что зарегистрировались
        чтобы пройти тестирование знаний об инструментах фондового рынка.
        Если вы не регистрировались, проигнорируйте это письмо.
    </p>
</div>
</body>
</html>
