# Application Flow Diagram

## 🔄 Complete User Journey

### 1. Authentication Flow
```
┌─────────────────┐
│  Landing Page   │
│   (Login)       │
└────────┬────────┘
         │
    ┌────▼─────┐
    │  Submit  │
    │  Creds   │
    └────┬─────┘
         │
    ┌────▼──────────────┐
    │  AuthController   │
    │  • Validate user  │
    │  • Check password │
    │  • Create session │
    └────┬──────────────┘
         │
    ┌────▼────────┐
    │  Dashboard  │
    └─────────────┘
```

### 2. License Management Flow
```
┌──────────────┐
│  Dashboard   │
└──────┬───────┘
       │
       ├─────► Create License
       │       ├─► Input domain
       │       ├─► Set limits
       │       ├─► Set expiry
       │       └─► Generate API key
       │
       ├─────► View Licenses
       │       ├─► Paginated list
       │       ├─► Search filter
       │       └─► Status badges
       │
       ├─────► Edit License
       │       ├─► Update info
       │       ├─► Change status
       │       └─► Modify limits
       │
       └─────► Delete License
               └─► Confirm & remove
```

### 3. API Validation Flow
```
┌──────────────────┐
│  Client App      │
│  Sends Request:  │
│  • api_key       │
│  • domain        │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  ApiController   │
│  validate()      │
└────────┬─────────┘
         │
    ┌────▼─────────────────┐
    │  Validation Checks   │
    ├──────────────────────┤
    │  1. API key exists?  │
    │  2. License active?  │
    │  3. Not expired?     │
    │  4. Domain matches?  │
    │  5. Under limit?     │
    └────┬─────────────────┘
         │
    ┌────▼────┐
    │  Valid? │
    └────┬────┘
         │
    ┌────▼─────────────┐     ┌──────────────┐
    │  YES              │     │  NO          │
    │  • Log success    │     │  • Log fail  │
    │  • Increment      │     │  • Return    │
    │  • Return data    │     │    error     │
    └───────────────────┘     └──────────────┘
```

### 4. Dashboard Data Flow
```
┌────────────────┐
│  User Login    │
└────────┬───────┘
         │
         ▼
┌──────────────────────┐
│  DashboardController │
└────────┬─────────────┘
         │
    ┌────▼──────────────────┐
    │  Fetch Statistics     │
    ├───────────────────────┤
    │  • Total licenses     │
    │  • Active licenses    │
    │  • Expired licenses   │
    │  • Expiring soon      │
    └────┬──────────────────┘
         │
    ┌────▼──────────────────┐
    │  Fetch Activities     │
    ├───────────────────────┤
    │  • Recent API logs    │
    │  • Status indicators  │
    │  • IP addresses       │
    └────┬──────────────────┘
         │
         ▼
    ┌───────────────┐
    │  Render View  │
    │  (Dashboard)  │
    └───────────────┘
```

## 🗺️ Routing Map

### Public Routes (No Auth Required)
```
/                    → AuthController@login
/login               → AuthController@login
/register            → AuthController@register
```

### Protected Routes (Auth Required)
```
/dashboard           → DashboardController@index
/logout              → AuthController@logout

Licenses:
/licenses            → LicenseController@index
/licenses/create     → LicenseController@create
/licenses/show       → LicenseController@show
/licenses/edit       → LicenseController@edit
/licenses/delete     → LicenseController@delete
/licenses/regenerate-api-key → LicenseController@regenerateApiKey
/licenses/reset-request-count → LicenseController@resetRequestCount

Users (Super Admin Only):
/users               → UserController@index
/users/edit          → UserController@edit
/users/delete        → UserController@delete
```

### API Routes (No Auth)
```
/api/validate        → ApiController@validate
```

## 📊 Database Relationships

```
┌─────────────┐
│   users     │
│─────────────│
│ id (PK)     │
│ username    │
│ email       │
│ password    │
│ role        │
└──────┬──────┘
       │
       │ created_by (FK)
       │
       ▼
┌─────────────┐
│  licenses   │
│─────────────│
│ id (PK)     │
│ domain      │
│ api_key     │
│ status      │
│ request_*   │
│ expires_at  │
│ created_by  │
└──────┬──────┘
       │
       │ license_id (FK)
       │
       ▼
┌─────────────┐
│  api_logs   │
│─────────────│
│ id (PK)     │
│ license_id  │
│ ip_address  │
│ status      │
│ message     │
│ created_at  │
└─────────────┘
```

## 🎨 View Hierarchy

