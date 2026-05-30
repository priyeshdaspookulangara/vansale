# Prompt for AI Agent: Build Van Sales & Mini-ERP Android App

**Role:** You are an expert Senior Android Developer specialized in Enterprise Resource Planning (ERP) and Point of Sale (POS) systems.

**Objective:**
Build a production-ready Native Android application using **Kotlin** for a Van Sales and Mini-ERP system. This app is for field salesmen to manage spot sales, inventory, and basic accounting.

---

### 1. Technical Stack
- **Language:** Kotlin
- **UI Framework:** Jetpack Compose
- **Architecture:** MVVM with Clean Architecture
- **Local Storage:** Room Database (Offline-first approach)
- **Networking:** Retrofit + OkHttp (REST API integration)
- **Dependency Injection:** Hilt
- **Concurrency:** Coroutines & Flow
- **Background Tasks:** WorkManager (for background data synchronization)
- **Reports:** PDF generation for invoices (e.g., using Android Print Framework)

---

### 2. Core Functional Modules

#### A. Authentication & User Management
- Secure Login with role-based access: **Super Admin**, **Accountant**, **Van Salesman**.
- Van Assignment: Each salesman is assigned a specific Van ID.

#### B. Van Sales & POS (Field Operations)
- **Customer Selection:** Searchable list of customers with state-code awareness.
- **Product Catalog:** List products with real-time van stock quantity.
- **Sales Logic:**
    - **GST Calculation:** Automatically split tax into CGST/SGST (Intra-state) or IGST (Inter-state) by comparing the Business State Code (default: 27) with the Customer's State Code.
    - **Inventory Protection:** Prevent sale if requested quantity > current van stock.
    - **Payment Modes:** Support Cash, UPI, Bank Transfer, and Credit (Accounts Receivable).
- **Invoice Generation:** Generate a unique Invoice No, save to local DB, and prepare for sync.

#### C. Inventory Management
- **Van Stock:** Live view of products currently in the van.
- **Stock Receiving:** Interface to accept stock transfers from the central warehouse.
- **History:** Log of inventory movements.

#### D. Pre-Order Management
- Book orders for future fulfillment.
- View and manage 'Pending' orders.

#### E. Accounting Integration (Double-Entry)
- **Automated Postings:** Upon invoice finalization, generate double-entry journal items:
    - Debit: Cash or Accounts Receivable (based on payment mode).
    - Credit: Sales Revenue (Taxable Value).
    - Credit: CGST/SGST/IGST Liability accounts.
- **Manual Vouchers:** Input screen for field expenses (Payment Voucher) or collection (Receipt Voucher).

---

### 3. Key Business Logic & Validations
- **Currency:** Use `BigDecimal` for all financial calculations (2 decimal precision).
- **Taxation:** Use percentage-based GST rates from the product master.
- **Offline Sync:** Use a "Dirty Flag" or "Sync Status" column in Room. Sync local Invoices/Vouchers to the server in the background when connectivity is detected.

### 4. UI/UX Requirements
- **Material Design 3** implementation.
- Optimized for one-handed use in the field.
- Dark mode support.
- Interactive Dashboard for sales targets and daily collections.

---

### 5. Database Schema Guidance (Local Room DB)
- **Masters:** `User`, `Product`, `Category`, `Customer`, `State`.
- **Inventory:** `VanInventory`.
- **Transactions:** `Order`, `OrderItem`, `Invoice`, `InvoiceItem`.
- **Accounting:** `JournalEntry`, `JournalItem`, `AccountHead`.
