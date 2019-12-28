[Задание на google.docs](https://docs.google.com/document/d/19YrVd-pbw_NrA4qs52MbEsVu8y_hBgecM3oGR_OgMpI)

- `$ composer install`

- `$ cp .env.example .env`

- создать базу, настроить параметры подключения в .env

- `$ php artisan key:generate`

- `$ php artisan migrate`

- `$ php artisan serve` ИЛИ `$ vendor/bin/phpunit test/Feature/ApiTest`

Получить список рутов: `$ php artisan route:list`

Frontend приложение получает token, обращаясь на */api/login* методом POST, Content-type: application/json, в теле содержится JSON с данными авторизации.

Данные авторизациии:
 - *login*: frontend app
 - *password*: содержится в */config/api.php*, ключ "api_password"
 
Все дальнейшие запросы на Api выполняются с использованием полученного токена в заголовке *Authorization: Bearer* ***\<token\>*** и указанием *Accept: application/json*.

В случае бездействия на протяжении 10 минут, token будет аннулирован.

Входная точка */api/cards/{id_bill}* получает на вход два параметра: **page** и **search**
- **search** - условие поиска "описание карточки содержит **search**"
- **page** - страница