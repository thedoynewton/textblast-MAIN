<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# BRANCH - Text Broadcasting System

## Important Information

Ensure you have the .env file within your laravel project folder.


### 1. Database Setup

**For Windows Users:**

1. Install Microsoft SQL Server for database purposes.
2. Clone the repository to your local environment.

**For Mac Users:**

1. Install Docker and Azure Data Studio.
2. Create a container in your docker before creating a database connection.
Run this docker command, to create a container specific to MacOS M1 Chip
```bash
# For Docker Set-Up
docker pull --platform linux/arm64/v8 mcr.microsoft.com/azure-sql-edge

# Replace userNameHere, YourPasswordHere, serverName base from your needs
docker run -e 'ACCEPT_EULA=Y' -e 'MSSQL_usernameHere_PASSWORD=YourPasswordHere' -p 1433:1433 --name serverName --platform linux/arm64/v8 -d mcr.microsoft.com/azure-sql-edge
```

3. Create a database connection in your Azure Data Studio
After the connection between Docker and Azure Data Studio is established,
4. Create an empty database without tables.
5. Run this command in your Laravel project terminal
```bash
# This should create the tables in your database
php artisan migrate
```

6. Run a new query in your database, for the data to be stored in the table
7. Clone the repository to your local environment.



### 2. Project Setup

Once the database has been set up, navigate to your project directory and run the following commands:

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm i
```

### 3. Running the Application

To start the application, execute the following commands:

```bash
# Start the Laravel development server
php artisan serve

# Start the queue worker
php artisan queue:work

# Compile assets with Vite
npm run dev  # For Vite to load
```

### 4. Blade Icons

If you encounter an error with Blade Icons, this command should install the required dependencies.
```bash
composer install

# For the Message Templates Icons
composer require codeat3/blade-teeny-icons
composer require postare/blade-mdi
```


### Additional Notes

1. Ensure your .env file is configured with the correct database connection details.
2. For further assistance, contact the development team
