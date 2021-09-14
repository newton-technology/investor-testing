<!doctype html>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Код авторизации</title>
    <style>
        p {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 18px;
            color: #000000;
            margin-top: 40px;
            line-height: 1.5rem;
        }

        .mt-0 {
            margin-top: 0;
        }

        .signature {
            font-size: 14px;
            line-height: 1.2rem;
        }

        #title {
            font-size: 20px;
            font-weight: bold;
        }

        #code {
            font-size: 17px;
        }

        div {
            max-width: 600px;
            margin: auto;
        }
    </style>
<body>
    <div>
        <p id="title">
            Скопируйте код, чтобы войти на сайт
        </p>
        <p class="mt-0">На сайте вы сможете пройти тестирование знаний об инструментах фондового рынка. Это позволит использовать финансовые инструменты, недоступные обычным инвесторам.</p>
        <p id="code">
            {{$code}}
        </p>
        <br/>
        <p class="signature">
            Если вы не регистрировались, проигнорируйте это письмо.
            <br/>
            Для вашей безопасности код действует только 10 минут.
        </p>
    </div>
</body>
</html>
