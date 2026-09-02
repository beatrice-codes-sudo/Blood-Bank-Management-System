# 📁 HemoLink — Codebase File Guide

> **Who is this for?** New developers trying to find where things live and what each file does.

---

## Quick Reference Table

| File | Layer | What It Does |
|---|---|---|
| `index.php` | Router | Receives every request, reads `?page=`, calls the right controller |
| `config/app.php` | Config | App-wide constants: name, roles, blood types, pricing plans |
| `config/database.php` | Config | Singleton database connection (PDO) |
| `config/init.php` | Bootstrap | Loads ALL files and defines global helper functions |
| `controllers/AuthController.php` | Controller | Login, logout, donor registration, hospital registration |
| `controllers/AdminController.php` | Controller | Everything the blood bank admin can do |
| `controllers/HospitalController.php` | Controller | Everything the hospital manager can do |
| `controllers/DonorController.php` | Controller | Everything the donor can do |
| `models/User.php` | Model | User login, registration, status toggle |
| `models/BloodInventory.php` | Model | Stock levels, blood units, emergency appeals, thresholds |
| `models/Hospital.php` | Model | Hospital records, requests, distributions |
| `models/Donor.php` | Model | Donor profiles, donations, appointments, health history |
| `models/Requests.php` | Model | Blood requisition CRUD helpers |
| `models/Payment.php` | Model | Paystack payment recording and verification |
| `BMS SCHEMA.sql` | Database | Creates all 14 tables from scratch |
| `seed.sql` | Database | Fills tables with realistic test data |
| `.env` | Config | Secret keys — **do not share or commit** |

---

## Config Files (`config/`)

### `config/app.php`
The heart of the app's configuration. Defines constants that are used everywhere:

```php
define('APP_NAME', 'HemoLink');
define('BASE_URL', '/BMS');
define('ROLE_ADMIN', 'Admin');
define('ROLE_HOSPITAL', 'Hospital');
define('ROLE_DONOR', 'Donor');
define('BLOOD_TYPES', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
```

It also loads the `.env` file and sets up the **Paystack pricing plan** definitions (Starter, Professional, Enterprise).

### `config/database.php`
The **Database class** — creates one shared MySQL connection for the whole app. Every model uses `Database::getInstance()->getConnection()` to get a `PDO` object.

### `config/init.php`
This is the **bootstrap file** — the first thing `index.php` loads. It:
1. Loads `app.php` (constants first, before session starts)
2. Starts the PHP session
3. Loads `database.php`
4. Loads all 6 model files
5. Loads all 4 controller files
6. Defines global helper functions (see below)

**Global Helper Functions defined in `init.php`:**

| Function | What it does |
|---|---|
| `isLoggedIn()` | Returns `true` if a user session exists |
| `getUserRole()` | Returns the current user's role (`'Admin'`, `'Hospital'`, `'Donor'`) |
| `requireAuth()` | Redirects to login if not logged in |
| `requireRole($role)` | Redirects to 403 page if user doesn't have the required role |
| `redirect($page, $msg)` | Redirects to a page with an optional flash message |
| `getFlashMessage()` | Retrieves and clears the one-time flash message from the session |
| `sanitize($input)` | Cleans user input to prevent XSS — **use this on all `$_POST` / `$_GET` values** |
| `getBloodTypeColorClass($type)` | Returns Tailwind CSS classes for a blood type badge color |

---

## Controllers (`controllers/`)

Controllers are the **brain** of each feature. They:
1. Check if the user has permission
2. Read data from `$_POST` or `$_GET`
3. Call the model to get/save data
4. Pass data to a view template

### `controllers/AuthController.php`

Handles all authentication flows.

| Method | Route (`?page=`) | What it does |
|---|---|---|
| `showLogin()` | `login` | Renders the login page |
| `login()` | `login_process` | Validates credentials, sets session, redirects to dashboard |
| `showRegisterDonor()` | `register_donor` | Shows the donor sign-up form |
| `registerDonor()` | `register_donor_process` | Creates a new donor in `users` table |
| `showRegisterHospital()` | `register_hospital` | Shows the hospital sign-up form |
| `registerHospital()` | `register_hospital_process` | Creates user + hospital record |
| `logout()` | `logout` | Destroys session, redirects to login |

### `controllers/AdminController.php`

The largest controller — manages everything the blood bank admin sees.

