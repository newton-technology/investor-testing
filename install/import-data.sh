#!/usr/bin/env bash

echo "Импорт данных в БД тестирования инвесторов"

echo "importing categories"
docker exec backend /opt/remi/php74/root/bin/php /var/www/projects/php/investor_testing/artisan import categories /var/www/projects/php/investor_testing/resources/demodata/categories.csv

echo "importing questions"
docker exec backend /opt/remi/php74/root/bin/php /var/www/projects/php/investor_testing/artisan import questions /var/www/projects/php/investor_testing/resources/demodata/questions.csv

echo "importing answers"
docker exec backend /opt/remi/php74/root/bin/php /var/www/projects/php/investor_testing/artisan import answers /var/www/projects/php/investor_testing/resources/demodata/answers.csv 

echo "Импорт завершен"
