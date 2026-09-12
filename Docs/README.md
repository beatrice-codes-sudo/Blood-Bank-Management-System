# 📖 HemoLink — Documentation Index

Welcome to the HemoLink documentation. These files are written to help you understand, navigate, and contribute to this codebase — whether you're brand new or coming back after a break.

---

## 📚 Documentation Files

| # | File | What it covers |
|---|---|---|
| 0 | [Getting Started](00-getting-started.md) | Install, set up, and run the project locally |
| 1 | [Architecture](01-architecture.md) | How the system is structured (MVC, routing, data flow) |
| 2 | [Codebase Guide](02-codebase-guide.md) | What every important file and class does |
| 3 | [User Journey](03-user-journey.md) | Step-by-step workflows for Admins, Hospitals, and Donors |
| 4 | [Security Guide](04-security.md) | Authentication, authorization, SQL injection protection |
| 5 | [Database Guide](05-database.md) | All 14 tables explained, with ERD and test credentials |
| 6 | [System Diagrams](images/diagrams.md) | ERD, DFD Level 0/1, Use Case, Architecture & Login Flowchart |

---

## 🗺️ Quick Reference

### I want to add a new page
1. Create the view in `views/<role>/newpage.php`
2. Add a method to the appropriate controller
3. Register the route in `index.php` with `case 'new_page_name':`

### I want to add a new database query
1. Add a method to the relevant model in `models/`
2. Use a prepared statement: `$this->db->prepare("...")` then `->execute([...])`
3. Call the model method from the controller

### I want to change who can access a page
- Use `requireRole(ROLE_ADMIN)`, `requireRole(ROLE_HOSPITAL)`, or `requireRole(ROLE_DONOR)` at the top of the controller method

### I want to reset the database
```bash
/opt/lampp/bin/mysql -u root bms_db < "BMS SCHEMA.sql"
/opt/lampp/bin/mysql -u root bms_db < seed.sql
```

---

## 🧭 Technology Stack

| Technology | Version | Role |
|---|---|---|
| PHP | 8.1+ | Server-side logic |
| MySQL | 8.0+ | Database |
| Apache | 2.4 | Web server (via LAMPP) |
| PDO | Built-in | Database abstraction layer |
| Tailwind CSS | CDN | UI styling |
| Font Awesome | CDN | Icons |
| Paystack | v2 API | Payment processing |

---

## 👥 User Roles

| Role | Login Email (seed) | Dashboard |
|---|---|---|
| Admin | `admin@hemolink.com` | `/index.php?page=admin_dashboard` |
| Hospital Manager | `manager@knh.go.ke` | `/index.php?page=hospital_dashboard` |
| Donor | `brian.otieno@gmail.com` | `/index.php?page=donor_dashboard` |

Default password for all seed accounts: `admin123` / `hospital123` / `donor123`
