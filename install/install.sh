#!/usr/bin/env bash

echo "Скрипт по развертке системы тестирования квалифицированных брокеров"
echo "Пожалуйтса, убедитесь, что у вас установлен нижеследующий список программ и компонентов:"

echo "- docker, docker-compose"
echo "- postgresql, psql, необходим доступ к СУБД с правами суперпользователя"
echo "- node.js v8^, yarn"

echo "Создание БД"

DONT_REPLACE="$"
export DONT_REPLACE

export $(grep -v '^#' .env | xargs)

envsubst < "./templates/prepare-database.sql.template" > ".prepare-database.tmp.sql"

echo "Подготовка БД. Введите пароль $PG_SUPERUSER"
psql -h $INVESTOR_TESTING_HOST -p $INVESTOR_TESTING_PORT -U $PG_SUPERUSER -W -f .prepare-database.tmp.sql postgres

rm .prepare-database.tmp.sql

echo "Создание схемы данных в БД investor_testing. Введите пароль пользователя admin"
psql -h $INVESTOR_TESTING_HOST -p $INVESTOR_TESTING_PORT -U admin -W -f ../database/make-database.sql $INVESTOR_TESTING_DATABASE

echo "Подготовка конфиг файлов"

APP_KEY=$(openssl rand -base64 32)
export APP_KEY

envsubst < "./templates/proxy_nginx.conf.template" > "../deploy/proxy_nginx.conf"

envsubst < "./templates/env.backend.template" > "../deploy/env"

envsubst < "./templates/env.frontend.template" > "../frontend/.env"

envsubst < "./templates/env.admin.template" > "../frontend/admin/.env"

echo "Сборка фронта"
cd ../frontend
yarn install
yarn build

echo "Сборка админки"
cd ./admin
yarn install 
yarn build

cd ../../deploy/

echo "Генерация ключей"
openssl genrsa -out private.pem 3072
openssl rsa -in private.pem -pubout -out public.pem

echo "Старт контейнеров"
docker-compose up -d

echo "Окончание установки системы тестирования инвесторов"
echo "Для заполнения БД данными, вставьте их в виде .csv файлов в директорию demodata и выполните import-data.sh"
