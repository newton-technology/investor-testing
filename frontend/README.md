# Investor-testing

Сервис тестирования инвесторов от команды [Ньютон Технологии](https://nwtn.io/).


## Установка

[NodeJS LTS](https://nodejs.org/)

```
yarn install --save-d
```

## Запуск приложения

Для запуска клиентской части приложения
```
yarn start-client
```

Для запуска административной части приложения
```
yarn start-admin
```

Если необходимо изменить порт (по умолчанию 3000)
```
PORT=8000 yarn start-client
```

## Сборка приложения

Сборка клиентской части приложения
```
yarn build-client
```

Сборка административной части приложения
```
yarn build-admin
```

## Кастомизация

Для изменения цветовой схемы требуется изменить файл

```
./src/customize.json

palette - основная цветовая схема
content - основной контент приложения
```

### Смена логотипа

Для смены логотипа нужно заменить файлы в папке /src/assets/img

```
logo.(png/jp(e)g/svg) - основной логотип
logoOnLogin.(png/jp(e)g/svg) - логотип при авторизации
```

### Настройка

Создать файл .env на примере .example.env

```
#  "proxy": "http://localhost:9000" - прокси для запросов
REACT_APP_API_URL=/api/investor_testing - URL для запросов по API
REACT_APP_ADMIN_URL=http://localhost:3000/admin - URL на котором находится панель администратора
```

## Панель администратора

[Создание пользователя с правами администратора](https://github.com/newton-technology/investor-testing/tree/master/backend/investor_testing#%D0%B4%D0%BE%D0%B1%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D1%83%D1%87%D0%B5%D1%82%D0%BD%D0%BE%D0%B9-%D0%B7%D0%B0%D0%BF%D0%B8%D1%81%D0%B8-%D0%B0%D0%B4%D0%BC%D0%B8%D0%BD%D0%B8%D1%81%D1%82%D1%80%D0%B0%D1%82%D0%BE%D1%80%D0%B0)

### Пример авторизация с паролем

```
Логин - example@mail.ru
Пароль - 1234567
```
