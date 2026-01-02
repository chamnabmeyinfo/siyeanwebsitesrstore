# Codebase Index - SR MAC Shop POS System

## Overview
A PHP 8.2+ web-based Point of Sale (POS) system for tracking MacBook inventory and sales. Built with SQLite for local storage, optimized for single-operator use.

## Project Structure

```
srmacshop/
├── siyean/                    # Main application directory
│   ├── config/                # Configuration files
│   │   ├── app.php           # Active configuration (not in git)
│   │   └── app.example.php   # Configuration template
│   ├── docs/                  # Documentation
│   │   └── pos_plan.md       # Architecture and design notes
│   ├── public/               # Web-accessible files
│   │   ├── index.php        # Front controller/router
│   │   └── assets/          # Static assets (logos, images)
│   ├── scripts/              # CLI utility scripts
│   │   ├── create_user.php  # User creation script
│   │   ├── seed_inventory.php # Sample data seeding
│   │   └── test_notifications.php
│   ├── src/                  # Application source code (PSR-4: App\)
│   │   ├── Database.php     # Database connection & migrations
│   │   ├── InventoryRepository.php
│   │   ├── SaleService.php
│   │   ├── ReportService.php
│   │   ├── UserRepository.php
│   │   ├── BookingRepository.php
│   │   └── NotificationService.php
│   ├── storage/              # Data storage
│   │   └── pos.db           # SQLite database (auto-created)
│   ├── templates/            # View templates
│   │   ├── layout.php       # Admin layout
│   │   ├── layout_auth.php  # Authentication layout
│   │   ├── layout_store.php  # Public storefront layout
│   │   ├── dashboard.php
│   │   ├── inventory.php
│   │   ├── inventory_form.php
│   │   ├── inventory_import.php
│   │   ├── sales.php
│   │   ├── sale_form.php
│   │   ├── bookings.php
│   │   ├── store.php
│   │   ├── store_product.php
│   │   ├── auth_login.php
│   │   └── 404.php
│   ├── vendor/               # Composer dependencies
│   ├── composer.json        # PHP dependencies
│   └── README.md            # Project documentation
└── README.md                # Root README
```

## Core Components

### 1. Database Layer (`src/Database.php`)
- **Purpose**: SQLite connection management and schema migrations
- **Key Features**:
  - Singleton PDO connection pattern
  - Auto-migration on first request
  - Schema evolution support (adds missing columns)
  - Slug backfilling for existing inventory

**Tables**:
- `inventory` - Product catalog
- `customers` - Customer records
- `sales` - Sales transactions
- `bookings` - Online reservations
- `users` - Authentication & authorization

### 2. Inventory Management (`src/InventoryRepository.php`)
- **Purpose**: CRUD operations for product inventory
- **Methods**:
  - `all(bool $onlyVisible)` - List all/visible items
  - `create(array $data)` - Add new SKU
  - `update(string $originalSku, array $data)` - Modify existing item
  - `delete(string $sku)` - Remove SKU (admin only)
  - `adjustQuantity(string $sku, int $delta)` - Quick stock adjustments
  - `findBySku(string $sku)` - Lookup by SKU

**Data Fields**:
- SKU, slug, model, storage capacity, color
- Cost price, list price, online price
- Quantity on hand
- Hero image, gallery images (JSON)
- Web description, visibility flag

### 3. Sales Processing (`src/SaleService.php`)
- **Purpose**: Record sales and manage inventory deductions
- **Methods**:
  - `record(array $payload)` - Create sale transaction
  - `sales(?string $from, ?string $to)` - Query sales history
  - `total(array $sale)` - Calculate sale total (with tax/discount)

**Features**:
- Automatic stock validation
- Customer deduplication (upsert)
- Transaction-safe inventory updates
- Supports discounts, tax rates, payment methods

### 4. Reporting (`src/ReportService.php`)
- **Purpose**: Business metrics and analytics
- **Methods**:
  - `summary(?string $from, ?string $to)` - Revenue/unit aggregations
  - `inventorySnapshot()` - Current stock levels

