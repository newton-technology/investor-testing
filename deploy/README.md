# Руководство по утсановке

## Для установки сервиса тестирования инвесторов необходимы следующие программы и компонеты
- docker + docker-compose
- node.js версии 8 и выше, yarn - для сборки фронта
- СУБД `postgresql` + клиент (psql, например)
- SMTP-сервер для рассылки почты.
- Возможно понадобятся доменное имя, запись в DNS и сертификат (при выводе приложения на прод).

## Порядок установки
1. Подготовка БД:
- Создать группу `admins`

```
postgres=# create role admins;
```

- Создать пользователя `admin`, включить в группу `admins`

```
postgres=# create user admin in role admins with password 'some-password';
```

- Создать БД `investor_testing`, принадлежащую группе admins

```
postgres=# create database investor_testing owner admins;
```

- Создать сущности в БД, применив скрипт `make-database.sql`

```
$ psql -h host -U admin -W -f ./make-database.sql investor_testing
```

2. Поготовка сервиса:

- В директории `deploy` скопировать файл `env.example` в `env`

- Заполнить значения env-переменных в файле `env`. (Инструкция по заполнению в файле backend/investor_testing/README.md).

3. Подготовка веб сервера:

- В файле `proxy_nginx.conf` задать домен в поле `server_name`.

- ОПЦИОНАЛЬНО: если на вашем сервере уже есть веб сервер, пробросить 80 порт сервиса `proxy` на другой порт хоста и проксировать трафик на этот порт.

4. В директории `frontend` скомпилировать проект, для этого:
из директории `frontend`

-  Скопировать файл `.env.example` в файл `.env` и заполнить переменные значениями

```
$ cp .env.example .env
```

- Установить зависимости

```
$ yarn install
```

- Скомпилировать проект

```
$ yarn build
```

> Note: Предусмотрена возможность кастомизации контента на фронте. Для этого необходимо перед компиляцией фронта внести изменения в файл `customize.json`. Подробнее в frontend/README.md

5. Деплой:

В директории `deploy`:

- Создать пару приватного и публичного ключа

```
$ openssl genrsa -out private.pem 3072
$ openssl rsa -in private.pem -pubout -out public.pem
```

- Запустить загрузку контейнеров. Для этого запустить команду

```
$ docker-compose up -d --build
```

Сервис должен заработать.

10. Заполнить БД тестовыми данными

```
$ docker exec backend "php /var/www/projects/php/investor_testing/artisan import categories /var/www/projects/php/investor_testing/resources/demodata/categories.csv"
$ docker exec backend "php /var/www/projects/php/investor_testing/artisan import questions /var/www/projects/php/investor_testing/resources/demodata/questions.csv
$ docker exec backend "php /var/www/projects/php/investor_testing/artisan import answers /var/www/projects/php/investor_testing/resources/demodata/answers.csv
```

В БД будут импортированы тестовые данные из файлов внутри контейнера:
- /var/www/projects/php/investor_testing/resources/demodata/categories.csv
- /var/www/projects/php/investor_testing/resources/demodata/questions.csv
- /var/www/projects/php/investor_testing/resources/demodata/answers.csv
(Если необходимо залить другие данные, можно пробросить директорию со своими файлами внутрь контейнера)