```
layouts/main.php (Base Layout)
│
├── auth/
│   ├── login.php
│   └── register.php
│
├── dashboard/
│   └── index.php
│       ├── Statistics Cards
│       ├── Quick Actions
│       ├── Expiring Soon
│       └── Recent Activities
│
├── licenses/
│   ├── index.php (List)
│   │   ├── Search Bar
│   │   ├── Table
│   │   └── Pagination
│   │
│   ├── create.php (Form)
│   │   ├── Domain Input
│   │   ├── Status Select
│   │   ├── Limit Input
│   │   └── Expiry Date
│   │
│   ├── edit.php (Form)
│   │   └── Pre-filled Data
│   │
│   └── show.php (Details)
│       ├── License Info
│       ├── API Key Display
│       ├── Usage Stats
│       ├── Code Examples
│       └── Activity Logs
│
└── users/
    ├── index.php (List)
    │   ├── User Table
    │   └── Actions
    │
    └── edit.php (Form)
        ├── Username
        ├── Email
        ├── Role
        └── Password
```

## 🔐 Security Flow

```
┌──────────────┐
│  User Input  │
└──────┬───────┘
       │
       ▼
┌──────────────────┐
│  Input Filter    │
│  • Trim          │
│  • Validate      │
│  • Sanitize      │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│  Controller      │
│  • Check Auth    │
│  • Check Roles   │
│  • Process Data  │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│  Model (PDO)     │
│  • Prepared Stmt │
│  • Params Bind   │
│  • Execute       │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│  View Output     │
│  • htmlspecial   │
│  • Escape        │
│  • Render        │
└──────────────────┘
```

## 📈 Request Counter Flow

```
API Request
    │
    ▼
Validate License
    │
    ▼
Check request_count < request_limit
    │
    ├─── YES ──► Allow
    │            │
    │            ▼
    │       Increment request_count
    │            │
    │            ▼
    │       Log Success
    │
    └─── NO ───► Block
                 │
                 ▼
            Log Blocked
            │
            ▼
        Return 429
```

## ⏰ Expiry Check Flow

```
Dashboard Load
    │
    ▼
updateExpiredLicenses()
    │
    ▼
UPDATE licenses
SET status = 'expired'
WHERE expires_at <= CURDATE()
  AND status = 'active'
    │
    ▼
Get Expiring Soon (7 days)
    │
    ▼
Display Warnings
```

## 🔄 Session Management

```
Login Success
    │
    ▼
Create Session
    ├─► user_id
    ├─► username
    ├─► role
    └─► timestamp
        │
        ▼
    Each Request
        │
        ▼
    Check Session
        │
        ├─── Exists ──► Continue
        │
        └─── Not ────► Redirect to Login
```

## 📝 Log Creation Flow

```
API Validation
    │
    ▼
Determine Status
    ├─► Success
    ├─► Failed
    └─► Blocked
        │
        ▼
Create Log Entry
    ├─► license_id
    ├─► ip_address
    ├─► request_domain
    ├─► status
    ├─► message
    └─► timestamp
        │
        ▼
    Store in DB
        │
        ▼
    Display in Dashboard/License Detail
```

## 🎯 Complete Application Architecture

```
┌─────────────────────────────────────────┐
│            Web Browser                  │
│  ┌────────────────────────────────┐    │
│  │    User Interface (Views)      │    │
│  │    • HTML + Tailwind CSS       │    │
│  │    • Alpine.js Interactions    │    │
│  └────────────────────────────────┘    │
└──────────────┬──────────────────────────┘
               │ HTTP Request
               ▼
┌─────────────────────────────────────────┐
│         .htaccess (URL Rewrite)         │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│          index.php (Router)             │
│  • Parse URL                            │
│  • Load Config                          │
│  • Load Models                          │
│  • Route to Controller                  │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│       Controllers (Business Logic)      │
│  • AuthController                       │
│  • DashboardController                  │
│  • LicenseController                    │
│  • UserController                       │
│  • ApiController                        │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│       Models (Data Layer)               │
│  • Database                             │
│  • User                                 │
│  • License                              │
│  • ApiLog                               │
└──────────────┬──────────────────────────┘
               │ PDO
               ▼
┌─────────────────────────────────────────┐
│          MySQL Database                 │
│  • users                                │
│  • licenses                             │
│  • api_logs                             │
└─────────────────────────────────────────┘
```

---

## 🎨 UI Component Hierarchy

```
Page Structure:
├── Navigation Bar
│   ├── Logo
│   ├── Menu Items
│   └── User Dropdown
│
├── Flash Messages
│   ├── Success Alert
│   └── Error Alert
│
├── Main Content Area
│   ├── Page Header
│   ├── Action Buttons
│   ├── Statistics Cards (Dashboard)
│   ├── Tables (List Views)
│   ├── Forms (Create/Edit)
│   └── Detail Views (Show)
│
└── Footer
    └── Copyright Info
```

---

This flow diagram provides a complete visual understanding of how the License Management System operates from end to end.
