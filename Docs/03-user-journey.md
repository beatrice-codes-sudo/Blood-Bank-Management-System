# 🚶 HemoLink — User Journey Guide

> **Who is this for?** Anyone who wants to understand what each type of user can do, step by step.

HemoLink has three types of users. Each gets their own dashboard and workflow:

---

## 1. 🩸 Blood Donor

Donors are community members who donate blood. They self-register through the public website.

### Registration Journey

```mermaid
flowchart TD
    A([Visit Landing Page]) --> B[Click Register as Donor]
    B --> C[Fill in: Name, Email, Password]
    C --> D[Fill in: Blood Type, Date of Birth, Gender]
    D --> E[Fill in: Address & City]
    E --> F{Valid?}
    F -- No --> G[Show error messages] --> C
    F -- Yes --> H[Account created in users table]
    H --> I[Redirected to Login]
    I --> J[Login with email & password]
    J --> K[Donor Dashboard 🎉]
```

### Ongoing Donor Journey

Once registered and logged in, here's what a donor does:

```mermaid
flowchart TD
    Start([Donor Logs In]) --> Dashboard

    Dashboard --> Banner{Emergency Appeal\nfor my blood type?}
    Banner -- Yes --> BannerBtn[Click Book Urgent Donation]
    Banner -- No --> Normal

    BannerBtn --> BookAppt[Book Appointment]
    Normal --> BookAppt

    BookAppt --> PickDate[Choose Date & Time]
    PickDate --> Confirm[Appointment Confirmed]
    Confirm --> Donate[Go to blood bank & donate]

    Donate --> History[View Donation History]
    History --> Cert[Download Donation Certificate]

    Dashboard --> Profile[Edit Profile / Update Address]
```

### Donor Portal Pages

| Page | What to do there |
|---|---|
| **Dashboard** | See your next appointment, total donations, and any emergency blood appeals |
| **Appointments** | Book, reschedule, or cancel donation appointments |
| **Donation History** | See all your past donations with dates and locations |
| **Certificate** | Download a certificate for any completed donation |
| **Profile** | Update your contact details and city |

### What a Donor CANNOT do
- ❌ See other donors' information
- ❌ View hospital requests or inventory
- ❌ Access admin features

---

## 2. 🏥 Hospital Manager

Hospital managers represent partner hospitals and clinics that need blood for their patients.

### Registration Journey

```mermaid
flowchart TD
    A([Visit Landing Page]) --> B[Click Register Hospital]
    B --> C[Fill in Manager Details\nName, Email, Password]
    C --> D[Fill in Hospital Details\nName, Code, Address, License No.]
    D --> E{Valid?}
    E -- No --> F[Show errors] --> C
    E -- Yes --> G[User account created]
    G --> H[Hospital record created & linked]
    H --> I[Login → Hospital Dashboard]
```

### Requesting Blood

This is the core workflow for hospital managers:

```mermaid
flowchart TD
    A([Hospital Manager Logs In]) --> B[Click New Blood Request]
    B --> C[Select Blood Type & Units Needed]
    C --> D[Set Urgency: Normal / Urgent / Emergency]
    D --> E[Add Notes / Special Requirements]
    E --> F[Submit Request]
    F --> G[Status: PENDING]

    G --> H{Admin Reviews}
    H -- Rejects --> I[Status: REJECTED\nManager sees reason]
    H -- Approves --> J[Admin prepares blood units]
    J --> K[Status: PROCESSING]

    K --> L{Delivery Method?}
    L -- Pickup --> M[Admin marks Ready for Pickup]
    M --> N[Status: Ready for Pickup\nRelease PIN generated]
    N --> O[Hospital staff goes to blood bank]
    O --> P[Shows PIN to admin]
    P --> Q[Admin verifies PIN]
    Q --> R[Blood handed over]
    R --> S[Status: Collected]

    L -- Dispatch --> T[Admin dispatches with driver]
    T --> U[Status: Dispatched]
    U --> V[Manager clicks Confirm Receipt]
    V --> W[Status: Received / Fulfilled ✅]
```

### Hospital Portal Pages

| Page | What to do there |
|---|---|
| **Dashboard** | See pending requests, monthly stats, subscription status |
| **Blood Requests** | View all requests, submit new ones, track statuses |
| **Request Detail** | See full timeline, collection status, and the release PIN for pickup |
| **Subscription** | Compare plans (Starter, Professional, Enterprise) and upgrade via Paystack |

### Request Status Flow

Each request moves through these statuses:

```
Pending → Processing → [Ready for Pickup / Dispatched] → [Collected / Received] → Fulfilled
                                                       ↘ Rejected / Cancelled
```

| Status | What it means |
|---|---|
| `Pending` | Submitted, awaiting admin review |
| `Processing` | Admin accepted and is preparing units |
| `Ready for Pickup` | Blood is bagged, waiting for hospital pickup with PIN |
| `Dispatched` | Blood is on its way via courier/ambulance |
| `Collected` | Hospital picked up the blood (PIN verified) |
| `Received` | Hospital confirmed delivery |
| `Fulfilled` | Request fully complete |
| `Partially Fulfilled` | Only some units were available |
| `Rejected` | Denied by admin (e.g., insufficient stock) |
| `Cancelled` | Cancelled by hospital |

### Subscription Plans

Hospital managers can upgrade their plan to unlock more features:

