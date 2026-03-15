# Grease Monkey Inventory

Grease Monkey is a web-based inventory system for managing Ford parts and other mechanical items. It helps you keep track of products, stock levels, and transactions in an organized way.

## Features

- Add, update, and remove inventory items (Each item has a unique SKU; part numbers are required depending on the category)  
- Add, update, and delete categories (Specify if a category requires a part number)
- Import and export database backups locally or on the server
- Track stock in and out (Stock Logs)  
- Track user activity (Activity Logs)
- Print stock logs, activity logs, and products can be filtered by (e.g date, remarks, category) 
- Rate limiter for login and forgot password attempts  
- Built with clean, modular PHP code (MVC structure)

## Technologies

- Vanilla PHP (procedural)  
- MySQL  
- Brevo SMTP for email notifications  
- HTML, CSS (Bootstrap), JavaScript  
- Composer for dependency management

## Demo Account

You can try the live system using the backup database:

**Username:** demo123  
**Password:** demo123  

[Check out the live system](https://grease-monkey.ct.ws)  

## Installation
1. Clone the repo
2. Login to the provided demo account and export the SQL backup.
3. Create a database name "inventory" and import the SQL backup.
4. Configure database in config.php
5. Run the system on your local server (XAMPP, Laragon, etc.)

## Note
⚠️ This database is for testing only; changes here do not affect any real system.  
⚠️ The landing page is for advertisement purposes only.
