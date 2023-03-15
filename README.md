# Karma8 Laravel demo

Cервис проверки почтовых адресов и отправки писем.

Два варианта запуска и использования.

## 1. Локальный

### Системные требования

* PHP 8.2
    * ext-ctype
    * ext-curl
    * ext-dom
    * ext-session
    * ext-mbstring
    * ext-openssl
    * ext-pdo_sqlite
    * ext-phar
    * ext-tokenizer
    * ext-zend-opcache
* Composer

Проверка системный требований

```bash
composer check-platform-reqs --no-dev
```

### Установка

```bash
git clone https://github.com/karma8-demo/laravel.git
cd laravel
composer install --no-dev
mv .env.production .env
php artisan migrate --force
php artisan optimize
```

Создание 1 млн тестовых данных (может занять несколько минут)

```bash
php artisan db:seed --force
```

Альтернативный вариант - использование предварительно наполненной БД

```bash
mv database/database.example.sqlite database/database.sqlite
```

### Использование

Запустить воркеры для проверки почтовых адресов и отправки писем

```bash
php artisan queue:work --queue=checks
php artisan queue:work --queue=sends
```

Выполнить команды для загрузки почтовых адресов в очереди

```bash
php artisan emails:promote
php artisan emails:check
php artisan emails:send
```

Запустить ежечасный планировщик

```bash
php artisan schedule:work
```

Просмотр логов

```bash
tail -f storage/logs/laravel.log
```

## 2. Docker

### Системные требования

Docker Desktop (Windows, MacOS) или docker-cli и docker-compose (Linux)

### Установка

```bash
git clone https://github.com/karma8-demo/laravel.git
cd laravel
docker build --progress=plain --tag laravel .
```

### Использование

Запустить микросервис с воркерами и планировщиком

```bash
docker run --interactive --tty laravel
```

Логи выводится на экран и в файл `storage/logs/laravel.log` в контейнере

## Ключевые моменты

* Максимально облегчённая конфигурация Laravel
* БД SQLite с некоторыми ограничениями:
    * При запуске в Docker используется предварительно наполненная БД, чтобы избежать долгого создания тестовых данных
    * Возможны блокировки БД или отдельных таблиц
* Поле emails.email, дублирующее users.email, заменено на внешний ключ user_id
* Добавлены индексы по всем полям для выборок
* Выбор адресов для проверки и рассылки производится в командах *app/Console/Commands/EmailsPromote.php*, *app/Console/Commands/EmailsCheck.php* и *app/Console/Commands/EmailsSend.php*. Запуск возможен вручную (`php artisan emails:promote`, `php artisan emails:check` и `php artisan emails:send` соответственно) или планировщиком (каждый час)
* Выбранные адреса загружаются в очереди *checks* и *sends* и обрабатываются в *app/Jobs/EmailCheck.php* и *app/Jobs/EmailSend.php*, в них же эмулируется случайная скорость проверки адресов и отправки сообщений. Для каждой очереди можно запустить любое количество отдельных воркеров для достижения необходимой производительности
* Проверенным адресом присваивается случайное значение valid=true|false
* Confirmed-адреса не проверяются: им сразу присваивается checked=true и valid=true (экономия стоимости проверки)
* Время отправки письма записывается в поле users.notifiedts, для исключения повторной отправки
* Очереди реализованы в общей БД с данными

## Возможные улучшения

* Замена SQLite на внешнюю БД (MariaDB, MySQL, PostgreSQL и т.д.) и перенос очередей, например, в Redis для избежания блокировок БД при записи адресов и увеличения производительности
* Перенос поля confirmed в таблицу emails
* Перенос воркеров и планировщика в отдельные контейнеры Docker для гибкого и независимого масштабирования
* Множественные адреса для одного пользователя
* Не спамить: слать письма только на confirmed адреса - в таком случае не нужны дополнительные проверки адресов
* Использование Postfix или другого MTA для ускорения обработки очереди отправки писем
* Использование transactional-писем c шаблонами вместо непосредственной отправки
