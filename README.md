# rest-api-wedevs-v1
# Project for order system using RAW PHP with OOP
How works: 
(This is all about backened API. There dont have any frontend developed. Just i have used POSTMAN to do this all)
- This is very very simple product and order processing system
- Two types of user : admin / user. On signup have to use $_POST['user_type'] = 'admin' or $_POST['user_type'] = 'user' (Its must)
- User can signup with his / her credential (firstname, lastname, email, password and user_type)
- Login and then token will be generate
- with that token ANY forntend part can communication with this API. (example #  Authorization: Bearer token_string)
- and then will get user data then

# How to install
- git clone https://github.com/sudiptokarmoker/rest-api-wedevs-v1.git
- composer install
- create .ENV file and paste this code settings : 

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=api_example_wedevs
DB_USERNAME=root
DB_PASSWORD=

- run by terminal or cmd : 
php -S 127.0.0.1:8000 -t public

- done
===================================================
# Route details :
Signup : http://127.0.0.1:8000/user/signup
Login : http://127.0.0.1:8000/user/login
GET User BY Token : http://127.0.0.1:8000/user/auth

Create Product : http://127.0.0.1:8000/product/insert/
Edit Product : http://127.0.0.1:8000/product/update/
Delete Product : http://127.0.0.1:8000/product/delete/

Create Order : http://127.0.0.1:8000/order/create/
Order Status : http://127.0.0.1:8000/order/status/
Order Status Change : http://127.0.0.1:8000/order/update/

For Parameter list please import json file OF POSTMAN. I am sending that.

# what still need to do:
- Frontend integration (there is not any Frontent part of this Assignment)
- Validation need to do

# Featues: 
- This backened has been made on PSR-4 autoload features using composer