# Woven ERP System - Project Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Technology Stack](#technology-stack)
4. [Installation & Setup](#installation--setup)
5. [System Modules](#system-modules)
6. [Workflows](#workflows)
7. [Database Schema](#database-schema)
8. [User Roles & Permissions](#user-roles--permissions)
9. [API Routes](#api-routes)
10. [Key Features](#key-features)

---

## Project Overview

**Woven ERP** is a comprehensive Enterprise Resource Planning system designed for textile/manufacturing companies. It manages the complete business cycle from procurement to sales, including production, inventory, and financial tracking.

### Key Objectives
- Streamline business operations
- Manage multi-branch organizations
- Track inventory (raw materials and finished goods)
- Handle sales and purchase transactions
- Monitor production workflows
- Track payments and expenses
- Manage employee attendance and leaves
- Provide role-based access control

---

## System Architecture

### Architecture Pattern
- **Framework**: Laravel 8.x (MVC Pattern)
- **Database**: MySQL
- **Frontend**: Blade Templates with JavaScript
- **PDF Generation**: DomPDF (barryvdh/laravel-dompdf)

### Directory Structure
```
woven_erp/
├── app/
│   ├── Console/Commands/        # Artisan commands
│   ├── Http/
│   │   ├── Controllers/          # Application controllers
│   │   ├── Middleware/           # Custom middleware
│   │   └── Requests/             # Form requests
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic services
│   ├── Helpers/                  # Helper functions
│   └── Traits/                   # Reusable traits
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/                    # Blade templates
│   ├── css/                      # Stylesheets
│   └── js/                       # JavaScript files
├── routes/
│   └── web.php                   # Web routes
└── public/                       # Public assets
```

---

## Technology Stack

### Backend
- **PHP**: 7.3+ / 8.0+
- **Laravel**: 8.75+
- **MySQL**: Database
- **Composer**: Dependency management

### Frontend
- **Blade Templates**: Server-side rendering
- **JavaScript**: Vanilla JS for interactivity
- **CSS**: Custom styling
- **Select2**: Enhanced dropdowns

### Third-Party Packages
- `barryvdh/laravel-dompdf`: PDF generation
- `laravel/sanctum`: API authentication
- `guzzlehttp/guzzle`: HTTP client

---

## Installation & Setup

### Prerequisites
- PHP 7.3+ or 8.0+
- Composer
- MySQL 5.7+
- Node.js & NPM (for assets)

### Installation Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd woven_erp
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=woven_erp
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Create storage link**
```bash
php artisan storage:link
```

7. **Start development server**
```bash
php artisan serve
```

### Initial Setup
- Create Super Admin user:
```bash
php artisan create:super-admin
```
- Access the application at `http://localhost:8000`
- Login with Super Admin credentials
- Configure Company Information
- Create Branches
- Set up Users and Roles

---

## System Modules

### 1. System Administration
- **Organizations**: Multi-tenant organization management
- **Branches**: Branch/location management
- **Users**: User account management
- **Roles**: Role definition
- **Permissions**: Permission management
- **Company Information**: Company details and settings

### 2. Masters
- **Suppliers**: Supplier/vendor management
- **Customers**: Customer management
- **Products**: Finished goods management
- **Raw Materials**: Raw material inventory
- **Employees**: Employee management

### 3. Transactions

#### Purchase Management
- **Purchase Orders**: Create and manage purchase orders
- **Material Inwards**: Record material receipts

#### Production Management
- **Work Orders**: Production work orders
- **Productions**: Production tracking

#### Sales Management
- **Sales Invoices**: Create and manage sales invoices
- **Payment Tracking**: Track customer payments

### 4. Inventory
- **Stock Transactions**: Stock movement tracking
- **Raw Material Stock**: Raw material inventory reports
- **Finished Goods Stock**: Finished goods inventory reports

### 5. HR Management
- **Attendance**: Employee attendance tracking
- **Leaves**: Leave management

### 6. Financial Management
- **Petty Cash**: Daily expense tracking
- **Payment Tracking**: Customer payment records

### 7. CRM
- **Notes**: Customer/transaction notes
- **Tasks**: Task management

---

## Workflows

### 1. Sales Invoice Workflow

```
┌─────────────────┐
│  Create Customer │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│  Create Product │
└────────┬─────────┘
         │
         ▼
┌─────────────────────┐
│  Create Sales Invoice│
│  - Select Customer  │
│  - Add Products      │
│  - Set Tax Type     │
│  (Auto: CGST+SGST   │
│   or IGST)          │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Calculate Totals   │
│  - Subtotal         │
│  - GST (18%)        │
│  - Grand Total      │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Save Invoice       │
│  - Generate Invoice │
│    Number           │
│  - Save Items       │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Print & Save       │
│  - Save Record      │
│  - Open Print Dialog│
└─────────────────────┘
         │
         ▼
┌─────────────────────┐
│  Payment Tracking   │
│  - Record Payments  │
│  - Track Balance    │
└─────────────────────┘
```

**Key Features:**
- Auto-select Tax Type based on company state vs customer billing state
- Default GST: 18% (readonly)
- Mode of Order: Default "IMMEDIATE" (editable)
- Billing Address: Read-only (from customer)
- Shipping Address: Editable with "Same as Billing" option
- Print functionality with PDF generation

### 2. Purchase Order Workflow

```
┌─────────────────┐
│  Create Supplier │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│  Create Raw     │
│  Material       │
└────────┬─────────┘
         │
         ▼
┌─────────────────────┐
│  Create Purchase    │
│  Order              │
│  - Select Supplier  │
│  - Add Raw Materials│
│  - Set GST (18%)    │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Save Purchase Order│
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Material Inward    │
│  - Receive Materials│
│  - Update Stock     │
└─────────────────────┘
```

### 3. Production Workflow

```
┌─────────────────┐
│  Create Work     │
│  Order           │
│  - Select Product│
│  - Set Quantity  │
└────────┬─────────┘
         │
         ▼
┌─────────────────────┐
│  Allocate Raw       │
│  Materials          │
│  - Select Materials │
│  - Set Quantities   │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Start Production   │
│  - Record Start     │
│  - Track Progress   │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Complete Production│
│  - Record Output    │
│  - Update Stock     │
└─────────────────────┘
```

### 4. Payment Tracking Workflow

```
┌─────────────────────┐
│  Select Customer    │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Load Invoices      │
│  (Unpaid/Partially  │
│   Paid Only)        │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Select Invoice     │
│  - View Balance     │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Enter Payment      │
│  - Payment Date     │
│  - Payment Amount   │
│  - Payment Method   │
│  - Remarks          │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Validate & Save    │
│  - Check Amount ≤   │
│    Invoice Balance  │
│  - Save Payment     │
│  - Update Balance   │
└─────────────────────┘
```

### 5. User Authentication Workflow

```
┌─────────────────┐
│  Login Page      │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│  Enter Email    │
│  & Password     │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│  OTP Sent       │
│  (if enabled)   │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│  Verify OTP     │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│  Branch         │
│  Selection      │
└────────┬─────────┘
         │
         ▼
┌─────────────────┐
│  Dashboard      │
└─────────────────┘
```

### 6. Permission Management Workflow

```
┌─────────────────┐
│  Create Role    │
└────────┬─────────┘
         │
         ▼
┌─────────────────────┐
│  Assign Permissions │
│  - Read             │
│  - Write            │
│  - Delete           │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Assign Role to     │
│  User               │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  User Access        │
│  Controlled         │
└─────────────────────┘
```

---

## Database Schema

### Core Tables

#### Organizations & Branches
- `organizations`: Organization details
- `branches`: Branch/location information
- `company_information`: Company details per branch

#### User Management
- `users`: User accounts
- `roles`: Role definitions
- `permissions`: Permission definitions
- `role_permissions`: Role-Permission mapping (with read/write/delete flags)
- `role_permission_audit`: Audit trail for permission changes

#### Masters
- `suppliers`: Supplier information
- `customers`: Customer information
- `products`: Finished goods
- `raw_materials`: Raw materials
- `employees`: Employee information

#### Transactions
- `purchase_orders`: Purchase orders
- `purchase_order_items`: Purchase order line items
- `material_inwards`: Material receipt records
- `material_inward_items`: Material receipt line items
- `work_orders`: Production work orders
- `work_order_materials`: Raw materials allocated to work orders
- `productions`: Production records
- `sales_invoices`: Sales invoices
- `sales_invoice_items`: Sales invoice line items
- `payment_trackings`: Payment records

#### Inventory
- `stock_transactions`: Stock movement history

#### HR
- `attendances`: Attendance records
- `leaves`: Leave applications
- `leave_types`: Leave type definitions

#### Financial
- `petty_cash`: Daily expense records

#### CRM
- `notes`: Notes/remarks
- `note_attachments`: Note file attachments
- `tasks`: Task management

### Key Relationships

```
Organization
  └── Branches (1:N)
      └── Users (N:M)
          └── Roles (N:M)
              └── Permissions (N:M)

Customer
  └── Sales Invoices (1:N)
      └── Sales Invoice Items (1:N)
      └── Payment Trackings (1:N)

Supplier
  └── Purchase Orders (1:N)
      └── Purchase Order Items (1:N)

Product
  └── Sales Invoice Items (1:N)
  └── Work Orders (1:N)

Raw Material
  └── Purchase Order Items (1:N)
  └── Work Order Materials (1:N)
  └── Stock Transactions (1:N)
```

---

## User Roles & Permissions

### Role Hierarchy
1. **Super Admin**: Full system access
2. **Admin**: Branch-level administration
3. **Manager**: Department/function management
4. **User**: Standard user with assigned permissions

### Permission Model
- **Resource-based**: One permission per resource (e.g., `customers`, `suppliers`)
- **Action Flags**: Read, Write, Delete flags in pivot table
- **Granular Control**: Per-resource access control

### Permission Check Flow
```
User Request
    │
    ▼
Check User Roles
    │
    ▼
Check Role Permissions
    │
    ▼
Verify Action (Read/Write/Delete)
    │
    ▼
Allow/Deny Access
```

---

## API Routes

### Authentication Routes
- `GET /login` - Show login form
- `POST /login` - Authenticate user
- `POST /logout` - Logout user
- `GET /forgot-password` - Password reset request
- `POST /forgot-password` - Send reset link

### Dashboard
- `GET /dashboard` - Main dashboard

### System Administration
- `GET /organizations` - List organizations
- `POST /organizations` - Create organization
- `GET /branches` - List branches
- `POST /branches` - Create branch
- `GET /users` - List users
- `POST /users` - Create user
- `GET /roles` - List roles
- `POST /roles` - Create role
- `GET /role-permissions/{role}/edit` - Edit role permissions
- `POST /role-permissions/{role}/update` - Update role permissions

### Masters
- `GET /suppliers` - List suppliers
- `POST /suppliers` - Create supplier
- `GET /customers` - List customers
- `POST /customers` - Create customer
- `GET /products` - List products
- `POST /products` - Create product
- `GET /raw-materials` - List raw materials
- `POST /raw-materials` - Create raw material
- `GET /employees` - List employees
- `POST /employees` - Create employee

### Transactions

#### Purchase
- `GET /purchase-orders` - List purchase orders
- `POST /purchase-orders` - Create purchase order
- `GET /material-inwards` - List material inwards
- `POST /material-inwards` - Create material inward

#### Production
- `GET /work-orders` - List work orders
- `POST /work-orders` - Create work order
- `GET /productions` - List productions
- `POST /productions` - Create production

#### Sales
- `GET /sales-invoices` - List sales invoices
- `POST /sales-invoices` - Create sales invoice
- `GET /sales-invoices/{id}/print` - Print sales invoice
- `GET /payment-trackings` - List payment trackings
- `POST /payment-trackings` - Create payment tracking
- `GET /payment-trackings/get-invoices` - Get invoices for customer (AJAX)

### Inventory
- `GET /stock/raw-material` - Raw material stock report
- `GET /stock/finished-goods` - Finished goods stock report
- `GET /stock-transactions` - List stock transactions
- `POST /stock-transactions` - Create stock transaction

### HR
- `GET /attendances` - List attendance records
- `POST /attendances` - Create attendance
- `GET /leaves` - List leaves
- `POST /leaves` - Create leave

### Financial
- `GET /petty-cash` - List petty cash records
- `POST /petty-cash` - Create petty cash record
- `GET /petty-cash-report` - Petty cash report
- `GET /petty-cash-export/pdf` - Export PDF
- `GET /petty-cash-export/excel` - Export Excel

### CRM
- `GET /notes` - List notes
- `POST /notes` - Create note
- `GET /tasks` - List tasks
- `POST /tasks` - Create task

---

## Key Features

### 1. Multi-Branch Support
- Organizations can have multiple branches
- Users can be assigned to specific branches
- Branch-specific company information
- Branch filtering for transactions

### 2. Role-Based Access Control (RBAC)
- Resource-based permissions
- Granular control (Read/Write/Delete)
- Permission audit trail
- Dynamic permission checking

### 3. Tax Management
- Auto-selection of Tax Type:
  - **Intra-State**: CGST + SGST (same state)
  - **Inter-State**: IGST (different states)
- Default GST: 18% (readonly)
- Tax calculation in invoices

### 4. Address Management
- Individual address fields (line 1, line 2, city, state, postal code, country)
- Billing address (read-only from customer)
- Shipping address (editable)
- "Same as Billing" option

### 5. Payment Tracking
- Track customer payments against invoices
- Show only unpaid/partially paid invoices
- Payment amount validation (cannot exceed balance)
- Multiple payment methods
- Payment history

### 6. Print Functionality
- PDF generation for invoices
- Print dialog integration
- Print & Save workflow
- Custom invoice templates

### 7. Stock Management
- Raw material stock tracking
- Finished goods stock tracking
- Stock transaction history
- Stock reports

### 8. Production Management
- Work order creation
- Raw material allocation
- Production tracking
- Output recording

### 9. Attendance & Leave Management
- Daily attendance recording
- Leave application and approval
- Attendance reports
- Export to PDF/Excel

### 10. Expense Management
- Petty cash tracking
- Daily expense recording
- Expense reports
- Export functionality

### 11. CRM Features
- Notes and attachments
- Task management
- Customer interaction tracking

### 12. Search & Filtering
- Global search functionality
- Filter by date range
- Filter by status
- Pagination support

### 13. Data Export
- PDF export
- Excel export
- Report generation

### 14. Audit Trail
- Role permission changes logged
- User activity tracking
- Change history

---

## Form Specifications

### Sales Invoice Form
- **Customer**: Dropdown (searchable, mandatory)
- **Invoice Date**: Date picker (mandatory, defaults to today)
- **Mode of Order**: Text field (default: "IMMEDIATE", editable)
- **Buyer Order Number**: Text field (optional)
- **Tax Type**: Auto-selected based on state comparison
- **GST Percentage**: Default 18% (readonly)
- **Billing Address**: Individual fields (readonly)
- **Shipping Address**: Individual fields (editable)
- **Same as Billing**: Checkbox
- **Products**: Dynamic line items with:
  - Product selection
  - Quantity
  - Rate
  - Description
  - GST calculation
- **Actions**: Save, Print & Save, Cancel

### Payment Tracking Form
- **Customer**: Dropdown (searchable, mandatory)
- **Invoice Number**: Dropdown (dependent on customer, mandatory)
- **Payment Date**: Date picker (mandatory, defaults to today, past dates only)
- **Payment Amount**: Numeric (mandatory, positive, ≤ invoice balance)
- **Payment Method**: Dropdown (default: Cash)
- **Remarks**: Text area (optional)
- **Actions**: Record Payment, Cancel

### Supplier Form
- **Supplier Name**: Text (mandatory)
- **Contact Name**: Text (optional)
- **Phone**: Text (optional)
- **Email**: Email (optional)
- **Address**: Individual fields (line 1, line 2, city, state, postal code, country)
- **State**: Dropdown (mandatory)
- **GST Number**: Text (optional)
- **Bank Information**: Bank name, account number, IFSC code

### Customer Form
- **Customer Name**: Text (mandatory)
- **Code**: Text (optional)
- **Contact Name**: Text (optional)
- **Phone**: Text (optional)
- **Email**: Email (optional)
- **Billing Address**: Individual fields (line 1, line 2, city, state, postal code, country)
- **State**: Dropdown (mandatory)
- **Shipping Address**: Individual fields (line 1, line 2, city, state, postal code, country)
- **GST Number**: Text (optional)

---

## Security Features

### Authentication
- Email/Password login
- OTP verification (optional)
- Session management
- Password reset functionality

### Authorization
- Role-based access control
- Permission-based page access
- Branch-level data isolation
- CSRF protection

### Data Protection
- Input validation
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade escaping)
- File upload validation

---

## Best Practices

### Code Organization
- Controllers handle HTTP requests
- Models contain business logic
- Services for complex operations
- Helpers for reusable functions
- Traits for shared functionality

### Database
- Use migrations for schema changes
- Use seeders for initial data
- Soft deletes for data retention
- Foreign key constraints
- Indexes for performance

### Frontend
- Blade templates for views
- JavaScript for interactivity
- CSS for styling
- AJAX for dynamic content

### Error Handling
- Try-catch blocks in controllers
- Validation rules
- Error logging
- User-friendly error messages

---

## Troubleshooting

### Common Issues

1. **Permission Denied**
   - Check user roles
   - Verify permissions assigned
   - Check branch access

2. **Invoice Not Loading in Payment Tracking**
   - Verify customer has invoices
   - Check invoice balance > 0
   - Clear route cache: `php artisan route:clear`

3. **Tax Type Not Auto-Selecting**
   - Verify company state is set
   - Check customer/supplier state
   - Ensure JavaScript is enabled

4. **Print Not Working**
   - Check DomPDF installation
   - Verify storage link: `php artisan storage:link`
   - Check file permissions

5. **Branch Not Switching**
   - Clear session: `php artisan session:clear`
   - Verify branch is active
   - Check user branch assignment

---

## Future Enhancements

### Planned Features
- Advanced reporting and analytics
- Mobile app integration
- Email notifications
- Barcode scanning
- Multi-currency support
- Advanced approval workflows
- Integration with accounting software
- Real-time notifications
- Dashboard widgets
- Custom report builder

---

## Support & Maintenance

### Logs
- Application logs: `storage/logs/laravel.log`
- Error tracking via Laravel logging

### Backup
- Regular database backups recommended
- File storage backups for uploads

### Updates
- Keep Laravel and dependencies updated
- Test migrations before production
- Backup before major updates

---

## Contact & Documentation

For additional information or support, please refer to:
- Laravel Documentation: https://laravel.com/docs
- Project Repository: [Repository URL]
- Issue Tracker: [Issue Tracker URL]

---

**Document Version**: 1.0  
**Last Updated**: December 24, 2025  
**Maintained By**: Development Team

