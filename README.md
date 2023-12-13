<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



## About Project

<h5>-----------------------ESSENTIALS------------------------</h5>

composer update

php artisan migrate

php artisan migrate --seed

php artisan serve

* run 'php artisan migrate --seed' to insert 10 parking slots in to the db


<h5>----------------------API COLLECTION----------------------</h5>

POST localhost:8000/api/login?email=&password=

POST localhost:8000/api/register?name=&email=&password=&password_confirmation=&vehicle_reg_no=&tel_no=&street=&city=&postcode=

GET localhost:8000/api/user

PUT localhost:8000/api/user/1?name=&email=&tel_no=&vehicle_reg_no=&street=&city=&postcode=

GET localhost:8000/api/booking-list?dateFrom=&dateTo=

POST localhost:8000/api/bookings?user_entry_date=&user_exit_date=&parking_slot_id=&user_id=

PUT localhost:8000/api/bookings/2?user_entry_date=&user_exit_date=&parking_slot_id=&user_id=

DELETE localhost:8000/api/bookings/2

POST localhost:8000/api/logout

POST localhost:8000/api/test/payment?user_entry_date=&user_exit_date=&parking_slot_id=


<h5>-----------------------ASSUMPTIONS---------------------------</h5>

User:Booking -> 1:M Relation

Booking:Parking Slot -> 1:M Relation

Parking Slot Price Ranges -> 1.June to August(Summer Price) /2.December to February(Winter Price) /3.Remaining Months(Weekend Price/ Weekday Price)
