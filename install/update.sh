#!/usr/bin/env bash

INFO='\033[0;34m'
SUCCESS='\033[0;32m' 
ERROR='\033[0;31m'
DEFAULT='\033[0m\n'

echo -en "${INFO}Скрипт по обновлению системы тестирования квалифицированных инвесторов\n${DEFAULT}" \

requirments="docker docker-compose psql envsubst"

for cmd in ${requirments}
do
    if ! command -v $cmd &> /dev/null
    then
        echo -en "${ERROR}Ошибка! Отсутвует команда или пакет, необходимые для работы скрипта установки: ${cmd} ${DEFAULT}"
        exit 1
    fi
done

if [[ ! -f .env ]]
then
    echo -en "${ERROR}Для сборки необходим файл .env. ${DEFAULT}" >&2
    echo -en "${ERROR}Пожалуйтса, скопируйте файл .env.example и заполните переменные значениями ${DEFAULT}" >&2
    echo -en "${ERROR}Подробнее в README.md ${DEFAULT}" >&2
    exit 1
fi

if [[ ! $(grep APP_KEY .env) ]]
then
    echo -en "${INFO}Генерация ключа приложения${DEFAULT}"
    echo "APP_KEY=$(openssl rand -base64 32)" >> .env 
fi

echo -en "${INFO}Обновление схемы данных в БД ${INVESTOR_TESTING_DATABASE}.${DEFAULT}"
PGPASSWORD=$INVESTOR_TESTING_ADMIN_PASSWORD psql -h $INVESTOR_TESTING_HOST -p $INVESTOR_TESTING_PORT -U admin -f ../database/make-database.sql $INVESTOR_TESTING_DATABASE

if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешно создана схема БД ${INVESTOR_TESTING_DATABASE}${DEFAULT}"
else
    echo -en "${ERROR}Не удалось создать схему БД ${INVESTOR_TESTING_DATABASE}${DEFAULT}" >&2
    exit 1
fi

echo -en "${INFO}Подготовка конфигурационных файлов${DEFAULT}"

envsubst < "./templates/proxy_nginx.conf.template" > "../deploy/proxy_nginx.conf"

envsubst < "./templates/env.backend.template" > "../deploy/env"

envsubst < "./templates/env.frontend.template" > "../frontend/.env"

envsubst < "./templates/env.admin.template" > "../frontend/admin/.env"

cd ../frontend
echo -en "${INFO}Сборка фронтенда${DEFAULT}"
echo -en "${INFO}Установка зависимостей${DEFAULT}"
docker run -t --name frontend_builder -v `pwd`:/frontend -w /frontend nwtndevops/frontend_builder:latest yarn install 
if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешная установка зависимостей ${DEFAULT}"
    docker rm frontend_builder
else
    echo -en "${ERROR}Не удалось установить зависимости ${DEFAULT}" >&2
    docker rm frontend_builder
    exit 1
fi

echo -en "${INFO}Сборка сайта${DEFAULT}"
docker run -t --name frontend_builder -v `pwd`:/frontend -w /frontend nwtndevops/frontend_builder:latest yarn build-client
if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешная компиляция сайта ${DEFAULT}"
else
    echo -en "${ERROR}Не удалось собрать сайт ${DEFAULT}" >&2
fi
docker rm frontend_builder

echo -en "${INFO}Сборка консоли администратора${DEFAULT}"
docker run -t --name frontend_builder -v `pwd`:/frontend -w /frontend nwtndevops/frontend_builder:latest yarn build-admin
if [[ $? -eq 0 ]]
then
    echo -en "${SUCCESS}Успешная компиляция консоли администратора ${INVESTOR_TESTING_DATABASE}${DEFAULT}"
else
    echo -en "${ERROR}Не скомпилировать консоль администратора ${DEFAULT}" >&2
fi
docker rm frontend_builder

cd ../deploy/

echo -en "${INFO}Рестарт контейнеров${DEFAULT}"
docker-compose restart
docker-compose up -d

echo -en "${INFO}Окончание установки системы тестирования инвесторов.${DEFAULT}"
echo -en "${INFO}Для заполнения БД данными, вставьте их в виде .csv файлов в директорию demodata и выполните import-data.sh${DEFAULT}"