**Metrics**:
- Sale count, units sold, total revenue
- Average ticket size
- Inventory levels sorted by quantity

### 5. User Management (`src/UserRepository.php`)
- **Purpose**: Authentication and role-based access control
- **Methods**:
  - `create(string $name, string $email, string $password, string $role)`
  - `findByEmail(string $email)`
  - `findById(int $id)`
  - `count()` - Total user count

**Roles**:
- `admin` - Full access (inventory delete/import)
- `clerk` - Sales and inventory management
- `ecommerce` - Booking management

### 6. Booking System (`src/BookingRepository.php`)
- **Purpose**: Online reservation management
- **Methods**:
  - `create(array $payload)` - New booking
  - `findAll(?string $status)` - List bookings (optionally filtered)
  - `findById(int $id)` - Get booking details
  - `updateStatus(int $id, string $status)` - Change booking state

**Status Flow**:
- `pending` → `confirmed` → `picked_up` / `cancelled`

### 7. Notifications (`src/NotificationService.php`)
- **Purpose**: Alert system for bookings and low stock
- **Channels**:
  - Email (via PHP `mail()`)
  - Telegram (via Bot API)
- **Triggers**:
  - New booking created
  - Booking status changed
  - Low stock threshold reached

## Routing & Request Handling (`public/index.php`)

### Authentication Routes
- `GET /login` - Login form
- `POST /login` - Authenticate user
- `POST /logout` - End session

### Admin Dashboard
- `GET /` - Dashboard with metrics and recent inventory

### Inventory Management
- `GET /inventory` - List all inventory
- `GET /inventory/new` - Create form
- `GET /inventory/edit?sku=...` - Edit form
- `POST /inventory/store` - Create item
- `POST /inventory/update` - Update item
- `POST /inventory/delete` - Delete item (admin only)
- `POST /inventory/adjust` - Quick quantity adjustment
- `GET /inventory/import` - CSV import form (admin)
- `POST /inventory/import` - Process CSV upload (admin)
- `GET /inventory/export` - Download CSV export (admin)

### Sales Management
- `GET /sales` - Sales history table
- `GET /sales/new` - New sale form
- `POST /sales/create` - Record sale

### Booking Management
- `GET /bookings` - Booking console (admin/ecommerce)
- `POST /bookings/status` - Update booking status

### Public Storefront
- `GET /store` - Product catalog (public)
- `GET /store/product?slug=...` - Product detail page (public)
- `POST /store/book` - Submit booking request (public)

## Helper Functions (in `public/index.php`)

- `render(string $template, array $data)` - Template rendering with layout
- `redirect(string $path)` - HTTP redirect
- `setFlash(string $status, string $message)` - Flash messages
- `currentUser()` - Get authenticated user
- `requireAuth()` - Enforce authentication
- `requireRole(array $roles)` - Enforce role-based access
- `isPublicRoute(string $method, string $path)` - Route visibility check
- `slugify(string $value)` - URL-friendly slug generation

## Configuration (`config/app.php`)

**Structure**:
```php
[
    'notifications' => [
        'low_stock_threshold' => int,
        'email' => [
            'enabled' => bool,
            'from' => string,
            'to' => string
        ],
        'telegram' => [
            'enabled' => bool,
            'bot_token' => string,
            'chat_id' => string
        ]
    ]
]
```

**Environment Variables**:
- `LOW_STOCK_THRESHOLD` - Stock alert threshold
- `MAIL_ENABLED` - Enable email notifications
- `MAIL_FROM` - Sender email address
- `MAIL_TO` - Recipient email(s), comma-separated
- `TELEGRAM_ENABLED` - Enable Telegram notifications
- `TELEGRAM_BOT_TOKEN` - Telegram bot token
- `TELEGRAM_CHAT_ID` - Telegram chat ID

## Utility Scripts (`scripts/`)

### `create_user.php`
Creates a new user account via CLI.

**Usage**:
```bash
php scripts/create_user.php --name="Admin" --email="admin@example.com" --password="secret" [--role=admin]
```

**Roles**: `admin`, `clerk`, `ecommerce`

