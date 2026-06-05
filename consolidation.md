# HemoLink Schema Consolidation Plan

## 1. Why Consolidate?

The current schema has **14 tables**. Six of those are either small lookup tables with fixed values (best expressed as ENUMs) or 1:1 extension tables that duplicate data already present on `users`. Consolidating them will:

- **Reduce JOINs** — Most queries currently require 2–3 JOINs just to assemble basic user/donor/hospital data.
- **Eliminate data duplication** — `donors.first_name` / `donors.last_name` duplicate `users.first_name` / `users.last_name`, creating sync issues.
- **Simplify the codebase** — Registration flows currently INSERT into two tables (`users` + `donors` or `users` + `hospitals`). After consolidation, one INSERT is enough.
- **Remove trivial lookup tables** — `roles` (3 rows), `blood_types` (8 rows), and `components` (5 rows) are static value sets that never change at runtime. ENUMs are the correct tool for these.

---

## 2. Tables Being Removed (6)

### 2.1 `roles` → ENUM on `users`

| Before | After |
|--------|-------|
| `users.role_id INT FK → roles` | `users.role ENUM('Admin','Hospital','Donor')` |

**Reason**: Only 3 roles exist and they are defined as PHP constants (`ROLE_ADMIN`, `ROLE_HOSPITAL`, `ROLE_DONOR`). A lookup table adds an unnecessary JOIN on every login and user query.

### 2.2 `blood_types` → ENUM/VARCHAR on relevant tables

| Before | After |
|--------|-------|
| `donors.blood_type_id INT FK → blood_types` | `users.blood_type ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-')` |
| `donations.blood_type_id FK` | `donations.blood_type ENUM(...)` |
| `blood_units.blood_type_id FK` | `blood_units.blood_type ENUM(...)` |
| `requests.blood_type_id FK` | `requests.blood_type ENUM(...)` |
| `request_items.blood_type_id FK` | `request_items.blood_type ENUM(...)` |

**Reason**: There are exactly 8 blood types; this is a scientific constant. The `blood_types` table and all its FK joins can be replaced by inline ENUMs.

### 2.3 `components` → ENUM on `request_items`

| Before | After |
|--------|-------|
| `request_items.component_id INT FK → components` | `request_items.component ENUM('Whole Blood','Red Blood Cells','Platelets','Plasma','Cryoprecipitate')` |

**Reason**: Same rationale as blood types — fixed, small value set.

### 2.4 `donors` → Columns on `users`

| Before | After |
|--------|-------|
| Separate `donors` table with `user_id` FK (1:1) | Donor-specific columns added directly to `users` |

Columns migrated to `users`:
- `blood_type` (ENUM, replaces `blood_type_id`)
- `date_of_birth` (DATE)
- `gender` (ENUM)
- `address` (VARCHAR)
- `city` (VARCHAR)
- `eligibility_status` (ENUM)

Columns **dropped** (3NF violations — derived data):
- `last_donation_date` — computed via `MAX(donations.donation_date)`
- `total_donations` — computed via `COUNT(donations)`

**Reason**: `donors` has a 1:1 relationship with `users` and duplicates `first_name`/`last_name`. All donor fields become nullable columns on `users`, populated only when `role = 'Donor'`.

### 2.5 `staff` → Columns on `users`

| Before | After |
|--------|-------|
| Separate `staff` table with `user_id` FK (1:1) | Staff-specific columns added directly to `users` |

Columns migrated to `users`:
- `employee_id` (VARCHAR, UNIQUE)
- `department` (ENUM)
- `designation` (VARCHAR)
- `qualification` (VARCHAR)
- `date_joined` (DATE)
- `is_verified` (BOOLEAN)

**Reason**: 1:1 with `users`, no independent identity.

### 2.6 `hospital_staff` → Columns on `users`

| Before | After |
|--------|-------|
| Separate `hospital_staff` table with `user_id` FK (1:1) | Hospital staff columns added to `users` |

Columns migrated to `users`:
- `hospital_id` (FK to hospitals, nullable)
- `is_authorized_requester` (BOOLEAN)

Note: `department` and `designation` are already being added from `staff` above and can be shared.

