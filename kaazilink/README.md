# KaaziLink — Local Service & Skill Marketplace Platform

## Overview

**KaaziLink** is a full-stack, Kenyan-context digital marketplace designed to connect households and businesses with vetted local service providers ("fundis", cleaners, artisans, technicians, caregivers, event vendors, and agricultural workers). Operating across **51 service categories** organized into **6 core domain verticals**, KaaziLink bridges the gap between informal service labor and clients seeking reliable, verified local expertise.

Built with a trust-first ethos, the platform incorporates a rigorous **4-step provider onboarding wizard**, **manual national ID & KYC verification**, **dynamic custom rate setting**, **geographic service area mapping via MapLibre GL JS**, **escrow-style booking state management**, **M-PESA mobile money payment processing via PayHero Kenya**, and an automated **Kenyan-brand email notification engine**.

- **Live Platform URL**: [https://kaazilink.onrender.com](https://kaazilink.onrender.com)
- **Tagline**: *"Be Connected — Jobs, Services, Opportunities"* / *"Tupate Fundi Haraka"*

---

## Problem / Purpose

### The Problem
In Kenya and broader East African markets, hiring local service providers ("fundis", plumbers, house help, electricians, crop technicians) heavily relies on word-of-mouth recommendations or unverified social media channels. This informal ecosystem creates significant friction:
1. **Lack of Trust & Safety**: Clients hesitate to invite unvetted individuals into their homes without verified identity credentials.
2. **Opaque Pricing**: Service pricing is inconsistent, frequently negotiated ad-hoc without baseline reference rates.
3. **Low Visibility for Skilled Workers**: Talented micro-entrepreneurs and tradesmen lack formal digital portfolios, reputational ratings, or verified badges to market their skills.

### Target Users
- **Clients / Households**: Individuals and businesses looking for verified, location-matched professionals across domestic, technical, emergency, and agricultural categories.
- **Service Providers ("Fundis")**: Independent tradesmen, informal workers, and local service professionals seeking job leads, transparent pricing control, and verified identity badges.
- **Platform Administrators**: Managers overseeing provider KYC verification, document safety, site settings, and category metrics.

---

## Key Features

1. **51 Service Categories Across 6 Domain Verticals**:
   - **Home Care**: Mama Fua/Laundry, House Help, Nannies, Cooks, Caregivers, Deep Cleaning, Pest Control, Carpet/Sofa Cleaning.
   - **Fundi & Technical Repairs**: Plumbers, Electricians, Carpenters, Welders, Masons, Painters, Electronics Repair, AC/Fridge Repair, Washing Machine Repair, Generator Mechanics.
   - **Daily Needs & Emergency Response**: Gas Delivery, Water Delivery, Errand Runners, Boda Boda Riders, Emergency Towing, Locksmiths, Ambulance Transport.
   - **Beauty / Events / Social Support**: Mobile Hairdressers, Barbers, Makeup Artists, Henna Artists, DJs/MCs, Photographers, Decorators, Catering, Tent Rental.
   - **Agriculture / Rural / Farming**: Farm Laborers, Tractor Hire, Vets, Agronomists, Gardeners, Tree Cutters, Poultry Experts, Borehole Drillers.
   - **Business / Cyber / Education / Health**: Graphic Designers, KRA Returns Assistants, Data Entry, Tutors, Business Registration Agents.

2. **4-Step Provider Onboarding Wizard (`/provider/onboarding`)**:
   - **Step 1 (Personal Details)**: Strict 18+ age validation (`before_or_equal:-18 years`), National ID number, address, emergency contact, and detailed professional bio.
   - **Step 2 (Services & Pricing)**: Dynamic rate configuration for standard directory services, plus creation of custom provider offerings (`is_custom = true`) with custom names, descriptions, and KES pricing.
   - **Step 3 (Service Areas)**: Selection of geographic coverage areas across 53 Kenyan county locations.
   - **Step 4 (KYC Document Verification)**: Mandatory upload of National ID (Front & Back), KRA PIN certificate, and live selfie.

3. **Admin KYC Manual Verification Dashboard (`/admin/kyc`)**:
   - Admin view to inspect pending provider submissions.
   - Integrated protected document viewer route (`/kyc/document/{path}`) enforcing ownership/admin checks.
   - One-click **Approval** (assigns `kyc_status: approved` badge and sends welcome email) or **Rejection** (dispatches rejection notification with custom administrator rationale).

4. **Interactive Mapping & Geographic Radius Filtering**:
   - Client location filtering using **MapLibre GL JS** and **OpenFreeMap** (Positron tile style).
   - Real-time map pins for approved provider service locations across Kenya.
   - Backend Haversine distance calculations for location-matched queries.

5. **Escrow-Style Booking Lifecycle**:
   - Job request creation with custom service rates and datetime scheduling.
   - Provider acceptance/rejection flow.
   - Dual-role completion confirmation (Client or Provider can mark completed).
   - Star rating (1-5 stars) and written client reviews upon job completion.

6. **M-PESA Lipa Na M-PESA STK Push Integration**:
   - Native integration with **PayHero Kenya API v2** payment gateway.
   - One-click "Pay with M-PESA" button initiating STK Push prompts directly to client phones.
   - Automated callback webhook handling with receipt recording (`transaction_id`) and fallback relay support.

7. **Automated Kenyan-Brand Email Notification Engine**:
   - 7 dedicated notification classes: `NewBookingRequestNotification`, `BookingCompletedNotification`, `KycPendingReviewNotification`, `KycApprovedNotification`, `KycRejectedNotification`, `NewReviewNotification`, `WelcomeNotification`.
   - Branded account verification ("Karibu Kaazilink — Verify Your Account").
   - Sensitive PII stripping (National ID numbers omitted from emails for privacy).

8. **Responsive Mobile UI & Legal Compliance**:
   - Alpine.js slide-out mobile navigation drawer menu.
   - Terms of Service (`/terms`) and Privacy Policy (`/privacy`) compliant with the Kenya Data Protection Act (KDPA) 2019.
   - Mandatory legal terms agreement checkbox gating user signup.

---

## User Flows

```
[Client Journey]
Browse Categories / Search Map ──> View Provider Profile & Rates ──> Open Booking Modal
                                                                            │
                                                                            ▼
Leave Star Rating & Review  <── Mark Completed <── Pay via M-PESA STK <── Provider Accepts
```

```
[Provider Journey]
Register Account ──> Upgrade to Provider ──> Complete 4-Step Onboarding Wizard
                                                              │
                                                              ▼
Manage Incoming Jobs <── Receive Approval Email <── Admin Approves KYC (ID + KRA)
```

```
[Admin Journey]
Login to /admin ──> Inspect Pending KYC Submissions ──> Preview ID Scans securely ──> Approve / Reject
```

---

## Tech Stack

### Backend
- **Framework**: Laravel 11 / 12 (PHP 8.3 / 8.4)
- **Reactivity & Components**: Livewire 4 (full-stack reactive components)
- **Authentication**: Laravel Built-in Auth + Laravel Socialite (^5.30) for Google OAuth
- **Payment Processing**: PayHero Kenya SDK (`payherokenya/payhero-php` ^1.0)
- **Storage Driver**: Cloudflare R2 / AWS S3 SDK (`league/flysystem-aws-s3-v3` ^3.35) with local temporary staging

### Frontend
- **CSS Framework**: Tailwind CSS v4 (`@tailwindcss/vite` plugin)
- **UI Component Library**: WireKit (`pushery/wirekit` v2.38)
- **JavaScript & Interactivity**: Alpine.js (integrated with Livewire)
- **Interactive Mapping**: MapLibre GL JS with OpenFreeMap tiles
- **Typography**: Google Fonts (Inter, Outfit)

### Database & Storage
- **Production Database**: PostgreSQL (NeonDB serverless cloud instance with SSL)
- **Local Database**: SQLite (for rapid development/testing)
- **Object Storage**: Cloudflare R2 via custom `kyc` disk abstraction

### Infrastructure & Operations
- **Hosting**: Render.com (Docker Runtime, Free Web Service tier)
- **Web Server & Container**: Nginx + PHP-FPM 8.4 Alpine + Supervisor
- **Build System**: Vite (^8.0.0), `laravel-vite-plugin` (^3.1), Composer
- **Testing**: PHPUnit (^12.5) unit/feature tests, Playwright (^1.63) E2E browser testing

---

## Architecture / Implementation

### Two-Phase Development Methodology
1. **Phase 1 ("The Sockets")**: Scaffolded full visual UI layer, page routing, and model data structures before writing business logic. Stubbed all state machine transition methods with TODO hooks.
2. **Phase 2 ("The Appliances")**: Systematically implemented features one-by-one (OAuth -> KYC -> Service Areas -> Maps -> Bookings -> Notifications -> M-PESA Payments), wiring each into Phase 1 sockets.

### Locked Booking State Machine
The `Booking` model enforces a strict status lifecycle through the following enum values:
`requested` $\rightarrow$ `accepted` $\rightarrow$ `in_progress` $\rightarrow$ `completed`
*(Supported cancellation & exception paths: `rejected`, `cancelled_by_client`, `cancelled_by_provider`, `no_show_client`, `no_show_provider`, `disputed`, `resolved`, `refunded`)*.
All schedule modifications are logged in `booking_reschedules`.

### Payment Gateway Strategy Pattern
- Abstracted via `PaymentGatewayInterface` and `PaymentManager` service class.
- Supports multi-gateway configuration (`PAYMENT_GATEWAY=payhero`).
- **Gateway Lock-in**: At payment creation, the active gateway is recorded in `payments.gateway`. Payment retries strictly enforce the original gateway, ensuring transaction consistency across configuration updates.

### Local Staging for Cloud Temporary File Uploads
- **Serverless Fix**: Livewire temporary file uploads require native PHP stream handles (`stream_get_meta_data`). Cloud S3/R2 drivers crash on temporary file creation on serverless/ephemeral hosts.
- **Solution**: Configured `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=local` pointing to a local writable directory (`storage/app/tmp`), while preserving the final storage disk (`FILESYSTEM_DISK=r2` / `kyc`).

---

## APIs & Integrations

- **PayHero Kenya API v2**:
  - M-PESA STK Push initiation (`SendCustomerMpesaStkPush`).
  - Webhook callback listener handling success/failure/cancellation payloads.
  - PayHero transaction status query API.
- **Google OAuth 2.0 via Laravel Socialite**:
  - Stateless OAuth sign-in flow (`Socialite::driver('google')->stateless()`).
  - Automatic account email deduplication logic.
- **OpenFreeMap & MapLibre GL JS**:
  - Free vector map tiles (Positron style) for service coverage visualization without Google Maps API keys or usage fees.
- **Cloudflare R2 S3-Compatible API**:
  - Secure object storage for provider identity documentation.

---

## Authentication & Security

- **Role-Based Access Control (RBAC)**: Middleware-protected routes for `admin`, `provider`, and `client` roles.
- **Account Deduplication Engine**: Automatically links Google OAuth logins with existing email accounts to prevent duplicate profiles.
- **Protected KYC Document Stream**: KYC document scans are strictly gated behind `Route::get('/kyc/document/{path}')` with ownership and admin authorization checks. Files are never publicly exposed with direct bucket URLs.
- **Privacy Enforcement**: National ID numbers are stripped from outbound email templates to prevent plain-text PII transmission.
- **Webhook CSRF Exemption**: Webhook endpoints (`/api/payments/callback/payhero`) are explicitly exempted from CSRF middleware while enforcing payload validation.

---

## Database / Data Storage

### Database Schema (17 Migrations)

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│      users      │1     *│ kyc_submissions │       │  service_areas  │
├─────────────────┤───────┼─────────────────┤       ├─────────────────┤
│ id              │       │ id              │       │ id              │
│ name, email     │       │ user_id         │       │ name, county    │
│ role, kyc_status│       │ id_front_path   │       │ lat, lng        │
│ phone, bio      │       │ id_back_path    │       └────────┬────────┘
│ id_number, dob  │       │ kra_pin_path    │                │*
└────────┬────────┘       │ selfie_path     │                │
         │                └─────────────────┘                │*
         │1                                         ┌─────────────────┐
         ├─────────────────────────────────────────>│service_area_user│
         │                                          └─────────────────┘
         │1
         ├──────────────────┐
         │*                 │*
┌────────┴────────┐   ┌─────┴───────────┐           ┌─────────────────┐
│provider_services│   │    bookings     │1         *│    payments     │
├─────────────────┤   ├─────────────────┤───────────┼─────────────────┤
│ id, user_id     │   │ id              │           │ id, booking_id  │
│ service_slug    │   │ client_id       │           │ gateway, amount │
│ price           │   │ provider_id     │           │ phone, status   │
│ custom_name     │   │ price, status   │           │ reference       │
│ is_custom       │   │ scheduled_at    │           │ transaction_id  │
└─────────────────┘   └────────┬────────┘           └─────────────────┘
                               │1
                               │*
                      ┌────────┴────────┐
                      │     reviews     │
                      ├─────────────────┤
                      │ id, booking_id  │
                      │ rating (1-5)    │
                      │ comment         │
                      └─────────────────┘
```

---

## Deployment / Infrastructure

- **Containerized Environment**: Custom multi-stage `Dockerfile` (PHP 8.4 FPM Alpine, Nginx, Supervisor, Vite asset build).
- **Cloud Hosting**: Deployed on **Render.com** (Free Web Service tier).
- **Database**: Serverless PostgreSQL hosted on **NeonDB** (`DB_SSLMODE=require`).
- **Storage**: Cloudflare R2 object storage.
- **Container Startup Sequence (`.docker/start.sh`)**:
  1. Compiles Nginx config templates dynamically.
  2. Runs database migrations (`php artisan migrate --force`).
  3. Executes idempotent seeders (`ProviderSeeder`, `ServiceAreaSeeder`, `ReviewSeeder`).
  4. Starts Supervisor managing Nginx and PHP-FPM worker processes.
- **Uptime Maintenance**: `/ping` endpoint returning `pong` (200 OK) configured for external cron keep-alive pinging to prevent Render free-tier instance sleep.

---

## Technical Highlights & Problem Solving

1. **Livewire 3 Overlay Teleport Trap**:
   - *Issue*: WireKit modal components teleported actions into `#wk-overlay-root`, trapping event dispatchers and causing modals to fail silently on category listing pages.
   - *Fix*: Refactored modal triggers to global Alpine/Livewire event listeners (`#[On('open-booking-modal')]`) and decoupled modal state from teleport boundaries.

2. **Serverless Temp Directory Stream Error**:
   - *Issue*: On Windows and serverless hosts without `sys_temp_dir`, Livewire's `tmpfile()` call returned `false`, triggering a fatal `TypeError: stream_get_meta_data(): Argument #1 ($stream) must be of type resource, false given`.
   - *Fix*: Created dedicated `storage/app/tmp` directory, explicitly configured `upload_tmp_dir` and `sys_temp_dir` in `php.ini` / `.env`, and updated Livewire upload disk settings.

3. **Google OAuth Redirect Loop Across Cross-Site Cookies**:
   - *Issue*: Modern browser cross-site cookie restrictions caused session loss during Google OAuth redirects on Render domains, looping users back to `/login`.
   - *Fix*: Enabled `.stateless()` mode in Laravel Socialite, enforced exact redirect URIs, and added explicit session regeneration on authentication.

4. **PayHero Webhook Reference Key Alignment**:
   - *Issue*: PayHero STK callback webhooks sent payment references under varying key names (`User_Reference`, `ExternalReference`, `CheckoutRequestID`), causing payments to remain in pending states.
   - *Fix*: Robustly extracted references using multi-key fallback resolution and deployed a PHP relay script to forward callback payloads smoothly.

---

## Developer Contributions

- **Architected & Engineered Core Platform**: Built the entire Laravel 11/12 and Livewire 4 codebase from initial concept through production deployment.
- **Designed 4-Step Onboarding & KYC Pipeline**: Developed the multi-step provider wizard, document upload system, and protected admin review panel.
- **Integrated M-PESA STK Push Payments**: Implemented the `PayHeroDriver` payment gateway service, transaction management, and webhook handling.
- **Built Map Integration**: Implemented MapLibre GL JS and OpenFreeMap tile integration with backend Haversine distance calculations.
- **Crafted Notification Engine**: Authored 7 Kenyan-branded email notification classes and integrated them with domain lifecycle events.
- **DevOps & Cloud Deployment**: Configured Docker, Nginx, Supervisor, Render.yaml blueprints, NeonDB PostgreSQL connection, and Cloudflare R2 storage integration.
- **Comprehensive Quality Assurance**: Wrote 53 automated PHPUnit feature tests achieving a **100% pass rate**, backed by end-to-end Playwright browser automation suites.

---

## Project Status

- **Status**: **Complete & Production Deployed**
- **Live URL**: `https://kaazilink.onrender.com`
- **Test Coverage**: 53 automated PHPUnit feature tests passed (200+ assertions), zero failing tests.
- **Current Phase**: Phase 1 ("Sockets") and Phase 2 Features 1–7 (Auth, KYC, Service Areas, Maps, Booking & Reviews, Email Notifications, M-PESA STK Push Payments) are 100% complete and verified in production.

---

## Portfolio Description

> ### **KaaziLink — On-Demand Local Service & Skill Marketplace**
>
> **KaaziLink** is a full-stack, Kenyan-context digital marketplace connecting households and businesses with vetted local service providers ("fundis", cleaners, artisans, technicians, and agricultural specialists) across 51 service categories.
>
> **Key Achievements & Technical Accomplishments:**
> - **Full-Stack Reactive Architecture**: Built using Laravel 11/12, Livewire 4, Alpine.js, WireKit, and Tailwind CSS v4 for a seamless single-page application experience without heavy SPA JavaScript frameworks.
> - **4-Step Provider Verification & KYC Pipeline**: Engineered a robust provider onboarding wizard requiring National ID, KRA PIN, and selfie submissions, backed by an admin verification panel with protected document streaming (`/kyc/document/{path}`).
> - **M-PESA STK Push Payment Gateway**: Developed a custom payment driver integrating PayHero Kenya API v2 for instant M-PESA mobile money prompts, transaction receipting, and callback webhook handling.
> - **Geographic Service Mapping**: Integrated MapLibre GL JS with OpenFreeMap tiles and custom raw SQL Haversine distance calculations for location-matched provider discovery across 53 Kenyan county service areas.
> - **Automated Email Engine**: Designed 7 domain notification flows adhering to authentic Kenyan brand voice ("Tupate Fundi Haraka").
> - **Cloud & Container Infrastructure**: Containerized via Docker (PHP 8.4 Alpine, Nginx, Supervisor) and deployed on Render.com with NeonDB serverless PostgreSQL and Cloudflare R2 object storage.
>
> **Tech Stack**: Laravel, Livewire 4, Alpine.js, Tailwind CSS v4, PostgreSQL (NeonDB), Docker, Render, M-PESA (PayHero), MapLibre GL JS, Cloudflare R2, PHPUnit, Playwright.
