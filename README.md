## New Vision Project Installation Steps

This is a Laravel Application For Tourism Services.

### Installation Steps:
```bash
composer install
```
```bash
npm install && npm run dev
```
- Create .env file
- Put your database connection in env file
- if you have mail-trap credentials place it in env file
```bash
php artisan key:generate
```

```bash
php artisan app:install
```
- app:install run only once after initialization of project
  - This command runs migration, seeders, roles and permissions, storage link...etc
- After first deployment any changes made you may need to run php artisan deploy
- You Can find an Admin Account in database/seeders/AdminSeeder.php

### Notes

Every thing is a component base in this project, You should browse resources/components/dashboard folder to know what's the components created to use it

- Not Allowed to make any changes to the pre created components
- Any Module With Translation should follow [astrotomic/laravel-translatable](https://docs.astrotomic.info/laravel-translatable/installation)

### New Module Specifications
Every new Module should have:
- Model
- Dashboard controller
- Datatable
- Form Request Validation
- views
- permissions for module CRUD 
  - Browse database/seeders/Permissions
- Dashboard Resource Routes
