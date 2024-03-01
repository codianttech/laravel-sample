## Project Setup
- php version 8.1
- node version 16.x

- composer install
- cp .env.example .env
- Create database in your local phpmyadmin
- Update the DB configurations
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=database that you have created
DB_USERNAME=root
DB_PASSWORD=
```
- php artisan key:generate
- php artisan migrate 
- npm install
- npm run dev
- php artisan serve
- You can access your application using http://localhost:8000 url



#### application start process
- Login page for user http://{APP_URL}/login
- signup page for user http://{APP_URL}/register
