<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Project

---------------------ESSENTIALS----------------------
composer update

php artisan migrate

php artisan migrate --seed

php artisan serve

* run 'php artisan migrate --seed' to insert the parking slot info to the db


--------------------API COLLECTION--------------------

POST http://localhost:8000/api/login?email=&password=

POST http://localhost:8000/api/register?name=&email=&password=&password_confirmation=&vehicle_reg_no=&tel_no=&street=&city=&postcode=

GET localhost:8000/api/user

PUT localhost:8000/api/user/1?name=&email=&tel_no=&vehicle_reg_no=&street=&city=&postcode=

GET http://localhost:8000/api/booking-list?dateFrom=&dateTo=

POST http://localhost:8000/api/bookings?user_entry_date=&user_exit_date=&parking_slot_id=&user_id=

PUT http://localhost:8000/api/bookings/2?user_entry_date=&user_exit_date=&parking_slot_id=&user_id=

DELETE localhost:8000/api/bookings/2

POST localhost:8000/api/logout

POST localhost:8000/api/test/payment?user_entry_date=&user_exit_date=&parking_slot_id=