**Reason**: 1:1 with `users`, no independent identity.

---

## 3. Tables Kept (8)

| Table | Reason |
|-------|--------|
| `users` | Central entity — absorbs 4 satellite tables |
| `hospitals` | An *organization*, not a person — must remain separate |
| `donations` | 1:many relationship with donors |
| `blood_units` | 1:many inventory records |
| `requests` | 1:many from hospitals |
| `request_items` | 1:many line items per request; absorbs `components` |
| `blood_tests` | 1:many test records per blood unit |
| `appointments` | 1:many per donor |
| `distributions` | 1:many dispatch records |
| `donor_health_history` | 1:many health screening records |
| `audit_logs` | System-level logging |

**Final count: 11 tables** (down from 14).

---

## 4. Consolidated `users` Table Schema

```sql
CREATE TABLE `users` (
    `user_id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role`                 ENUM('Admin','Hospital','Donor') NOT NULL,
    `username`             VARCHAR(50) NOT NULL,
    `email`                VARCHAR(100) NOT NULL,
    `password_hash`        VARCHAR(255) NOT NULL,
    `first_name`           VARCHAR(50) NOT NULL,
    `last_name`            VARCHAR(50) NOT NULL,
    `phone`                VARCHAR(20) NULL,
    `is_active`            BOOLEAN NULL DEFAULT 1,
    `last_login`           TIMESTAMP NULL,

    -- Donor-specific (NULL for non-donors)
    `blood_type`           ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL,
    `date_of_birth`        DATE NULL,
    `gender`               ENUM('Male','Female','Other') NULL,
    `address`              VARCHAR(255) NULL,
    `city`                 VARCHAR(50) NULL,
    `eligibility_status`   ENUM('Eligible','Deferred','Permanently Deferred') NULL DEFAULT 'Eligible',

    -- Staff-specific (NULL for non-staff)
    `employee_id`          VARCHAR(20) NULL,
    `department`           ENUM('Collection','Laboratory','Inventory','Administration','Nursing') NULL,
    `designation`          VARCHAR(50) NULL,
    `qualification`        VARCHAR(100) NULL,
    `date_joined`          DATE NULL,
    `is_verified`          BOOLEAN NULL,

    -- Hospital-staff-specific
    `hospital_id`          INT UNSIGNED NULL,
    `is_authorized_requester` BOOLEAN NULL,

    `created_at`           TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`           TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),

    UNIQUE KEY `users_username_unique` (`username`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_employee_id_unique` (`employee_id`),
    CONSTRAINT `users_hospital_id_foreign` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 5. FK Reference Changes (other tables)

After removing `donors` and `blood_types`, tables that referenced `donor_id` or `blood_type_id` must be updated:

### 5.1 `donations`
```diff
- `donor_id`      INT UNSIGNED FK → donors(donor_id)
+ `donor_id`      INT UNSIGNED FK → users(user_id)
- `blood_type_id` INT UNSIGNED FK → blood_types(blood_type_id)
+ `blood_type`    ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-')
```

### 5.2 `blood_units`
```diff
- `blood_type_id` INT UNSIGNED FK → blood_types(blood_type_id)
+ `blood_type`    ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL
```

### 5.3 `requests`
```diff
- `blood_type_id` INT UNSIGNED FK → blood_types(blood_type_id)
+ `blood_type`    ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-')
```

### 5.4 `request_items`
```diff
- `blood_type_id` INT UNSIGNED FK → blood_types(blood_type_id)
+ `blood_type`    ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL
- `component_id`  INT UNSIGNED FK → components(component_id)
+ `component`     ENUM('Whole Blood','Red Blood Cells','Platelets','Plasma','Cryoprecipitate') NOT NULL
```

### 5.5 `appointments`
```diff
- `donor_id` INT UNSIGNED FK → donors(donor_id)
+ `donor_id` INT UNSIGNED FK → users(user_id)
```

### 5.6 `donor_health_history`
```diff
- `donor_id` INT UNSIGNED FK → donors(donor_id)
+ `donor_id` INT UNSIGNED FK → users(user_id)
```

> **Note**: The column name stays `donor_id` for readability, but it now references `users(user_id)`.

---

## 6. Backend Impact Analysis

### 6.1 Role Constants (`config/app.php`)

**Current**: Roles are integer IDs matching `roles.role_id`.
```php
define('ROLE_ADMIN', 1);
define('ROLE_HOSPITAL', 2);
define('ROLE_DONOR', 3);
```

**After**: Roles become string constants matching the ENUM.
```php
define('ROLE_ADMIN', 'Admin');
define('ROLE_HOSPITAL', 'Hospital');
define('ROLE_DONOR', 'Donor');
```

**Impact**: `$_SESSION['role_id']` should be renamed to `$_SESSION['role']` and will store a string. All `requireRole()`, `getUserRole()`, and switch/case blocks already compare with the constants so they will work without further changes — only the constant values change.

---

### 6.2 `models/User.php` — Changes Required

| Method | Change |
|--------|--------|
| `register()` | Replace `role_id` param with `role`. Remove the extra donor-specific fields that were being inserted into `donors` separately — they now go into `users` directly. |
| `login()` | Remove `JOIN roles r ON u.role_id = r.role_id`. Select `u.role` directly instead of `r.role_name`. |
| `findById()` | Same — remove roles JOIN. |
| `getAll()` | Same — remove roles JOIN, select `u.role`. |
| `countByRole()` | Change `WHERE role_id = :role_id` → `WHERE role = :role`. |
| `getRecentRegistrations()` | Remove roles JOIN. |

**Example — `login()` before vs after:**
```diff
  public function login($email, $password) {
-     $sql = "SELECT u.*, r.role_name
-             FROM users u
-             JOIN roles r ON u.role_id = r.role_id
-             WHERE u.email = :email AND u.is_active = 1";
+     $sql = "SELECT * FROM users
+             WHERE email = :email AND is_active = 1";
```

---

### 6.3 `models/Donor.php` — Major Refactor

This model is the most heavily affected. The `donors` table no longer exists, so every query must target `users` directly.

| Method | Change |
|--------|--------|
| `create()` | **Remove entirely** — donor fields are now part of the `User::register()` INSERT. |
| `findByUserId()` | Query `users` directly. Remove JOIN to `donors` and `blood_types`. The `user_id` IS the donor ID now. |
| `findById()` | Same — query `users WHERE user_id = :id AND role = 'Donor'`. |
| `getAll()` | `SELECT * FROM users WHERE role = 'Donor'` — no JOINs needed. |
| `countAll()` | `SELECT COUNT(*) FROM users WHERE role = 'Donor'`. |
| `getDonationHistory()` | Change `WHERE dn.donor_id = :donor_id` → `WHERE dn.donor_id = :user_id`. Remove `blood_types` JOIN; select `dn.blood_type` directly. |
| `getStats()` | Same FK column rename (`donor_id` now references `user_id`). |
| `update()` | Update `users` table directly. No separate donors update needed. Remove `updateUserContact()` — it's now a single UPDATE on `users`. |
| `updateUserContact()` | **Remove entirely** — merged into `update()`. |
| `getGlobalStats()` | Replace `FROM donors` with `FROM users WHERE role = 'Donor'`. Remove all `donors ↔ users` JOINs. |
| `getBloodTypes()` | **Remove entirely** — blood types are now an ENUM. Dropdowns should be populated from a PHP constant array instead. |
| `delete()` | Simplify to `DELETE FROM users WHERE user_id = :id`. No need to look up the donor first. |
| `updateEligibility()` | `UPDATE users SET eligibility_status = :status WHERE user_id = :id`. |
| `getAppointments()` | Change `WHERE a.donor_id = :donor_id` — the value passed in is now `user_id`. |
| `createAppointment()` | Same — `donor_id` value is the `user_id`. |
| `rescheduleAppointment()` | Same adjustment. |
| `cancelAppointment()` | Same adjustment. |
| `getAppointmentStats()` | Same adjustment. |
| `getAppointmentsByMonth()` | Same adjustment. |

**Example — `findByUserId()` before vs after:**
```diff
  public function findByUserId($userId) {
-     $sql = "SELECT d.*, bt.type_name as blood_type, u.email, u.phone, u.username
-             FROM donors d
-             LEFT JOIN blood_types bt ON d.blood_type_id = bt.blood_type_id
-             JOIN users u ON d.user_id = u.user_id
-             WHERE d.user_id = :user_id";
+     $sql = "SELECT * FROM users
+             WHERE user_id = :user_id AND role = 'Donor'";
```

---

### 6.4 `models/Hospital.php` — Moderate Changes

The `hospitals` table is **kept**, so this model has fewer changes. The main impact is removing `blood_types` JOINs.

| Method | Change |
|--------|--------|
| `findByUserId()` | No change — still JOINs `hospitals` ↔ `users`. |
| `getRecentRequests()` | Remove `blood_types` JOIN. Select `r.blood_type` directly. |
| `getAllRequests()` | Same — remove `blood_types` JOIN. |
| `getRequestById()` | Same — remove `blood_types` JOIN. |
| `createRequest()` | Replace `blood_type_id` param with `blood_type` (string). |
| `updateRequest()` | Replace `blood_type_id` param with `blood_type` (string). |
| `getBloodTypes()` | **Remove entirely** — use PHP constant array. |

---

### 6.5 `models/BloodInventory.php` — Moderate Changes

| Method | Change |
|--------|--------|
| `getInventorySummary()` | Remove `blood_types` JOIN. Group by `bu.blood_type` directly. Need a different approach to list all 8 types (use a UNION or PHP-side fill). |
| `getCriticalStock()` | Same — group by `bu.blood_type`. |
| `getBloodTypes()` | **Remove entirely**. |

**Example — `getInventorySummary()` after:**
```sql
SELECT blood_type, COUNT(*) as unit_count
FROM blood_units
WHERE status = 'Available'
GROUP BY blood_type
ORDER BY blood_type
```
> Missing types (zero stock) should be filled in PHP using the constant array.

---

### 6.6 `controllers/AuthController.php` — Significant Changes

| Method | Change |
|--------|--------|
| `login()` | Session now stores `$_SESSION['role']` (string) instead of `$_SESSION['role_id']` and `$_SESSION['role_name']`. |
| `registerDonor()` | **Biggest change**: Currently does 2 inserts (`users` + `donors`). After consolidation, only 1 insert into `users` with all donor fields included. Remove `$this->donorModel->create()` call. |
| `registerHospital()` | No major change — still inserts into `users` + `hospitals` (hospitals table is kept). |
| `showRegisterDonor()` | Blood types dropdown: Replace `$bloodInventory->getBloodTypes()` with a PHP constant array. |
| `redirectToDashboard()` | Change `getUserRole()` comparisons to use string constants. |

**Example — `registerDonor()` after:**
```php
$userId = $this->userModel->register([
    'role'               => ROLE_DONOR,
    'username'           => $username,
    'email'              => $data['email'],
    'password'           => $data['password'],
    'first_name'         => $data['first_name'],
    'last_name'          => $data['last_name'],
    'phone'              => $data['phone'],
    'blood_type'         => $data['blood_type'],
    'date_of_birth'      => $data['date_of_birth'],
    'gender'             => $data['gender'],
    'address'            => $data['address'],
    'city'               => $data['city'],
]);
// No more $this->donorModel->create() call needed
```

---

### 6.7 `controllers/DonorController.php` — Moderate Changes

The main change is that `$donor['donor_id']` no longer exists. The donor IS the user, so `$_SESSION['user_id']` is used directly.

| Method | Change |
|--------|--------|
| `dashboard()` | Replace `$donor['donor_id']` with `$_SESSION['user_id']` when calling `getStats()` and `getDonationHistory()`. |
| `appointments()` | Same — pass `$_SESSION['user_id']` instead of `$donor['donor_id']`. |
| `addAppointment()` | `'donor_id' => $_SESSION['user_id']` instead of `$donor['donor_id']`. |
| `rescheduleAppointment()` | Same. |
| `cancelAppointment()` | Same. |
| `donationHistory()` | Same. |
| `profile()` | `getBloodTypes()` call replaced with PHP constant array. |
| `updateProfile()` | Single `UPDATE users` query. No separate `updateUserContact()` call. |

---

### 6.8 `controllers/AdminController.php` — Moderate Changes

| Method | Change |
|--------|--------|
| `manageDonors()` | `getBloodTypes()` → PHP constant array. |
| `addDonor()` | Single `User::register()` call with donor fields. Remove `$this->donorModel->create()`. |
| `editDonor()` | Single `UPDATE users` query. Remove the raw DB call that updates `users.phone` separately. |
| `deleteDonor()` | `DELETE FROM users WHERE user_id = :id` directly. |
| `viewDonor()` / `donorHistory()` | Replace `donor_id` param with `user_id`. |

---

### 6.9 `controllers/HospitalController.php` — Minor Changes

| Method | Change |
|--------|--------|
| `bloodRequests()` | `getBloodTypes()` → PHP constant array. |
| `addRequest()` | Pass `blood_type` string instead of `blood_type_id` int. |
| `editRequest()` | Same. |

---

### 6.10 Views Affected

| View File | Change |
|-----------|--------|
| `views/auth/register_donor.php` | Blood type dropdown: iterate PHP array instead of DB result. |
| `views/donor/profile.php` | Same for blood type dropdown. Replace `$donor['donor_id']` with `$donor['user_id']`. `$donor['blood_type']` is now a direct string, no `type_name` alias. |
| `views/admin/donors.php` | Same for blood type dropdown. Replace `donor_id` references with `user_id`. `blood_type` is a direct column. |
| `views/hospital/blood_requests.php` | Blood type dropdown from PHP array. `blood_type` is a direct string column. |

---

### 6.11 New PHP Helper — Blood Type Constants

Add to `config/app.php`:
```php
// Blood Types (replaces blood_types table)
define('BLOOD_TYPES', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);

// Components (replaces components table)
define('BLOOD_COMPONENTS', [
    'Whole Blood', 'Red Blood Cells', 'Platelets', 'Plasma', 'Cryoprecipitate'
]);
```

---

### 6.12 Session Variable Changes

```diff
- $_SESSION['role_id']    // integer (1, 2, 3)
- $_SESSION['role_name']  // string from roles table
+ $_SESSION['role']       // ENUM string ('Admin', 'Hospital', 'Donor')
```

Update `getUserRole()` in `config/init.php`:
```diff
  function getUserRole() {
-     return $_SESSION['role_id'] ?? null;
+     return $_SESSION['role'] ?? null;
  }
```

---

## 7. Migration Strategy

### Step 1: Create new schema
Run the consolidated `BMS SCHEMA.sql` (fresh install approach since this is a dev environment).

### Step 2: Update seed data
Update `seed.sql` to match the new schema — remove roles/blood_types/components/donors INSERTs, inline those values into `users` and other tables.

### Step 3: Update backend (order matters)
1. `config/app.php` — Add constants, change role definitions
2. `config/init.php` — Update `getUserRole()`
3. `models/User.php` — Remove roles JOINs, expand `register()`
4. `models/Donor.php` — Rewrite all queries against `users`
5. `models/Hospital.php` — Remove blood_types JOINs
6. `models/BloodInventory.php` — Remove blood_types JOINs
7. `controllers/AuthController.php` — Simplify registration flows
8. `controllers/DonorController.php` — Use `user_id` everywhere
9. `controllers/AdminController.php` — Simplify donor CRUD
10. `controllers/HospitalController.php` — Update blood type handling
11. Views — Update dropdowns and variable names

### Step 4: Test all flows
- Admin login + dashboard
- Donor registration + login + profile + appointments
- Hospital registration + login + blood requests
- AJAX endpoints (view donor, view request, request history)

---

## 8. Risk Assessment

| Risk | Severity | Mitigation |
|------|----------|------------|
| Wider `users` table with NULLable columns | Low | Acceptable trade-off at this scale. MySQL handles sparse NULLs efficiently. |
| Breaking all existing sessions | Low | Users simply need to re-login after deployment. |
| ENUM changes require ALTER TABLE | Low | ENUMs can be extended with `ALTER TABLE ... MODIFY COLUMN`. |
| Seed data mismatch | Medium | Re-seed the database entirely after schema change. |
| Missed `donor_id` → `user_id` reference | Medium | Thorough grep + test all 4 user flows. |