| Plan | Monthly Price | Request Limit | Emergency Priority |
|---|---|---|---|
| **Starter** (Clinic) | KES 6,000 | 15/month | ❌ |
| **Professional** (Hospital) | KES 18,000 | Unlimited | ✅ |
| **Enterprise** (Network) | KES 45,000 | Unlimited | ✅ |

Annual billing saves **15%**.

---

## 3. 🛡️ Blood Bank Admin

The admin manages the entire system — inventory, donors, hospitals, and emergency responses.

### Admin Dashboard Overview

When the admin logs in, they see:

```mermaid
flowchart TD
    Admin([Admin Logs In]) --> DB[Admin Dashboard]

    DB --> Stock[Blood Stock Cards\nOne per blood type]
    Stock --> CritAlert{Any type below\nthreshold?}
    CritAlert -- Yes --> Banner[Critical Stock Alert Banner]
    Banner --> Mobilize[Click Mobilize Donors]
    Mobilize --> Appeal[Publish Emergency Appeal\nto matching donor dashboards]

    DB --> Requests[Pending Hospital Requests]
    Requests --> Review[Review each request]
    Review --> Approve[Approve & Prepare Units]
    Approve --> DispatchOrPickup[Dispatch or Mark Ready for Pickup]

    DB --> Donors[Donor Directory]
    Donors --> Filter[Filter by blood type, eligibility]
    Filter --> ViewDonor[View donor profile & history]
    ViewDonor --> EditDonor[Edit eligibility status]
```

### Emergency Appeal Workflow

When a blood type hits critical levels:

```mermaid
flowchart TD
    A[Admin sees Critical Stock banner] --> B[Clicks Mobilize Donors]
    B --> C[Emergency Appeal modal opens]
    C --> D[Admin writes appeal message]
    D --> E[Clicks Publish Appeal]
    E --> F[appeal saved in emergency_appeals table\nis_active = 1]

    F --> G[All matching-blood-type donors\nsee a banner on their dashboard]
    G --> H[Donor clicks Book Urgent Donation]
    H --> I[Donor books appointment]

    F --> J[Admin sees active appeals bar\non dashboard]
    J --> K[Clicks End Appeal]
    K --> L[appeal resolved_at = NOW\nis_active = 0]
    L --> M[Banner disappears from donor dashboards]
```

### Blood Request Fulfilment Workflow

```mermaid
flowchart TD
    A[Admin views pending requests] --> B[Opens a request]
    B --> C[Clicks Prepare Units]
    C --> D[System checks stock\nRed warning if insufficient]
    D --> E[Admin enters units to fulfill]
    E --> F{Delivery method?}

    F -- Pickup --> G[Click Mark Ready for Pickup]
    G --> H[6-digit PIN generated]
    H --> I[PIN shown to hospital manager]
    I --> J[Hospital staff arrives with PIN]
    J --> K[Admin clicks Verify PIN]
    K --> L{PIN correct?}
    L -- No --> M[Error — try again]
    L -- Yes --> N[Blood handed over\nStatus: Collected]

    F -- Dispatch --> O[Click Dispatch Units]
    O --> P[Enter driver name & transport details]
    P --> Q[Status: Dispatched]
    Q --> R[Hospital confirms receipt]
    R --> S[Status: Fulfilled ✅]
```

### Inventory Management

Admins control both the stock and the safety thresholds:

```mermaid
flowchart TD
    A[Admin goes to blood inventory section] --> B[Views stock per blood type]
    B --> C[Each card shows:\nUnits Available\nUnits Reserved\nStatus: Optimal / Low / Empty]
    B --> D[Click Add Blood Units]
    D --> E[Enter blood type, quantity, expiry date]
    E --> F[Units added to blood_units table]

    B --> G[Click Update Thresholds]
    G --> H[Enter new minimum for each blood type]
    H --> I[Thresholds saved in stock_thresholds table]
    I --> J[Dashboard recalculates critical status]
```

### Admin's Full Capabilities

| Feature | What the admin can do |
|---|---|
| **Donors** | View all donors, add manually, edit details, defer eligibility, delete |
| **Hospitals** | View all hospitals, see their request history and subscription status |
| **Blood Requests** | Review, approve, reject, fulfill, dispatch, verify PIN |
| **Inventory** | Add units, set thresholds, view stock per blood type |
| **Emergency Appeals** | Publish broadcasts to eligible donors, end active appeals |
| **Users** | View all system accounts, enable/disable any user |
| **Appointments** | View and delete donor appointments |

---

## 4. 🌐 Public Visitor (Not Logged In)

Even without an account, visitors can:

- Visit the **landing page** (`index.php`)
- Register as a **Donor**
- Register as a **Hospital**
- Access the **login page**

Attempting to visit any dashboard page while not logged in will redirect to the login page automatically.

---

## Session Data

Once logged in, the following data is stored in the session and used throughout:

| Key | Example Value | Used for |
|---|---|---|
| `$_SESSION['user_id']` | `5` | Identifying the logged-in user |
| `$_SESSION['role']` | `'Donor'` | Role-based access control |
| `$_SESSION['username']` | `'brian.otieno'` | Display in nav |
| `$_SESSION['first_name']` | `'Brian'` | Personalised greeting |
| `$_SESSION['email']` | `'brian@gmail.com'` | Profile reference |
