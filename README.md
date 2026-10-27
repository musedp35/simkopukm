<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# SIMKOP — Sistem Informasi Koperasi

Migrasi dari **CodeIgniter 3** ke **Laravel 10** untuk aplikasi sistem informasi koperasi.

## Tech Stack

| Komponen | Versi |
|---------|-------|
| PHP | 8.1.10 |
| Laravel | 10.x |
| Database | MySQL 8 (lokal, db: simkopukm) |
| PDF | barryvdh/laravel-dompdf |
| Excel | phpspreadsheet |

## Struktur Folder

```
app/
├── Enums/              # Enum PHP native (Role, Status, dll.)
├── Http/Controllers/
│   ├── Auth/           # LoginController, LogoutController
│   ├── Admin/          # AdminController (dashboard admin)
│   ├── Member/         # MemberController
│   ├── Saving/         # Simpanan controller
│   └── Loan/           # Pinjaman controller
├── Models/             # 41 Eloquent Model
└── Services/           # Service layer (audit, logging, report)

database/
├── seeders/DatabaseSeeder.php   # Seed semua tabel
└── simkopukm_schema.sql         # DDL 43 tabel + seed

resources/views/
├── layouts/app.blade.php        # Layout utama (sidebar + topbar)
├── auth/login.blade.php         # Halaman login
└── dashboard/                    # Dashboard per-role

routes/web.php                  # Semua route
public/assets/                  # Asset dari CI3 (CSS/JS)
```

## 43 Tabel Database

### Master (ma_)
ma_user, ma_role, ma_module, ma_submodule, ma_member, ma_member_management,
ma_member_status, ma_village, ma_district, ma_city, ma_type_city, ma_images,
ma_member_address, ma_employee, ma_constants, ma_audit, ma_family,
ma_family_status, ma_heiress, ma_heiress_address, ma_fee, ma_business

### Simpanan (mu_)
mu_savings_account, mu_simpanan_product, mu_transaction_account,
mu_trans_daily_savings, mu_trans_ntr_savings, mu_trans_ssb_savings,
mu_trans_setiaplus_savings, mu_trans_deposit_interest, mu_trans_setiaplus_interest

### Pinjaman (mu_)
mu_loan, mu_loan_account, mu_loan_product, mu_trans_realization,
mu_trans_installment, mu_collateral_bpkb, mu_collateral_gold,
mu_collateral_salary, mu_collateral_savings, mu_collateral_certificate,
mu_insurance, mu_survey, mu_vehicle

## Setup & Run

```bash
# 1. Buat database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS simkopukm;"

# 2. Import skema + seed
mysql -u root < database/simkopukm_schema.sql

# 3. Jalankan seeder Laravel (untuk password hash)
php artisan db:seed

# 4. Jalankan server
php artisan serve --port=8080
# Buka: http://127.0.0.1:8080/login
```

## Kredensial Test

| Username | Password | Role |
|----------|----------|------|
| admin | admin123 | Admin (A) |
| budi | admin123 | Manager (M) - Member MEM001 |

## Konvensi Penamaan (Rules)

- File: UpperCamelCase.php
- Method: camelCase, prefix: get*, find*, save*, update*, delete*
- Table prefix: ma_ (master/admin), mu_ (member user)
- Field: UPPER_SNAKE_CASE (ikuti CI3)
- PK: MEMBER_REGISTRY (string, bukan auto-increment) untuk tabel utama

## Progress

### [x] Fase 0-1 - Setup & Database (SELESAI)
- [x] Laravel 10 project
- [x] 43 tabel database simkopukm
- [x] DatabaseSeeder (roles, modules, members, users, products)
- [x] Eloquent Models (41 model)
- [x] Login/Logout flow
- [x] Dashboard per-role (admin, dispatcher, manager, member)
- [x] Layout Blade (sidebar + topbar, asset CI3)

### [ ] Fase 2 - CRUD Admin
- [x] Member Management (CRUD)
- [x] Role Management (CRUD)
- [x] Module Management (CRUD)
- [x] Admin sidebar + layout
- [ ] Village/District/City CRUD
- [ ] Employee Management
- [ ] Fee Management
- [ ] User (akun login) Management

### [ ] Fase 3 - Member Self-Service
- [ ] Profil member
- [ ] Riwayat simpanan
- [ ] Riwayat pinjaman & angsuran
- [ ] Pengajuan pinjaman online

### [ ] Fase 4 - Savings Module
- [ ] Deposit / Withdraw
- [ ] Interest calculation
- [ ] Statement PDF

### [ ] Fase 5 - Loan Module
- [ ] Pengajuan pinjaman
- [ ] Approval workflow
- [ ] Angsuran & reminder
- [ ] Collateral management
- [ ] Insurance

### [ ] Fase 6 - Reports & Export
- [ ] Laporan member
- [ ] Laporan keuangan
- [ ] Export PDF (dompdf)
- [ ] Export Excel (phpspreadsheet)

---

*Terakhir update: 2026-10-20*
