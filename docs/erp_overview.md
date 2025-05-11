# ERP Module Overview

This document provides a comprehensive overview of the ERP modules within the Ultimate POS system. It outlines the roles, responsibilities, and interdependencies of each module to aid developers and stakeholders in understanding and maintaining the system.

## Modules

### Sales
Handles all sales transactions, invoicing, quotations, and payment processing.

### Purchases
Manages purchase orders, supplier interactions, and purchase returns.

### Inventory
Tracks product stock levels, stock adjustments, transfers, and opening stock.

### Financials
Manages accounts, payments, ledgers, and financial reporting.

### User Management
Handles user roles, permissions, and authentication.

### Reporting
Generates various reports including sales, purchases, stock, and financial summaries.

### Notifications
Manages email and SMS notifications related to transactions and system events.

## Interdependencies

Modules interact through shared services, middleware, and database transactions to ensure data consistency and workflow integrity.

## References

- Controllers: Located primarily in `app/Http/Controllers/`
- Routes: Defined in `routes/web.php`
- Middleware: Located in `app/Http/Middleware/`
- Helpers: Defined in `app/Http/helpers.php`
- Third-party packages: Refer to `composer.json` for integrated packages supporting PDF generation, notifications, and more.
