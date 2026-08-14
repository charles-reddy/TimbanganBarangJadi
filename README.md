# Logistic Application - Multi Product Weighing System

Aplikasi Logistik untuk manajemen penimbangan multi produk dengan fitur approval, koreksi B10, dan reporting.

---

## 📚 Dokumentasi Proyek

### Dokumentasi Utama

- **[LOGIC_MULTI_PRODUCT_WEIGHING.md](LOGIC_MULTI_PRODUCT_WEIGHING.md)** - Logika dan algoritma penimbangan multi produk
- **[DEPLOYMENT_MANUAL_MULTI_PRODUCT.md](DEPLOYMENT_MANUAL_MULTI_PRODUCT.md)** - Panduan deployment
- **[DEPLOYMENT_GUIDE_INPUT_B10_MULTI_PRODUCT.md](DEPLOYMENT_GUIDE_INPUT_B10_MULTI_PRODUCT.md)** - Panduan deployment fitur input B10

### Update & Enhancement Log

- **[UPDATE_B10_CORRECTION_APPROVAL_WITHOUT_QTY_CHANGE.md](UPDATE_B10_CORRECTION_APPROVAL_WITHOUT_QTY_CHANGE.md)** ⭐ **NEW** - Fitur submit approval tanpa ubah qty (14 Aug 2026)
- **[UPDATE_GANTITGLTM_QUOTA_SHIFT.md](UPDATE_GANTITGLTM_QUOTA_SHIFT.md)** - Update quota shift management

### Saran Implementasi

- **[SARAN_IMPLEMENTASI_MULTI_PRODUCT.md](SARAN_IMPLEMENTASI_MULTI_PRODUCT.md)** - Saran implementasi multi produk
- **[SARAN_IMPLEMENTASI_OPSI_B_INPUT_B10_MULTI_PRODUCT.md](SARAN_IMPLEMENTASI_OPSI_B_INPUT_B10_MULTI_PRODUCT.md)** - Saran implementasi input B10

---

## 🚀 Fitur Utama

- ✅ **Multi Product Weighing** - Timbang multiple produk dalam satu transaksi
- ✅ **B10 Input & Correction** - Input dan koreksi qty karung B10
- ✅ **Approval Workflow** - Sistem approval untuk transaksi out of range
- ✅ **Photo Evidence** - Upload foto bukti untuk koreksi
- ✅ **Audit Trail** - History lengkap semua transaksi dan koreksi
- ✅ **Real-time Preview** - Preview perhitungan sebelum save
- ⭐ **NEW: Approval tanpa ubah qty** - Submit ke approval dengan foto bukti tanpa koreksi qty

---

## 📋 Tech Stack

- **Framework**: Laravel 10.x
- **Frontend**: Livewire, Bootstrap 5, Bootstrap Icons
- **Database**: MySQL
- **File Storage**: Laravel Storage (public disk)

---

## 🔧 Installation

```bash
# Clone repository
git clone [repository-url]

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Link storage
php artisan storage:link

# Compile assets
npm run build

# Start server
php artisan serve
```

---

## 📖 Quick Start

### 1. Workflow Penimbangan

```
Weigh In → Input B10 → Weigh Out → [Out of Range?]
                                          ↓
                                    Yes → Koreksi B10
                                          ↓
                                    [Ubah Qty atau Submit ke Approval]
```

### 2. Role & Permission

- **Operator**: Input weigh in/out, input B10
- **Supervisor B10**: Koreksi B10, upload foto bukti
- **Manager**: Approval/reject transaksi out of range

### 3. Dokumentasi Lengkap

Lihat [UPDATE_B10_CORRECTION_APPROVAL_WITHOUT_QTY_CHANGE.md](UPDATE_B10_CORRECTION_APPROVAL_WITHOUT_QTY_CHANGE.md) untuk panduan lengkap fitur terbaru.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