| Feature Group | Methods | What they do |
|---|---|---|
| **Dashboard** | `dashboard()` | Loads stock summary, pending requests, active appeals |
| **Donors** | `manageDonors()`, `viewDonor()`, `addDonor()`, `editDonor()`, `deleteDonor()` | Full donor CRUD |
| **Hospitals** | `manageHospitals()`, `viewHospitalProfile()` | View and manage partner hospitals |
| **Requests** | `listRequests()`, `fulfillRequest()`, `dispatchUnits()` | Process blood requisitions |
| **Click & Collect** | `markReadyForPickup()`, `verifyReleasePin()` | PIN-based secure handover |
| **Inventory** | `addBloodUnits()`, `updateStockThresholds()` | Manage blood stock |
| **Emergency** | `sendEmergencyAppeal()`, `resolveEmergencyAppeal()`, `getEligibleDonorsAjax()` | Donor mobilization |
| **Users** | `manageUsers()`, `toggleUserStatus()` | Enable/disable user accounts |

### `controllers/HospitalController.php`

| Method | Route | What it does |
|---|---|---|
| `dashboard()` | `hospital_dashboard` | Shows stats, recent requests, quick actions |
| `bloodRequests()` | `hospital_requests` | Lists all this hospital's requests |
| `addRequest()` | `hospital_request_add` | Submit a new blood requisition |
| `viewRequest()` | `hospital_request_view` | Detail view of one request + PIN handover |
| `confirmReceipt()` | `hospital_confirm_receipt` | Hospital confirms they received the blood |
| `subscription()` | `hospital_subscription` | Shows plan pricing and upgrade options |
| `verifySubscription()` | `hospital_verify_subscription` | Paystack payment callback verification |

### `controllers/DonorController.php`

| Method | Route | What it does |
|---|---|---|
| `dashboard()` | `donor_dashboard` | Overview + emergency appeal banner if active |
| `appointments()` | `donor_appointments` | List and manage appointments |
| `addAppointment()` | `donor_appointment_add` | Book a new donation appointment |
| `donationHistory()` | `donor_history` | Full history of past donations |
| `certificate()` | `donor_certificate` | Generate downloadable donation certificate |
| `profile()` | `donor_profile` | View donor profile |
| `updateProfile()` | `donor_profile_update` | Save profile edits |

---

## Models (`models/`)

Models handle **all database interaction**. No controller should write a raw SQL query — it must go through a model.

### `models/User.php`

Handles the `users` table. All three roles (Admin, Hospital, Donor) live here.

| Method | What it does |
|---|---|
| `register($data)` | INSERT new user with bcrypt hashed password |
| `login($email, $password)` | Find user, verify password hash, update `last_login` |
| `findByEmail($email)` | Used during registration to check for duplicates |
| `findById($id)` | Look up any user by their ID |
| `getAll($limit, $offset)` | Paginated list of all users |
| `updateStatus($userId, $isActive)` | Enable or disable an account |
| `countByRole($role)` | Quick stats count |

### `models/BloodInventory.php`

The most complex model — manages the actual blood supply.

| Method | What it does |
|---|---|
| `getStockSummary()` | Counts available units per blood type, compares against thresholds |
| `addUnits($data)` | Add new blood units to inventory |
| `getAvailableUnits($bloodType)` | List units ready for dispatch |
| `getCriticalStockTypes()` | Returns blood types below their safety threshold |
| `updateThresholds($thresholds)` | Save new safety minimums |
| `publishEmergencyAppeal($data)` | Creates a new active emergency appeal |
| `getActiveAppeals()` | Returns all currently active appeals |
| `getActiveAppealForDonor($bloodType)` | Returns an appeal matching a donor's blood type |
| `resolveEmergencyAppeal($appealId)` | Marks an appeal as resolved |
| `getEligibleDonorsForAppeal($bloodType)` | Finds eligible donors for in-app notification |

### `models/Hospital.php`

| Method | What it does |
|---|---|
| `create($data)` | Register a new hospital |
| `findByUserId($userId)` | Get hospital linked to a manager account |
| `getAll()` | List all hospitals (admin view) |
| `getRequests($hospitalId)` | All blood requests from a hospital |
| `createRequest($data)` | Submit a new requisition |
| `updateRequestStatus($id, $status)` | Move a request through its workflow |
| `generateReleasePin($requestId)` | Create and store a secure 6-digit handover PIN |
| `verifyPin($requestId, $pin)` | Validate the PIN during physical collection |
| `confirmReceipt($requestId)` | Mark blood as received |

### `models/Donor.php`