### `seed_inventory.php`
Populates database with sample MacBook/PC inventory items.

**Usage**:
```bash
php scripts/seed_inventory.php
```

## Data Model

### Inventory
```sql
- id (PK)
- sku (UNIQUE)
- slug (UNIQUE, URL-friendly)
- model
- storage_capacity (INT)
- color
- cost_price (REAL)
- list_price (REAL)
- online_price (REAL, nullable)
- quantity_on_hand (INT, default 0)
- hero_image (TEXT, nullable)
- gallery_images (TEXT, JSON array, nullable)
- web_description (TEXT, nullable)
- visible_online (INT, default 1)
- created_at (TEXT, timestamp)
```

### Customers
```sql
- id (PK)
- name
- email (nullable)
- phone (nullable)
```

### Sales
```sql
- id (PK)
- inventory_id (FK → inventory.id)
- customer_id (FK → customers.id)
- quantity (INT)
- unit_price (REAL)
- discount (REAL, default 0)
- tax_rate (REAL, default 0)
- payment_method (TEXT)
- notes (TEXT, nullable)
- sold_at (TEXT, timestamp)
```

### Bookings
```sql
- id (PK)
- inventory_id (FK → inventory.id)
- customer_name (TEXT)
- customer_email (TEXT)
- customer_phone (TEXT)
- quantity (INT, default 1)
- deposit_amount (REAL, default 0)
- preferred_date (TEXT, nullable)
- preferred_time (TEXT, nullable)
- status (TEXT, default 'pending')
- notes (TEXT, nullable)
- created_at (TEXT, timestamp)
- converted_sale_id (FK → sales.id, nullable)
```

### Users
```sql
- id (PK)
- name
- email (UNIQUE)
- password_hash
- role (TEXT, default 'admin')
- created_at (TEXT, timestamp)
```

## Security Features

1. **Password Hashing**: Uses PHP `password_hash()` with `PASSWORD_DEFAULT`
2. **Session Management**: `session_regenerate_id()` on login/logout
3. **Role-Based Access Control**: Route-level authorization checks
4. **SQL Injection Prevention**: PDO prepared statements throughout
5. **CSRF Protection**: (Not implemented - consider adding for production)

## Dependencies

- **PHP**: 8.2+ with `ext-pdo` and `ext-sqlite3`
- **Composer**: Dependency management
- **No external packages**: Pure PHP implementation

## Development Workflow

1. **Setup**:
   ```bash
   composer install
   composer serve  # Starts dev server on http://127.0.0.1:8000
   ```

2. **Initialization**:
   ```bash
   php scripts/create_user.php --name="Admin" --email="admin@example.com" --password="password" --role=admin
   php scripts/seed_inventory.php  # Optional
   ```

3. **Configuration**:
   - Copy `config/app.example.php` to `config/app.php`
   - Configure notification settings

## Key Design Decisions

1. **SQLite**: Chosen for portability and zero-configuration
2. **Single-file router**: `public/index.php` handles all routing
3. **Template-based views**: PHP templates with layout system
4. **Repository pattern**: Data access abstraction
5. **Service layer**: Business logic separation (SaleService, ReportService)
6. **No framework**: Minimal dependencies for easy deployment

## Extension Points

- **Payment Processing**: Integrate payment gateways in `SaleService::record()`
- **Barcode Scanning**: Add SKU lookup endpoint
- **Multi-tenant**: Add `store_id` foreign keys
- **API Layer**: Extract business logic for REST API
- **Testing**: Add PHPUnit test suite with in-memory SQLite

## Known Limitations

1. Single-user concurrency (no locking)
2. No payment processor integration
3. No barcode scanner support
4. Basic email notifications (PHP `mail()`)
5. No CSRF protection
6. No rate limiting on public routes

## File Count Summary

- **PHP Source Files**: 7 classes in `src/`
- **Templates**: 14 view files in `templates/`
- **Scripts**: 3 utility scripts
- **Configuration**: 2 config files
- **Total PHP Files**: ~26 (excluding vendor)

---

*Generated: $(date)*
*Last Updated: Indexing complete*
