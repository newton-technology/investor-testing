# Руководство по установке

## Для установки сервиса тестирования инвесторов необходимы следующие программы и компонеты:

- docker, docker-compose
- node.js 8+
- yarn
- СУБД `postgresql` + клиент (psql, например)
- SMTP-сервер для рассылки почты
- Возможно понадобятся доменное имя, запись в DNS и сертификат (при выводе приложения на прод).
- Будет необходим одтельный домен для админки. Рекомендуется ограничить доступ к этому домену с использованием VPN. 

## Порядок установки

### Подготовка БД:

#### Создать группу `admins`

```
postgres=# create role admins;
```

#### Создать пользователя `admin`, включить в группу `admins`

```
postgres=# create user admin in role admins with password 'some-password';
```

#### Создать БД `investor_testing`, принадлежащую группе admins

```
postgres=# create database investor_testing owner admins;
```

#### Создать сущности в БД, применив скрипт `make-database.sql`

```
$ psql -h host -U admin -W -f ./make-database.sql investor_testing
```

### Поготовка сервиса:

- В директории `deploy` скопировать файл `env.example` в `env`

- Заполнить значения env-переменных в файле `env`. Инструкция по заполнению в [описании бекенда](/../backend/investor_testing/README.md]).

### Подготовка веб сервера:

#### Скопировать proxy_nginx.conf.example в файл proxy_nginx.conf

```
cp proxy_nginx.conf.example proxy_nginx.conf 
```

#### В файле `proxy_nginx.conf` задать домены в поле `server_name`.

#### ОПЦИОНАЛЬНО: если на вашем сервере уже есть веб сервер, пробросить 80 порт сервиса `proxy` на другой порт хоста и проксировать трафик на этот порт.

### В директории `frontend` скомпилировать проект, для этого:

Из директории `frontend/`

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

> Note: Предусмотрена возможность кастомизации контента на фронте. Для этого необходимо перед компиляцией фронта внести изменения в файл `customize.json`. Подробнее в (описании фронтенда)[/frontend/README.md]

### Скомпилировать админку, для этого:

Из директории `frontend/admin/`

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

### Деплой:

В директории `deploy`:

- Создать пару приватного и публичного ключа

```
$ openssl genrsa -out private.pem 3072
$ openssl rsa -in private.pem -pubout -out public.pem
```

- Запустить загрузку контейнеров. Для этого запустить команду

```
$ docker-compose up -d
```

Сервис должен заработать.

### Заполнить БД тестовыми данными

```
$ docker exec -it backend bash
# php /var/www/projects/php/investor_testing/artisan import categories /var/www/projects/php/investor_testing/resources/demodata/categories.csv
# php /var/www/projects/php/investor_testing/artisan import questions /var/www/projects/php/investor_testing/resources/demodata/questions.csv
# php /var/www/projects/php/investor_testing/artisan import answers /var/www/projects/php/investor_testing/resources/demodata/answers.csv
```

В БД будут импортированы тестовые данные из файлов внутри контейнера:

- /var/www/projects/php/investor_testing/resources/demodata/categories.csv
- /var/www/projects/php/investor_testing/resources/demodata/questions.csv
- /var/www/projects/php/investor_testing/resources/demodata/answers.csv

(Если необходимо залить другие данные, можно пробросить директорию со своими файлами внутрь контейнера)

### Создать пользователя с админискими правами. Для этого:

- Создать пользователя с паролем:

```
$ docker exec backend "php /var/www/projects/php/investor_testing/artisan user:password:add admin@example.com"
```

Далее подтвердить создание

- Добавить роль администратора к существующей учетной записи можно при помощи следующей команды:

```
$ php /var/www/projects/php/investor_testing/artisan user:role:add admin@example.com admin
```

Подробнее читайте в [описании бекенда](/../backend/investor_testing/README.md])
 
> ВНИМАНИЕ: У пользователя, вошедшего без пароля (вошедшего по ОТП) не будет прав на использование API админки