| Method | What it does |
|---|---|
| `getProfile($userId)` | Fetch full donor record |
| `updateProfile($userId, $data)` | Save donor profile changes |
| `getDonations($donorId)` | History of donations |
| `getAppointments($donorId)` | Scheduled and past appointments |
| `createAppointment($data)` | Book a new appointment |
| `updateAppointment($id, $data)` | Reschedule or cancel |
| `getHealthHistory($donorId)` | Medical screening history |
| `getEligibleDonors($bloodType)` | Find eligible donors for a blood type |
| `updateEligibilityStatus($donorId, $status)` | Admin sets eligibility (Eligible/Deferred) |

### `models/Payment.php`

| Method | What it does |
|---|---|
| `createPending($data)` | Record a payment intent before Paystack redirect |
| `verifyAndUpdate($reference, $response)` | After Paystack callback, mark as Success/Failed |
| `getByHospital($hospitalId)` | Payment history for a hospital |
| `getByReference($ref)` | Look up one payment by its Paystack reference |

---

## Views (`views/`)

Views are the HTML templates. They are **not complete HTML pages** by themselves — they are loaded *inside* a layout file.

### Layouts (`views/layouts/`)

The shared "shells" that wrap each page:

| Layout File | Used by | Contains |
|---|---|---|
| `dashboard_layout.php` | Admin pages | Red sidebar, top navbar, flash message area |
| `hospital_layout.php` | Hospital pages | Blue sidebar, hospital branding |
| `donor_layout.php` | Donor pages | Clean donor sidebar |
| `header.php` + `footer.php` | Auth/public pages | Simple nav + scripts |

### Admin Views (`views/admin/`)

| File | What the user sees |
|---|---|
| `dashboard.php` | Overview cards, blood stock status, critical alerts, emergency appeal controls |
| `donors.php` | Searchable donor directory with filters, eligibility status, blood group |
| `hospitals.php` | Hospital list with status badges and links to profiles |
| `hospital_profile.php` | One hospital's full detail — requests, distribution history, actions |
| `requests.php` | All requisitions with status filters, prepare/dispatch modals |
| `users.php` | All system users, enable/disable toggle |

### Hospital Views (`views/hospital/`)

| File | What the user sees |
|---|---|
| `dashboard.php` | Request summary cards, quick-add request button, alerts |
| `blood_requests.php` | All this hospital's requests, status tracking, PIN collection modal |
| `subscription.php` | Plan comparison table, Paystack upgrade buttons |

### Donor Views (`views/donor/`)

| File | What the user sees |
|---|---|
| `dashboard.php` | Welcome card, next appointment, emergency appeal banner (if active) |
| `appointments.php` | List of appointments with reschedule/cancel controls |
| `donation_history.php` | Past donations with dates, volume, location |
| `certificate.php` | Printable/downloadable donation certificate |
| `profile.php` | Personal info and blood type edit form |

### Auth Views (`views/auth/`)

| File | What it is |
|---|---|
| `login.php` | Email + password login form |
| `register_donor.php` | Donor sign-up form (name, blood type, DOB, etc.) |
| `register_hospital.php` | Hospital registration form (facility details + manager account) |

---

## Database Files

### `BMS SCHEMA.sql`
Run this to create the entire database from scratch. It:
1. Drops and recreates all 14 tables
2. Sets up all foreign key relationships
3. Uses `utf8mb4` (supports emoji and special characters)

### `seed.sql`
Run this after the schema to populate the database with test data including:
- 1 admin, 3 hospital managers, 6 donors
- 3 hospitals (KNH, Aga Khan, M.P. Shah)
- 40 blood units across all types
- 12 sample requests with various statuses and release PINs
- 1 active emergency appeal for A- blood

**How to reset the database:**
```bash
/opt/lampp/bin/mysql -u root bms_db < "BMS SCHEMA.sql"
/opt/lampp/bin/mysql -u root bms_db < seed.sql
```

---

## The `.env` File

Never commit this. It contains:

```
DB_HOST=localhost
DB_NAME=bms_db
DB_USER=root
DB_PASS=your_password

PAYSTACK_PUBLIC_KEY=pk_test_...
PAYSTACK_SECRET_KEY=sk_test_...
PAYSTACK_CURRENCY=KES

PLAN_STARTER_MONTHLY=6000
PLAN_PRO_MONTHLY=18000
PLAN_ENTERPRISE_MONTHLY=45000
```

---

## Next Steps

- 🚶 [User Journey](03-user-journey.md) — Step-by-step flows for each user type
- 🔒 [Security Features](04-security.md) — Authentication, authorization, and data protection
