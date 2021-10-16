#!/usr/bin/env bash

INFO='\033[0;34m'
SUCCESS='\033[0;32m' 
ERROR='\033[0;31m'
DEFAULT='\033[0m\n'

echo -en "${INFO}Скрипт по развертке системы тестирования квалифицированных брокеров\n" \
"Пожалуйтса, убедитесь, что у вас установлен нижеследующий список программ и компонентов:\n" \
 "- docker, docker-compose\n" \
 "- postgresql, psql, необходим доступ к СУБД с правами суперпользователя\n" \
 "- node.js v8^, yarn ${DEFAULT}" \ 

requirments="docker docker-compose node yarn psql envsubst openssl"
success_check=1
for cmd in ${requirments}
do
    if ! command -v $cmd &> /dev/null
    then
        echo -en "${ERROR}Ошибка! Отсутвует команда или пакет, необходимые для работы скрипта установки: ${cmd} ${DEFAULT}"
        success_check=0
    fi
done

if [ $success_check -eq 0 ]
then
    exit 1
fi

required_node_major_version=8

if [ $(node -v | awk -F"." '{print $1}' | awk -F"v" '{print $2}') -lt $required_node_major_version ]
then
    echo -en "${ERROR}Ошибка! Необходим node v${required_node_major_version}^ ${DEFAULT}"
    exit 1
fi 

echo -en "${INFO}Создание БД${DEFAULT}"

DONT_REPLACE="$"
export DONT_REPLACE

export $(grep -v '^#' .env | xargs)

PG_SUPERUSER=postgres

read -p "Подготовка БД. Введите логин суперпользователя (по-умолчанию ${PG_SUPERUSER}): " input_pg_superuser

if [[ $input_pg_superuser ]]
then
    PG_SUPERUSER=$input_pg_superuser
fi

export PG_SUPERUSER

echo -en "${INFO}Подготовка БД. Введите пароль суперпользователя ${PG_SUPERUSER}:${DEFAULT}"

psql -h $INVESTOR_TESTING_HOST -p $INVESTOR_TESTING_PORT -U $PG_SUPERUSER -W postgres << EOF 
$(envsubst < ./templates/prepare-database.sql.template)
EOF

if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешно создана БД ${INVESTOR_TESTING_DATABASE}${DEFAULT}"
else
    echo -en "${ERROR}Не удалось создать БД ${INVESTOR_TESTING_DATABASE}${DEFAULT}" >&2
    exit 1
fi

echo -en "${INFO}Создание схемы данных в БД ${INVESTOR_TESTING_DATABASE}.${DEFAULT}"
PGPASSWORD=$INVESTOR_TESTING_ADMIN_PASSWORD psql -h $INVESTOR_TESTING_HOST -p $INVESTOR_TESTING_PORT -U admin -f ../database/make-database.sql $INVESTOR_TESTING_DATABASE

if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешно создана схема БД ${INVESTOR_TESTING_DATABASE}${DEFAULT}"
else
    echo -en "${ERROR}Не удалось создать схему БД ${INVESTOR_TESTING_DATABASE}${DEFAULT}" >&2
    exit 1
fi

echo -en "${INFO}Подготовка конфигурационных файлов${DEFAULT}"

APP_KEY=$(openssl rand -base64 32)
export APP_KEY

envsubst < "./templates/proxy_nginx.conf.template" > "../deploy/proxy_nginx.conf"

envsubst < "./templates/env.backend.template" > "../deploy/env"

envsubst < "./templates/env.frontend.template" > "../frontend/.env"

envsubst < "./templates/env.admin.template" > "../frontend/admin/.env"

echo -en "${INFO}Сборка сайта${DEFAULT}"
cd ../frontend
yarn install
yarn build

if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешная компиляция сайта ${DEFAULT}"
else
    echo -en "${ERROR}Не удалось скомпилирвать сайта ${DEFAULT}" >&2
    exit 1
fi

echo -en "${INFO}Сборка консоли администратора${DEFAULT}"
cd ./admin
yarn install 
yarn build

if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешная компиляция консоли администратора ${INVESTOR_TESTING_DATABASE}${DEFAULT}"
else
    echo -en "${ERROR}Не скомпилировать консоль администратора ${DEFAULT}" >&2
    exit 1
fi

cd ../../deploy/

echo -en "${INFO}Генерация ключей${DEFAULT}"
openssl genrsa -out private.pem 3072
openssl rsa -in private.pem -pubout -out public.pem

echo -en "${INFO}Старт контейнеров${DEFAULT}"
docker-compose up -d

echo -en "${INFO}Окончание установки системы тестирования инвесторов.${DEFAULT}"
echo -en "${INFO}Для заполнения БД данными, вставьте их в виде .csv файлов в директорию demodata и выполните import-data.sh${DEFAULT}"
