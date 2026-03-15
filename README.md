# Grease Monkey Inventory

Grease Monkey is a web-based inventory system for managing Ford parts and other mechanical items. It helps you keep track of products, stock levels, and transactions in an organized way.

## Features

- Add, update, and remove inventory items (Each item has a unique SKU; part numbers are required depending on the category)  
- Add, update, and delete categories (Specify if a category requires a part number)
- Import and export database backups locally or on the server
- Track stock in and out (Stock Logs)  
- Track user activity (Activity Logs)
- print stock logs, activity logs, and products can be filtered by (e.g date, remarks, category) 
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

⚠️ This database is for testing purposes only — feel free to add, update, or delete items without affecting the real system.
