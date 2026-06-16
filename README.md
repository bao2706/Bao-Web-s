# Personal Finance Tracker

A simple PHP and MySQL web app for tracking personal income and expenses.

## Features

- User registration and login with hashed passwords
- Session-based authentication
- Add, edit, and delete income or expense transactions
- Filter recent transactions by day, week, or month
- Monthly income, expense, balance, and spending progress summary
- Basic request protection for write actions

## Tech Stack

- PHP
- MySQL or MariaDB
- Bootstrap 5
- JavaScript

## Setup

1. Copy this project into your XAMPP `htdocs` folder.
2. Start Apache and MySQL from XAMPP.
3. Import `test3.sql` into MySQL or phpMyAdmin.
4. Open `http://localhost/Bao-Web-s/register/register.php`.
5. Create an account, then log in and start adding transactions.

## Database

The app uses a MySQL database named `test3` with two tables:

- `members` for user accounts
- `income` for income and expense transactions

The SQL file contains only schema, not personal sample data.
