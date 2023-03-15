#!/bin/sh

php artisan queue:work --queue=checks --no-ansi --no-interaction &
php artisan queue:work --queue=sends --no-ansi --no-interaction &

php artisan emails:promote --no-ansi --no-interaction
php artisan emails:check --no-ansi --no-interaction
php artisan emails:send --no-ansi --no-interaction

php artisan schedule:work --no-ansi --no-interaction
